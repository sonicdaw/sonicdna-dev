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

abstract class PluginBandTable extends opAccessControlDoctrineTable
{
  public function retrievesByMemberId($memberId, $limit = 5, $isRandom = false)
  {
    $bandMembers = Doctrine::getTable('BandMember')->createQuery()
      ->select('band_id')
      ->where('is_pre = ?', false)
      ->andWhere('member_id = ?', $memberId)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($bandMembers as $bandMember)
    {
      $ids[] = $bandMember[0];
    }

    if (empty($ids))
    {
      return;
    }

    $q = $this->createQuery()->whereIn('id', $ids);

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

  public function getJoinBandListPager($memberId, $page = 1, $size = 20)
  {
    $bandMembers = Doctrine::getTable('BandMember')->createQuery()
      ->select('band_id')
      ->where('member_id = ?', $memberId)
      ->andWhere('is_pre = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($bandMembers as $bandMember)
    {
      $ids[] = $bandMember[0];
    }

    $pager = new sfDoctrinePager('Band', $size);

    if (empty($ids))
    {
      return $pager;
    }

    $q = $this->createQuery()
      ->whereIn('id', $ids);
 
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getBandMemberListPager($bandId, $page = 1, $size = 20)
  {
    $bandMembers = Doctrine::getTable('BandMember')->createQuery()
      ->select('member_id')
      ->where('band_id = ?', $bandId)
      ->andWhere('is_pre = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($bandMembers as $bandMember)
    {
      $ids[] = $bandMember[0];
    }

    $pager = new opNonCountQueryPager('Member', $size);

    if (empty($ids))
    {
      return $pager;
    }

    $q = Doctrine::getTable('Member')->createQuery()
      ->whereIn('id', $ids);

    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getIdsByMemberId($memberId)
  {
    $result = array();

    $resultSet = Doctrine::getTable('BandMember')->createQuery()
      ->select('band_id')
      ->where('member_id = ?', $memberId)
      ->andWhere('is_pre = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    foreach ($resultSet as $value)
    {
      $result[] = $value[0];
    }

    return $result;
  }

  public function getDefaultCommunities()
  {
    $bandConfigs = Doctrine::getTable('BandConfig')->createQuery()
      ->select('band_id')
      ->where('name = ?', 'is_default')
      ->andWhere('value = ?', true)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($bandConfigs as $bandConfig)
    {
      $ids[] = $bandConfig[0];
    }
    if (empty($ids))
    {
      return null;
    }

    return $this->createQuery()
      ->whereIn('id', $ids)
      ->execute();
  }

  public function getPositionRequestCommunitiesQuery($position = 'admin', $memberId = null)
  {
    if (null === $memberId)
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    $bandMemberPositions = Doctrine::getTable('BandMemberPosition')->findByMemberIdAndName($memberId,  $position.'_confirm');

    if (!$bandMemberPositions || !count($bandMemberPositions))
    {
      return null;
    }

    return $this->createQuery()
      ->whereIn('id', array_values($bandMemberPositions->toKeyValueArray('id', 'band_id')));
  }

  public function getPositionRequestCommunities($position = 'admin', $memberId = null)
  {
    $q = $this->getPositionRequestCommunitiesQuery($position, $memberId);
    return $q ? $q->execute() : null;
  }

  public function countPositionRequestCommunities($position = 'admin', $memberId = null)
  {
    $q = $this->getPositionRequestCommunitiesQuery($position, $memberId);
    return $q ? $q->count() : null;
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
      ->allow('everyone', $resource, 'view')
      ->allow('sub_admin', $resource, 'edit')
      ->allow('admin', $resource, 'edit');
  }

  protected static function confirmList(sfEvent $event, $position = 'admin')
  {
    $communities = Doctrine::getTable('Band')->getPositionRequestCommunities($position, $event['member']->id);

    if (!$communities)
    {
      return false;
    }

    $list = array();
    foreach ($communities as $band)
    {
      $list[] = array(
        'id' => $band->id,
        'image' => array(
          'url' => $band->getAdminMember()->getImageFileName(),
          'link' => '@member_profile?id='.$band->getAdminMember()->id,
        ),
        'list' => array(
          '%nickname%' => array(
            'text' => $band->getAdminMember()->name,
            'link' => '@member_profile?id='.$band->getAdminMember()->id,
          ),
          '%band%' => array(
            'text' => $band->name,
            'link' => '@band_home?id='.$band->id,
          ),
        ),
      );
    }

    $event->setReturnValue($list);

    return true;
  }

  public static function adminConfirmList(sfEvent $event)
  {
    return self::confirmList($event, 'admin');
  }

  public static function subAdminConfirmList(sfEvent $event)
  {
    return self::confirmList($event, 'sub_admin');
  }

  public static function processAdminConfirm(sfEvent $event)
  {
    $bandMemberPosition = Doctrine::getTable('BandMemberPosition')
      ->findOneByMemberIdAndBandIdAndName($event['member']->id, $event['id'], 'admin_confirm');
    if (!$bandMemberPosition)
    {
      return false;
    }

    if ($event['is_accepted'])
    {
      Doctrine::getTable('BandMember')->changeAdmin($event['member']->id, $event['id']);
      $event->setReturnValue('You have just accepted taking over %band%');
    }
    else
    {
      $bandMemberPosition->getBandMember()->removePosition('admin_confirm');
      $event->setReturnValue('You have just rejected taking over %band%');
    }

    return true;
  }

  public static function processSubAdminConfirm(sfEvent $event)
  {
    $bandMemberPosition = Doctrine::getTable('BandMemberPosition')
      ->findOneByMemberIdAndBandIdAndName($event['member']->id, $event['id'], 'sub_admin_confirm');
    if (!$bandMemberPosition)
    {
      return false;
    }

    if ($event['is_accepted'])
    {
      Doctrine::getTable('BandMember')->addSubAdmin($event['member']->id, $event['id']);
      $event->setReturnValue('You have just accepted request of %band% sub-administrator');
    }
    else
    {
      $bandMemberPosition->getBandMember()->removePosition('sub_admin_confirm');
      $event->setReturnValue("You have just rejected request of %band% sub-administrator");
    }

    return true;
  }
}
