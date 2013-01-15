<?php slot('op_sidemenu'); ?>
<?php
$options = array(
  'object' => $band,
);
op_include_parts('memberImageBox', 'bandImageBox', $options);
?>

<?php
$options = array(
  'title' => __('%band% Members', array('%band%' => $op_term['band']->titleize())),
  'list' => $members,
  'crownIds' => array($bandAdmin->getId()),
  'link_to' => '@member_profile?id=',
  'use_op_link_to_member' => true,
  'moreInfo' => array(link_to(sprintf('%s(%d)', __('Show all'), $band->countBandMembers()), '@band_memberList?id='.$band->getId())),
);
if ($isAdmin || $isSubAdmin)
{
  $options['moreInfo'][] = link_to(__('Management member'), '@band_memberManage?id='.$band->getId());
}
op_include_parts('nineTable', 'frendList', $options);
?>
<?php end_slot(); ?>

<?php slot('op_top') ?>
<?php if ($isBandPreMember) : ?>
<?php op_include_parts('descriptionBox', 'informationAboutBand',  array('body' => __('You are waiting for the participation approval by %band%\'s administrator.'))) ?>
<?php endif; ?>
<?php end_slot(); ?>

<div class="parts">
<div class="partsHeading"><h3>Tunes</h3></div>
<table>
<td bgcolor=white>Song</td>
<td bgcolor=white>Aritst</td>
<td bgcolor=white width=40>Link</td>
<td bgcolor=white width=60>Duration</td>
<td bgcolor=white width=136>Event</td>
<?php
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
            echo link_to(op_image_tag_sf_image($event->getImageFileName(), array('size' => '136x50')), '@event_home?id='. $tune->getEventId()) . "<br> ";
          }
//          echo link_to($event->getName(), '@event_home?id='. $tune->getEventId());
  }
  echo "</td></tr>";
}
?>
</table></div>
<?php //echo link_to('create tune', url_for('@tune_newForBand?band_id='.$band->getId())); ?>
<BR><BR>
</p>

<?php
$list = array(__('%band% Name', array('%band%' => $op_term['band']->titleize())) => $band->getName());

// Official Url
if($band->getOfficialUrl())
{
  $official_url = link_to($band->getOfficialUrl(),       $band->getOfficialUrl(), 'target="_blank"');
  $list += array(__('Official Page')      => $official_url );
}

// Member List
if($band->getMemberList())
{
  $member_list = nl2br(preg_replace('/&lt;linkoff&gt;(\s)*/i', '', $band->getMemberList()));
  $list += array(__('Other Members')      => $member_list );
}

if ($band->band_category_id)
{
  $list[__('%band% Category', array('%band%' => $op_term['band']->titleize()))] = $band->getBandCategory();
}
$list += array(__('Date Created')       => op_format_date($band->getCreatedAt(), 'D'),
               __('Administrator')      => link_to($bandAdmin->getName(), '@member_profile?id='.$bandAdmin->getId()),
);
$subAdminCaption = '';
foreach ($bandSubAdmins as $m)
{
  $subAdminCaption .= "<li>".link_to($m->getName(), '@member_profile?id='.$m->getId())."</li>\n";
}
if ($subAdminCaption)
{
  $list[__('Sub Administrator')] = '<ul>'.$subAdminCaption.'</ul>';
}
$list[__('Count of Members')] = $band->countBandMembers();
foreach ($band->getConfigs() as $key => $config)
{
  if ('%band% Description' === $key)
  {
    $list[__('%band% Description', array('%band%' => $op_term['band']->titleize()), 'form_band')] = op_url_cmd(nl2br($band->getConfig('description')));
  }
  else
  {
    $list[__($key, array(), 'form_band')] = $config;
  }
}
$list[__('Register policy', array(), 'form_band')] = __($band->getRawValue()->getRegisterPolicy());

$options = array(
  'title' => __('%band%', array('%band%' => $op_term['band']->titleize())),
  'list' => $list,
);
op_include_parts('listBox', 'bandHome', $options);
?>

<ul>
<?php if ($isEditBand): ?>
<li><?php echo link_to(__('Edit this %band%'), '@band_edit?id=' . $band->getId()) ?></li>
<?php endif; ?>

<?php if (!$isAdmin): ?>
<?php if ($isBandMember): ?>
<li><?php echo link_to(__('Leave this %band%'), '@band_quit?id=' . $band->getId()) ?></li>
<?php else : ?>
<li><?php echo link_to(__('Join this %band%'), '@band_join?id=' . $band->getId()) ?></li>
<?php endif; ?>
<?php endif; ?>
</ul>
