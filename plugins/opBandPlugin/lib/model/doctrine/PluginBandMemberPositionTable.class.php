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
 * BandMemberPositionTable
 * 
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage model
 * @author     Shogo Kawahara <kawahara@tejimaya.net> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginBandMemberPositionTable extends Doctrine_Table
{
  public function getPositionsByMemberIdAndBandId($memberId, $bandId)
  {
    $objects = $this->createQuery()
      ->where('member_id = ?', $memberId)
      ->andWhere('band_id = ?', $bandId)
      ->execute();

    $results = array();
    foreach ($objects as $obj)
    {
      $results[] = $obj->getName();
    }
    return $results;
  }
}
