<?php op_include_yesno('delete_confirm', new BaseForm(), new BaseForm(array(), array(), false), array(
  'title' => __('Delete your %1% account', array('%1%' => $op_config['sns_name'])),
  'body' => __('Are you sure you want to delete this account?<br>This operation cannot be undone.'),
  'yes_method' => 'get',
  'yes_url' => url_for('@member_delete'),
  'no_method' => 'get',
  'no_url' => url_for('@homepage'),
))
?>
