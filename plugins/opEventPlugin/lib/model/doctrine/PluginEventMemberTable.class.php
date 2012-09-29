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

abstract class PluginEventMemberTable extends opAccessControlDoctrineTable
{
  public function retrieveByMemberIdAndEventId($memberId, $eventId)
  {
    return $this->createQuery()
        ->where('member_id = ?', $memberId)
        ->andWhere('event_id = ?', $eventId)
        ->fetchOne();
  }

  protected function isPosition($memberId, $eventId, $position)
  {
    $object = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if ($object)
    {
      return $object->hasPosition($position);
    }
    return false;
  }

  public function isMember($memberId, $eventId)
  {
    if ($this->retrieveByMemberIdAndEventId($memberId, $eventId))
    {
      return !$this->isPreMember($memberId, $eventId);
    }
    return false;
  }

  public function isPreMember($memberId, $eventId)
  {
    $object = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if ($object && $object->getIsPre())
    {
      return true;
    }
    return false;
  }

  public function isAdmin($memberId, $eventId)
  {
    return $this->isPosition($memberId, $eventId, 'admin');
  }

  public function isSubAdmin($memberId, $eventId)
  {
    return $this->isPosition($memberId, $eventId, 'sub_admin');
  }

  public function join($memberId, $eventId, $isRegisterPolicy = 'open')
  {
    if ($this->isPreMember($memberId, $eventId))
    {
      throw new Exception('This member has already applied this event.');
    }

    if ($this->isMember($memberId, $eventId))
    {
      throw new Exception('This member has already joined this event.');
    }

    $eventMember = new EventMember();
    $eventMember->setMemberId($memberId);
    $eventMember->setEventId($eventId);
    if ($isRegisterPolicy == 'close')
    {
      $eventMember->setIsPre(true);
    }
    $eventMember->save();
  }

  public function quit($memberId, $eventId)
  {
    if (!$this->isMember($memberId, $eventId)) {
      throw new Exception('This member is not a member of this event.');
    }

    if ($this->isAdmin($memberId, $eventId)) {
      throw new Exception('This member is event admin.');
    }

    $eventMember = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    $eventMember->delete();
  }

  public function getEventAdmin($eventId)
  {
    return Doctrine::getTable('EventMemberPosition')->findOneByEventIdAndName($eventId, 'admin');
  }

  public function getEventSubAdmin($eventId)
  {
    return Doctrine::getTable('EventMemberPosition')->findByEventIdAndName($eventId, 'sub_admin');
  }

  public function getEventIdsOfAdminByMemberId($memberId)
  {
    $objects = Doctrine::getTable('EventMemberPosition')->findByMemberIdAndName($memberId, 'admin');

    $results = array();
    foreach ($objects as $obj)
    {
      $results[] = $obj->getEventId();
    }
    return $results;
  }

  public function getEventMembersPreQuery($memberId)
  {
    $adminEventIds = $this->getEventIdsOfAdminByMemberId($memberId);

    if (count($adminEventIds))
    {
      return Doctrine::getTable('EventMember')->createQuery()
        ->whereIn('event_id', $adminEventIds)
        ->andWhere('is_pre = ?', true);
    }

    return false;
  }

  public function getEventMembersPre($memberId)
  {
    $q = $this->getEventMembersPreQuery($memberId);

    if (!$q)
    {
      return array();
    }

    return $q->execute();
  }

  public function countEventMembersPre($memberId)
  {
    $q = $this->getEventMembersPreQuery($memberId);
    if (!$q)
    {
      return 0;
    }

    return $q->count();
  }

  public function getEventMembers($eventId)
  {
    $subqueryResults = Doctrine::getTable('EventMemberPosition')->createQuery()
      ->where('event_id = ?', $eventId)
      ->andWhere('name = ?','admin')
      ->execute();

    $ids = array();
    foreach ($subqueryResults as $result)
    {
      $ids[] = $result->getEventMemberId();
    }

    return $this->createQuery()
      ->where('event_id = ?', $eventId)
      ->andWhere('is_pre = ?', false)
      ->andWhereNotIn('id', $ids)
      ->execute();
  }

  protected function requestChangePosition($memberId, $eventId, $fromMemberId = null, $position = 'admin')
  {
    if (null === $fromMemberId)
    {
      $fromMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    if (!$this->isAdmin($fromMemberId, $eventId))
    {
      throw new Exception("Requester isn't event's admin.");
    }

    $eventMember = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if (!$eventMember)
    {
      throw new Exception("Invalid event member.");
    }

    if ($eventMember->getIsPre())
    {
      throw new Exception("This member is pre-member.");
    }

    $dennyPositions = array('admin', 'admin_confirm');
    if ('admin' !== $position)
    {
      $dennyPositions[] = $position;
      $dennyPositions[] = $position.'_confirm';
    }
    if ($eventMember->hasPosition($dennyPositions))
    {
      throw new Exception("This member is already position of something.");
    }

    $nowRequestMember = Doctrine::getTable('EventMemberPosition')->findOneByEventIdAndName($eventId, $position.'_confirm');
    if ($nowRequestMember)
    {
      $nowRequestMember->delete();
    }

    $eventMember->addPosition($position.'_confirm');
  }

  public function requestAddPosition($memberId, $eventId, $fromMemberId = null, $position = 'sub_admin')
  {
    if (null === $fromMemberId)
    {
      $fromMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    if (!$this->isAdmin($fromMemberId, $eventId))
    {
      throw new Exception("Requester isn't event's admin.");
    }

    $eventMember = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if (!$eventMember)
    {
      throw new Exception("Invalid event member.");
    }

    if ($eventMember->getIsPre())
    {
      throw new Exception("This member is pre-member.");
    }

    $dennyPositions = array('admin', 'admin_confirm');
    $dennyPositions[] = $position;
    $dennyPositions[] = $position.'_confirm';
    if ($eventMember->hasPosition($dennyPositions))
    {
      throw new Exception("This member is already position of something.");
    }

    $eventMember->addPosition($position.'_confirm');
    $eventMember->save();
  }

  public function requestChangeAdmin($memberId, $eventId, $fromMemberId = null)
  {
    $this->requestChangePosition($memberId, $eventId, $fromMemberId, 'admin');
  }

  public function requestSubAdmin($memberId, $eventId, $fromMemberId = null)
  {
    $this->requestAddPosition($memberId, $eventId, $fromMemberId, 'sub_admin');
  }

  protected function addPosition($memberId, $eventId, $position = 'sub_admin')
  {
    $eventMember = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if (!$eventMember)
    {
      throw new Exception("Invalid event member.");
    }
    if (!$eventMember->hasPosition($position.'_confirm'))
    {
      throw new Exception('This member position isn\'t "'.$position.'_confirm".');
    }

    try
    {
      $this->getConnection()->beginTransaction();

      $eventMember->removePosition($position.'_confirm');
      $eventMember->addPosition($position);

      $this->getConnection()->commit();
    }
    catch (Exception $e)
    {
      $this->getConnection()->rollback();
      throw $e;
    }
  }

  public function changeAdmin($memberId, $eventId)
  {
    $eventMember = $this->retrieveByMemberIdAndEventId($memberId, $eventId);
    if (!$eventMember)
    {
      throw new Exception("Invalid event member.");
    }
    if (!$eventMember->hasPosition('admin_confirm'))
    {
      throw new Exception('This member position isn\'t "admin_confirm".');
    }

    $nowAdmin = $this->getEventAdmin($eventId);
    if (!$nowAdmin)
    {
      throw new Exception("Event's admin was not found.");
    }

    try
    {
      $this->getConnection()->beginTransaction();

      $eventMember->removeAllPosition();
      $eventMember->addPosition('admin');
      $nowAdmin->delete();

      $this->getConnection()->commit();
    }
    catch(Exception $e)
    {
      $this->getConnection()->rollback();
      throw $e;
    }
  }

  public function addSubAdmin($memberId, $eventId)
  {
    $this->addPosition($memberId, $eventId, 'sub_admin');
  }

  public function getMemberIdsByEventId($eventId)
  {
    return Doctrine::getTable('EventMember')->createQuery()
      ->select('id', 'member_id')
      ->where('event_id = ?', $eventId)
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
    $members = Doctrine::getTable('EventMember')->getEventMembersPre($event['member']->id);
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
          '%event%' => array(
            'text' => $member->getEvent()->name,
            'link' => '@event_home?id='.$member->getEvent()->id,
          ),
        ),
      );
    }

    $event->setReturnValue($list);

    return true;
  }

  public static function processJoinConfirm(sfEvent $event)
  {
    $eventMember = Doctrine::getTable('EventMember')->find($event['id']);
    if (!($eventMember && $eventMember->getIsPre()))
    {
      return false;
    }

    $i18n = sfContext::getInstance()->getI18N();
    if ($event['is_accepted'])
    {
      $eventMember->setIsPre(false);
      $eventMember->save();

      opEventPluginAction::sendJoinMail($eventMember->getMember()->id, $eventMember->getEvent()->id);

      $event->setReturnValue($i18n->__('You have just accepted joining to %1%', array('%1%' => $eventMember->getEvent()->getName())));
    }
    else
    {
      $eventMember->delete();

      $event->setReturnValue($i18n->__('You have just rejected joining to %1%', array('%1%' => $eventMember->getEvent()->getName())));
    }

    return true;
  }
}
