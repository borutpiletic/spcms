<?php
/**
 * spcms\core\Bootstrap
 * 
 * SimplCMS application bootstrap class,
 */

namespace spcms\core;

interface IBootstrap
{
	static function load();
}

abstract class Bootstrap implements IBootstrap
{
	
}
