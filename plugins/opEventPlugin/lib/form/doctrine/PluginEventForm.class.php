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
 * Event form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginEventForm extends BaseEventForm
{
  protected $configForm;

  public function setup()
  {
    parent::setup();

    unset($this['created_at'], $this['updated_at'], $this['file_id']);
    unset($this->widgetSchema['id']);

    $this->widgetSchema->setLabel('name', '%event% Name');
    $this->setValidator('name', new opValidatorString(array('max_length' => 64, 'trim' => true)));

    $q = Doctrine::getTable('EventCategory')->getAllChildrenQuery();
    if (1 != sfContext::getInstance()->getUser()->getMemberId())
    {
      $q->andWhere('is_allow_member_event = 1');
    }
    $eventCategories = $q->execute();
    if (0 < count($eventCategories))
    {
      $choices = array();
      foreach ($eventCategories as $category)
      {
        $choices[$category->id] = $category->name;
      }
      $this->setWidget('event_category_id', new sfWidgetFormChoice(array('choices' => array('' => '') + $choices)));
      $this->widgetSchema->setLabel('event_category_id', '%event% Category');
    }
    else
    {
      unset($this['event_category_id']);
    }

    // event status
    $eventStatus = array();
    $eventStatus[1] = 'Planning';
    $eventStatus[2] = 'Open';
    $eventStatus[3] = 'Closed'; 
    $this->setWidget('event_status_id', new sfWidgetFormChoice(array('choices' => array('' => '') + $eventStatus)));
    $this->widgetSchema->setLabel('event_status_id', '%event% Status');

    // hide
    unset($this['rehearsal_config']);
    unset($this['info_staff']);
    unset($this['address']);   // Reserved
    unset($this['latitude']);   // Reserved
    unset($this['longitude']);   // Reserved


    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_event');

    $years = range(2012, 2015);
    $this->setWidget('event_date', new sfWidgetFormDate(array(
      'default' => '01/01/2012',
      'format' => '%year% - %month% - %day%',
      'years'   => array_combine($years, $years)
    )));
    $this->widgetSchema->setLabel('event_date', "Event Date");

    $this->setWidget('event_open_time',   new sfWidgetFormTime(array('default' => '12:00')));
    $this->widgetSchema->setLabel('event_open_time', "Gate's Opening Time");

    $this->setWidget('event_start_time',   new sfWidgetFormTime(array('default' => '12:00')));
    $this->widgetSchema->setLabel('event_start_time', 'Event Start Time');

    $this->setWidget('event_rehearsal_start_time',   new sfWidgetFormTime(array('default' => '12:00')));
    $this->widgetSchema->setLabel('event_rehearsal_start_time', 'Rehearsal Start Time');

    $this->setWidget('official_url',   new sfWidgetFormInputText(array('label' => 'official_url'),   array('size' => 76, 'class' => 'official_url')));
    $this->widgetSchema->setLabel('official_url', 'Official URL');

    $this->setWidget('access',         new sfWidgetFormInputText(array('label' => 'access'),         array('size' => 76, 'class' => 'access')));
    $this->widgetSchema->setLabel('access', 'Venue');

    $this->setWidget('access_url',     new sfWidgetFormInputText(array('label' => 'access_url'),     array('size' => 76, 'class' => 'access_url')));
    $this->widgetSchema->setLabel('access_url', 'Venue URL');

    $this->setWidget('access_map_url', new sfWidgetFormInputText(array('label' => 'access_map_url'), array('size' => 76, 'class' => 'access_map_url')));
    $this->widgetSchema->setLabel('access_map_url', 'Venue Map URL');

    $this->setWidget('ticket',         new sfWidgetFormInputText(array('label' => 'ticket'),         array('size' => 76, 'class' => 'ticket')));
    $this->widgetSchema->setLabel('ticket', 'Ticket');

    $this->setWidget('ticket_url',     new sfWidgetFormInputText(array('label' => 'ticket_url'),     array('size' => 76, 'class' => 'ticket_url')));
    $this->widgetSchema->setLabel('ticket_url', 'Ticket URL');

    $this->widgetSchema->setHelp('lineup_config', 'See Help page from the link below');
    $this->widgetSchema->setLabel('lineup_config', 'Lineup Configuration');
    $this->widgetSchema->setHelp('rehearsal_config', 'Not Used');
    $this->widgetSchema->setHelp('info_staff', 'Not Used');


    $uniqueValidator = new sfValidatorDoctrineUnique(array('model' => 'Event', 'column' => array('name')));
    $uniqueValidator->setMessage('invalid', 'An object with the same "name" already exist in other %event%.');
    $this->validatorSchema->setPostValidator($uniqueValidator);

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkCreatable'))));
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);

    $this->saveMember($object);

    return $object;
  }

  public function saveMember(Event $event)
  {
    if ($this->isNew())
    {
      $member = new EventMember();
      $member->setMemberId(sfContext::getInstance()->getUser()->getMemberId());
      $member->setEvent($event);
      $member->addPosition('admin');
      $member->save();
    }
  }

  public function checkCreatable($validator, $value)
  {
    if (empty($value['event_category_id']))
    {
      return $value;
    }

    $category = Doctrine::getTable('EventCategory')->find($value['event_category_id']);
    if (!$category)
    {
      throw new sfValidatorError($validator, 'invalid');
    }

    if ($category->getIsAllowMemberEvent())
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
