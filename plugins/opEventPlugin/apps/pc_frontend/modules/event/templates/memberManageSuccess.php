<?php slot('pager') ?>
<?php op_include_pager_navigation($pager, '@event_memberManage?page=%d&id='.$sf_params->get('id')); ?>
<?php end_slot(); ?>

<div class="parts">
<div class="partsHeading"><h3><?php echo __('Management member') ?></h3></div>
<?php include_slot('pager') ?>
<div class="item">
<table>
<tbody>
<?php foreach ($pager->getResults() as $member) : ?>
<?php 
$customizeOption = array('member' => $member, 'event' => $event);
$eventMember = Doctrine::getTable('EventMember')->retrieveByMemberIdAndEventId($member->getId(), $event->getId());
?>

<tr>
<?php include_customizes('id_member', 'before', $customizeOption) ?>
<td class="member"><?php echo op_link_to_member($member); ?></td>

<td class="drop">
<?php if (!($eventMember->hasPosition(array('admin', 'sub_admin')) || $eventMember->getMemberId() === $sf_user->getMemberId())) : ?>
<?php echo link_to(__('Drop this member'), 'event/dropMember?id='.$event->getId().'&member_id='.$member->getId()) ?>
<?php else: ?>
&nbsp;
<?php endif; ?>
</td>

<?php if ($isAdmin): ?>
<td class="sub_admin_request">
<?php if (!$eventMember->hasPosition(array('admin', 'admin_confirm', 'sub_admin'))) : ?>
<?php if ($eventMember->hasPosition('sub_admin_confirm')): ?>
<?php echo __("You are requesting this %event%'s sub-administrator to this member now.") ?>
<?php else: ?>
<?php echo link_to(__("Request this %event%'s sub-administrator to this member"), 'event/subAdminRequest?id='.$event->getId().'&member_id='.$member->getId()) ?>
<?php endif; ?>
<?php elseif ($eventMember->hasPosition('sub_admin')): ?>
<?php echo link_to(__("Demote this member from this %event%'s sub-administrator"), 'event/removeSubAdmin?id='.$event->getId().'&member_id='.$member->getId()) ?>
<?php else: ?>
&nbsp;
<?php endif; ?>
</td>

<td class="admin_request">
<?php if (!$eventMember->hasPosition('admin')) : ?>
<?php if ($eventMember->hasPosition('admin_confirm')): ?>
<?php echo __("You are taking over this %event%'s administrator to this member now.") ?>
<?php else: ?>
<?php echo link_to(__("Take over this %event%'s administrator to this member"), 'event/changeAdminRequest?id='.$event->getId().'&member_id='.$member->getId()) ?>
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
