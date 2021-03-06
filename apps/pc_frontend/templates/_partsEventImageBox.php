<?php $options->setDefault('single', true) ?>
<?php $options->setDefault('name_method', 'getNameAndCount') ?>

<div class="sortHandle">
<p class="photo">
<?php $imgParam = array('size' => 'x', 'alt' => $options->object->getName()) ?>
<?php $nameMethod = $options->name_method ?>
<?php if ($options->object): ?>
<?php echo op_image_tag_sf_image($options->object->getImageFileName(), $imgParam) ?>
<?php else: ?>
<?php echo op_image_tag('no_image.gif', $imgParam) ?>
<?php endif; ?>
</p>
<p class="text"><?php echo $options->object->$nameMethod() ?></p>
</div>
