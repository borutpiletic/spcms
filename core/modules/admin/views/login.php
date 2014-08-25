<?php
/* @var $this spcms\core\View */ 
/* @var $formLogin spcms\core\Form */ 
?>

<?php if ( $formLogin->isSubmitted() && !empty($formLogin->getErrors()) ): ?>
	<?php print $formLoginErrors ?>
<?php endif ?>


<?php print $formLogin->start(array('role' => 'form', 'class' => 'form-signin')) ?>

		<div class="text-center">
			<h4 class="form-signin-heading"><?php print t('Administration') ?></h4>
			<p><?php print SimplCMS::$app->baseUrl ?></p>
		</div>


	
		<?php print $formLogin->elementText('username', array('class' => 'form-control', 'placeholder' => t('Your username or email address') )) ?>

		<?php print $formLogin->elementTextarea('message', array('class' => 'form-control', 'placeholder' => t('Your message') )) ?>
	
		<?php print $formLogin->elementPassword('password', array('class' => 'form-control', 'placeholder' => t('Your password'), '#captureValue' => false)) ?>
	
		<label class="checkbox">
			<?php //print $formLogin->elementCheckbox('remember', array('value' => 1)) ?> <?php print t('Remember me!') ?>

		</label>
		
		<?php 
			$categories = array(
				12 => 'Socks',
				13 => 'Polo shirts',
				14 => 'Hats'
			);
			
			print $formLogin->elementCheckbox('remember', array('value' => 1));
			
		?>

		<?php print $formLogin->elementSubmit('login', array('class' => array('btn btn-primary btn-block'), 'value' =>  t('Login') )) ?>

<?php print $formLogin->end() ?>

