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
 * opChangeEventAdminRequestForm
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
class opEventJoiningForm extends opBaseForm
{
  public function setup()
  {
    $this->widgetSchema->setNameFormat('event_join[%s]');
  }
}
