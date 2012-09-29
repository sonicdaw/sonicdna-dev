<?php slot('firstRow') ?>
<tr><th><?php echo __('Photo') ?></th><td><?php echo link_to(op_image_tag_sf_image($event->getImageFileName(), array('size' => '204x75')), '@event_home?id='.$id) ?> </td></tr>
<tr><th><?php echo __('%event%', array('%event%' => $op_term['event']->titleize())) ?></th><td><?php echo link_to($event->getName(), '@event_home?id='.$id) ?></td></tr>
<?php end_slot() ?>
<?php op_include_form('eventQuiting', $form, array(
  'title'    => __('Quit "%1%"', array('%1%' => $event->getName())),
  'body'     => __('Do you really quit the following %event%?'),
  'firstRow' => get_slot('firstRow')
)) ?>
