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

abstract class PluginEventMember extends BaseEventMember implements opAccessControlRecordInterface
{
  public function generateRoleId(Member $member)
  {
    return $this->Event->generateRoleId($member);
  }

  public function getPositions()
  {
    return Doctrine::getTable('EventMemberPosition')->getPositionsByMemberIdAndEventId($this->getMemberId(), $this->getEventId());
  }

  public function hasPosition($name)
  {
    if (!is_array($name))
    {
      $name = array($name);
    }
    foreach ($name as $n)
    {
      if (in_array($n, $this->getPositions()))
      {
        return true;
      }
    }
    return false;
  }

  public function addPosition($name)
  {
    $object = null;
    if (!$this->isNew())
    {
      $object = Doctrine::getTable('EventMemberPosition')->findOneByEventMemberIdAndName($this->getId(), $name);
    }
    if (!$object)
    {
      $object = new EventMemberPosition();
      $object->setMemberId($this->getMemberId());
      $object->setEventId($this->getEventId());
      $object->setEventMember($this);
      $object->setName($name);
      $object->save();
    }
  }

  public function removePosition($name)
  {
    if ($this->isNew())
    {
      return false;
    }
    $object = Doctrine::getTable('EventMemberPosition')->findOneByEventMemberIdAndName($this->getId(), $name);
    if (!$object)
    {
      throw new LogicException('The role data does not exist.');
    }
    $object->delete();
  }

  public function removeAllPosition()
  {
    if ($this->isNew())
    {
      return false;
    }
    Doctrine::getTable('EventMemberPosition')->createQuery()
      ->where('event_member_id = ?', $this->getId())
      ->delete()
      ->execute();
  }
}
