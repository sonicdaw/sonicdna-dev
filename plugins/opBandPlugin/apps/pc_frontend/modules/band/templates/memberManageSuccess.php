<?php slot('pager') ?>
<?php op_include_pager_navigation($pager, '@band_memberManage?page=%d&id='.$sf_params->get('id')); ?>
<?php end_slot(); ?>

<div class="parts">
<div class="partsHeading"><h3><?php echo __('Management member') ?></h3></div>
<?php include_slot('pager') ?>
<div class="item">
<table>
<tbody>
<?php foreach ($pager->getResults() as $member) : ?>
<?php 
$customizeOption = array('member' => $member, 'band' => $band);
$bandMember = Doctrine::getTable('BandMember')->retrieveByMemberIdAndBandId($member->getId(), $band->getId());
?>

<tr>
<?php include_customizes('id_member', 'before', $customizeOption) ?>
<td class="member"><?php echo op_link_to_member($member); ?></td>

<td class="drop">
<?php if (!($bandMember->hasPosition(array('admin', 'sub_admin')) || $bandMember->getMemberId() === $sf_user->getMemberId())) : ?>
<?php echo link_to(__('Drop this member'), 'band/dropMember?id='.$band->getId().'&member_id='.$member->getId()) ?>
<?php else: ?>
&nbsp;
<?php endif; ?>
</td>

<?php if ($isAdmin): ?>
<td class="sub_admin_request">
<?php if (!$bandMember->hasPosition(array('admin', 'admin_confirm', 'sub_admin'))) : ?>
<?php if ($bandMember->hasPosition('sub_admin_confirm')): ?>
<?php echo __("You are requesting this %band%'s sub-administrator to this member now.") ?>
<?php else: ?>
<?php echo link_to(__("Request this %band%'s sub-administrator to this member"), 'band/subAdminRequest?id='.$band->getId().'&member_id='.$member->getId()) ?>
<?php endif; ?>
<?php elseif ($bandMember->hasPosition('sub_admin')): ?>
<?php echo link_to(__("Demote this member from this %band%'s sub-administrator"), 'band/removeSubAdmin?id='.$band->getId().'&member_id='.$member->getId()) ?>
<?php else: ?>
&nbsp;
<?php endif; ?>
</td>

<td class="admin_request">
<?php if (!$bandMember->hasPosition('admin')) : ?>
<?php if ($bandMember->hasPosition('admin_confirm')): ?>
<?php echo __("You are taking over this %band%'s administrator to this member now.") ?>
<?php else: ?>
<?php echo link_to(__("Take over this %band%'s administrator to this member"), 'band/changeAdminRequest?id='.$band->getId().'&member_id='.$member->getId()) ?>
<?php endif; ?>
<?php else: ?>
&nbsp;
<?php endif; ?>
</td>
<?php endif; ?>

<?php include_customizes('id_member', 'after', $customizeOption) ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php include_slot('pager') ?>
</div>
