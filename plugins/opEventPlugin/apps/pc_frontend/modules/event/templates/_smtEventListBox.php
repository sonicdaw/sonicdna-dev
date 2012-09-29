<div class="row">
  <div class="gadget_header span12">コミュニティ情報</div>
</div>
<div class="row">
  <div class="span12">
    <hr class="toumei" />
    <?php echo op_image_tag_sf_image($event->getImageFileName(), array('size' => '320x320', 'format' => 'jpg')) ?>
    <hr class="toumei" />
  </div>
</div>
<div class="row">
<table class="table table-striped span12">
<tbody>
<tr>
  <td><?php echo __('Date Created') ?></td>
  <td><?php echo op_format_date($event->getCreatedAt(), 'D') ?></td>
</tr>
<tr>
  <td><?php echo __('Administrator') ?></td>
  <td><?php echo link_to($eventAdmin->getName(), '@member_profile?id='.$eventAdmin->getId()) ?></td>
</tr>
<?php
$subAdminCaption = array();
foreach ($eventSubAdmins as $m) 
{
  $subAdminCaption[] = link_to($m->getName(), '@member_profile?id='.$m->getId());
}
?>
<?php if (count($subAdminCaption)): ?>
<tr>
  <td><?php echo __('Sub Administrator') ?></td>
  <td><?php echo implode("<br />\n", $subAdminCaption) ?></td>
</tr>
<?php endif; ?>
<?php if ($event->event_category_id): ?>
<tr>
  <td><?php echo __('%event% Category', array('%event%' => $op_term['event']->titleize()), 'form_event') ?>:</td>
  <td><?php echo $event->getEventCategory() ?></td>
</tr>
<?php endif; ?>
<tr>
  <td><?php echo __('Register policy', array('%event%' => $op_term['event']->titleize()), 'form_event') ?>:</td>
  <td><?php echo __($sf_data->getRaw('event')->getRegisterPolicy()) ?></td>
</tr>
<tr>
  <td><?php echo __('Count of Members'); ?></td>
  <td><?php echo $event->countEventMembers(); ?></td>
</tr>
<tr>
  <td><?php echo __('%event% Description', array('%event%' => $op_term['event']->titleize()), 'form_event') ?></td>
  <td><?php echo nl2br($event->getConfig('description')) ?></td>
</tr>
<tr>
  <td></td>
  <td>
  <?php if ($isEditEvent) : ?>
  <?php endif; ?>
  <?php if (!$isAdmin) : ?>
  <?php if ($isEventMember) : ?>
  <p id="leaveEventLink"><a href="#" id="leaveEvent"><?php echo __('Leave this %event%', array('%event%' => $op_term['event']->titleize())) ?></a></p>
  <p id="leaveEventLoading" class="hide"><?php echo op_image_tag('ajax-loader.gif') ?></p>
  <p id="leaveEventFinish" class="hide"><?php echo __('You have just quitted this %event%.') ?></p>
  <p id="leaveEventError" class="hide"><?php echo __('You haven\'t joined this %event% yet.') ?></p>
  <?php else : ?>
  <?php if ($isEventPreMember) : ?>
  <?php echo __('You are waiting for the participation approval by %event%\'s administrator.', array('%event%' => $op_term['event']->titleize())) ?>
  <?php else: ?>
  <p id="joinEventLink"><a href="#" id="joinEvent"><?php echo __('Join this %event%', array('%event%' => $op_term['event']->titleize())) ?></a></p>
  <p id="joinEventLoading" class="hide"><?php echo op_image_tag('ajax-loader.gif') ?></p>
  <p id="joinEventFinish" class="hide"><?php echo __('You have just joined to this %event%.') ?></p>
  <p id="joinEventError" class="hide"><?php echo __('You are already joined to this %event%.') ?></p>
  <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>
  </td></tr>
</tbody>
</table>
</div>
<script type="text/javascript">
$(function(){

  $('#leaveEvent').click(function(){
    $('#leaveEventLoading').show();
    $('#leaveEventLink').hide();
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'event/join.json',
      data: 'event_id=<?php echo $event->getId() ?>&leave=true&apiKey=' + openpne.apiKey,
      success: function(json){
        $('#leaveEventFinish').show();
        $('#leaveEventLoading').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#leaveEventError').show();
        $('#leaveEventLoading').hide();
      },
    });
  });

  $('#joinEvent').click(function(){
    $('#joinEventLoading').show();
    $('#joinEventLink').hide();
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'event/join.json',
      data: 'event_id=<?php echo $event->getId() ?>&apiKey=' + openpne.apiKey,
      success: function(json){
        $('#joinEventFinish').show();
        $('#joinEventLoading').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#joinEventError').show();
        $('#joinEventLoading').hide();
      },
    });
  });

});
</script>
