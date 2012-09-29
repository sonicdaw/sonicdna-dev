<?php slot('op_sidemenu'); ?>

<?php
$list = array(__('%event% Name', array('%event%' => $op_term['event']->titleize())) => $event->getName());


// event status
switch ($event->getEventStatusId()) {
    case 1:
        $event_status = 'Planning';
        break;
    case 2:
        $event_status = 'Open';
        break;
    case 3:
        $event_status = 'Closed';
        break;
}
$list += array(__('Status')      => $event_status );



// Official Url
if($event->getOfficialUrl())
{
  $official_url = link_to($event->getOfficialUrl(),       $event->getOfficialUrl(), 'target="_blank"');
  $list += array(__('Official Page')      => $official_url );
}

if ($event->event_category_id)
{
  $list[__('%event% Category', array('%event%' => $op_term['event']->titleize()))] = $event->getEventCategory();
}

// sonicdna.net
$list += array(__('Event Date')           => op_format_date($event->getEventDate(), 'D'),
               __('Rehearsal<br>Start Time') => op_format_date($event->getEventRehearsalStartTime(),'t'),
               __("Gate's<br>Opening Time")            => op_format_date($event->getEventOpenTime(),'t'),
               __('Event<br>Start Time')           => op_format_date($event->getEventStartTime(),'t'),
);
foreach ($event->getConfigs() as $key => $config)
{
  if ('%event% Description' === $key)
  {
    $list[__('%event%<br>Description', array('%event%' => $op_term['event']->titleize()), 'form_event')] = op_url_cmd(nl2br($event->getConfig('description')));
  }
}

// Access Info
if($event->getAccess())
  $access_description = $event->getAccess();
else
  $access_description = 'info';
if($event->getAccessUrl() && ('http://' !== $event->getAccessUrl()) ){
    $access_info = link_to(nl2br($access_description),       $event->getAccessUrl(), 'target="_blank"');
}else{
    $access_info = $access_description;
}
if($event->getAccessMapUrl() && ('http://' !== $event->getAccessMapUrl())){
    $access_map = ' ( ' . link_to('map', $event->getAccessMapUrl(), 'target="_blank"') . ' )';
}else{
   $access_map = '';
}
$list += array(__('Access')      => $access_info . $access_map );

// Ticket Info
if($event->getTicket())
  $ticket_description = $event->getTicket();
else
  $ticket_description = 'info';
if($event->getTicketUrl() && ('http://' !== $event->getTicketUrl()) )
  $list += array(__('Ticket')      => link_to(nl2br($ticket_description), $event->getTicketUrl(), 'target="_blank"'),);
else
  $list += array(__('Ticket')      => nl2br($ticket_description));


$options = array(
  'title' => __('%event%', array('%event%' => $op_term['event']->titleize())),
  'list' => $list,
);
op_include_parts('listBox', 'eventHome', $options);
//--------------------------------------------

// sonic dna
?>
<ul>
<?php if ($isEditEvent): ?>
<li><?php echo link_to(__('Edit this %event%'), '@event_edit?id=' . $event->getId()) ?></li>
<?php endif; ?>

<?php if (!$isAdmin): ?>
<?php if ($isEventMember): ?>
<li><?php echo link_to(__('Leave this %event%'), '@event_quit?id=' . $event->getId()) ?></li>
<?php else : ?>
<li><?php echo link_to(__('Join this %event%'), '@event_join?id=' . $event->getId()) ?></li>
<?php endif; ?>
<?php endif; ?>
</ul>
<br><br>
<?php



// sonicdna.net
//$list += array(__('Date Created')       => op_format_date($event->getCreatedAt(), 'D'),
$list = array(__('Date Created')       => op_format_date($event->getCreatedAt(), 'D'),
               __('Administrator')      => link_to($eventAdmin->getName(), '@member_profile?id='.$eventAdmin->getId()),
);
$subAdminCaption = '';
foreach ($eventSubAdmins as $m)
{
  $subAdminCaption .= "<li>".link_to($m->getName(), '@member_profile?id='.$m->getId())."</li>\n";
}
if ($subAdminCaption)
{
  $list[__('Sub Administrator')] = '<ul>'.$subAdminCaption.'</ul>';
}
$list[__('Count of Members')] = $event->countEventMembers();
foreach ($event->getConfigs() as $key => $config)
{
  if ('%event% Description' === $key)
  {
// sonicdna.net
//    $list[__('%event% Description', array('%event%' => $op_term['event']->titleize()), 'form_event')] = op_url_cmd(nl2br($event->getConfig('description')));
  }
  else
  {
    $list[__($key, array(), 'form_event')] = $config;
  }
}
$list[__('Register policy', array(), 'form_event')] = __($event->getRawValue()->getRegisterPolicy());

$options = array(
  'title' => __('%event% details', array('%event%' => $op_term['event']->titleize())),
  'list' => $list,
);
op_include_parts('listBox', 'eventHome', $options);
?>


<?php
$options = array(
  'title' => __('%event% Members', array('%event%' => $op_term['event']->titleize())),
  'list' => $members,
  'crownIds' => array($eventAdmin->getId()),
  'link_to' => '@member_profile?id=',
  'use_op_link_to_member' => true,
  'moreInfo' => array(link_to(sprintf('%s(%d)', __('Show all'), $event->countEventMembers()), '@event_memberList?id='.$event->getId())),
);
if ($isAdmin || $isSubAdmin)
{
  $options['moreInfo'][] = link_to(__('Management member'), '@event_memberManage?id='.$event->getId());
}
op_include_parts('nineTable', 'frendList', $options);
?>
<?php end_slot(); ?>

<?php slot('op_top') ?>
<?php if ($isEventPreMember) : ?>
<?php op_include_parts('descriptionBox', 'informationAboutEvent',  array('body' => __('You are waiting for the participation approval by %event%\'s administrator.'))) ?>
<?php endif; ?>

<div id="eventImageBox" class="parts memberImageBox">
<?php
$options = array(
  'object' => $event,
);
//op_include_parts('memberImageBox', 'eventImageBox', $options);
op_include_parts('eventImageBox', 'eventImageBox', $options);
?>
</div>
<?php end_slot(); ?>


<?php if ('event_home_artists_frame' !== sfContext::getInstance()->getRouting()->getCurrentRouteName()): ?>
<div class="partsHeading"><h3>Artists</h3></div>
<?php endif; ?>

<table>
<td width=100></td>
<td width=5></td>
<td width=150></td>
<td></td>
<?php
// target blank for artists/frame
if ('event_home_artists_frame' !== sfContext::getInstance()->getRouting()->getCurrentRouteName())
{
  $target = '';
}else{
  $target = 'target="_blank"';
}

for($i = count($eventlineup); $i >= 0; $i--)
{
  $lineup = $eventlineup[$i];

  echo "<tr><td>";
  switch ($lineup->getSlotType()){
        case 'band':
          $band = Doctrine::getTable('Band')->find($lineup->getBandId());
          if($band->getImageFileName()){
            echo link_to(op_image_tag_sf_image($band->getImageFileName(), array('size' => '120x120')), '@band_home?id='. $lineup->getBandId(), $target) . "</td><td> ";
          }else{
            echo op_image_tag('no_image.gif', array('size' => '120x120', 'alt' => '')) . "</td><td>";
          }
          echo "</td><td>";
          echo link_to($band->getName(), '@band_home?id='. $lineup->getBandId(), $target) . "</td><td>";

          $bandMembers = Doctrine::getTable('BandMember')->findByBandId($lineup->getBandId());
          echo "</td><td>";
          foreach($bandMembers as $bandMember){
            $lineupmem = Doctrine::getTable('Member')->find($bandMember->getMemberId());
            echo link_to($lineupmem->getName(), "@obj_member_profile?id=".$lineupmem->getId(), $target);
            if ($lineupmem->getProfile('part', true)){
              echo " ( " . $lineupmem->getProfile('part', true) ." )";
            }
            echo "<br>";
          }
          // Other Member List
          if($band->getMemberList())
          {
            echo nl2br($band->getMemberList());
          }

          break;

        case 'member':
          $lineupmem = Doctrine::getTable('Member')->find($lineup->getMemberId());
          if($lineupmem->getImageFileName()){
            echo link_to(op_image_tag_sf_image($lineupmem->getImageFileName(), array('size' => '120x120')), "@obj_member_profile?id=".$lineup->getMemberId(), $target) . "</td><td> ";
          }else{
            echo op_image_tag('no_image.gif', array('size' => '120x120', 'alt' => '')) . "</td><td>";
          }
          echo "</td><td>";
          echo link_to($lineupmem->getName(), "@obj_member_profile?id=".$lineup->getMemberId(), $target);
          if ($lineupmem->getProfile('part', true)){
            echo " ( " . $lineupmem->getProfile('part', true) ." )";
          }
          echo "</td><td> ";
          break;

        case 'slot':
          break;
  }
  echo "</td></tr>";
}
?></table>


