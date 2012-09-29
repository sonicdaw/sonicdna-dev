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

abstract class PluginBandCategoryTable extends Doctrine_Table
{
  //TODO: use findAll()
  public function retrieveAll()
  {
    return $this->createQuery()->execute();
  }

  //TODO: use getTree()->fetchRoots()
  public function retrieveAllRoots($sort = true)
  {
    return $this->getAllRootsQuery($sort)->execute();
  }

  public function getAllRootsQuery($sort = true)
  {
    $q = $this->createQuery()->where('lft = 1');
    if ($sort)
    {
      $q->orderBy('sort_order');
    }

    return $q;
  }

  public function retrieveAllChildren($sort = true)
  {
    return $this->getAllChildrenQuery($sort)->execute();
  }

  public function getAllChildrenQuery($sort = true)
  {
    $q = $this->createQuery()->where('lft > 1');
    if ($sort)
    {
      $q->orderBy('sort_order');
    }

    return $q;
  }
}
