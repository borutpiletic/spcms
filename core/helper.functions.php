<?php
/**
 * This file contains some useful CMS helper functions just 
 * to avoid typing long class names.
 */

/**
 * Translate string into current selected language
 * @param string $text
 */
function t($text)
{	
	$transFile = SimplCMS::$app->basePath. '/data/translations/'. SimplCMS::$app->locale->getLocale(SimplCMS::$app->language). '.php';
	
	if(!file_exists($transFile)) 
		throw new Exception('No translation file found');
	
	$strings = require($transFile);
	
	if(isset($strings[$text]))
		return $strings[$text];
	
	return $text;
}

/**
 * Dump variable
 * @param mixed $var
 * @param boolean $terminate Terminate script
 */
function dump($var, $terminate = false)
{
	print '<pre style="border:1px solid blue;">dump() output:<br/>';	
	print_r($var);		
	print '</pre>';
	
	if($terminate === true)
		exit;
}

