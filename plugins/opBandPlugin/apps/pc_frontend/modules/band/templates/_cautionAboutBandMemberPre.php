<?php if ($bandMembersCount): ?>
<p class="caution">
<?php echo __('You\'ve gotten %1% one\'s %band% joining requests', array('%1%' => $bandMembersCount)); ?>
&nbsp;
<?php echo link_to(__('Go to Confirmation Page'), '@confirmation_list?category=band_confirm') ?>
</p>
<?php endif; ?>
