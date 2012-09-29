<?php
$options = array(
  'title' => __('%band% List', array('%band%' => $op_term['band']->titleize())),
  'list' => $pager->getResults(),
  'crownIds' => $sf_data->getRaw('crownIds'),
  'link_to' => '@band_home?id=',
  'pager' => $pager,
  'link_to_pager' => '@band_joinlist?page=%d&id='.$member->getId(),
);
op_include_parts('photoTable', 'bandList', $options)
?>
