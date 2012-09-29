<p>
<div class="parts">
<div class="partsHeading"><h3>Tunes</h3></div>

<?php echo link_to('Create a new tune', '@tune_newForEvent?event_id=0&band_id=0&event_member_id='.$sf_user->getMemberId().'&player_sub_id=1'); ?>
<br><br>
<table>
<td bgcolor=white>Song</td>
<td bgcolor=white>Aritst</td>
<td bgcolor=white width=76>Link</td>
<td bgcolor=white width=76>Duration</td>
<td bgcolor=white width=76>Player</td>
<td bgcolor=white width=204>Event</td>

<?php
foreach($tunes as $tune)
{
  echo "<tr>";

  // tune info
  echo "<td>" . link_to(nl2br($tune->getTuneName()), "@tune_show?id=".$tune->getId()) . "</td>";
  echo "<td>" . nl2br($tune->getArtistName())       . "</td>";
  echo "<td>";
  if($tune->getUrl())
  {
    echo link_to(nl2br("link"), $tune->getUrl(), 'target="_blank"');
  }

  // duration to perform
  echo "</td><td>";
  echo $tune->getDuration();
  echo " min</td>";

  // Player info
  echo "<td>";

  if($tune->getBandId() != 0){
          // Player is Band
          $band = Doctrine::getTable('Band')->find($tune->getBandId());
          if($band->getImageFileName()){
            echo link_to(op_image_tag_sf_image($band->getImageFileName(), array('size' => '76x76')), '@band_home?id='. $tune->getBandId()) . "<br> ";
          }
   }else{
          // Player is solo Member
          $lineupmem = Doctrine::getTable('Member')->find($tune->getMemberId());
          if($lineupmem->getImageFileName()){
            echo link_to(op_image_tag_sf_image($lineupmem->getImageFileName(), array('size' => '76x76')), "@obj_member_profile?id=".$tune->getMemberId()) . "</br> ";
          }
  }


  echo "</td><td>";

  if($tune->getEventId() != 0){
          $event = Doctrine::getTable('Event')->find($tune->getEventId());
          if($event->getImageFileName()){
            echo link_to(op_image_tag_sf_image($event->getImageFileName(), array('size' => '204x75')), '@event_home?id='. $tune->getEventId()) . "<br> ";
          }
  }
  echo "</td></tr>";
}
?>
</table></div>
<BR>
<BR>
</p>
