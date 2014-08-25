<?php /* @var $settingsHelper spcms\core\modules\admin\helpers\SettingsHelper */ ?>
<?php /* @var $formRbac spcms\core\Form */ ?>

<h1><?php print t('Manage permissions') ?></h1>

<?php print $formRbac->start() ?>
	<table class="table table-striped" style="width:100%">
		<thead>
			<tr>
				<th><?php print t('Module / actions') ?></th>
				<th><?php print t('Roles / permissions') ?></th>
			</tr>
		</thead>
		<tr>
			<td style="width: 20%"></td>	
			<td style="width: 80%">
				<table style="width:100%;">
					<tr>
						<?php foreach ($roles as $role): ?>
							<td><em><?php print $role['name'] ?></em></td>
						<?php endforeach; ?>
					</tr>
				</table>
			</td>

		<?php foreach ($modules as $module): ?>
			<!-- MODULE NAME --->
			<tr>
				<td><b><?php print ($module['name']) ?></b></td>
				<td></td>
			</tr>

				<!-- MODULE ACTIONS --->
				<?php $actions = $settingsHelper->getRbacModuleActions($module['name']); ?>

				<?php foreach ($actions as $action): ?>
					<tr>
						<td style="padding-left: 30px !important;"><?php print $action['name'] ?></td>
						<td>
							<table style="width: 100%;">
								<tr>
									<?php foreach ($roles as $role): ?>
										<td>
											<?php if ($settingsHelper->getRbacPermission($module['name'], $action['name'], $role['id']) === true): ?>
												<?php print $formRbac->elementCheckbox("permission_{$module['name']}_{$action['name']}_{$role['id']}", array('checked' => 1)) ?>
											<?php else: ?>
												<?php print $formRbac->elementCheckbox("permission_{$module['name']}_{$action['name']}_{$role['id']}") ?>
											<?php endif ?>
										</td>
									<?php endforeach; ?>
								</tr>
							</table>
						</td>
					</tr>
				<?php endforeach; ?>			
				<!-- MODULE ACTIONS --->

			<?php endforeach; ?>
			<!-- MODULE NAME --->
	</table> 
	<div class="well">
		<?php print $formRbac->elementSubmit('save', array('value' => t('Update'), 'class' => 'btn btn-primary')); ?>
	</div>
<?php $formRbac->end() ?>