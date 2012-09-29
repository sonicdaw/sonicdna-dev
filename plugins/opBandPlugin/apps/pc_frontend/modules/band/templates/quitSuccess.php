<?php slot('firstRow') ?>
<tr><th><?php echo __('Photo') ?></th><td><?php echo link_to(op_image_tag_sf_image($band->getImageFileName(), array('size' => '76x76')), '@band_home?id='.$id) ?> </td></tr>
<tr><th><?php echo __('%band%', array('%band%' => $op_term['band']->titleize())) ?></th><td><?php echo link_to($band->getName(), '@band_home?id='.$id) ?></td></tr>
<?php end_slot() ?>
<?php op_include_form('bandQuiting', $form, array(
  'title'    => __('Quit "%1%"', array('%1%' => $band->getName())),
  'body'     => __('Do you really quit the following %band%?'),
  'firstRow' => get_slot('firstRow')
)) ?>
