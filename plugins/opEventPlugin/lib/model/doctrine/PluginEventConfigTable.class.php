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

abstract class PluginEventConfigTable extends Doctrine_Table
{
  public function retrievesByEventId($eventId)
  {
    return $this->createQuery()
      ->where('event_id = ?', $eventId)
      ->execute();
  }

  public function retrieveByNameAndEventIdQuery($name, $eventId)
  {
    return $this->createQuery()
      ->where('name = ?', $name)
      ->andWhere('event_id = ?', $eventId);
  }

  public function retrieveByNameAndEventId($name, $eventId)
  {
    $q = $this->retrieveByNameAndEventIdQuery($name, $eventId);

    return $q->fetchOne();
  }

  public function retrieveValueByNameAndEventId($name, $eventId)
  {
    $q = $this->retrieveByNameAndEventIdQuery($name, $eventId);

    $result = $q->select('value')
      ->fetchOne(array(), Doctrine::HYDRATE_NONE);

    if (!$result)
    {
      return null;
    }

    return $result[0];
  }
}
