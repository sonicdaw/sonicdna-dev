<?php if ($isAdmin): ?>
<?php $body =  __('The administrator doesn\'t leave the %event%.') ?>
<?php else: ?>
<?php $body =  __('You haven\'t joined this %event% yet.') ?>
<?php endif; ?>
<?php op_include_box('error', $body, array('title' => __('Errors'))) ?>

<?php use_helper('Javascript') ?>
<?php op_include_line('backLink', link_to_function(__('Back to previous page'), 'history.back()')) ?>
