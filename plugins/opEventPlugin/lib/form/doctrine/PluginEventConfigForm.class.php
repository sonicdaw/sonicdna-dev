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
 * EventConfig form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
abstract class PluginEventConfigForm extends BaseForm
{
  protected
    $configSettings = array(),
    $category = '',
    $event,
    $isNew = false,
    $isAutoGenerate = true;

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $this->setEvent($this->getOption('event'));

    $this->setConfigSettings();

    if ($this->isAutoGenerate)
    {
      $this->generateConfigWidgets();
    }

    $this->widgetSchema->setNameFormat('event_config[%s]');
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('form_event');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function setEvent($event)
  {
    if (!($event instanceof Event))
    {
      $event = new Event();
    }
    $this->event = $event;
  }

  public function generateConfigWidgets()
  {
    foreach ($this->configSettings as $key => $value)
    {
      $this->setConfigWidget($key);
    }

    $app = 'mobile_frontend' == sfConfig::get('sf_app') ? 'mobile' : 'pc';
    $template = 'joinEvent';
    $notificationMail = Doctrine::getTable('NotificationMail')->findOneByName($app.'_'.$template);

    // sonic dna
    if (!$notificationMail || $notificationMail->getIsEnabled())
    {
/*      $i18n = sfContext::getInstance()->getI18n();
      $choices = array(
        1 => $i18n->__('Receive'),
        0 => $i18n->__('Don\'t Receive')
      );
*/      $name = 'is_send_'.$app.'_'.$template.'_mail';
/*      $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true)));
      $this->setValidator($name, new sfValidatorChoice(array('choices' => array_keys($choices))));
      $this->widgetSchema->setLabel($name, $i18n->__('Receive a notice mail when member joined'));
      $this->widgetSchema->setHelp($name, $i18n->__('Send a notice mail to administrator when new member joined the %event%.'));

      $default = $this->event->getConfig($name);
      $default = is_null($default) ? 1 : $default;
      $this->setDefault($name, $default);
*/      $this->setDefault($name, 0);
    }
  }

  public function setConfigWidget($name)
  {
    $config = $this->configSettings[$name];
    $this->widgetSchema[$name] = opFormItemGenerator::generateWidget($config);
    $this->widgetSchema->setLabel($name, $config['Caption']);
    $eventConfig = Doctrine::getTable('EventConfig')->retrieveByNameAndEventId($name, $this->event->getId());
    if ($eventConfig)
    {
      $this->setDefault($name, $eventConfig->getValue());
    }
    $this->validatorSchema[$name] = opFormItemGenerator::generateValidator($config);
  }

  public function setConfigSettings($category = '')
  {
    $categories = sfConfig::get('openpne_event_category');
    $configs = sfConfig::get('openpne_event_config');

    if (!$category)
    {
      $this->configSettings = $configs;
      return true;
    }

    foreach ($categories[$category] as $value)
    {
      $this->configSettings[$value] = $configs[$value];
    }
  }

  public function save()
  {
    foreach ($this->getValues() as $key => $value)
    {
      $config = Doctrine::getTable('EventConfig')->retrieveByNameAndEventId($key, $this->event->getId());
      if (!$config)
      {
        $config = new EventConfig();
        $config->setEvent($this->event);
        $config->setName($key);
      }
      $config->setValue($value);
      $config->save();
    }
  }
}
