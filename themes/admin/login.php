<?php /* @var $this \spcms\core\Theme */ ?>
<?php require_once 'head.php' ?>
	<body>
		<header>
		</header>
			

			<div class="container">	
				<?php print $content ?>
			</div> <!-- /container -->		
		
		<?php require_once 'footer.php'; ?>
	</body>
	<?php print 'MEMORY USED:'. (memory_get_usage() / (1024*1024) ). ' Mb'; ?>	
</html>