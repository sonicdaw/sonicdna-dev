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
 * form to change event's admin request
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage form
 * @author     Shogo Kawahara <kawahara@tejimaya.net> (OpenPNE)
 * @author     Kousuke Ebihara <ebihara@tejimaya.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
class opChangeEventAdminRequestForm extends opBaseForm
{
  public function setup()
  {
    $this->widgetSchema->setNameFormat('admin_request[%s]');
  }
}
