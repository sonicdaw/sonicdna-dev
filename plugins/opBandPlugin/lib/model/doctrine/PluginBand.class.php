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

abstract class PluginBand extends BaseBand implements opAccessControlRecordInterface
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
    $configs = sfConfig::get('openpne_band_config');

    $myConfigs = Doctrine::getTable('BandConfig')->findByBandId($this->id);

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
    return Doctrine::getTable('BandConfig')->retrieveValueByNameAndBandId($configName, $this->getId());
  }

  public function setConfig($name, $value)
  {
    $config = Doctrine::getTable('BandConfig')->findOneByNameAndBandId($name, $this->getId());

    if (!$config)
    {
      $config = new BandConfig();
      $config->setBand($this);
      $config->setName($name);
    }

    $config->setValue($value);
    $config->save();
  }

  public function getMembers($limit = null, $isRandom = false)
  {
    $bandMembers = Doctrine::getTable('BandMember')->createQuery()
      ->where('band_id = ?', $this->id)
      ->andWhere('is_pre = ?', false)
      ->execute();

    $q = Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', array_values($bandMembers->toKeyValueArray('id', 'member_id')));

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
    return Doctrine::getTable('BandMember')->getBandAdmin($this->getId())->getMember();
  }

  public function getSubAdminMembers()
  {
    $bandMemberPositions = Doctrine::getTable('BandMember')->getBandSubAdmin($this->getId());
    if (!($bandMemberPositions && count($bandMemberPositions)))
    {
      return array();
    }
    return Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', array_values($bandMemberPositions->toKeyValueArray('id', 'member_id')))
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
    return Doctrine::getTable('BandMember')->isMember($memberId, $this->id);
  }

  public function isAdmin($memberId)
  {
    return Doctrine::getTable('BandMember')->isAdmin($memberId, $this->id);
  }

  public function countBandMembers()
  {
    $inactiveMemberIds = Doctrine::getTable('Member')->getInactiveMemberIds();

   return Doctrine::getTable('BandMember')->createQuery()
      ->whereNotIn('member_id', $inactiveMemberIds)
      ->andWhere('band_id = ?', $this->id)
      ->andWhere('is_pre = ?', false)
      ->count();
  }

  public function getNameAndCount($format = '%s (%d)')
  {
    return sprintf($format, $this->getName(), $this->countBandMembers());
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
      return '%Band%\'s admin authorization needed';
    }
  }

  public function getChangeAdminRequestMember()
  {
    $bandMemberPosition = Doctrine::getTable('BandMemberPosition')->findOneByBandIdAndName($this->id, 'admin_confirm');
    if ($bandMemberPosition)
    {
      return $bandMemberPosition->getMember();
    }
    return null;
  }

  public function generateRoleId(Member $member)
  {
    if (Doctrine::getTable('BandMember')->isAdmin($member->id, $this->id))
    {
      return 'admin';
    }
    elseif (Doctrine::getTable('BandMember')->isSubAdmin($member->id, $this->id))
    {
      return 'sub_admin';
    }
    elseif (Doctrine::getTable('BandMember')->isMember($member->id, $this->id))
    {
      return 'member';
    }

    return 'everyone';
  }
}
