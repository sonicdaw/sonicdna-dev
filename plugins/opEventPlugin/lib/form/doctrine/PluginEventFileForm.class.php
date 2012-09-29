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
 * Event file form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Shogo Kawahara <kawahara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */

abstract class PluginEventFileForm extends BaseForm
{
  protected
    $event;

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $this->setEvent($this->getOption('event'));

    $options = array(
      'file_src'     => '',
      'is_image'     => true,
      'with_delete'  => true,
      'delete_label' => sfContext::getInstance()->getI18N()->__('Remove the current photo')
    );

    if (!$this->event->isNew() && $this->event->getFileId())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
      $options['edit_mode'] = true;
//      $options['template'] = get_partial('default/formEditImage', array('image' => $this->event));
      $options['template'] = get_partial('default/formEditImageEvent', array('image' => $this->event));
      $this->setValidator('file_delete', new sfValidatorBoolean(array('required' => false)));
    }
    else
    {
      $options['edit_mode'] = false;
    }

    $this->setWidget('file', new sfWidgetFormInputFileEditable($options, array('size' => 40)));
    $this->setValidator('file', new opValidatorImageFile(array('required' => false)));

    $this->widgetSchema->setLabel('file', 'Photo');

    $this->widgetSchema->setNameFormat('event_file[%s]');

    $this->widgetSchema->setHelp('file', 'Recommended size: 680 x 250');
  }

  public function setEvent($event)
  {
    if (!($event instanceof Event))
    {
      $event = new Event();
    }
    $this->event = $event;
  }

  public function save()
  {
    if ($this->getValue('file'))
    {
      if ($this->event->getFile())
      {
        $this->event->getFile()->delete(); 
      }

      $file = new File();
      $file->setFromValidatedFile($this->getValue('file'));
      $file->setName('c_'.$this->event->getId().'_'.$file->getName());

      $this->event->setFile($file);
    }
    elseif ($this->getValue('file_delete'))
    {
      $this->event->getFile()->delete();
      $this->event->setFile(null);
    }
    else
    {
      return;
    }

    $this->event->save();
  }
}
