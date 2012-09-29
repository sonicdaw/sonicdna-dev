<?php if ($eventMembersCount): ?>
<p class="caution">
<?php echo __('You\'ve gotten %1% one\'s %event% joining requests', array('%1%' => $eventMembersCount)); ?>
&nbsp;
<?php echo link_to(__('Go to Confirmation Page'), '@confirmation_list?category=event_confirm') ?>
</p>
<?php endif; ?>
