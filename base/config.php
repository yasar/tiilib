<?php 
class TiiConfig extends TiiBase {
    private $config;
    
    public function __construct() {
    }
    
    public function LoadFromFile($config_file, $type = TII_CONFIG_FILE_TYPE_JSON ) {
        if (!isset($config_file))
            throw new Exception('Config file name is not provided.');
          
		if(!file_exists($config_file))
		throw new Exception(Tii::Out('Configuration file "%s" does not exist.',$config_file));
		 
        Tii::Import('helper/array.php');
        switch ($type) {
            case TII_CONFIG_FILE_TYPE_JSON:
                $this->config = TiiArray::Extend($this->config, (array) json_decode(file_get_contents($config_file)));
                break;
                
            case TII_CONFIG_FILE_TYPE_INI:
                $this->config = TiiArray::Extend($this->config, parse_ini_file($config_file, true));
                break;
        }
    }
    
    public function Get($path) {
        $path = explode('/', $path);
        $value = $this->config;
        for ($i = 0, $n = count($path); $i < $n; $i++) {
            if (!isset($value[$path[$i]])){
                throw new Exception(Tii::Out('Path "%s" is not valid in the configuration', $path[$i]));
			}
			$value = $value[$path[$i]];
            is_object($value) && $value = (array) $value;
        }
        
        return $value;
    }
    
    public function Set($path, $value) {
        $path = explode('/', $path);
        $config = &$this->config;
        for ($i = 0, $n = count($path) - 1; $i < $n; $i++) {
            is_object($config) && $config = &$config-> {
                $path[$i]
            } || is_array($config) && $config = &$config[$path[$i]];
        }
        
        is_object($config) && $config-> {
            $path[$i]
        } = $value || is_array($config) && $config[$path[$i]] = $value;
    }
}
