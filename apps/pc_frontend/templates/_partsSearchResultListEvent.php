<?php slot('pager') ?>
<?php op_include_pager_navigation($options['pager'], $options['link_to_page'], array('use_current_query_string' => true)); ?>
<?php end_slot(); ?>

<div class="block">
<?php foreach ($options['pager']->getResults() as $key => $result): ?>
<?php $list = $options->list->getRaw($key); ?>
<div class="ditem"><div class="item">
<?php echo link_to(op_image_tag_sf_image($result->getImageFilename()), sprintf($options['link_to_detail'], $result->getId())); ?><br />
<?php foreach ($list as $caption => $item) : ?>
<?php endforeach; ?>
</div></div>
<?php endforeach; ?>


<!-- Old events which is not in db -->

<div class="ditem"><div class="item">
<a href="https://github.com/sonicdna/sonicdna-dev/"><img alt="" src="http://sonicdna.net/2012/sonicdna2012.jpg" width="680" height="250" /></a><br />
</div></div>

<div class="ditem"><div class="item">
<a href="http://sonicdna.net/2011/"><img alt="" src="http://sonicdna.net/2011/sonicdna2011Banner.jpg" width="680" height="250" /></a><br />
</div></div>

<div class="ditem"><div class="item">
<a href="http://sonicdna.net/2010/"><img alt="" src="http://sonicdna.net/2010/image/logo_sonicdna10winter.jpg" width="680" /></a><br />
</div></div>

<div class="ditem"><div class="item">
<a href="http://sonicdna.net/2009/"><img alt="" src="http://sonicdna.net/2009/Image/BACK.JPG" width="680" height="250" /></a><br />
</div></div>

<div class="ditem"><div class="item">
<a href="http://sonicdna.net/2008/"><img alt="" src="http://sonicdna.net/2008/Image/BACK.JPG" width="680" height="250" /></a><br />
</div></div>

</div>
