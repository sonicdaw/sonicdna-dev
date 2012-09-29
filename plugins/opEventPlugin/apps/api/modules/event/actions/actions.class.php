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
 * event actions.
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     Kimura Youichi <kim.upsilon@gmail.com> (OpenPNE)
 * @author     msum (sonicdna.net)
 */
class eventActions extends opJsonApiActions
{
  public function executeSearch(sfWebRequest $request)
  {
    $query = Doctrine::getTable('Event')->createQuery();

    if (isset($request['keyword']))
    {
      $query->andWhereLike('name', $request['keyword']);
    }

    $this->events = $query
      ->limit(sfConfig::get('op_json_api_limit', 20))
      ->execute();

    $this->setTemplate('array');
  }

  public function executeMember(sfWebRequest $request)
  {
    if (isset($request['event_id']))
    {
      $eventId = $request['event_id'];
    }
    elseif (isset($request['id']))
    {
      $eventId = $request['id'];
    }
    else
    {
      $this->forward400('event_id parameter not specified.');
    }

    $this->members = Doctrine::getTable('Member')->createQuery('m')
      ->addWhere('EXISTS (FROM EventMember cm WHERE m.id = cm.member_id AND cm.is_pre = false AND cm.event_id = ?)', $eventId)
      ->limit(sfConfig::get('op_json_api_limit', 20))
      ->execute();

    $this->setTemplate('array', 'member');
  }

  public function executeJoin(sfWebRequest $request)
  {
    $memberId = $this->getUser()->getMemberId();

    if (isset($request['event_id']))
    {
      $eventId = $request['event_id'];
    }
    elseif (isset($request['id']))
    {
      $eventId = $request['id'];
    }
    else
    {
      $this->forward400('event_id parameter not specified.');
    }

    $event = Doctrine::getTable('Event')->find($eventId);
    if (!$event)
    {
      $this->forward404('This event does not exist.');
    }

    $eventJoinPolicy = $event->getConfig('register_policy');

    $eventMember = Doctrine::getTable('EventMember')
      ->retrieveByMemberIdAndEventId($memberId, $event->getId());

    if ($request['leave'])
    {
      if (!$eventMember || $eventMember->hasPosition('admin'))
      {
        $this->forward400('You can\'t leave this event.');
      }

      Doctrine::getTable('EventMember')->quit($memberId, $eventId);
    }
    else
    {
      if ($eventMember)
      {
        if ($eventMember->getIsPre())
        {
          $this->forward400('You are already sent request to join this event.');
        }
        else
        {
          $this->forward400('You are already this event\'s member.');
        }
      }

      Doctrine::getTable('EventMember')->join($memberId, $eventId, $eventJoinPolicy);
    }

    return $this->renderJSON(array('status' => 'success'));
  }
}
