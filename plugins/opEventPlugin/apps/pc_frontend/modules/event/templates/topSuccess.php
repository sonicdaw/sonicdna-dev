<?php if ($pager->getNbResults()): ?>

<?php
$list = array();
foreach ($pager->getResults() as $key => $event)
{
  $list[$key] = array();
  $list[$key][__('%event% Name', array('%event%' => $op_term['event']->titleize()))] = $event->getName();
//  $list[$key][__('Count of Members')] = $event->countEventMembers();
  $list[$key][__('Description')] = $event->getConfig('description');
}

$options = array(
  'title'          => __('SONIC DNA'),
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
