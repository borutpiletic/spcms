<?php
/**
 * This is default CMS error page template. Override this template by putting it into your
 * themes/[themename]/error-page.php file.
 * 
 */

/* @var $this spcms\core\Response */ 
?>
<html>
	<head>
		<title><?php print self::$httpStatusCodes[$code] ?></title>
	</head>
	<body>
		<h1>Ups, something's wrong!</h1>
		<h4>Your request returned: <?php print self::$httpStatusCodes[$code] ?></h4>		
	</body>
</html>