<?php if ($bandCount): ?>
<p class="caution">
<?php echo __('You\'ve gotten %1% %band% sub-administrator requests', array('%1%' => $bandCount)) ?>
&nbsp;
<?php echo link_to(__('Go to Confirmation Page'), '@confirmation_list?category=band_sub_admin_request') ?>
</p>
<?php endif; ?>

