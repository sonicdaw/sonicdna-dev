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

class eventComponents extends opEventPluginComponents
{
  public function executeJoinListBox($request)
  {
    if ($request->hasParameter('id') && $request->getParameter('module') == 'member' && $request->getParameter('action') == 'profile')
    {
      $this->member = Doctrine::getTable('Member')->find($request->getParameter('id'));
    }
    else
    {
      $this->member = $this->getUser()->getMember();
    }
    $this->row = $this->gadget->getConfig('row');
    $this->col = $this->gadget->getConfig('col');
    $this->crownIds = Doctrine::getTable('EventMember')->getEventIdsOfAdminByMemberId($this->member->getId());
    $this->communities = Doctrine::getTable('Event')->retrievesByMemberId($this->member->getId(), $this->row * $this->col, true);
  }

  public function executeSmtEventListBox($request)
  {
    $this->id = $request->getParameter('id');

    $memberId = $this->getUser()->getMemberId();
    $eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($memberId, $this->id);

    if ($eventMember)
    {
      $this->isEventMember = !$eventMember->getIsPre();
      $this->isEventPreMember = $eventMember->getIsPre();

      $positions = Doctrine::getTable('EventMemberPosition')->getPositionsByMemberIdAndEventId($memberId, $this->id);
      $this->isAdmin = in_array('admin', $positions);
      $this->isSubAdmin = in_array('sub_admin', $positions);
      $this->isEditEvent = $this->isAdmin || $this->isSubAdmin;
    }
    else
    {
      $this->isEventMember = false;
      $this->isEventPreMember = false;
      $this->isAdmin = false;
      $this->isSubAdmin = false;
      $this->isEditEvent = false;
    }

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->eventAdmin = $this->event->getAdminMember();
    $this->eventSubAdmins = $this->event->getSubAdminMembers();
  }

  public function executeSmtEventMemberJoinListBox($request)
  {
    $this->id = $request->getParameter('id');
    $this->event = Doctrine::getTable('Event')->find($this->id);
  }
}
