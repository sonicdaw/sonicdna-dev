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
 * BandCategory form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginBandCategoryForm extends BaseBandCategoryForm
{
  public function setup()
  {
    parent::setup();
    unset(
      $this['id'],
      $this['sort_order'],
      $this['lft'],
      $this['rgt'],
      $this['level'],
      $this->widgetSchema['tree_key'],
      $this['created_at'], $this['updated_at']
    );

    $obj = $this->isNew() ? $this->getOption('category') : $this->getObject();
    if ($obj instanceof BandCategory)
    {
      $this->setWidget('tree_key', new sfWidgetFormInputHidden(array('default' => $obj->getTreeKey())));
    }

    $this->widgetSchema->setLabel('name', 'Category Name');

    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_band');

    $this->setValidator('name', new opValidatorString(array('max_length' => 64, 'trim' => true)));
  }
}
