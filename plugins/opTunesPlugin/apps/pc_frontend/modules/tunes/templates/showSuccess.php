<p>
<BR>
<div class="partsHeading"><h3>
<?php
echo nl2br($tunes->getTuneName())
?>
</h3></div>

Artist:  
<?php
echo nl2br($tunes->getArtistName())
?>
</p>
<?php
//echo op_link_to_member($tunes->getMember())
?>
<br>
<table><tr><td>
<?php
$tune = $tunes;
echo "<td>Player: <BR>";
if($tune->getBandId() != 0){
          // Player is Band
          $band = Doctrine::getTable('Band')->find($tune->getBandId());
          if($band->getImageFileName()){
            echo link_to(op_image_tag_sf_image($band->getImageFileName(), array('size' => '76x76')), '@band_home?id='. $tune->getBandId()) . "<br> ";
          }
          echo link_to($band->getName(), '@band_home?id='. $tune->getBandId()) . "<br>";

/*          $bandMembers = Doctrine::getTable('BandMember')->findByBandId($tune->getBandId());
          foreach($bandMembers as $bandMember){
            $lineupmem = Doctrine::getTable('Member')->find($bandMember->getMemberId());
            echo " " . $lineupmem->getName() . " ";
            echo " ( " . $lineupmem->getProfile('part', true) ." )<br>";
          }
*/   }else{
          // Player is solo Member
          $lineupmem = Doctrine::getTable('Member')->find($tune->getMemberId());
          if($lineupmem->getImageFileName()){
            echo link_to(op_image_tag_sf_image($lineupmem->getImageFileName(), array('size' => '76x76')), "@obj_member_profile?id=".$tune->getMemberId()) . "
</br> ";
          }
          echo link_to($lineupmem->getName(), "@obj_member_profile?id=".$tune->getMemberId()) . "<br> ";
  }


  echo "</td><td>";

  if($tune->getEventId() != 0){
          echo "Event:<br>";
          $event = Doctrine::getTable('Event')->find($tune->getEventId());
          if($event->getImageFileName()){
            echo link_to(op_image_tag_sf_image($event->getImageFileName(), array('size' => '204x75')), '@event_home?id='. $tune->getEventId()) . "<br> ";
          }
          echo link_to($event->getName(), '@event_home?id='. $tune->getEventId());
  }
?>
</td></tr></table>


<?php if ($tunes->getLyric()): ?>
<br><br><div class="partsHeading"><h3>LYRIC</h3></div>
<?php
echo nl2br($tunes->getLyric())
?>
<?php endif; ?>



<?php if ($tunes->getUrl()): ?>
<br><br><br><div class="partsHeading"><h3>LINK</h3></div>
<?php
echo link_to($tunes->getUrl(), $tunes->getUrl(), 'target="_blank"');
?>
<?php endif; ?>



<br><br><br><br>
<?php if ($tunes->getMemberId() === $sf_user->getMemberId()): ?>
<p>
<?php echo link_to('[ Edit ]', '@tune_edit?id='.$tunes->getId()) ?>
 / 
<?php echo link_to('[ Delete ]', '@tune_delete?id='.$tunes->getId()) ?>
</p>
<?php endif; ?>
<br><br>
