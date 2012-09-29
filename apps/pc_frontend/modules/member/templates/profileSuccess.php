<?php use_helper('Javascript') ?>

<?php if (isset($topGadgets)): ?>
<?php slot('op_top') ?>
<?php foreach ($topGadgets as $gadget): ?>
<?php if ($gadget->isEnabled()): ?>
<?php include_component($gadget->getComponentModule(), $gadget->getComponentAction(), array('gadget' => $gadget)); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php end_slot() ?>
<?php endif; ?>

<?php if (isset($sideMenuGadgets)): ?>
<?php slot('op_sidemenu') ?>
<?php foreach ($sideMenuGadgets as $gadget): ?>
<?php if ($gadget->isEnabled()): ?>
<?php include_component($gadget->getComponentModule(), $gadget->getComponentAction(), array('gadget' => $gadget)); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php end_slot() ?>
<?php endif; ?>

<?php if (isset($contentsGadgets)): ?>
<?php foreach ($contentsGadgets as $gadget): ?>
<?php if ($gadget->isEnabled()): ?>
<?php include_component($gadget->getComponentModule(), $gadget->getComponentAction(), array('gadget' => $gadget)); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

<div class="parts">
<div class="partsHeading"><h3>Tunes</h3></div>
<table>
<td bgcolor=white>Song</td>
<td bgcolor=white>Aritst</td>
<td bgcolor=white width=40>Link</td>
<td bgcolor=white width=60>Duration</td>
<td bgcolor=white width=136>Event</td>
<?php
// Copied from opEventPlugin showSuccess.php
foreach($tunes as $tune)
{
  echo "<tr>";

  echo "<td>" . link_to(nl2br($tune->getTuneName()), "@tune_show?id=".$tune->getId()) . "</td>";
  echo "<td>" . nl2br($tune->getArtistName())       . "</td>";
  echo "<td>";
  if($tune->getUrl())
  {
    echo link_to(nl2br("link"), $tune->getUrl(), 'target="_blank"');
  }
  echo "</td><td>";
  echo $tune->getDuration();
  echo " min</td>";

  echo "</td><td>";

  if($tune->getEventId() != 0){
          $event = Doctrine::getTable('Event')->find($tune->getEventId());
          if($event->getImageFileName()){
            echo link_to(op_image_tag_sf_image($event->getImageFileName(), array('size' => '136x50')), '@event_home?id='. $tune->getEventId())."<br> ";
          }
//          echo link_to($event->getName(), '@event_home?id='. $tune->getEventId());
  }
  echo "</td></tr>";
}
?>
</table></div>
<?php //echo link_to('create tune', url_for('@tune_new')); ?>
<BR><BR>


<?php if (isset($bottomGadgets)): ?>
<?php slot('op_bottom') ?>
<?php foreach ($bottomGadgets as $gadget): ?>
<?php if ($gadget->isEnabled()): ?>
<?php include_component($gadget->getComponentModule(), $gadget->getComponentAction(), array('gadget' => $gadget)); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php end_slot() ?>
<?php endif; ?>

<?php slot('op_top'); ?>
<?php if ($relation->isSelf()): ?>
<?php ob_start() ?>
<p><?php echo __('Other members look your page like this.') ?></p>
<p><?php echo __('If you teach your page to other members, please use following URL.') ?><br />
<?php echo url_for('@member_profile?id='.$member->getId(), true) ?></p>
<p><?php echo __('If you edit this page, please visit %1%.', array('%1%' => link_to(__('Edit profile'), '@member_editProfile'))) ?></p>
<?php $content = ob_get_clean() ?>
<?php op_include_parts('descriptionBox', 'informationAboutThisIsYourProfilePage', array('body' => $content)) ?>
<?php else: ?>

<?php // sonic dna deleted ?>

<?php endif; ?>
<?php if (isset($topGadgets)): ?>
<?php foreach ($topGadgets as $gadget): ?>
<?php if ($gadget->isEnabled()): ?>
<?php include_component($gadget->getComponentModule(), $gadget->getComponentAction(), array('gadget' => $gadget)); ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php end_slot(); ?>
