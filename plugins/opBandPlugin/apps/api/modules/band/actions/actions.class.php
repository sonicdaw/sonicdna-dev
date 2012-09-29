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
 * band actions.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     Kimura Youichi <kim.upsilon@gmail.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
class bandActions extends opJsonApiActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Band')->createQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('name', $request['keyword']);
    }

    $this->bands = $query
      ->limit(sfConfig::get('op_json_api_limit', 20))
      ->execute();

    $this->setTemplate('array');
  }

  public function executeMember(sfWebRequest $request)
  {
    if (isset($request['band_id']))
    {
      $bandId = $request['band_id'];
    }
    elseif (isset($request['id']))
    {
      $bandId = $request['id'];
    }
    else
    {
      $this->forward400('band_id parameter not specified.');
    }

    $this->members = Doctrine::getTable('Member')->createQuery('m')
      ->addWhere('EXISTS (FROM BandMember cm WHERE m.id = cm.member_id AND cm.is_pre = false AND cm.band_id = ?)', $bandId)
      ->limit(sfConfig::get('op_json_api_limit', 20))
      ->execute();

    $this->setTemplate('array', 'member');
  }

  public function executeJoin(sfWebRequest $request)
  {
    $memberId = $this->getUser()->getMemberId();

    if (isset($request['band_id']))
    {
      $bandId = $request['band_id'];
    }
    elseif (isset($request['id']))
    {
      $bandId = $request['id'];
    }
    else
    {
      $this->forward400('band_id parameter not specified.');
    }

    $band = Doctrine::getTable('Band')->find($bandId);
    if (!$band)
    {
      $this->forward404('This band does not exist.');
    }

    $bandJoinPolicy = $band->getConfig('register_policy');

    $bandMember = Doctrine::getTable('BandMember')
      ->retrieveByMemberIdAndBandId($memberId, $band->getId());

    if ($request['leave'])
    {
      if (!$bandMember || $bandMember->hasPosition('admin'))
      {
        $this->forward400('You can\'t leave this band.');
      }

      Doctrine::getTable('BandMember')->quit($memberId, $bandId);
    }
    else
    {
      if ($bandMember)
      {
        if ($bandMember->getIsPre())
        {
          $this->forward400('You are already sent request to join this band.');
        }
        else
        {
          $this->forward400('You are already this band\'s member.');
        }
      }

      Doctrine::getTable('BandMember')->join($memberId, $bandId, $bandJoinPolicy);
    }

    return $this->renderJSON(array('status' => 'success'));
  }
}
