<?php op_include_parts('yesNo', 'removeSubAdminConfirmForm', array(
  'body'      => __("Do you demote %0% from this %event%'s sub-administrator?", array('%0%' => $member->getName())),
  'yes_form'  => new BaseForm(),
  'no_url'    => url_for('@event_memberManage?id='.$event->getId()),
  'no_method' => 'get',
)) ?>
