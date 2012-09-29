<?php
$options = array(
  'title'    => __('Search %band%', array('%band%' => $op_term['band']->titleize()->pluralize())),
  'url'      => url_for('@band_search'),
  'button'   => __('Search'),
  'moreInfo' => array(link_to(__('Create a new %band%'), '@band_edit')),
  'method'   => 'get'
);

op_include_form('searchBand', $filters, $options);
?>

<?php if ($pager->getNbResults()): ?>

<?php
$list = array();
foreach ($pager->getResults() as $key => $band)
{
  $list[$key] = array();
  $list[$key][__('%band% Name', array('%band%' => $op_term['band']->titleize()))] = $band->getName();
  $list[$key][__('Count of Members')] = $band->countBandMembers();
  $list[$key][__('Description')] = $band->getConfig('description');
}

$options = array(
  'title'          => __('Search Results'),
  'pager'          => $pager,
  'link_to_page'   => '@band_search?page=%d',
  'link_to_detail' => '@band_home?id=%d',
  'list'           => $list,
);

op_include_parts('searchResultList', 'searchBandResult', $options);
?>
<?php else: ?>
<?php op_include_box('searchBandResult', __('Your search queries did not match any %band%.', array('%band%' => $op_term['band']->pluralize())), array('title' => __('Search Results'))) ?>
<?php endif; ?>
