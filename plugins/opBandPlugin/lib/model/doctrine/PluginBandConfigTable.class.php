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

abstract class PluginBandConfigTable extends Doctrine_Table
{
  public function retrievesByBandId($bandId)
  {
    return $this->createQuery()
      ->where('band_id = ?', $bandId)
      ->execute();
  }

  public function retrieveByNameAndBandIdQuery($name, $bandId)
  {
    return $this->createQuery()
      ->where('name = ?', $name)
      ->andWhere('band_id = ?', $bandId);
  }

  public function retrieveByNameAndBandId($name, $bandId)
  {
    $q = $this->retrieveByNameAndBandIdQuery($name, $bandId);

    return $q->fetchOne();
  }

  public function retrieveValueByNameAndBandId($name, $bandId)
  {
    $q = $this->retrieveByNameAndBandIdQuery($name, $bandId);

    $result = $q->select('value')
      ->fetchOne(array(), Doctrine::HYDRATE_NONE);

    if (!$result)
    {
      return null;
    }

    return $result[0];
  }
}
