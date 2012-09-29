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
 * Band file form.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Shogo Kawahara <kawahara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */

abstract class PluginBandFileForm extends BaseForm
{
  protected
    $band;

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    return parent::__construct($defaults, $options, false);
  }

  public function setup()
  {
    parent::setup();

    $this->setBand($this->getOption('band'));

    $options = array(
      'file_src'     => '',
      'is_image'     => true,
      'with_delete'  => true,
      'delete_label' => sfContext::getInstance()->getI18N()->__('Remove the current photo')
    );

    if (!$this->band->isNew() && $this->band->getFileId())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
      $options['edit_mode'] = true;
      $options['template'] = get_partial('default/formEditImage', array('image' => $this->band));
      $this->setValidator('file_delete', new sfValidatorBoolean(array('required' => false)));
    }
    else
    {
      $options['edit_mode'] = false;
    }

    $this->setWidget('file', new sfWidgetFormInputFileEditable($options, array('size' => 40)));
    $this->setValidator('file', new opValidatorImageFile(array('required' => false)));

    $this->widgetSchema->setLabel('file', 'Photo');

    $this->widgetSchema->setNameFormat('band_file[%s]');
  }

  public function setBand($band)
  {
    if (!($band instanceof Band))
    {
      $band = new Band();
    }
    $this->band = $band;
  }

  public function save()
  {
    if ($this->getValue('file'))
    {
      if ($this->band->getFile())
      {
        $this->band->getFile()->delete(); 
      }

      $file = new File();
      $file->setFromValidatedFile($this->getValue('file'));
      $file->setName('c_'.$this->band->getId().'_'.$file->getName());

      $this->band->setFile($file);
    }
    elseif ($this->getValue('file_delete'))
    {
      $this->band->getFile()->delete();
      $this->band->setFile(null);
    }
    else
    {
      return;
    }

    $this->band->save();
  }
}
