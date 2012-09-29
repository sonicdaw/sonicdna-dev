<div class="partsHeading2"><h3>Create music events and connect with people</h3></div>
<table bgcolor=white width=600><tr>
<td width=20></td><td><b><font size=3>Be an event Organizer</font></b></td>
<td>Create an event and connect with people</td>
</tr><tr>
<td></td><td><b><font size=3>Be players</font></b></td>
<td>Create a band and share your music</td>
</tr><tr>
<td></td><td><b><font size=3>Be Audiences</font></b></td>
<td>Find and join an event</td>
</tr><tr>
<td></td><td><b><font size=3>Be Staff</font></b></td>
<td>Collaborate in an event</td>
</tr></table>
<br><br><br>


<?php if ($pager->getNbResults()): ?>

<?php
$list = array();
foreach ($pager->getResults() as $key => $event)
{
  $list[$key] = array();
  $list[$key][__('%event% Name', array('%event%' => $op_term['event']->titleize()))] = $event->getName();
  $list[$key][__('Count of Members')] = $event->countEventMembers();
  $list[$key][__('Description')] = $event->getConfig('description');
}

$options = array(
  'title'          => __('Upcoming Events'),
  'pager'          => $pager,
  'link_to_page'   => '@event_search?page=%d',
  'link_to_detail' => '@event_home?id=%d',
  'list'           => $list,
);

op_include_parts('searchResultListEvent', 'searchEventResult', $options);
?>
<?php else: ?>
<?php op_include_box('searchEventResult', __('Your search queries did not match any %event%.', array('%event%' => $op_term['event']->pluralize())), array('title' => __('Search Results'))) ?>
<?php endif; ?>
