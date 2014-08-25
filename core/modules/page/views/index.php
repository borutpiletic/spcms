view: index.php

<?php $form = new spcms\core\Form('search', 'POST') ?>

<?php print $form->start() ?>

<?php print $form->elementText('username', array('class' => 'form')) ?>

<?php print $form->elementSubmit('submit', array('class' => 'btn btn-primary')) ?>
