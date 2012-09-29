<?php if ($bandForm->isNew()): ?>
<?php echo form_tag(url_for('@band_edit'), 'multipart=true') ?>
<?php else: ?>
<?php echo form_tag(url_for('@band_edit?id='.$band->getId()), 'multipart=true') ?>
<?php endif; ?>
<div class="row">
<?php if ($bandForm->isNew()): ?>
  <div class="gadget_header span12"> <?php echo __('Create a new %band%'); ?> </div>
<?php else: ?>
  <div class="gadget_header span12"> <?php echo __('Edit the %band%'); ?> </div>
<?php endif; ?>
</div>

<?php $errors = array(); ?>
<?php if ($bandForm->hasGlobalErrors()): ?>
<?php $errors[] = $bandForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($bandConfigForm->hasGlobalErrors()): ?>
<?php $errors[] = $bandConfigForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($bandFileForm->hasGlobalErrors()): ?>
<?php $errors[] = $bandFileForm->renderGlobalErrors(); ?>
<?php endif; ?>
<?php if ($errors): ?>
<div class="row">
<div class="alert alert-error">
<a class="close" href="#">x</a>
<?php foreach ($errors as $error): ?>
<p><?php echo __($error) ?></p>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>

<div class="row">
<?php foreach ($bandForm as $cf): ?>
<?php if (!$cf->isHidden()): ?>
<div class="control-group<?php echo $cf->hasError()? ' error' : '' ?>">
  <label class="control-label"><?php echo $cf->renderLabel() ?></label>
  <div class="controls">
    <?php if ($cf->hasError()): ?>
    <span class="label label-important label-block"><?php echo __($cf->renderError()); ?></span>
    <?php endif ?>
    <?php echo $cf->render(array('class' => 'span12')) ?>
    <span class="help-block"><?php echo $cf->renderHelp(); ?></span>    
  </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<?php foreach ($bandConfigForm as $ccf): ?>
<?php if (!$ccf->isHidden()): ?>
<div class="control-group<?php echo $ccf->hasError()? ' error' : '' ?>">
  <label class="control-label"><?php echo $ccf->renderLabel() ?></label>
  <div class="controls">
    <?php if ($ccf->hasError()): ?>
    <span class="label label-important label-block"><?php echo __($ccf->renderError()); ?></span>
    <?php endif ?>
    <?php echo $ccf->render(array('class' => 'span12')) ?>
    <span class="help-block"><?php echo $ccf->renderHelp(); ?></span>    
  </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<?php foreach ($bandFileForm as $cff): ?>
<?php if (!$cff->isHidden()): ?>
<div class="control-group<?php echo $cff->hasError()? ' error' : '' ?>">
  <label class="control-label"><?php echo $cff->renderLabel() ?></label>
  <div class="controls">
    <?php if ($cff->hasError()): ?>
    <span class="label label-important label-block"><?php echo __($cff->renderError()); ?></span>
    <?php endif ?>
    <?php echo $cff->render(array('class' => 'span12')) ?>
    <span class="help-block"><?php echo $cff->renderHelp(); ?></span>    
  </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div>

<div class="row">
<div class="span12 center">
<input type="submit" name="submit" value="<?php echo __('Send') ?>" class="btn btn-primary" />
<?php echo $bandForm->renderHiddenFields(); ?>
<?php echo $bandConfigForm->renderHiddenFields(); ?>
<?php echo $bandFileForm->renderHiddenFields(); ?>
</form>
</div>
</div>

</div>
