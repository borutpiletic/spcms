<?php /* @var $this \spcms\core\Theme */ ?>

	<?php $this->includeTemplateFile('head'); ?>

	<body>
		<!-- HEADER SECTION / START -->
		<div class="header container-fluid bgcolor-black">
			<div class="container">
				<div class="row">
					<div class="col-md-10">							
						<div class="logo">
							SimplCMS
							<span class="glyphicon glyphicon-pencil"></span>
						</div>						
					</div>
					<div class="col-md-2">
						<ul>
							<li>
</li>
						</ul>
					</div>					
					
					
					<div class="col-md-10">
							<?php print $this->getSection('header', 'mainMenu'); ?>
					</div>
					<div class="col-md-2">
						<ul>
							<li>Odjava</li>
						</ul>
					</div>					
				</div>
			</div>
		</div>	
		<!-- HEADER SECTION / END -->
		
		<!-- MAIN CONTENT SECTION / START -->
		<div class="container main-content">								
			<div class="row">
				<!--
				<div class="col-md-12">
					
					<nav class="navbar navbar-default navbar-square breadcrumb">
						
						<?php print $this->getSection('header', 'mainMenu'); ?>
						
					</nav>					
				</div>-->
				<div class="col-md-3">
					
					<?php print $this->getSection('sidebarLeft', 'modulesMenu'); ?>					
					
				</div>
				<div class="col-md-9">
					<?php print $content; ?>								
				</div>
			</div>
			
			<?php $this->includeTemplateFile('footer'); ?>
		</div>		
		<!-- CONTENT / END -->
		
	</body>
	
	<?php print 'MEMORY USED:'. (memory_get_usage() / (1024*1024) ). ' Mb'; ?>	
</html>