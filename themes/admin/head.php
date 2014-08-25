<?php /* @var $this \spcms\core\Theme */ ?>
<!DOCTYPE HTML>
<html>
	<head>
		<?php print $this->getHtmlHeaders(); ?>

		<title><?php print $this->pageTitle; ?></title>

		<?php print $this->getScriptFiles(); ?>
		<style type="text/css">@import url("//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css");</style>
		<?php print $this->getStylesheetFiles(); ?>
	</head>