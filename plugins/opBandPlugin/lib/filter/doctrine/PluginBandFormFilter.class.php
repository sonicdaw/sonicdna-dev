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
 * Band filter form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage filter
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginBandFormFilter extends BaseBandFormFilter
{
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $q = Doctrine::getTable('BandCategory')->getAllChildrenQuery();
    $widgets = array(
      'name'                  => new sfWidgetFormInput(),
      'band_category_id' => new sfWidgetFormDoctrineChoice(array(
        'model'       => 'BandCategory',
        'add_empty'   => sfContext::getInstance()->getI18N()->__('All categories', array(), 'form_band'),
        'query'    => $q,
        'default' => 0)),
    );

    $validators = array(
      'name'                  => new opValidatorSearchQueryString(array('required' => false)),
      'band_category_id' => new sfValidatorPass(),
    );

    if ($this->getOption('use_id'))
    {
      $widgets = array('id' => new sfWidgetFormFilterInput(array('with_empty' => false, 'label' => 'ID'))) + $widgets;
      $validators = array('id' => new sfValidatorPass()) + $validators;
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setLabel('name', '%band% Name');
    $this->widgetSchema->setLabel('band_category_id', '%band% Category');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    $this->widgetSchema->setNameFormat('band[%s]');
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_band');
  }

  protected function addNameColumnQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $query->andWhereLike('r.'.$fieldName, $value);
      }
    }
  }
}
