<?php

/**
 * PluginTune form.
 *
 * @package    sonicdna.net
 * @subpackage form
 * @author     msum
 */
abstract class PluginTuneForm extends BaseTuneForm
{
  public function setup()
  {
    parent::setup();

    $field_list = array('tune_name','artist_name','url','duration','lyric','event_id'); 

    $this->setWidget('tune_name',   new sfWidgetFormInputText(array('label' => 'tune_name'),
                                                              array('size' => 20, 'class' => 'tune_name')));
    $this->widgetSchema->setLabel('tune_name', 'Song Title');

    $this->setWidget('artist_name', new sfWidgetFormInputText(array('label' => 'artist_name'),
                                                              array('size' => 20, 'class' => 'artist_name')));
    $this->widgetSchema->setLabel('artist_name', 'Artist');

    $this->setWidget('url',         new sfWidgetFormInputText(array('label' => 'url'),
                                                              array('size' => 20, 'class' => 'url')));
    $this->widgetSchema->setLabel('url', 'URL');

    $this->setWidget('duration',         new sfWidgetFormInputText(array('label' => 'duration'),
                                                                    array('size' => 20, 'class' => 'duration')));
    $this->widgetSchema->setLabel('duration', 'Duration of performance<br>(min)');




    $member_id = sfContext::getInstance()->getUser()->getMemberId();

    // events
    $events = array();
    $q = Doctrine::getTable('EventLineUp')->createQuery('e')
      ->where('e.member_id = ?', $member_id);       // As Solo Member for the event
//      ->groupBy('e.event_id');      // not need because it puts into the same array num 
    foreach ($band_list as $band_select)            // As Band Member for the event
    {
      $q->orWhere('e.band_id = ?', $band_select->getBandId());
    }
    $event_lineup_list = $q->fetchArray();

    foreach ($event_lineup_list as $event_lineup)
    {
        $event_select = Doctrine::getTable('Event')->find($event_lineup['event_id']);
        $events[$event_select->getId()] = $event_select->getName();
    }
    $this->setWidget('event_id', new sfWidgetFormChoice(array('choices' => array('0' => '') + $events)));
    $this->widgetSchema->setLabel('event_id', 'Event');

    // Event sub id
    $event_sub_id_list = array();
    $q->AddGroupBy('e.sub_id');
    $event_lineup_list = $q->fetchArray();
    if(count($event_lineup_list) > 1)
    {
      array_push($field_list, 'player_sub_id');
      foreach($event_lineup_list as $event_lineup)
      {
        $event_sub_id_list[$event_lineup['sub_id']] = $event_lineup['sub_id'];
      }
      $this->setWidget('player_sub_id', new sfWidgetFormChoice(array('choices' => $event_sub_id_list)));
      $this->widgetSchema->setLabel('player_sub_id', 'Performances');
    }

    // bands
    array_push($field_list, 'band_id');
    $bands = array();
    $band_list = Doctrine::getTable('BandMember')->findByMemberId($member_id);
    foreach ($band_list as $band_select)
    {
      $band = Doctrine::getTable('Band')->find($band_select->getBandId());
      $bands[$band->getId()] = $band->getName();
    }
    $this->setWidget('band_id', new sfWidgetFormChoice(array('choices' => array('0' => '') + $bands)));
    $this->widgetSchema->setLabel('band_id', 'Band');


    // Public Scope  (Reserved)

/*    array_push($field_list, 'visibility');
    $visibilityScope = array();
    $visibilityScope[1] = 'Public';
    $visibilityScope[2] = 'Only Staff, All Players';
    $visibilityScope[3] = 'Only Staff, My Bands, Me';
    $this->setWidget('visibility', new sfWidgetFormChoice(array('choices' => $visibilityScope)));
    $this->widgetSchema->setLabel('visibility', 'Visibility');
*/

    $this->useFields($field_list);
  }
}
