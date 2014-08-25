<h1><?php print t('Edit site structure'); ?></h1>

<script type="text/javascript">
	
	$(document).ready(function()
	{
		<?php foreach ($menus as $menu): ?>
			
			var data = <?php print $menuStructures[ $menu['sysMenuName'] ] ?>;
			
			// Create jqTree structure
			$('#<?php print $menu['sysMenuName'] ?>').tree({
				data: data,
				saveState: true,
				dragAndDrop: true,
				useContextMenu: true
			});	
			
			// TODO: Bind context menu
			$('#<?php print $menu['sysMenuName'] ?>').bind(
				'tree.contextmenu',
				function(event) 
				{
					var node = event.node;
					alert(node.name);
				}
			);
	
			// Bind - on tree.move element
			$('#<?php print $menu['sysMenuName'] ?>').bind(
				'tree.move',
				function(event) 
				{
					event.preventDefault();
					event.move_info.do_move();
					
					var parentId = (event.move_info.moved_node.parent.id !== undefined) ? event.move_info.moved_node.parent.id : 0;
					
					// Moved menu item weight is determined by
					// next element weight
					console.log(event);
					
					// Update tree structure via AJAX
					$.post(
						SimplCMS.config.baseUrl + '/menu/updateStructure',
						{
							menuItemId: event.move_info.moved_node.id,
							menuItemParentId: parentId,
							weight: _newMenuItemWeight
						}
					);
				}
			);			
	
		<?php endforeach; ?>
	});
	
</script>

<div class="btn-toolbar">
	<div class="btn-group">
		<button class="btn btn-sm btn-default"><?php print t('Add new menu') ?> <i class="icon-bar"></i></button>
	</div>
</div>


<div class="tabbable">
	<ul class="nav nav-tabs">
		
		<?php foreach ($menus as $i => $menu): ?><?php //dump($menu) ?>
		<li>
			<a 
				<?php if ($i==0): ?>class="active"<?php endif; ?> 
				href="#tab_<?php print $menu['sysMenuName'] ?>" 
				data-toggle="tab">
				<?php print $menu['name'] ?>
			</a>
		</li>
		<?php endforeach; ?>
		
	</ul>
	<div class="tab-content">
		
		<?php foreach ($menus as $i => $menu): ?>			
			<div class="tab-pane <?php if ($i==0): ?>active<?php endif; ?>" id="tab_<?php print $menu['sysMenuName'] ?>">				
				<div id="<?php print $menu['sysMenuName'] ?>"></div>
			</div>			
		<?php endforeach; ?>
		
	</div>
</div>