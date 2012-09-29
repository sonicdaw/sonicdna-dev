<?php op_include_box('noJoinEvent', __('You don\'t have any joined %event%.', array('%event%' => $op_term['event']->pluralize())), array('title' => __('Joined %event%', array('%event%' => $op_term['event']->pluralize()->titleize())))) ?>

<?php use_helper('Javascript') ?>
<?php op_include_line('backLink', link_to_function(__('Back to previous page'), 'history.back()')) ?>
