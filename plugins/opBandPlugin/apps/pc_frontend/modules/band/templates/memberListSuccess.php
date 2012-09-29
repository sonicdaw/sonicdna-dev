<?php
$options = array(
  'title' => __('%band% Members', array('%band%' => $op_term['band']->titleize())),
  'list' => $pager->getResults(),
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@member_profile?id=',
  'pager' => $pager,
  'link_to_pager' => '@band_memberList?page=%d&id='.$band->getId(),
  'use_op_link_to_member' => true,
);
op_include_parts('photoTable', 'bandMembersList', $options)
?>
