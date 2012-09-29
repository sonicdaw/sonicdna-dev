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

abstract class PluginEventTable extends opAccessControlDoctrineTable
{
  public function retrievesByMemberId($memberId, $limit = 5, $isRandom = false)
  {
    $eventMembers = Doctrine::getTable('EventMember')->createQuery()
      ->select('event_id')
      ->where('is_pre = ?', false)
      ->andWhere('member_id = ?', $memberId)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($eventMembers as $eventMember)
    {
      $ids[] = $eventMember[0];
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

  public function getJoinEventListPager($memberId, $page = 1, $size = 20)
  {
    $eventMembers = Doctrine::getTable('EventMember')->createQuery()
      ->select('event_id')
      ->where('member_id = ?', $memberId)
      ->andWhere('is_pre = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($eventMembers as $eventMember)
    {
      $ids[] = $eventMember[0];
    }

    $pager = new sfDoctrinePager('Event', $size);

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

  public function getEventMemberListPager($eventId, $page = 1, $size = 20)
  {
    $eventMembers = Doctrine::getTable('EventMember')->createQuery()
      ->select('member_id')
      ->where('event_id = ?', $eventId)
      ->andWhere('is_pre = ?', false)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($eventMembers as $eventMember)
    {
      $ids[] = $eventMember[0];
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

    $resultSet = Doctrine::getTable('EventMember')->createQuery()
      ->select('event_id')
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
    $eventConfigs = Doctrine::getTable('EventConfig')->createQuery()
      ->select('event_id')
      ->where('name = ?', 'is_default')
      ->andWhere('value = ?', true)
      ->execute(array(), Doctrine::HYDRATE_NONE);

    $ids = array();
    foreach ($eventConfigs as $eventConfig)
    {
      $ids[] = $eventConfig[0];
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

    $eventMemberPositions = Doctrine::getTable('EventMemberPosition')->findByMemberIdAndName($memberId,  $position.'_confirm');

    if (!$eventMemberPositions || !count($eventMemberPositions))
    {
      return null;
    }

    return $this->createQuery()
      ->whereIn('id', array_values($eventMemberPositions->toKeyValueArray('id', 'event_id')));
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
    $communities = Doctrine::getTable('Event')->getPositionRequestCommunities($position, $event['member']->id);

    if (!$communities)
    {
      return false;
    }

    $list = array();
    foreach ($communities as $event)
    {
      $list[] = array(
        'id' => $event->id,
        'image' => array(
          'url' => $event->getAdminMember()->getImageFileName(),
          'link' => '@member_profile?id='.$event->getAdminMember()->id,
        ),
        'list' => array(
          '%nickname%' => array(
            'text' => $event->getAdminMember()->name,
            'link' => '@member_profile?id='.$event->getAdminMember()->id,
          ),
          '%event%' => array(
            'text' => $event->name,
            'link' => '@event_home?id='.$event->id,
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
    $eventMemberPosition = Doctrine::getTable('EventMemberPosition')
      ->findOneByMemberIdAndEventIdAndName($event['member']->id, $event['id'], 'admin_confirm');
    if (!$eventMemberPosition)
    {
      return false;
    }

    if ($event['is_accepted'])
    {
      Doctrine::getTable('EventMember')->changeAdmin($event['member']->id, $event['id']);
      $event->setReturnValue('You have just accepted taking over %event%');
    }
    else
    {
      $eventMemberPosition->getEventMember()->removePosition('admin_confirm');
      $event->setReturnValue('You have just rejected taking over %event%');
    }

    return true;
  }

  public static function processSubAdminConfirm(sfEvent $event)
  {
    $eventMemberPosition = Doctrine::getTable('EventMemberPosition')
      ->findOneByMemberIdAndEventIdAndName($event['member']->id, $event['id'], 'sub_admin_confirm');
    if (!$eventMemberPosition)
    {
      return false;
    }

    if ($event['is_accepted'])
    {
      Doctrine::getTable('EventMember')->addSubAdmin($event['member']->id, $event['id']);
      $event->setReturnValue('You have just accepted request of %event% sub-administrator');
    }
    else
    {
      $eventMemberPosition->getEventMember()->removePosition('sub_admin_confirm');
      $event->setReturnValue("You have just rejected request of %event% sub-administrator");
    }

    return true;
  }
}
