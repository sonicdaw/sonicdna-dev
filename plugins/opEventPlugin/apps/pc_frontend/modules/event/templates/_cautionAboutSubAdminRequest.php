<?php if ($eventCount): ?>
<p class="caution">
<?php echo __('You\'ve gotten %1% %event% sub-administrator requests', array('%1%' => $eventCount)) ?>
&nbsp;
<?php echo link_to(__('Go to Confirmation Page'), '@confirmation_list?category=event_sub_admin_request') ?>
</p>
<?php endif; ?>

