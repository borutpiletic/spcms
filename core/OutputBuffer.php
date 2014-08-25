<?php
namespace spcms\core;
use \SimplCMS;

class OutputBuffer
{
    private static $buffer;
    
    /**
     * Begin capture into output buffer
     * 
     * @param boolean $chunkSize Flush buffer after this length
     * @param boolean $clean If is set to false buffer cannot be deleted before the script finishes. Cleaning functions (ob_clean..) will have no effect.
     * 
     * @return boolean
     */
    public static function start($callback = null, $chunkSize = 0)
    {
        if(ob_start($callback, $chunkSize) === false)
            throw new \Exception('Output buffering could not be started');
    }
    
    public static function end($output = false)
    {
        ($output === true) ? ob_end_flush() : ob_end_clean();
    }

    /**
     * Return contents of the output buffer
     * @return type
     */
    public static function getContents()
    {
        if(empty(self::$buffer))
            return ob_get_contents();
        
        return self::$buffer;
    }
    
    /**
     * Clean output buffer. No output is returned.
     */
    public static function clean()
    {
        ob_clean();
    }

    public static function postProcess()
    {
        print 123;
    }
    
    public static function output($terminate = false)
    {
        
    }
    
    /**
     * Turn on HTML page compression with GZIP
     */
    public static function enableGzipCompression()
    {
        ob_start('ob_gzhandler');
    }
}
