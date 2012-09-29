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
 * Event filter form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage filter
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginEventFormFilter extends BaseEventFormFilter
{
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $q = Doctrine::getTable('EventCategory')->getAllChildrenQuery();
    $widgets = array(
      'name'                  => new sfWidgetFormInput(),
      'event_category_id' => new sfWidgetFormDoctrineChoice(array(
        'model'       => 'EventCategory',
        'add_empty'   => sfContext::getInstance()->getI18N()->__('All categories', array(), 'form_event'),
        'query'    => $q,
        'default' => 0)),
    );

    $validators = array(
      'name'                  => new opValidatorSearchQueryString(array('required' => false)),
      'event_category_id' => new sfValidatorPass(),
    );

    if ($this->getOption('use_id'))
    {
      $widgets = array('id' => new sfWidgetFormFilterInput(array('with_empty' => false, 'label' => 'ID'))) + $widgets;
      $validators = array('id' => new sfValidatorPass()) + $validators;
    }

    $this->setWidgets($widgets);
    $this->setValidators($validators);

    $this->widgetSchema->setLabel('name', '%event% Name');
    $this->widgetSchema->setLabel('event_category_id', '%event% Category');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    $this->widgetSchema->setNameFormat('event[%s]');
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_event');
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
