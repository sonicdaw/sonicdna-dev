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
 * opBandComponents
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     Shogo Kawahara <kawahara@tejimaya.net> (OpenPNE)
 * @author     Kousuke Ebihara <ebihara@php.net> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class opBandPluginComponents extends sfComponents
{
  public function executeCautionAboutBandMemberPre()
  {
    $memberId = sfContext::getInstance()->getUser()->getMemberId();

    $this->bandMembersCount = Doctrine::getTable('BandMember')->countBandMembersPre($memberId);
  }

  public function executeCautionAboutChangeAdminRequest()
  {
    $this->bandCount = Doctrine::getTable('Band')->countPositionRequestCommunities('admin');
  }

  public function executeCautionAboutSubAdminRequest()
  {
    $this->bandCount = Doctrine::getTable('Band')->countPositionRequestCommunities('sub_admin');
  }

}
