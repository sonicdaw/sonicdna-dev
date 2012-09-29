<?php op_include_parts('yesNo', 'removeSubAdminConfirmForm', array(
  'body'      => __("Do you demote %0% from this %band%'s sub-administrator?", array('%0%' => $member->getName())),
  'yes_form'  => new BaseForm(),
  'no_url'    => url_for('@band_memberManage?id='.$band->getId()),
  'no_method' => 'get',
)) ?>
