<?php   
namespace spcms\core;

/**
 * This class represents application error handling and logging mechanism.
 */
class RuntimeLog
{
    protected static $errorType = array (
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    );
    
    public function __construct() 
    {
        $this->run();
    }
    
    /**
     * Write error into error log
     * @param string $error
     */
    public static function write($error)
    {
        $handle = fopen(\SimplCMS::app()->basePath. '/data/error.log', 'at');
        fwrite($handle, strip_tags($error));
        fclose($handle);
    }
    
    /**
     * Clean-up error log
     */
    public static function clean()
    {
        $handle = fopen(\SimplCMS::app()->basePath. '/data/error.log', 'w');
        ftruncate($handle, 0);
        fclose($handle);
    }

    protected function run()
    {
		// Set custom error handler method
        set_error_handler(array(__CLASS__, 'errorHandler'));
    }
    
    protected static function printMessage($message)
    {
        print "{$message}\n\n";
        print "by \SimplCMS runtime logger. Log file path: data/error.log";
    }
    
	/**
	 * SimplCMS custom error handling method
	 * @param type $error
	 * @param type $message
	 * @param type $file
	 * @param type $line
	 */
    public static function errorHandler($error, $message, $file, $line)
    {
        $errorMessage  = '<div style="background:#333; color:#fff !important; position:fixed; width:100%; padding:10px;">['. date('d.m.Y h:i:s'). '] '. strtoupper(self::$errorType[$error]);
        $errorMessage .= "Message: <b>{$message}</b> [Line: {$line}] [File: {$file}]</div><br/><br/>\n";
        
        // Get app error logging config
        $config = \SimplCMS::app()->getConfig()->runtimeLogOutput;
        $config = explode('|', $config);

        // Writing to error log
        if(in_array('log', $config))
            self::write($errorMessage);
        
        // Displaying message to the client
        if(in_array('client', $config))
            self::printMessage($errorMessage);
    }
}