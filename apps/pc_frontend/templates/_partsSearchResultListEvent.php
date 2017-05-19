<?php slot('pager') ?>
<?php op_include_pager_navigation($options['pager'], $options['link_to_page'], array('use_current_query_string' => true)); ?>
<?php end_slot(); ?>
<?php include_slot('pager') ?>

<div class="block">
<?php foreach ($options['pager']->getResults() as $key => $result): ?>
<?php $list = $options->list->getRaw($key); ?>
<div class="ditem"><div class="item">
<?php echo link_to(op_image_tag_sf_image($result->getImageFilename()), sprintf($options['link_to_detail'], $result->getId())); ?><br />
<?php foreach ($list as $caption => $item) : ?>
<?php endforeach; ?>
</div></div>
<?php endforeach; ?>
</div>

<?php include_slot('pager') ?>
