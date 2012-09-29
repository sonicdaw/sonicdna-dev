<?php op_include_parts('yesNo', 'dropMemberConfirmForm', array(
  'body'      => __('Do you drop %0% from this %event%?', array('%0%' => $member->getName())),
  'yes_form'  => new sfForm(),
  'no_url'    => url_for('@event_memberManage?id='.$event->getId()),
  'no_method' => 'get',
)) ?>
