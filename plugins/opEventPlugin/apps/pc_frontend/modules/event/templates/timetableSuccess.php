<div class="parts">

<?php if ('event_timetable_frame' !== sfContext::getInstance()->getRouting()->getCurrentRouteName()): ?>
<div class="partsHeading"><h3>Timetable</h3></div>
<?php endif; ?>

<table>
<td bgcolor=white width=100>Time</td>
<td bgcolor=white>Player</td>
<td bgcolor=white width=40>min</td>
<?php
$event_time = $event->getEventStartTime();
$event_time_val = explode(":", $event_time); 
$event_time_unix = mktime($event_time_val[0],$event_time_val[1],$event_time_val[2],0,0,0);

if($event->getEventOpenTime()){
  echo "<tr><td>" . op_format_date($event->getEventOpenTime(),'t') . "</td><td>Open</td><td></td></tr>";
}

// target blank for artists/frame
if ('event_timetable_frame' !== sfContext::getInstance()->getRouting()->getCurrentRouteName())
{
  $target = '';
}else{
  $target = 'target="_blank"';
}

foreach($eventlineup as $lineup)
{
  echo "<tr>";

  echo "<td>" . date("H:i", $event_time_unix) . " - ";
  $event_time_unix = $event_time_unix + $lineup->getDuration() * 60;
  echo date("H:i", $event_time_unix);
  echo "</td>";

  echo "<td>";
  switch ($lineup->getSlotType()){

        case 'band':
          // Band Name, Icon
          $band = Doctrine::getTable('Band')->find($lineup->getBandId()); 
          if($band->getImageFileName()){
            echo link_to(op_image_tag_sf_image($band->getImageFileName(), array('size' => '76x76')), '@band_home?id='. $lineup->getBandId(), $target) . " ";
          }else{
            echo op_image_tag('no_image.gif', array('size' => '76x76', 'alt' => ''));
	  }
          echo link_to($band->getName(), '@band_home?id='. $lineup->getBandId(), $target). "<br><br>";

          // Tune List   
          $q = Doctrine::getTable('Tune')->createQuery('u')
                   ->where('u.event_id = ?', $event->getId())
                   ->andWhere('u.band_id = ?', $lineup->getBandId()) 
                   ->andWhere('u.player_sub_id = ?', $lineup->getSubId()); 
          $tunes = $q->execute(); 
          echo '<table>';
          foreach($tunes as $tune)
          {
            echo '<tr><td>';
            echo link_to(nl2br($tune->getTuneName()), "@tune_show?id=".$tune->getId(), $target) . "</td><td>";
            echo nl2br($tune->getArtistName()). "</td><td width=76>";
            echo $tune->getDuration(). " min</td>";
          }
          echo '</tr></table><BR>';

          // edit   
          $q = Doctrine::getTable('BandMember')->createQuery('u')
                   ->where('u.band_id = ?', $lineup->getBandId())
                   ->andWhere('u.member_id = ?', $sf_user->getMemberId()); 
          $band_members = $q->execute(); 
          if($band_members->count()>0){
            echo link_to('<b>[+]', '@tune_newForEvent?event_id='.$event->getId().'&band_id='.$lineup->getBandId().'&event_member_id='.$sf_user->getMemberId().'&player_sub_id='.$lineup->getSubId(), $target);
            echo "  <font color=red>tune</font></b>";
          }
          break;

        case 'member':
          // Member(Solo) Name, Icon 
          $lineupmem = Doctrine::getTable('Member')->find($lineup->getMemberId()); 
          if($lineupmem->getImageFileName()){
            echo link_to(op_image_tag_sf_image($lineupmem->getImageFileName(), array('size' => '76x76')), "@obj_member_profile?id=".$lineup->getMemberId(), $target) . " ";
          }else{
            echo op_image_tag('no_image.gif', array('size' => '76x76', 'alt' => ''));
          }
          echo link_to($lineupmem->getName(), "@obj_member_profile?id=".$lineup->getMemberId(), $target). "<br><br>";

          // Tune List
          $q = Doctrine::getTable('Tune')->createQuery('u')
                   ->where('u.event_id = ?', $event->getId())
                   ->andWhere('u.member_id = ?', $lineup->getMemberId()) 
                   ->andWhere('u.band_id = ?', 0) 
                   ->andWhere('u.player_sub_id = ?', $lineup->getSubId()); 
          $tunes = $q->execute(); 
          echo '<table border=0>';
          foreach($tunes as $tune)
          {
            echo '<tr><td>';
            echo link_to(nl2br($tune->getTuneName()), "@tune_show?id=".$tune->getId(), $target) . "</td><td>";
            echo nl2br($tune->getArtistName()). "</td><td width=76>";
            echo $tune->getDuration(). " min</td>";
          }
          echo '</tr></table><BR>';

          // edit
          if($lineup->getMemberId() == $sf_user->getMemberId()){ 
            echo link_to('<b>[+]', '@tune_newForEvent?event_id='.$event->getId().'&band_id=0&event_member_id='.$lineup->getMemberId().'&player_sub_id='.$lineup->getSubId(), $target);
            echo " <font color=red>tune</font></b>";
          }
          break;

        case 'slot':
          echo nl2br($lineup->getSlotName());
          break;
  }
  echo "</td>";
  echo "<td>";
  echo $lineup->getDuration();
  echo "</td>";

  echo "</tr>";
}

?>
</TABLE><BR><BR>
</div>
</p>

