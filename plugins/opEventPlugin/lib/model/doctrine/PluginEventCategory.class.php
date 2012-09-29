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

abstract class PluginEventCategory extends BaseEventCategory
{
  public function __toString()
  {
    return $this->name;
  }

  public function save(Doctrine_Connection $conn = null)
  {
    if ($this->isNew())
    {
      if ($this->getTreeKey())
      {
        $parent = Doctrine::getTable('EventCategory')->find($this->getTreeKey());
        if ($parent)
        {
          $this->getNode()->insertAsLastChildOf($parent);
        }
      }
      else
      {
        parent::save($conn);

        $treeObject = Doctrine::getTable('EventCategory')->getTree();
        $treeObject->createRoot($this);
      }
    }

    return parent::save($conn);
  }

  public function getForm()
  {
    return new EventCategoryForm($this);
  }

  public function getChildren()
  {
    $q = Doctrine::getTable('EventCategory')->getAllChildrenQuery();
    $q->addWhere('tree_key = ?', $this->getId());

    return $q->execute();
  }
}
