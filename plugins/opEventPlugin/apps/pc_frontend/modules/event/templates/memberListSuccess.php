<?php
$options = array(
  'title' => __('%event% Members', array('%event%' => $op_term['event']->titleize())),
  'list' => $pager->getResults(),
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@member_profile?id=',
  'pager' => $pager,
  'link_to_pager' => '@event_memberList?page=%d&id='.$event->getId(),
  'use_op_link_to_member' => true,
);
op_include_parts('photoTable', 'eventMembersList', $options)
?>
