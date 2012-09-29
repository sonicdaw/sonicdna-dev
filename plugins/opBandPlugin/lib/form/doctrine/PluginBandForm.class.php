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
 * Band form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginBandForm extends BaseBandForm
{
  protected $configForm;

  public function setup()
  {
    parent::setup();

    unset($this['created_at'], $this['updated_at'], $this['file_id']);
    unset($this->widgetSchema['id']);

    $this->widgetSchema->setLabel('name', '%band% Name');
    $this->setValidator('name', new opValidatorString(array('max_length' => 64, 'trim' => true)));

    $q = Doctrine::getTable('BandCategory')->getAllChildrenQuery();
    if (1 != sfContext::getInstance()->getUser()->getMemberId())
    {
      $q->andWhere('is_allow_member_band = 1');
    }
    $bandCategories = $q->execute();
    if (0 < count($bandCategories))
    {
      $choices = array();
      foreach ($bandCategories as $category)
      {
        $choices[$category->id] = $category->name;
      }
      $this->setWidget('band_category_id', new sfWidgetFormChoice(array('choices' => array('' => '') + $choices)));
      $this->widgetSchema->setLabel('band_category_id', '%band% Category');
    }
    else
    {
      unset($this['band_category_id']);
    }

    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_band');

    // sonic dna
    $this->setWidget('official_url',         new sfWidgetFormInputText(array('label' => 'official_url'),         array('size' => 76, 'class' => 'official_url')));
    $this->widgetSchema->setHelp('member_list', 'Members who are not joined in this site.');

    $uniqueValidator = new sfValidatorDoctrineUnique(array('model' => 'Band', 'column' => array('name')));
    $uniqueValidator->setMessage('invalid', 'An object with the same "name" already exist in other %band%.');
    $this->validatorSchema->setPostValidator($uniqueValidator);

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkCreatable'))));
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);

    $this->saveMember($object);

    return $object;
  }

  public function saveMember(Band $band)
  {
    if ($this->isNew())
    {
      $member = new BandMember();
      $member->setMemberId(sfContext::getInstance()->getUser()->getMemberId());
      $member->setBand($band);
      $member->addPosition('admin');
      $member->save();
    }
  }

  public function checkCreatable($validator, $value)
  {
    if (empty($value['band_category_id']))
    {
      return $value;
    }

    $category = Doctrine::getTable('BandCategory')->find($value['band_category_id']);
    if (!$category)
    {
      throw new sfValidatorError($validator, 'invalid');
    }

    if ($category->getIsAllowMemberBand())
    {
      return $value;
    }

    if (1 == sfContext::getInstance()->getUser()->getMemberId())
    {
      return $value;
    }

    throw new sfValidatorError($validator, 'invalid');
  }
}
