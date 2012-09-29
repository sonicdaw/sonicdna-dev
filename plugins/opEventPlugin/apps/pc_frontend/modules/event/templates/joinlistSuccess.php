<?php
$options = array(
  'title' => __('%event% List', array('%event%' => $op_term['event']->titleize())),
  'list' => $pager->getResults(),
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@event_home?id=',
  'pager' => $pager,
  'link_to_pager' => '@event_joinlist?page=%d&id='.$member->getId(),
);
op_include_parts('photoTable', 'eventList', $options)
?>
