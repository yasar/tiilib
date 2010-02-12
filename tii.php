<?php 
if (!defined('TII_PATH_ROOT'))
    throw new Exception('TII_PATH_ROOT constant is not defined');
    
if (!defined('TII_PATH_FRAMEWORK'))
    throw new Exception('TII_PATH_FRAMEWORK constant is not defined');

class Tii {
	static private $_imports = array(
		'base/base.php',
		'base/core.php',
		'base/controller.php',
        'base/application.php',
		'base/collection.php',
		'vendor/simple_html_dom.php',
		'base/template.php',
		'base/config.php',
        'helper/request.php',
        'base/error.php',
        'base/collections/errors.php',
        'base/db.php',
        'helper/common.php'
	);
	
    /**
     * Configuration object tahts is instance of TiiConfig
     * @var {TiiConfig}
     */
    static private $_Config;
	
	/**
	 * Holds the last application created
	 * or the one that is explicitly loaded
	 * 
	 * @var TiiApplication
	 */
	static private $_App;
	
	/**
	 * holds the application start time
	 * @var
	 */
	static private $_start_time;
    
    /**
     * holds all applications created
     * 
     * @var Array
     */
    static private $_apps;
    
    static private $_DB;
    
    static public $debug = false;
    
    /**
    * Holds the authenticated Account
    * 
    * @var TiiModel_Account
    */
    static public $ActiveAccount;
    
    static public function Init($config_file = null, $validate=false) {
        self::$_start_time = microtime(true);
		
    	foreach(self::$_imports as $import){
    		self::Import($import);
    	}
        
		// Create the global config object
		self::$_Config = new TiiConfig();
		
		// if a config_file is passed in, load it
		if (! is_null($config_file)) self::LoadConfig($config_file);
		
		//if (self::Config('debug')))
		
		self::$_DB = new TiiDB();
    }
    
    static public function Finish(){
		$end_time = microtime(true);
		return $end_time - self::$_start_time;
    }
	
	static public function LoadConfig($config_file){
		self::$_Config->LoadFromFile($config_file);
	}
	
	static public function Config($path, $value = null){
		if(is_null($value)) return self::$_Config->Get($path);
		self::$_Config->Set($path, $value);
	}
    
    /**
     * Imports/includes the given file
     * and returns the reference to Tii
     * ex: Tii::Import('helper/array.php');
     *
     * @param string $path
     * @return Tii
     */
    static public function Import($path) {
    	// fix path seperator
    	if (_ !== '/') $path = str_replace('/',_,$path);
    	
    	static $imports = array();
		if (in_array($path, $imports)) return;
		
        $filename = TII_PATH_FRAMEWORK._.$path;
        if (file_exists($filename)){
            include_once $filename;
			$imports[] = $path;
		}
    }
    
    /**
     * Creates a new TApplication
     * and returns the referenece to this application
     *
     * @param string $name
     * @return TiiApplication
     */
    static public function CreateApp($app_name, $init=true) {
    	$path = TII_PATH_ROOT._.'apps'._.strtolower($app_name)._.'application.php';
		if (! file_exists($path)) throw new Exception (Tii::Out('Application "%s" could not be found at: %s', $app_name, $path));
    	
		include $path;
		
		if (! class_exists($app_name)) throw new Exception(Tii::Out('Application class for "%s" does not exist.',$app_name));
		
		
        self::$_apps[] = self::$_App = new $app_name();
        
        if($init) self::$_App->Init();
		
		return self::$_App;
    }
    
	/**
	 * 
	 * @return TiiApplication
	 */
	static public function App(){return self::$_App;}
	
    /**
     * Returns the requested application
     * If application name is not supplied
     * and there is onlyone application created
     * then it will return it.
     *
     * otherwise an application name has to be provided
     *
     * @param string $name [optional]
     * @return TiiApplication
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
            throw new Exception(Tii::Out('The application (%s) is not found.',$name));
            
        return self::$_apps[$name];
    }
	
	static public function &DB(){
		return self::$_DB;
	}
	
	/**
	 * Takes at least one parameter which is the string to be displayed.
	 * if more parameters are passed in, sprintf will be used to format the string accordingly
	 * 
	 * @return string
	 */
	static public function Out(){
		if(func_num_args() == 1) return func_get_arg(0);
		
		$args = func_get_args();
		$str = array_shift($args);
		return vsprintf($str, $args);
	}
	
	static public function ExceptionHandler(Exception $e){
		echo Tii::Out(file_get_contents(TII_PATH_FRAMEWORK._.'base'._.'resources'._.'exception.html'), 
			get_class($e), $e->getCode(), htmlentities($e->getMessage()), $e->getFile(), $e->getLine(), $e->getTraceAsString());
	}
	
	
	/**
	* put your comment there...
	* 
	* @param string $key
	* @param mixed $value
	* @return Tii|boolean
	*/
	static public function Session($key, $value=null){
		if(is_null($value)) {
			if(! isset($_SESSION[$key])) return false;
			else return $_SESSION[$key];
		}
		$_SESSION[$key] = $value;
		return self;
	}
}
