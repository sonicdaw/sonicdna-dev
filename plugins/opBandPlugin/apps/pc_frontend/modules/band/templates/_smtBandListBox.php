<div class="row">
  <div class="gadget_header span12">コミュニティ情報</div>
</div>
<div class="row">
  <div class="span12">
    <hr class="toumei" />
    <?php echo op_image_tag_sf_image($band->getImageFileName(), array('size' => '320x320', 'format' => 'jpg')) ?>
    <hr class="toumei" />
  </div>
</div>
<div class="row">
<table class="table table-striped span12">
<tbody>
<tr>
  <td><?php echo __('Date Created') ?></td>
  <td><?php echo op_format_date($band->getCreatedAt(), 'D') ?></td>
</tr>
<tr>
  <td><?php echo __('Administrator') ?></td>
  <td><?php echo link_to($bandAdmin->getName(), '@member_profile?id='.$bandAdmin->getId()) ?></td>
</tr>
<?php
$subAdminCaption = array();
foreach ($bandSubAdmins as $m) 
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
<?php if ($band->band_category_id): ?>
<tr>
  <td><?php echo __('%band% Category', array('%band%' => $op_term['band']->titleize()), 'form_band') ?>:</td>
  <td><?php echo $band->getBandCategory() ?></td>
</tr>
<?php endif; ?>
<tr>
  <td><?php echo __('Register policy', array('%band%' => $op_term['band']->titleize()), 'form_band') ?>:</td>
  <td><?php echo __($sf_data->getRaw('band')->getRegisterPolicy()) ?></td>
</tr>
<tr>
  <td><?php echo __('Count of Members'); ?></td>
  <td><?php echo $band->countBandMembers(); ?></td>
</tr>
<tr>
  <td><?php echo __('%band% Description', array('%band%' => $op_term['band']->titleize()), 'form_band') ?></td>
  <td><?php echo nl2br($band->getConfig('description')) ?></td>
</tr>
<tr>
  <td></td>
  <td>
  <?php if ($isEditBand) : ?>
  <?php endif; ?>
  <?php if (!$isAdmin) : ?>
  <?php if ($isBandMember) : ?>
  <p id="leaveBandLink"><a href="#" id="leaveBand"><?php echo __('Leave this %band%', array('%band%' => $op_term['band']->titleize())) ?></a></p>
  <p id="leaveBandLoading" class="hide"><?php echo op_image_tag('ajax-loader.gif') ?></p>
  <p id="leaveBandFinish" class="hide"><?php echo __('You have just quitted this %band%.') ?></p>
  <p id="leaveBandError" class="hide"><?php echo __('You haven\'t joined this %band% yet.') ?></p>
  <?php else : ?>
  <?php if ($isBandPreMember) : ?>
  <?php echo __('You are waiting for the participation approval by %band%\'s administrator.', array('%band%' => $op_term['band']->titleize())) ?>
  <?php else: ?>
  <p id="joinBandLink"><a href="#" id="joinBand"><?php echo __('Join this %band%', array('%band%' => $op_term['band']->titleize())) ?></a></p>
  <p id="joinBandLoading" class="hide"><?php echo op_image_tag('ajax-loader.gif') ?></p>
  <p id="joinBandFinish" class="hide"><?php echo __('You have just joined to this %band%.') ?></p>
  <p id="joinBandError" class="hide"><?php echo __('You are already joined to this %band%.') ?></p>
  <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>
  </td></tr>
</tbody>
</table>
</div>
<script type="text/javascript">
$(function(){

  $('#leaveBand').click(function(){
    $('#leaveBandLoading').show();
    $('#leaveBandLink').hide();
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'band/join.json',
      data: 'band_id=<?php echo $band->getId() ?>&leave=true&apiKey=' + openpne.apiKey,
      success: function(json){
        $('#leaveBandFinish').show();
        $('#leaveBandLoading').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#leaveBandError').show();
        $('#leaveBandLoading').hide();
      },
    });
  });

  $('#joinBand').click(function(){
    $('#joinBandLoading').show();
    $('#joinBandLink').hide();
    $.ajax({
      type: 'GET',
      url: openpne.apiBase + 'band/join.json',
      data: 'band_id=<?php echo $band->getId() ?>&apiKey=' + openpne.apiKey,
      success: function(json){
        $('#joinBandFinish').show();
        $('#joinBandLoading').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown){
        $('#joinBandError').show();
        $('#joinBandLoading').hide();
      },
    });
  });

});
</script>
