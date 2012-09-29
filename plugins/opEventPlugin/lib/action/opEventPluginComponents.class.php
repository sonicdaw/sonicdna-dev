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

/**
 * opEventComponents
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     Shogo Kawahara <kawahara@tejimaya.net> (OpenPNE)
 * @author     Kousuke Ebihara <ebihara@php.net> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class opEventPluginComponents extends sfComponents
{
  public function executeCautionAboutEventMemberPre()
  {
    $memberId = sfContext::getInstance()->getUser()->getMemberId();

    $this->eventMembersCount = Doctrine::getTable('EventMember')->countEventMembersPre($memberId);
  }

  public function executeCautionAboutChangeAdminRequest()
  {
    $this->eventCount = Doctrine::getTable('Event')->countPositionRequestCommunities('admin');
  }

  public function executeCautionAboutSubAdminRequest()
  {
    $this->eventCount = Doctrine::getTable('Event')->countPositionRequestCommunities('sub_admin');
  }

}
