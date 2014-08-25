<?php /* @var $this \spcms\core\Theme */ ?>
<html doctype="html">
<head>
    <title><?php print $this->page->title ?></title>
    <meta name="description" content="<?php //print $this->page->metaDescription ?>" />
    <?php print $this->getScriptFiles() ?>
    <?php print $this->getStylesheetFiles() ?>
</head>  
    <body>
        <div id="container">
            <div id="header">
                <h1></h1>
            </div>
            <div id="navigation">
                <ul>
                    <li><a href="<?php print SimplCMS::app()->baseUrl ?>">Home</a></li>
                    <li><a href="<?php print SimplCMS::app()->buildUrl('contact') ?>">Contact</a></li>
					

                </ul>
				
            </div>
            <div id="content-container">
                <div id="content">
                    <p><?php print $content; ?></p>
                </div>
                <div id="aside">
                    <?php print $this->getSection('rightColumn'); ?>
                </div>
                <div id="footer">
                           &copy; SimplCMS 2012 - oldschool CMS project :)
                </div>
            </div>
        </div>
		<BR/><BR/><hr/>
		<?php print 'MEMORY USED:'. (memory_get_usage() / (1024*1024) ). ' Mb'; ?>
    </body>
	<?php dump( \SimplCMS::$app->request->getRoute(), false) ?>
</html>

