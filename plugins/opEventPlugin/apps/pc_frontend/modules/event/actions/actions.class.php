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
 * @subpackage event
 * @author     msum (sonicdna.net)  
 */
class eventActions extends opEventPluginAction
{
  // for frame unlimit login
  public function isSecure(){
    if (($member = $this->getUser()->getMember()) && $member->getIsActive())
    {
      return true;
    }

    $current_routing = sfContext::getInstance()->getRouting()->getCurrentRouteName();
    if (('event_edit' === $current_routing) ||
        ('event_delete' === $current_routing) ||
        ('event_deleteImage' === $current_routing) ||
        ('event_join' === $current_routing) ||
        ('event_quit' === $current_routing)){
      return true;
    }else{
      return false;
    }
  }

 /**
  * Executes home action
  *
  * @param opWebRequest $request A request object
  */
  public function executeHome(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtHome');
    $this->tunes = Doctrine::getTable('Tune')->findByMember_id(0);

    return parent::executeHome($request);
  }


 /**
  * Executes top action for sonic dna
  *
  * @param opWebRequest $request A request object
  */
  public function executeTop(opWebRequest $request)
  {
    return parent::executeSearch($request);
  }


 /**
  * Executes home action
  *
  * @param opWebRequest $request A request object
  */
  public function executeTimetable(opWebRequest $request)
  {
//    $this->forwardIf($request->isSmartphone(), 'event', 'smtHome');

    $this->tunes = Doctrine::getTable('Tune')->findByMember_id(0);

    return parent::executeHome($request);
  }


 /**
  * Executes smtHome action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtHome(opWebRequest $request)
  {
    $gadgets = Doctrine::getTable('Gadget')->retrieveGadgetsByTypesName('smartphoneEvent');
    $this->contentsGadgets = $gadgets['smartphoneEventContents'];

    $this->event = Doctrine::getTable('Event')->find($this->id);
    $this->forward404Unless($this->event);

    opSmartphoneLayoutUtil::setLayoutParameters(array('event' => $this->event));

    return sfView::SUCCESS;
  }

 /**
  * Executes edit action
  *
  * @param opWebRequest $request A request object
  */
  public function executeEdit(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtEdit');

    $this->enableImage = true;
    $result = parent::executeEdit($request);

    if ($this->event->isNew()) {
      sfConfig::set('sf_nav_type', 'default');
    }

    return $result;
  }

 /**
  * Executes smtEdit action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtEdit(opWebRequest $request)
  {
    $result = parent::executeEdit($request);

    if ($this->event->isNew())
    {
      $this->setLayout('smtLayoutHome');
    }
    else
    {
      opSmartphoneLayoutUtil::setLayoutParameters(array('event' => $this->event));
    }

    return $result;
  }

 /**
  * Executes memberList action
  *
  * @param opWebRequest $request A request object
  */
  public function executeMemberList($request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtMemberList');

    return parent::executeMemberList($request);
  }

 /**
  * Executes smtMemberList action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtMemberList(opWebRequest $request)
  {
    $result = parent::executeMemberList($request);

    opSmartphoneLayoutUtil::setLayoutParameters(array('event' => $this->event));

    return $result;
  }

 /**
  * Executes joinlist action
  *
  * @param opWebRequest $request A request object
  */
  public function executeJoinlist(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtJoinlist');

    sfConfig::set('sf_nav_type', 'default');

    if ($request->hasParameter('id') && $request->getParameter('id') != $this->getUser()->getMemberId())
    {
      sfConfig::set('sf_nav_type', 'friend');
    }

    return parent::executeJoinlist($request);
  }

 /**
  * Executes smtJoinlist action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtJoinlist(opWebRequest $request)
  {
    $result = parent::executeJoinlist($request);

    if ($request['id'] && $request['id'] !== $this->getUser()->getMemberId())
    {
      $this->targetMember = Doctrine::getTable('Member')->find((int)$request['id']);
    }
    else
    {
      $this->targetMember = $this->getUser()->getMember();
    }

    opSmartphoneLayoutUtil::setLayoutParameters(array('member' => $this->member)); 

    return $result;
  }

 /**
  * Executes join action
  *
  * @param opWebRequest $request A request object
  */
  public function executeJoin(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtJoin');

    return parent::executeJoin($request);
  }

 /**
  * Executes smtJoin action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtJoin(opWebRequest $request)
  {
    $result = parent::executeJoin($request);

    opSmartphoneLayoutUtil::setLayoutParameters(array('event' => $this->event));

    return $result;
  }

 /**
  * Executes quit action
  *
  * @param opWebRequest $request A request object
  */
  public function executeQuit(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtQuit');

    return parent::executeQuit($request);
  }

 /**
  * Executes smtJoin action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtQuit(opWebRequest $request)
  {
    $result = parent::executeQuit($request);

    opSmartphoneLayoutUtil::setLayoutParameters(array('event' => $this->event));

    return $result;
  }

 /**
  * Executes search action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSearch(opWebRequest $request)
  {
    $this->forwardIf($request->isSmartphone(), 'event', 'smtSearch');

    return parent::executeSearch($request);
  }

 /**
  * Executes smtSearch action
  *
  * @param opWebRequest $request A request object
  */
  public function executeSmtSearch(opWebRequest $request)
  {
    return sfView::SUCCESS;
  }
}
