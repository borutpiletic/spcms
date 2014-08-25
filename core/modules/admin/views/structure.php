<h1><?php print t('Edit site structure'); ?></h1>

<div class="btn-toolbar">
	<div class="btn-group">
		<button class="btn btn-sm btn-default"><?php print t('Add new menu') ?> <i class="icon-bar"></i></button>
	</div>
</div>


<div class="tabbable">
	<!-- MENU TABS / NAVIGATION -->
	<ul class="nav nav-tabs">		
		<?php for ($i = 0; $i < $menuCount; $i++): ?>
			<li>
				<a 
					<?php if ($i==0): ?>class="active"<?php endif; ?> 
						href="#tab_<?php print $menus[$i]['sysMenuName'] ?>" 
						data-toggle="tab">
					<?php print $menus[$i]['name'] ?>
				</a>
			</li>
		<?php endfor ?>			
	</ul>
	<!-- MENU TABS / NAVIGATION -->
	
	<!-- MENU TABS / STRUCTURES -->
	<div class="tab-content">
		
		<?php for ($i = 0; $i < $menuCount; $i++): ?>
			<div class="tab-pane <?php if ($i==0): ?>active<?php endif ?>" id="tab_<?php print $menus[$i]['sysMenuName'] ?>">
				
				<!-- MENU STRUCTURE / HTML LISTS -->	
					<div id="menu<?php print $i ?>">
						<?php print $menuHtmlLists[$i] ?>
					</div>
				<!-- MENU STRUCTURE / HTML LISTS -->
				
			</div>			
		<?php endfor; ?>
		
	</div>
	<!-- MENU TABS / STRUCTURES -->
	
</div>



<script type="text/javascript">	
	$(document).ready(function()
	{
		// Create menu instances using fancytree
		var menuCount = parseInt('<?php print $menuCount ?>');		
		MenuStructure.init(menuCount);
	});
</script>