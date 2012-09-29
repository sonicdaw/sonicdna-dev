<?php
$options = array(
  'isMultipart' => true,
);

if ($bandForm->isNew())
{
  $options['title'] = __('Create a new %band%');
  $options['url'] = url_for('@band_edit');
}
else
{
  $options['title'] = __('Edit the %band%');
  $options['url'] = url_for('@band_edit?id='.$band->getId());
}

op_include_form('formBand', array($bandForm, $bandConfigForm, $bandFileForm), $options);

if (!$bandForm->isNew() && $isDeleteBand)
{
  op_include_parts('buttonBox', 'deleteForm', array(
    'title' => __('Delete this %band%'),
    'body' => __('delete this %band%.if you delete this %band% please to report in advance for all this %band% members.'),
    'button' => __('Delete'),
    'method' => 'get',
    'url' => url_for('@band_delete?id=' . $band->getId()),
  ));
}

?>
