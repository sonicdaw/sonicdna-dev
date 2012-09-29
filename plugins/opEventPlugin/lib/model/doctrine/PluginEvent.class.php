<?php

/**
 * This file is part of the sonicdna.net package.
 * (c) sonicdna.net Project (http://sonicdna.net)
 *
 * This file is derivative work of community module in the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

abstract class PluginEvent extends BaseEvent implements opAccessControlRecordInterface
{
  public function getImageFileName()
  {
    if ($this->File)
    {
      return $this->File->name;
    }

    return '';
  }

  public function getConfigs()
  {
    $configs = sfConfig::get('openpne_event_config');

    $myConfigs = Doctrine::getTable('EventConfig')->findByEventId($this->id);

    $result = array();

    // initialize
    foreach ($configs as $key => $config)
    {
      $result[$config['Caption']] = '';
      if (isset($config[$key]['Default']))
      {
        $result[$config['Caption']] = $config[$key]['Default'];
      }
    }
    
    // set my configure
    foreach ($myConfigs as $myConfig)
    {
      $name = $myConfig->getName();
      if (isset($configs[$name]))
      {
        switch ($configs[$name]['FormType'])
        {
          case 'checkbox' :
          // FIXME
          case 'radio' :
          case 'select' :
            $value = $myConfig->getValue();
            if (isset($configs[$name]['Choices'][$value]))
            {
              $i18n = sfContext::getInstance()->getI18N();
              $result[$configs[$name]['Caption']] = $i18n->__($configs[$name]['Choices'][$value]);
            }
            break;
          default :
            $result[$configs[$name]['Caption']] = $myConfig->getValue();
        }
        $configs[$myConfig->getName()] = $myConfig->getValue();
      }
    }

    return $result;
  }

  public function getConfig($configName)
  {
    return Doctrine::getTable('EventConfig')->retrieveValueByNameAndEventId($configName, $this->getId());
  }

  public function setConfig($name, $value)
  {
    $config = Doctrine::getTable('EventConfig')->findOneByNameAndEventId($name, $this->getId());

    if (!$config)
    {
      $config = new EventConfig();
      $config->setEvent($this);
      $config->setName($name);
    }

    $config->setValue($value);
    $config->save();
  }

  public function getMembers($limit = null, $isRandom = false)
  {
    $eventMembers = Doctrine::getTable('EventMember')->createQuery()
      ->where('event_id = ?', $this->id)
      ->andWhere('is_pre = ?', false)
      ->execute();

    $q = Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', array_values($eventMembers->toKeyValueArray('id', 'member_id')));

    if (!is_null($limit))
    {
      $q->limit($limit);
    }

    if ($isRandom)
    {
      $expr = new Doctrine_Expression('RANDOM()');
      $q->orderBy($expr);
    }

    return $q->execute();
  }

  public function getAdminMember()
  {
    return Doctrine::getTable('EventMember')->getEventAdmin($this->getId())->getMember();
  }

  public function getSubAdminMembers()
  {
    $eventMemberPositions = Doctrine::getTable('EventMember')->getEventSubAdmin($this->getId());
    if (!($eventMemberPositions && count($eventMemberPositions)))
    {
      return array();
    }
    return Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', array_values($eventMemberPositions->toKeyValueArray('id', 'member_id')))
      ->execute();
  }

  public function checkPrivilegeBelong($memberId)
  {
    if (!$this->isPrivilegeBelong($memberId))
    {
      throw new opPrivilegeException('fail');
    }
  }

  public function isPrivilegeBelong($memberId)
  {
    return Doctrine::getTable('EventMember')->isMember($memberId, $this->id);
  }

  public function isAdmin($memberId)
  {
    return Doctrine::getTable('EventMember')->isAdmin($memberId, $this->id);
  }

  public function countEventMembers()
  {
    $inactiveMemberIds = Doctrine::getTable('Member')->getInactiveMemberIds();

   return Doctrine::getTable('EventMember')->createQuery()
      ->whereNotIn('member_id', $inactiveMemberIds)
      ->andWhere('event_id = ?', $this->id)
      ->andWhere('is_pre = ?', false)
      ->count();
  }

  public function getNameAndCount($format = '%s (%d)')
  {
    return sprintf($format, $this->getName(), $this->countEventMembers());
  }

  public function getRegisterPolicy()
  {
    $register_policy = $this->getConfig('register_policy');
    if ('open' === $register_policy)
    {
      return 'Everyone can join';
    }
    else if ('close' === $register_policy)
    {
      return '%Event%\'s admin authorization needed';
    }
  }

  public function getChangeAdminRequestMember()
  {
    $eventMemberPosition = Doctrine::getTable('EventMemberPosition')->findOneByEventIdAndName($this->id, 'admin_confirm');
    if ($eventMemberPosition)
    {
      return $eventMemberPosition->getMember();
    }
    return null;
  }

  public function generateRoleId(Member $member)
  {
    if (Doctrine::getTable('EventMember')->isAdmin($member->id, $this->id))
    {
      return 'admin';
    }
    elseif (Doctrine::getTable('EventMember')->isSubAdmin($member->id, $this->id))
    {
      return 'sub_admin';
    }
    elseif (Doctrine::getTable('EventMember')->isMember($member->id, $this->id))
    {
      return 'member';
    }

    return 'everyone';
  }
}
