<?php slot('body') ?>
delete this?
<p><?php echo $tunes->getTuneName() ?></p>
<p><?php echo $tunes->getArtistName() ?></p>
<?php end_slot() ?>

<?php op_include_yesno('tune_delete_confirm',
 new BaseForm(),
 new BaseForm(array(), array(), false),
 array(
   'body' => get_slot('body'),
   'yes_url' => url_for('@tune_delete?id='.$sf_params->get('id')),
   'no_url'  => url_for('@tune_show?id='.$sf_params->get('id')),
   'no_method' => 'get',
 )
) ?>
