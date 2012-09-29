<?php
$options = array(
  'isMultipart' => true,
);

if ($eventForm->isNew())
{
  $options['title'] = __('Create a new %event%');
  $options['url'] = url_for('@event_edit');
}
else
{
  $options['title'] = __('Edit the %event%');
  $options['url'] = url_for('@event_edit?id='.$event->getId());
}

op_include_form('formEvent', array($eventForm, $eventConfigForm, $eventFileForm), $options);

if (!$eventForm->isNew() && $isDeleteEvent)
{
  op_include_parts('buttonBox', 'deleteForm', array(
    'title' => __('Delete this %event%'),
    'body' => __('delete this %event%.if you delete this %event% please to report in advance for all this %event% members.'),
    'button' => __('Delete'),
    'method' => 'get',
    'url' => url_for('@event_delete?id=' . $event->getId()),
  ));
}

?>
