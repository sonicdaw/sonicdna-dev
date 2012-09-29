<?php op_include_parts('yesNo', 'dropMemberConfirmForm', array(
  'body'      => __('Do you drop %0% from this %band%?', array('%0%' => $member->getName())),
  'yes_form'  => new sfForm(),
  'no_url'    => url_for('@band_memberManage?id='.$band->getId()),
  'no_method' => 'get',
)) ?>
