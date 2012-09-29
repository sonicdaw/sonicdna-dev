<div class="row">
  <div class="gadget_header span12"><?php echo __('Errors') ?></div>
</div>

<div class="row">

<?php if ($isBandMember): ?>
<?php echo __('You are already joined to this %band%.') ?>
<?php else: ?>
<?php echo __('You have already sent the participation request to this %band%.') ?>
<?php endif; ?>

<?php use_helper('Javascript') ?>
<?php op_include_line('backLink', link_to_function(__('Back to previous page'), 'history.back()')) ?>

</div>

