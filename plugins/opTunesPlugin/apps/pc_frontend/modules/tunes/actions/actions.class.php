<?php

/**
 * tunes actions.
 *
 * @package    sonicdna.net
 * @subpackage tunes
 * @author     msum
 */
class tunesActions extends sfActions
{
public function isSecure()
{
    if (($member = $this->getUser()->getMember()) && $member->getIsActive())
    {
      return true;
    }

    $current_routing = sfContext::getInstance()->getRouting()->getCurrentRouteName();
    if (('tunes' === $current_routing) ||
        ('tune_show' === $current_routing)){
      return false;
    }else{
      return true;
    }
}

 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeList(sfWebRequest $request)
  {
    $this->tunes = Doctrine::getTable('Tune')->findAll();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TuneForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $tune = new Tune();
    $tune->setMemberId($this->getUser()->getMemberId());
    $this->form = new TuneForm($tune);

    $this->form->bind($request->getParameter('tune'));

    if ($this->form->isValid())
    {
//      $this->form->save();
      $this->redirect('@tunes');
    }

    $this->setTemplate('new');

  }

  public function executeNewForBand(sfWebRequest $request)
  {
    $this->form = new TuneForm();
    $this->band_id = $request->getParameter('band_id');
  }

  public function executeCreateForBand(sfWebRequest $request)
  {
    $tune = new Tune();
    $tune->setMemberId($this->getUser()->getMemberId());

    $this->band_id = $request->getParameter('band_id');
    $tune->setBand_id ($this->band_id);
    $this->form = new TuneForm($tune);

    $this->form->bind($request->getParameter('tune'));

    if ($this->form->isValid())
    {
//      $this->form->save();
      $this->redirect('@band_home?id='.$this->band_id);
    }

    $this->setTemplate('newForBand');
  }

// New For Festival
  public function executeNewForEvent(sfWebRequest $request)
  {
    $this->form = new TuneForm();

    $this->band_id =         $request->getParameter('band_id');
    $this->event_id =        $request->getParameter('event_id');
    $this->event_member_id = $request->getParameter('event_member_id');
    $this->player_sub_id   = $request->getParameter('player_sub_id');

    $this->forward404Unless($this->event_member_id === $this->getUser()->getMemberId());

    $this->form->setDefault('band_id', $this->band_id);
    $this->form->setDefault('event_id', $this->event_id);
  }

  public function executeCreateForEvent(sfWebRequest $request)
  {
    $tune = new Tune();

    $this->event_id = $request->getParameter('event_id');
    $tune->setEvent_id ($this->event_id);
    $this->band_id = $request->getParameter('band_id');
    $tune->setBand_id ($this->band_id);
    $this->event_member_id = $request->getParameter('event_member_id');
    $this->player_sub_id   = $request->getParameter('player_sub_id');
    $tune->setPlayerSubId ($this->player_sub_id);

    $this->forward404Unless($this->event_member_id === $this->getUser()->getMemberId());

    // Sort Order (MAX + 10)
    $tunes_forOrder = Doctrine_Query::create()
      ->select('max(u.sort_order) as max_sort_order')
      ->from('tune u')
      ->where('u.event_id = ?', $this->event_id)
      ->andWhere('u.player_sub_id = ?', $this->player_sub_id)
      ->execute();
    $max_order = 0;
    if($tunes_forOrder[0]['max_sort_order'] > 0){
      $max_order = $tunes_forOrder[0]['max_sort_order'];
    }
    $tune->setSortOrder ($max_order + 10);


    $tune->setMemberId($this->event_member_id);
    $this->form = new TuneForm($tune);

    $this->form->bind($request->getParameter('tune'));

    if ($this->form->isValid())
    {
      $this->form->save();
      $this->redirect('@homepage');
    }

    $this->setTemplate('newForEvent');
  }



  public function executeEdit(sfWebRequest $request)
  {
    $this->form = new TuneForm();
    $this->tunes = $this->getRoute()->getObject();

    $this->forward404Unless($this->tunes->getMemberId() === $this->getUser()->getMemberId());

    $this->form->setDefault('artist_name', $this->getRoute()->getObject()->getArtistName());
    $this->form->setDefault('tune_name', $this->getRoute()->getObject()->getTuneName());
    $this->form->setDefault('url', $this->getRoute()->getObject()->getUrl());
    $this->form->setDefault('duration', $this->getRoute()->getObject()->getDuration());
    $this->form->setDefault('lyric', $this->getRoute()->getObject()->getLyric());
    $this->form->setDefault('band_id', $this->getRoute()->getObject()->getBandId());
    $this->form->setDefault('event_id', $this->getRoute()->getObject()->getEventId());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->tunes = $this->getRoute()->getObject();
    $this->forward404Unless($this->tunes->getMemberId() === $this->getUser()->getMemberId());

    $tune = new Tune();
    $tune->setMemberId($this->getUser()->getMemberId());

    $this->form = new TuneForm($this->tunes);

    $this->form->bind($request->getParameter('tune'));

    if ($this->form->isValid())
    {
      $this->form->save();
      $this->redirect('@homepage');
    }

    $this->setTemplate('edit');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->tunes = $this->getRoute()->getObject();
  }

  public function executeDeleteConfirm(sfWebRequest $request)
  {
    $this->tunes = $this->getRoute()->getObject();

    $this->forward404Unless($this->tunes->getMemberId() === $this->getUser()->getMemberId());
  }

  public function executeDelete(sfWebRequest $request)
  {
    $tunes = $this->getRoute()->getObject();

    $this->forward404Unless($tunes->getMemberId() === $this->getUser()->getMemberId());
    $request->checkCSRFProtection();

    $tunes->delete();

    $this->redirect('@homepage');
  }
}
