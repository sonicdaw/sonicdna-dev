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
 * opEventAction
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     msum  
 */
abstract class opEventPluginAction extends sfActions
{
  public function preExecute()
  {
    $this->id = $this->getRequestParameter('id');

    $memberId = $this->getUser()->getMemberId();
    $this->isEventMember = Doctrine::getTable('EventMember')->isMember($memberId, $this->id);
    $this->isEventPreMember = Doctrine::getTable('EventMember')->isPreMember($memberId, $this->id);
    $this->isAdmin = Doctrine::getTable('EventMember')->isAdmin($memberId, $this->id);
    $this->isSubAdmin = Doctrine::getTable('EventMember')->isSubAdmin($memberId, $this->id);
    $this->isEditEvent = $this->isAdmin || $this->isSubAdmin;
    $this->isDeleteEvent = $this->isAdmin;
  }


 /**
  * Executes help action
  *
  * @param sfRequest $request A request object
  */
  public function executeHelp($request)
  {
    sfConfig::set('sf_nav_type', 'default');
  }

 /**
  * Executes unregister action
  *
  * @param sfRequest $request A request object
  */
  public function executeUnregister($request)
  {
    sfConfig::set('sf_nav_type', 'default');
  }

 /**
  * Executes home action
  *
  * @param sfRequest $request A request object
  */
  public function executeHome($request)
  {
    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->forward404Unless($this->event, 'Undefined event.');
    $this->eventAdmin = $this->event->getAdminMember();
    $this->eventSubAdmins = $this->event->getSubAdminMembers();

    if (!$this->membersSize)
    {
      $this->membersSize = 9;
    }
    $this->members = $this->event->getMembers($this->membersSize, true);

    // sonicdna.net
    $this->eventlineup = Doctrine::getTable('EventLineUp')->findByEventId($this->id); 
  }

 /**
  * Executes home action
  *
  * @param sfRequest $request A request object
  */
  public function executeTimetable($request)
  {
    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->forward404Unless($this->event, 'Undefined event.');
    $this->eventAdmin = $this->event->getAdminMember();
    $this->eventSubAdmins = $this->event->getSubAdminMembers();

    if (!$this->membersSize)
    {
      $this->membersSize = 9;
    }
    $this->members = $this->event->getMembers($this->membersSize, true);

    // sonicdna.net
    $this->eventlineup = Doctrine::getTable('EventLineUp')->findByEventId($this->id); 
  }

 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit($request)
  {
    $this->forward404If($this->id && !$this->isEditEvent);

    $this->event = Doctrine::getTable('Event')->find($this->id);
    if (!$this->event)
    {
      $this->event = new Event();
    }
    else
    {
      if ($request->isMethod('post') && $request->hasParameter('is_delete'))
      {
        $this->redirect('@event_delete');
      }
    }

    $this->eventForm       = new EventForm($this->event);
    $this->eventConfigForm = new EventConfigForm(array(), array('event' => $this->event));
    $this->eventFileForm = isset($this->enableImage) && $this->enableImage ?
      new EventFileForm(array(), array('event' => $this->event)) :
      new EventFileForm();

    if ($request->isMethod('post'))
    {
      $params = $request->getParameter('event');
      $params['id'] = $this->id;

      $this->eventForm->bind($params);
      $this->eventConfigForm->bind($request->getParameter('event_config'));
      $this->eventFileForm->bind($request->getParameter('event_file'), $request->getFiles('event_file'));
      if ($this->eventForm->isValid() && $this->eventConfigForm->isValid() && $this->eventFileForm->isValid())
      {
        $this->eventForm->save();
        $this->eventConfigForm->save();
        $this->eventFileForm->save();

        // sonicdna after event id defined
        $this->setupLineup($params['lineup_config'], $this->event->getId());

        $this->redirect('@event_home?id='.$this->event->getId());
      }
    }
  }

  // sonicdna.net
  public function setupLineup($request, $eventId)
  {
    $lineup_before = Doctrine::getTable('EventLineUp')->findByEventId($eventId);
    if($lineup_before){
      $lineup_before->delete();
    }

    $lineups = preg_split("/\r\n|\r|\n/", $request);
    foreach( $lineups as $lineups_key => $lineups_val ){
      $lineup = preg_split("/,/", $lineups_val);
      $eventlineup = new EventLineUp();
      $eventlineup->setEventId($eventId);

      if( is_numeric($lineup[3]) )
      {
        $sub_id = intval($lineup[3]);
      }else{
        $sub_id = 1;
      }

      switch ($lineup[0]){
        case 'band':
          if(Doctrine::getTable('Band')->find(intval($lineup[1])))
          {
            $eventlineup->setSlotType('band');
            $eventlineup->setBandId(intval($lineup[1]));
            $eventlineup->setDuration(intval($lineup[2]));
            $eventlineup->setSubId($sub_id);
            $eventlineup->save();
          }
          break;
        case 'member':
          if(Doctrine::getTable('Member')->find(intval($lineup[1])))
          {
            $eventlineup->setSlotType('member');
            $eventlineup->setMemberId(intval($lineup[1]));
            $eventlineup->setDuration(intval($lineup[2]));
            $eventlineup->setSubId($sub_id);
            $eventlineup->save();
          }
          break;
        case 'slot':
          $eventlineup->setSlotType('slot');
          $eventlineup->setSlotName($lineup[1]);
          $eventlineup->setDuration(intval($lineup[2]));
          $eventlineup->setSubId($sub_id);
          $eventlineup->save();
          break;
        default:
      }
    }
  }


 /**
  * Executes search action
  *
  * @param sfRequest $request A request object
  */
  public function executeSearch($request)
  {
    sfConfig::set('sf_nav_type', 'default');

    $params = $request->getParameter('event', array());
    if ($request->hasParameter('search_query'))
    {
      $params = array_merge($params, array('name' => $request->getParameter('search_query', '')));
    }

    $this->filters = new EventFormFilter();
    $this->filters->bind($params);

    if (!isset($this->size))
    {
      $this->size = 20;
    }

    $this->pager = new opNonCountQueryPager('Event', $this->size);
    $q = $this->filters->getQuery()->orderBy('id desc')
              ->addWhere('id != 1')->addWhere('id != 2');	// remove part of event in the list
    $this->pager->setQuery($q);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

 /**
  * Executes delete action
  *
  * @param sfRequest $request A request object
  */
  public function executeDelete($request)
  {
    $this->forward404If($this->id && !$this->isDeleteEvent);

    if ($request->isMethod('post'))
    {
      if($request->hasParameter('is_delete'))
      {
        $request->checkCSRFProtection();
        $event = Doctrine::getTable('Event')->find($this->id);
        if ($event)
        {
          $event->delete();
        }
        $this->redirect('event/search');
      }
      else
      {
        $this->redirect('@event_home?id=' . $this->id);
      }
    }
    $this->event = Doctrine::getTable('Event')->find($this->id);
  }

 /**
  * Executes joinlist action
  *
  * @param sfRequest $request A request object
  */
  public function executeJoinlist($request)
  {
    $memberId = $request->getParameter('id', $this->getUser()->getMemberId());

    $this->member = Doctrine::getTable('Member')->find($memberId);
    $this->forward404Unless($this->member);

    if (!$this->size)
    {
      $this->size = 20;
    }

    $this->pager = Doctrine::getTable('Event')->getJoinEventListPager($memberId, $request->getParameter('page', 1), $this->size);

    if (!$this->pager->getNbResults())
    {
      return sfView::ERROR;
    }

    $this->crownIds = Doctrine::getTable('EventMember')->getEventIdsOfAdminByMemberId($memberId);

    return sfView::SUCCESS;
  }

 /**
  * Executes memberList action
  *
  * @param sfRequest $request A request object
  */
  public function executeMemberList($request)
  {
    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->forward404Unless($this->event);

    if (!$this->size)
    {
      $this->size = 20;
    }
    $this->pager = Doctrine::getTable('Event')->getEventMemberListPager($this->id, $request->getParameter('page', 1), $this->size);

    if (!$this->pager->getNbResults()) {
      return sfView::ERROR;
    }
    
    $this->crownIds = array(Doctrine::getTable('EventMember')->getEventAdmin($this->id)->getMemberId());
    
    return sfView::SUCCESS;
  }

 /**
  * Executes join action
  *
  * @param sfRequest $request A request object
  */
  public function executeJoin($request)
  {
    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->forward404Unless($this->event);

    if ($this->isEventMember || $this->isEventPreMember)
    {
      return sfView::ERROR;
    }

    $this->form = new opEventJoiningForm();
    if ($request->hasParameter('event_join'))
    {
      $this->form->bind($request->getParameter('event_join'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('EventMember')->join($this->getUser()->getMemberId(), $this->id, $this->event->getConfig('register_policy'));
        self::sendJoinMail($this->getUser()->getMemberId(), $this->id);

        if ('close' !== $this->event->getConfig('register_policy'))
        {
          $this->getUser()->setFlash('notice', 'You have just joined to this %event%.');
        }

        $this->redirect('@event_home?id='.$this->id);
      }
    }

    return sfView::INPUT;
  }

 /**
  * Executes quit action
  *
  * @param sfRequest $request A request object
  */
  public function executeQuit($request)
  {
    if (!$this->isEventMember || $this->isAdmin)
    {
      return sfView::ERROR;
    }

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->form = new opEventQuittingForm();
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('event_quit'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('EventMember')->quit($this->getUser()->getMemberId(), $this->id);
        $this->getUser()->setFlash('notice', 'You have just quitted this %event%.');
        $this->redirect('@event_home?id='.$this->id);
      }
    }
  }

 /**
  * Executes memberManage action
  *
  * @param sfRequest $request A request object
  */
  public function executeMemberManage($request)
  {
    $this->redirectUnless($this->isAdmin || $this->isSubAdmin, '@error');

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->pager = Doctrine::getTable('Event')->getEventMemberListPager($this->id, $request->getParameter('page', 1));

    if (!$this->pager->getNbResults())
    {
      return sfView::ERROR;
    }
  }

 /**
  * Executes changeAdminRequest action
  *
  * @param sfRequest $request A request object
  */
  public function executeChangeAdminRequest($request)
  {
    $this->forward404Unless($this->isAdmin);

    $this->member = Doctrine::getTable('Member')->find($request->getParameter('member_id'));
    $this->forward404Unless($this->member);

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($this->member->getId(), $this->id);

    $this->forward404If($this->eventMember->getIsPre());
    $this->forward404If($this->eventMember->hasPosition(array('admin', 'admin_confirm')));

    $this->form = new opChangeEventAdminRequestForm();
    if ($request->hasParameter('admin_request'))
    {
      $this->form->bind($request->getParameter('admin_request'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('EventMember')->requestChangeAdmin($this->member->getId(), $this->id);
        $this->redirect('@event_memberManage?id='.$this->id);
      }
    }

    return sfView::INPUT;
  }

 /**
  * Executes subAdminRequest action
  *
  * @param sfRequest $request A request object
  */
  public function executeSubAdminRequest($request)
  {
    $this->forward404Unless($this->isAdmin);

    $this->member = Doctrine::getTable('Member')->find($request->getParameter('member_id'));
    $this->forward404Unless($this->member);

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($this->member->getId(), $this->id);

    $this->forward404If($this->eventMember->getIsPre());
    $this->forward404If($this->eventMember->hasPosition(array('admin', 'admin_confirm', 'sub_admin', 'sub_admin_confirm')));

    $this->form = new opChangeEventAdminRequestForm();
    if ($request->hasParameter('admin_request'))
    {
      $this->form->bind($request->getParameter('admin_request'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('EventMember')->requestSubAdmin($this->member->getId(), $this->id);
        $this->redirect('@event_memberManage?id='.$this->id);
      }
    }

    return sfView::INPUT;
  }

  public function executeRemoveSubAdmin($request)
  {
    $this->forward404Unless($this->isAdmin);

    $this->member = Doctrine::getTable('Member')->find($request->getParameter('member_id'));
    $this->forward404Unless($this->member);

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($this->member->getId(), $this->id);

    $this->forward404If($this->eventMember->getIsPre());
    $this->forward404If(!$this->eventMember->hasPosition('sub_admin'));

    if ($request->isMethod(sfWebRequest::POST))
    {
      $request->checkCSRFProtection();

      $this->eventMember->removePosition('sub_admin');
      $this->redirect('@event_memberManage?id='.$this->id);
    }

    return sfView::INPUT;
  }

 /**
  * Executes dropMember action
  *
  * @param sfRequest $request A request object
  */
  public function executeDropMember($request)
  {
    $this->redirectUnless($this->isAdmin || $this->isSubAdmin, '@error');
    $member = Doctrine::getTable('Member')->find($request->getParameter('member_id'));
    $this->forward404Unless($member);

    $isEventMember = Doctrine::getTable('EventMember')->isMember($member->getId(), $this->id);
    $this->redirectUnless($isEventMember, '@error');
    $isAdmin = Doctrine::getTable('EventMember')->isAdmin($member->getId(), $this->id);
    $isSubAdmin = Doctrine::getTable('EventMember')->isSubAdmin($member->getId(), $this->id);
    $this->redirectIf($isAdmin || $isSubAdmin, '@error');

    if ($request->isMethod(sfWebRequest::POST))
    {
      $request->checkCSRFProtection();

      Doctrine::getTable('EventMember')->quit($member->getId(), $this->id);
      $this->redirect('@event_memberManage?id='.$this->id);
    }

    $this->member    = $member;
    $this->event = Doctrine::getTable('Event')->find($this->id);
    return sfView::INPUT;
  }

  public static function sendJoinMail($memberId, $eventId)
  {
    $eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($memberId, $eventId);
    if (!$eventMember)
    {
      return false;
    }

    if (!$eventMember->getIsPre())
    {
      $event = Doctrine::getTable('event')->find($eventId);
      $member = Doctrine::getTable('Member')->find($memberId);
      $params = array(
        'subject'    => sfContext::getInstance()->getI18N()->__('%1% has just joined your %event%', array('%1%' => $member->name)),
        'admin'      => $event->getAdminMember(),
        'event'  => $event,
        'new_member' => $member,
      );

      $isSendPc     = $event->getConfig('is_send_pc_joinEvent_mail');
      $isSendMobile = $event->getConfig('is_send_mobile_joinEvent_mail');

      $options = array(
        'is_send_pc'     => (bool)(null === $isSendPc ? 1 : $isSendPc),
        'is_send_mobile' => (bool)(null === $isSendMobile ? 1 : $isSendMobile)
      );

      opMailSend::sendTemplateMailToMember('joinEvent', $event->getAdminMember(), $params, $options);
    }
  }
}
