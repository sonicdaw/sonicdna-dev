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

class bandComponents extends opBandPluginComponents
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
    $this->crownIds = Doctrine::getTable('BandMember')->getBandIdsOfAdminByMemberId($this->member->getId());
    $this->communities = Doctrine::getTable('Band')->retrievesByMemberId($this->member->getId(), $this->row * $this->col, true);
  }

  public function executeSmtBandListBox($request)
  {
    $this->id = $request->getParameter('id');

    $memberId = $this->getUser()->getMemberId();
    $bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($memberId, $this->id);

    if ($bandMember)
    {
      $this->isBandMember = !$bandMember->getIsPre();
      $this->isBandPreMember = $bandMember->getIsPre();

      $positions = Doctrine::getTable('BandMemberPosition')->getPositionsByMemberIdAndBandId($memberId, $this->id);
      $this->isAdmin = in_array('admin', $positions);
      $this->isSubAdmin = in_array('sub_admin', $positions);
      $this->isEditBand = $this->isAdmin || $this->isSubAdmin;
    }
    else
    {
      $this->isBandMember = false;
      $this->isBandPreMember = false;
      $this->isAdmin = false;
      $this->isSubAdmin = false;
      $this->isEditBand = false;
    }

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->bandAdmin = $this->band->getAdminMember();
    $this->bandSubAdmins = $this->band->getSubAdminMembers();
  }

  public function executeSmtBandMemberJoinListBox($request)
  {
    $this->id = $request->getParameter('id');
    $this->band = Doctrine::getTable('Band')->find($this->id);
  }
}
