<?php 
if (!defined('T_PATH_APPLICATION'))
    throw new Exception('T_PATH_APPLICATION constant is not defined');
defined('T_PATH_FRAMEWORK') || define('T_PATH_FRAMEWORK', dirname(__FILE__));

class T {
	static private $_imports = array(
		'base/core.php',
		'base/application.php',
		'base/template.php'
	);
	
    /**
     * @var {TConfig}
     */
    static public $Config;
    
    /**
     * @var Array
     */
    static private $_apps;
    
    static public function Init($config = null) {
    	foreach(self::$_imports as $import){
    		self::Import($import);
    	}
		
        self::Import('config.default.php');
        !is_null($config) && ! empty($config) && file_exists($config) && include ($config);
        self::$Config = new TConfig();
    }
    
    /**
     * Imports/includes the given file
     * and returns the reference to T
     *
     * @param string $path
     * @return T
     */
    static public function Import($path) {
        $filename = T_PATH_FRAMEWORK.DIRECTORY_SEPARATOR.$path;
        if (file_exists($filename))
            include_once $filename;
        //return T;
    }
    
    /**
     * Creates a new TApplication
     * and returns the referenece to this application
     *
     * @param string $name
     * @return TApplication
     */
    static public function CreateApp($name) {
        self::$_apps[$name] = new TApplication();
		self::$_apps[$name]->Name($name);
        return self::$_apps[$name];
    }
    
    /**
     * Returns the requested application
     * If application name is not supplied
     * and there is onlyone application created
     * then it will return it.
     *
     * otherwise an application name has to be provided
     *
     * @param string $name [optional]
     * @return TApplication
     */
    static public function GetApp($name = null) {
        if (is_null($name)) {
            if (count(self::$_apps) == 1)
                return array_pop(self::$_apps);
            elseif (count(self::$_apps) > 1)
                throw new Exception('Application name is not provided.');
            else
                throw new Exception('There is no any application created.');
        }
        
        if (!array_key_exists($name, self::$_apps))
            throw new Exception('The application ('.$name.') is not found.');
            
        return self::$_apps[$name];
    }
}
