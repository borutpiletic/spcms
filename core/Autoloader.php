<?php
namespace spcms\core;
use \SimplCMS;

/**
 * Simple autoloader for SimplCMS core classes autoloading
 */
class Autoloader
{
    /**
     * SimplCMS class autoloading
     */
    public static function load($className)
    {
        // Core namespace
        if(strstr($className, 'spcms\\') !== false)
        {
            $fileName = str_replace('spcms\\', '', $className);
            $fileName = str_replace('\\', '/', $fileName);
            
            $filePath = \SimplCMS::app()->basePath;
            
            if(file_exists("{$filePath}/{$fileName}.php"))
                include_once "{$filePath}/{$fileName}.php";
            else
                throw new \Exception("File with class {$className} does not exsist.");
        }
        
    }
    
    /**
     * Register class autoloader
     * @param string $className
     * @param string $method
     * @param boolean $prepend Prepend autoloader to the autoloading stack. Default: append.
     */
    public static function register($className = __CLASS__ , $methodName = 'load', $prepend = false)
    {
        self::registerCorePath();        
        spl_autoload_register(array(__CLASS__, $methodName), true, $prepend);
    }
    
    /**
     * Add CMS core files to include_path
     */
    public static function registerCorePath()
    {
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        
        // Add CMS core classes path
        $includePaths[] = SimplCMS::app()->basePath. '/core';
        
        set_include_path(implode(PATH_SEPARATOR, $includePaths));
    }
}

