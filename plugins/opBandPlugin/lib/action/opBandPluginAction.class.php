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
 * opBandAction
 *
 * @package    sonicdna.net (original: OpenPNE)
 * @subpackage action
 * @author     msum
 */
abstract class opBandPluginAction extends sfActions
{
  public function preExecute()
  {
    $this->id = $this->getRequestParameter('id');

    $memberId = $this->getUser()->getMemberId();
    $this->isBandMember = Doctrine::getTable('BandMember')->isMember($memberId, $this->id);
    $this->isBandPreMember = Doctrine::getTable('BandMember')->isPreMember($memberId, $this->id);
    $this->isAdmin = Doctrine::getTable('BandMember')->isAdmin($memberId, $this->id);
    $this->isSubAdmin = Doctrine::getTable('BandMember')->isSubAdmin($memberId, $this->id);
    $this->isEditBand = $this->isAdmin || $this->isSubAdmin;
    $this->isDeleteBand = $this->isAdmin;
  }

 /**
  * Executes home action
  *
  * @param sfRequest $request A request object
  */
  public function executeHome($request)
  {
    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->forward404Unless($this->band, 'Undefined band.');
    $this->bandAdmin = $this->band->getAdminMember();
    $this->bandSubAdmins = $this->band->getSubAdminMembers();

    if (!$this->membersSize)
    {
      $this->membersSize = 9;
    }
    $this->members = $this->band->getMembers($this->membersSize, true);
  }

 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit($request)
  {
    $this->forward404If($this->id && !$this->isEditBand);

    $this->band = Doctrine::getTable('Band')->find($this->id);
    if (!$this->band)
    {
      $this->band = new Band();
    }
    else
    {
      if ($request->isMethod('post') && $request->hasParameter('is_delete'))
      {
        $this->redirect('@band_delete');
      }
    }

    $this->bandForm       = new BandForm($this->band);
    $this->bandConfigForm = new BandConfigForm(array(), array('band' => $this->band));
    $this->bandFileForm = isset($this->enableImage) && $this->enableImage ?
      new BandFileForm(array(), array('band' => $this->band)) :
      new BandFileForm();

    if ($request->isMethod('post'))
    {
      $params = $request->getParameter('band');
      $params['id'] = $this->id;
      $this->bandForm->bind($params);
      $this->bandConfigForm->bind($request->getParameter('band_config'));
      $this->bandFileForm->bind($request->getParameter('band_file'), $request->getFiles('band_file'));
      if ($this->bandForm->isValid() && $this->bandConfigForm->isValid() && $this->bandFileForm->isValid())
      {
        $this->bandForm->save();
        $this->bandConfigForm->save();
        $this->bandFileForm->save();

        $this->redirect('@band_home?id='.$this->band->getId());
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

    $params = $request->getParameter('band', array());
    if ($request->hasParameter('search_query'))
    {
      $params = array_merge($params, array('name' => $request->getParameter('search_query', '')));
    }

    $this->filters = new BandFormFilter();
    $this->filters->bind($params);

    if (!isset($this->size))
    {
      $this->size = 20;
    }

    $this->pager = new opNonCountQueryPager('Band', $this->size);
    $q = $this->filters->getQuery()->orderBy('id desc');
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
    $this->forward404If($this->id && !$this->isDeleteBand);

    if ($request->isMethod('post'))
    {
      if($request->hasParameter('is_delete'))
      {
        $request->checkCSRFProtection();
        $band = Doctrine::getTable('Band')->find($this->id);
        if ($band)
        {
          $band->delete();
        }
        $this->redirect('band/search');
      }
      else
      {
        $this->redirect('@band_home?id=' . $this->id);
      }
    }
    $this->band = Doctrine::getTable('Band')->find($this->id);
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

    $this->pager = Doctrine::getTable('Band')->getJoinBandListPager($memberId, $request->getParameter('page', 1), $this->size);

    if (!$this->pager->getNbResults())
    {
      return sfView::ERROR;
    }

    $this->crownIds = Doctrine::getTable('BandMember')->getBandIdsOfAdminByMemberId($memberId);

    return sfView::SUCCESS;
  }

 /**
  * Executes memberList action
  *
  * @param sfRequest $request A request object
  */
  public function executeMemberList($request)
  {
    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->forward404Unless($this->band);

    if (!$this->size)
    {
      $this->size = 20;
    }
    $this->pager = Doctrine::getTable('Band')->getBandMemberListPager($this->id, $request->getParameter('page', 1), $this->size);

    if (!$this->pager->getNbResults()) {
      return sfView::ERROR;
    }
    
    $this->crownIds = array(Doctrine::getTable('BandMember')->getBandAdmin($this->id)->getMemberId());
    
    return sfView::SUCCESS;
  }

 /**
  * Executes join action
  *
  * @param sfRequest $request A request object
  */
  public function executeJoin($request)
  {
    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->forward404Unless($this->band);

    if ($this->isBandMember || $this->isBandPreMember)
    {
      return sfView::ERROR;
    }

    $this->form = new opBandJoiningForm();
    if ($request->hasParameter('band_join'))
    {
      $this->form->bind($request->getParameter('band_join'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('BandMember')->join($this->getUser()->getMemberId(), $this->id, $this->band->getConfig('register_policy'));
        self::sendJoinMail($this->getUser()->getMemberId(), $this->id);

        if ('close' !== $this->band->getConfig('register_policy'))
        {
          $this->getUser()->setFlash('notice', 'You have just joined to this %band%.');
        }

        $this->redirect('@band_home?id='.$this->id);
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
    if (!$this->isBandMember || $this->isAdmin)
    {
      return sfView::ERROR;
    }

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->form = new opBandQuittingForm();
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('band_quit'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('BandMember')->quit($this->getUser()->getMemberId(), $this->id);
        $this->getUser()->setFlash('notice', 'You have just quitted this %band%.');
        $this->redirect('@band_home?id='.$this->id);
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

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->pager = Doctrine::getTable('Band')->getBandMemberListPager($this->id, $request->getParameter('page', 1));

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

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($this->member->getId(), $this->id);

    $this->forward404If($this->bandMember->getIsPre());
    $this->forward404If($this->bandMember->hasPosition(array('admin', 'admin_confirm')));

    $this->form = new opChangeBandAdminRequestForm();
    if ($request->hasParameter('admin_request'))
    {
      $this->form->bind($request->getParameter('admin_request'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('BandMember')->requestChangeAdmin($this->member->getId(), $this->id);
        $this->redirect('@band_memberManage?id='.$this->id);
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

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($this->member->getId(), $this->id);

    $this->forward404If($this->bandMember->getIsPre());
    $this->forward404If($this->bandMember->hasPosition(array('admin', 'admin_confirm', 'sub_admin', 'sub_admin_confirm')));

    $this->form = new opChangeBandAdminRequestForm();
    if ($request->hasParameter('admin_request'))
    {
      $this->form->bind($request->getParameter('admin_request'));
      if ($this->form->isValid())
      {
        Doctrine::getTable('BandMember')->requestSubAdmin($this->member->getId(), $this->id);
        $this->redirect('@band_memberManage?id='.$this->id);
      }
    }

    return sfView::INPUT;
  }

  public function executeRemoveSubAdmin($request)
  {
    $this->forward404Unless($this->isAdmin);

    $this->member = Doctrine::getTable('Member')->find($request->getParameter('member_id'));
    $this->forward404Unless($this->member);

    $this->band = Doctrine::getTable('Band')->find($this->id);
    $this->bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($this->member->getId(), $this->id);

    $this->forward404If($this->bandMember->getIsPre());
    $this->forward404If(!$this->bandMember->hasPosition('sub_admin'));

    if ($request->isMethod(sfWebRequest::POST))
    {
      $request->checkCSRFProtection();

      $this->bandMember->removePosition('sub_admin');
      $this->redirect('@band_memberManage?id='.$this->id);
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

    $isBandMember = Doctrine::getTable('BandMember')->isMember($member->getId(), $this->id);
    $this->redirectUnless($isBandMember, '@error');
    $isAdmin = Doctrine::getTable('BandMember')->isAdmin($member->getId(), $this->id);
    $isSubAdmin = Doctrine::getTable('BandMember')->isSubAdmin($member->getId(), $this->id);
    $this->redirectIf($isAdmin || $isSubAdmin, '@error');

    if ($request->isMethod(sfWebRequest::POST))
    {
      $request->checkCSRFProtection();

      Doctrine::getTable('BandMember')->quit($member->getId(), $this->id);
      $this->redirect('@band_memberManage?id='.$this->id);
    }

    $this->member    = $member;
    $this->band = Doctrine::getTable('Band')->find($this->id);
    return sfView::INPUT;
  }

  public static function sendJoinMail($memberId, $bandId)
  {
    $bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($memberId, $bandId);
    if (!$bandMember)
    {
      return false;
    }

    if (!$bandMember->getIsPre())
    {
      $band = Doctrine::getTable('band')->find($bandId);
      $member = Doctrine::getTable('Member')->find($memberId);
      $params = array(
        'subject'    => sfContext::getInstance()->getI18N()->__('%1% has just joined your %band%', array('%1%' => $member->name)),
        'admin'      => $band->getAdminMember(),
        'band'  => $band,
        'new_member' => $member,
      );

      $isSendPc     = $band->getConfig('is_send_pc_joinBand_mail');
      $isSendMobile = $band->getConfig('is_send_mobile_joinBand_mail');

      $options = array(
        'is_send_pc'     => (bool)(null === $isSendPc ? 1 : $isSendPc),
        'is_send_mobile' => (bool)(null === $isSendMobile ? 1 : $isSendMobile)
      );

      opMailSend::sendTemplateMailToMember('joinBand', $band->getAdminMember(), $params, $options);
    }
  }
}
