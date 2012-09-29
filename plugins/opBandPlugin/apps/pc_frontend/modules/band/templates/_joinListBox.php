<?php
$options = array(
  'title' => __('%band% List', array('%band%' => $op_term['band']->titleize())),
  'list' => $communities,
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@band_home?id=',
  'moreInfo' => array(link_to(sprintf('%s(%d)', __('Show all'), $member->countJoinCommunity()), '@band_joinlist?id='.$member->id)),
  'type' => $sf_data->getRaw('gadget')->getConfig('type'),
  'row' => $row,
  'col' => $col,
);
op_include_parts('nineTable', 'bandList_'.$gadget->getId(), $options);
