<?php //op_include_box('informationThisPage',
//  '<p>'.__('Do you delete your %1% account?', array('%1%' => $op_config['sns_name'])).'</p>'.
//  '<p>'.__('Please input your password if you want to delete your account.').'</p>') ?>

<?php
//op_include_form('passwordForm', $form, array(
//  'title' => __('Delete your %1% account', array('%1%' => $op_config['sns_name'])),
//  'url' => url_for('member/delete'),
//))
?>


<?php op_include_yesno('delete_confirm', new BaseForm(), new BaseForm(array(), array(), false), array(
  'title' => __('Delete your %1% account', array('%1%' => $op_config['sns_name'])),
//  'body' => get_slot('body'),
  'body' => __('Final Answer?'),
  'yes_url' => url_for('member/delete'),
  'no_method' => 'get',
  'no_url' => url_for('@homepage'),
))
?>
