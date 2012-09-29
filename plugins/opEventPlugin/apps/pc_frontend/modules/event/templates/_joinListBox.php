<?php
$options = array(
  'title' => __('%event% List', array('%event%' => $op_term['event']->titleize())),
  'list' => $communities,
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@event_home?id=',
  'moreInfo' => array(link_to(sprintf('%s(%d)', __('Show all'), $member->countJoinCommunity()), '@event_joinlist?id='.$member->id)),
  'type' => $sf_data->getRaw('gadget')->getConfig('type'),
  'row' => $row,
  'col' => $col,
);
op_include_parts('nineTableEvent', 'eventList_'.$gadget->getId(), $options);
