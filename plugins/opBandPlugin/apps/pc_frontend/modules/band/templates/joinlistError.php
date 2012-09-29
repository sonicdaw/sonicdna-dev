<?php op_include_box('noJoinBand', __('You don\'t have any joined %band%.', array('%band%' => $op_term['band']->pluralize())), array('title' => __('Joined %band%', array('%band%' => $op_term['band']->pluralize()->titleize())))) ?>

<?php use_helper('Javascript') ?>
<?php op_include_line('backLink', link_to_function(__('Back to previous page'), 'history.back()')) ?>
