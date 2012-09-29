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

abstract class PluginBandMemberTable extends opAccessControlDoctrineTable
{
  public function retrieveByMemberIdAndBandId($memberId, $bandId)
  {
    return $this->createQuery()
        ->where('member_id = ?', $memberId)
        ->andWhere('band_id = ?', $bandId)
        ->fetchOne();
  }

  protected function isPosition($memberId, $bandId, $position)
  {
    $object = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if ($object)
    {
      return $object->hasPosition($position);
    }
    return false;
  }

  public function isMember($memberId, $bandId)
  {
    if ($this->retrieveByMemberIdAndBandId($memberId, $bandId))
    {
      return !$this->isPreMember($memberId, $bandId);
    }
    return false;
  }

  public function isPreMember($memberId, $bandId)
  {
    $object = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if ($object && $object->getIsPre())
    {
      return true;
    }
    return false;
  }

  public function isAdmin($memberId, $bandId)
  {
    return $this->isPosition($memberId, $bandId, 'admin');
  }

  public function isSubAdmin($memberId, $bandId)
  {
    return $this->isPosition($memberId, $bandId, 'sub_admin');
  }

  public function join($memberId, $bandId, $isRegisterPolicy = 'open')
  {
    if ($this->isPreMember($memberId, $bandId))
    {
      throw new Exception('This member has already applied this band.');
    }

    if ($this->isMember($memberId, $bandId))
    {
      throw new Exception('This member has already joined this band.');
    }

    $bandMember = new BandMember();
    $bandMember->setMemberId($memberId);
    $bandMember->setBandId($bandId);
    if ($isRegisterPolicy == 'close')
    {
      $bandMember->setIsPre(true);
    }
    $bandMember->save();
  }

  public function quit($memberId, $bandId)
  {
    if (!$this->isMember($memberId, $bandId)) {
      throw new Exception('This member is not a member of this band.');
    }

    if ($this->isAdmin($memberId, $bandId)) {
      throw new Exception('This member is band admin.');
    }

    $bandMember = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    $bandMember->delete();
  }

  public function getBandAdmin($bandId)
  {
    return Doctrine::getTable('BandMemberPosition')->findOneByBandIdAndName($bandId, 'admin');
  }

  public function getBandSubAdmin($bandId)
  {
    return Doctrine::getTable('BandMemberPosition')->findByBandIdAndName($bandId, 'sub_admin');
  }

  public function getBandIdsOfAdminByMemberId($memberId)
  {
    $objects = Doctrine::getTable('BandMemberPosition')->findByMemberIdAndName($memberId, 'admin');

    $results = array();
    foreach ($objects as $obj)
    {
      $results[] = $obj->getBandId();
    }
    return $results;
  }

  public function getBandMembersPreQuery($memberId)
  {
    $adminBandIds = $this->getBandIdsOfAdminByMemberId($memberId);

    if (count($adminBandIds))
    {
      return Doctrine::getTable('BandMember')->createQuery()
        ->whereIn('band_id', $adminBandIds)
        ->andWhere('is_pre = ?', true);
    }

    return false;
  }

  public function getBandMembersPre($memberId)
  {
    $q = $this->getBandMembersPreQuery($memberId);

    if (!$q)
    {
      return array();
    }

    return $q->execute();
  }

  public function countBandMembersPre($memberId)
  {
    $q = $this->getBandMembersPreQuery($memberId);
    if (!$q)
    {
      return 0;
    }

    return $q->count();
  }

  public function getBandMembers($bandId)
  {
    $subqueryResults = Doctrine::getTable('BandMemberPosition')->createQuery()
      ->where('band_id = ?', $bandId)
      ->andWhere('name = ?','admin')
      ->execute();

    $ids = array();
    foreach ($subqueryResults as $result)
    {
      $ids[] = $result->getBandMemberId();
    }

    return $this->createQuery()
      ->where('band_id = ?', $bandId)
      ->andWhere('is_pre = ?', false)
      ->andWhereNotIn('id', $ids)
      ->execute();
  }

  protected function requestChangePosition($memberId, $bandId, $fromMemberId = null, $position = 'admin')
  {
    if (null === $fromMemberId)
    {
      $fromMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    if (!$this->isAdmin($fromMemberId, $bandId))
    {
      throw new Exception("Requester isn't band's admin.");
    }

    $bandMember = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if (!$bandMember)
    {
      throw new Exception("Invalid band member.");
    }

    if ($bandMember->getIsPre())
    {
      throw new Exception("This member is pre-member.");
    }

    $dennyPositions = array('admin', 'admin_confirm');
    if ('admin' !== $position)
    {
      $dennyPositions[] = $position;
      $dennyPositions[] = $position.'_confirm';
    }
    if ($bandMember->hasPosition($dennyPositions))
    {
      throw new Exception("This member is already position of something.");
    }

    $nowRequestMember = Doctrine::getTable('BandMemberPosition')->findOneByBandIdAndName($bandId, $position.'_confirm');
    if ($nowRequestMember)
    {
      $nowRequestMember->delete();
    }

    $bandMember->addPosition($position.'_confirm');
  }

  public function requestAddPosition($memberId, $bandId, $fromMemberId = null, $position = 'sub_admin')
  {
    if (null === $fromMemberId)
    {
      $fromMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    if (!$this->isAdmin($fromMemberId, $bandId))
    {
      throw new Exception("Requester isn't band's admin.");
    }

    $bandMember = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if (!$bandMember)
    {
      throw new Exception("Invalid band member.");
    }

    if ($bandMember->getIsPre())
    {
      throw new Exception("This member is pre-member.");
    }

    $dennyPositions = array('admin', 'admin_confirm');
    $dennyPositions[] = $position;
    $dennyPositions[] = $position.'_confirm';
    if ($bandMember->hasPosition($dennyPositions))
    {
      throw new Exception("This member is already position of something.");
    }

    $bandMember->addPosition($position.'_confirm');
    $bandMember->save();
  }

  public function requestChangeAdmin($memberId, $bandId, $fromMemberId = null)
  {
    $this->requestChangePosition($memberId, $bandId, $fromMemberId, 'admin');
  }

  public function requestSubAdmin($memberId, $bandId, $fromMemberId = null)
  {
    $this->requestAddPosition($memberId, $bandId, $fromMemberId, 'sub_admin');
  }

  protected function addPosition($memberId, $bandId, $position = 'sub_admin')
  {
    $bandMember = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if (!$bandMember)
    {
      throw new Exception("Invalid band member.");
    }
    if (!$bandMember->hasPosition($position.'_confirm'))
    {
      throw new Exception('This member position isn\'t "'.$position.'_confirm".');
    }

    try
    {
      $this->getConnection()->beginTransaction();

      $bandMember->removePosition($position.'_confirm');
      $bandMember->addPosition($position);

      $this->getConnection()->commit();
    }
    catch (Exception $e)
    {
      $this->getConnection()->rollback();
      throw $e;
    }
  }

  public function changeAdmin($memberId, $bandId)
  {
    $bandMember = $this->retrieveByMemberIdAndBandId($memberId, $bandId);
    if (!$bandMember)
    {
      throw new Exception("Invalid band member.");
    }
    if (!$bandMember->hasPosition('admin_confirm'))
    {
      throw new Exception('This member position isn\'t "admin_confirm".');
    }

    $nowAdmin = $this->getBandAdmin($bandId);
    if (!$nowAdmin)
    {
      throw new Exception("Band's admin was not found.");
    }

    try
    {
      $this->getConnection()->beginTransaction();

      $bandMember->removeAllPosition();
      $bandMember->addPosition('admin');
      $nowAdmin->delete();

      $this->getConnection()->commit();
    }
    catch(Exception $e)
    {
      $this->getConnection()->rollback();
      throw $e;
    }
  }

  public function addSubAdmin($memberId, $bandId)
  {
    $this->addPosition($memberId, $bandId, 'sub_admin');
  }

  public function getMemberIdsByBandId($bandId)
  {
    return Doctrine::getTable('BandMember')->createQuery()
      ->select('id', 'member_id')
      ->where('band_id = ?', $bandId)
      ->execute()
      ->toKeyValueArray('id', 'member_id');
  }

  public function appendRoles(Zend_Acl $acl)
  {
    return $acl
      ->addRole(new Zend_Acl_Role('everyone'))
      ->addRole(new Zend_Acl_Role('member'), 'everyone')
      ->addRole(new Zend_Acl_Role('sub_admin'), 'member')
      ->addRole(new Zend_Acl_Role('admin'), 'member');
  }

  public function appendRules(Zend_Acl $acl, $resource = null)
  {
    return $acl
      ->allow('sub_admin', $resource, 'view')
      ->allow('sub_admin', $resource, 'edit')
      ->allow('admin', $resource, 'view')
      ->allow('admin', $resource, 'edit');
  }

  public static function joinConfirmList(sfEvent $event)
  {
    $list = array();
    $members = Doctrine::getTable('BandMember')->getBandMembersPre($event['member']->id);
    foreach ($members as $member)
    {
      $list[] = array(
        'id' => $member->id,
        'image' => array(
          'url' => $member->getMember()->getImageFileName(),
          'link' => '@member_profile?id='.$member->getMember()->id,
        ),
        'list' => array(
          '%nickname%' => array(
            'text' => $member->getMember()->name,
            'link' => '@member_profile?id='.$member->getMember()->id,
          ),
          '%band%' => array(
            'text' => $member->getBand()->name,
            'link' => '@band_home?id='.$member->getBand()->id,
          ),
        ),
      );
    }

    $event->setReturnValue($list);

    return true;
  }

  public static function processJoinConfirm(sfEvent $event)
  {
    $bandMember = Doctrine::getTable('BandMember')->find($event['id']);
    if (!($bandMember && $bandMember->getIsPre()))
    {
      return false;
    }

    $i18n = sfContext::getInstance()->getI18N();
    if ($event['is_accepted'])
    {
      $bandMember->setIsPre(false);
      $bandMember->save();

      opBandPluginAction::sendJoinMail($bandMember->getMember()->id, $bandMember->getBand()->id);

      $event->setReturnValue($i18n->__('You have just accepted joining to %1%', array('%1%' => $bandMember->getBand()->getName())));
    }
    else
    {
      $bandMember->delete();

      $event->setReturnValue($i18n->__('You have just rejected joining to %1%', array('%1%' => $bandMember->getBand()->getName())));
    }

    return true;
  }
}
