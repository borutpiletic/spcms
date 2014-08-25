<?php /* @var $formSettings spcms\core\Form */ ?>

<h1><?php print t('General settings') ?></h1>

<?php print $formSettings->start() ?>
	
	<label><?php print t('Site title') ?></label>
		<?php print $formSettings->elementText('email', array('class' => 'form-control', 'placeholder' => t('Site title') )) ?>
	
	<label><?php print t('Locale') ?></label>
	
	<?php print $formSettings->elementSelect('locale', array('class' => 'form-control', 'placeholder' => t('Site title') )) ?>

<?php print $formSettings->end() ?>
