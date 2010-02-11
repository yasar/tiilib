<?php 

class TiiTemplate extends TiiCore {
    /**
     * @var simple_html_dom
     */
    private $DOM;
    
    /**
     * template file to be used for parsing the user interface
     * must be absolute path to the file
     *
     * @var string
     */
    private $template_file;
    
    private $html;
    
    public $scripts=array();
    private $styles=array();
    private $includes=array();

    public function __construct($template_file = null) {
	    //$this->_var_permissions['deny_set'] = array('DOM');
	    parent::__construct();
       // echo 'Template created : ',$this->ID(),'<br />';
        if (! empty($template_file))
            $this->SetTemplate($template_file);
    }
    
    public function __destruct() {
        unset($this->DOM);
    }
    
	/**
	 * Set or Return the application path
	 * which is the absolute path to where the created application's application.php file is residing
	 *
	 * @return String
	 */
	public function Path($val = null){
		return $this->GetOrSet(__FUNCTION__, $val, $this->GetCreatorsPath());
	}
	
	
    /**
     * 
     * @return TiiTemplate
     */ 
	public function AddScript($file, $idx=999){
        $file = realpath($file);
        
		if ($this->IsIncluded($file)) return $this;
		
		if (! file_exists($file)){
			error_log(Tii::Out('File could not be found: %s. %s() in %s:  ',$file,__METHOD__,__FILE__), E_USER_WARNING);
			return true;
		}
		$this->scripts[$idx][]=$file;
		$this->includes[]=$file;
        
        return $this;
	}

    /**
     * 
     * @return TiiTemplate
     */ 
	public function AddStyle($file, $idx=999){
		if ($this->IsIncluded($file)) return $this;
		
		if (! file_exists($file)){
			error_log(Tii::Out('File could not be found: %s. %s() in %s:  ',$file,__METHOD__,__FILE__), E_USER_WARNING);
			return true;
		}
		$this->styles[$idx][]=$file;
		$this->includes[]=$file;
        
        return $this;
	}
	
	public function IsIncluded($file){
		return in_array($file, $this->includes);
	}
	
    /**
     * @param string $s
     * @return TiiTemplate
     */
    public function SetTemplate($s, $absolute_path = false) {
        $this->template_file = $absolute_path ? $s : Tii::App()->Path() ._.'templ'._. $s;
        $this->LoadDOM();
        return $this;
    }
    
    /**
     * @return simple_html_dom
     */ 
    public function DOM(){return $this->DOM();}
    
    private function LoadDOM() {
        if ( empty($this->template_file))
            throw new Exception('Template file is not set.');

        if(! file_exists($this->template_file))
        throw new Exception('Template file could not be found: '.$this->template_file);
			
        //$this->DOM = file_get_html($this->template_file);
	    $this->DOM = new simple_html_dom($this->template_file);

	   // $this->ProcessTiiTags();
	    $this->DOM->set_callback(array('TiiTemplate','ProcessTiiTags'));
    }
    
    /**
     *  @return TiiTemplate 
     */
    public function ProcessModuleTemplate($params){
        if (is_null($params) || empty($params) || !is_array($params)) return $this;
        
        Tii::Import('helper/html.php');
        
		if($holder = $this->DOM->find('#holder',0)){
			$holder->setAttribute('id',$params['holder']['id']);
		}
        
		if($buttons_holder = $this->DOM->find('#buttons_holder',0)){
			$buttons_holder->setAttribute('id',$params['buttons_holder']['id']);
		}
        
        // loop through all the fields
        foreach($params['fields'] as $field => $attributes){
            $id = 'field_'.$field;
            
            // find the el in the DOM
            $el = $this->DOM->find('#'.$id, 0);
            if($el !== false) {
                // if found, replace it with relevant html tag
                $el->outertext = TiiHlpHtml::GetTag($attributes);
                
                if (isset($attributes['label'])){
                    // check if there is label for this tag
                    $el_label = $this->DOM->find('label[for="'.$id.'"]',0);
                    if($el_label !== false) {
                        $el_label->setAttribute('for', $attributes['id']);
                        $el_label->innertext=$attributes['label'];
                    }
                }
            }
        }
        
        // loop through all the buttons
        foreach($params['buttons'] as $button => $attributes){
            $id = 'button_'.$field;
            
            // find the el in the DOM
            $el = $this->DOM->find('#'.$id, 0);
            if($el !== false && $el instanceof simple_html_dom_node) {
                // if found, replace it with relevant html tag
                $el->outertext = TiiHlpHtml::GetTag($attributes);
            }
        }
        
        return $this;
    }
    
	public static function ProcessTiiTags(simple_html_dom_node $_element){
		// all the variables defined in this function are starting with underscore (_)
		// this is to make sure no any defined variable will be override by the extracted variables from the attributes.
		
		switch($_element->tag){
			// look for tii tag
			// eg: <tii type="Module" class="Account" method="LoginControl" params="" base_path="/tiilib/modules" />
			case 'tii':
				// check the attribute:type
				switch($_element->getAttribute('type')){
					case 'Module':
						//base module is required in any case
						Tii::Import('base/module.php');
						
						// get all the attributes of the element: array(type,name,controller,...)
						// then extract them to local variables
						extract($_element->getAllAttributes());

						// fix the class name
						$class_name = 'TiiModuleController_'.ucwords($controller);

						// include the controller file 
                        // which includes the class_name definition
						Tii::Import('modules/'.$name.'/controllers/'.$controller.'.php');
						break;

						
					// if type attribute does not match anything
					// then do not modify anything but return
					default: return;
				}

				// create a reflection class for the module class
				$_RClass = new ReflectionClass($class_name);

				// check if the required method is available in the class
				if($_RClass->hasMethod(tii_setnot($method,TII_MODULE_CONTROLLER_DEFAULT_METHOD))) {

					// check is attribute:params is supplied
					$params = $_element->getAttribute('params');

					if($params !== false) {
						// make sure the supplied params which is in json format,
						// has the keys double quoted.
						$params = json_decode(str_replace('\'','"',$params), true);
					}
					else {
						// if no params is supplied, then set it to an empty array.
						$params = null;
					}
                    
					// create a new instance of the module class
					$_CLASS = $_RClass->newInstance($params);
					
					// invoke the requested method along with the parameters supplied
					$_return = $_CLASS->{$method}();
                    
					// replace the element with method's return value
					$_element->outertext=$_return;
				}
				else{
					// if the method is not found, throw an exception
					throw new Exception('Requested method is not defined: '.$class_name.'->'.$method.'()');
				}

				break;
		}
	}
    
    /**
     *
     * @return TiiTemplate
     * @param object $node_selector
     * @param object $html
     * @param object $idx[optional]
     */
    public function SetContent($node_selector, $html, $idx = 0, $append = false) {
        $el = $this->DOM->find('[id='.$node_selector.']', $idx);
        if (is_null($el))
            return $this;
            
        $el->innertext = $append ? $el->innertext.$html : $html;
        return $this;
    }
    
    /**
     * @param object $html
     * @return TiiTemplate
     */
    public function SetHeadContent($html) {
        $this->DOM->find('head', 0)->innertext = $html;
        return $this;
    }
    
    public function GetHTML($node_selector = '') {
        //throw new Exception();
        if (! empty($node_selector))
            $this->html = $this->DOM->find('[id='.$node_selector.']', 0)->outertext();
        else{
            $this->DOM->root->prepare_innertext();
            $this->ParseScripts()->ParseStyles();
            $this->html = $this->DOM->root->innertext(false);
        }
        //$this->html = str_replace(chr(10), '', $this->html);
        
        return $this->html;
    }
    
    /**
     * @return TiiTemplate
     */ 
    private function ParseScripts(){
        if(($filename = $this->ParseFiles($this->scripts,'js')) !== false){
            // add this cache file to the template
            $this->AddToHead(TII_URL_ROOT.$filename,'<script type="text/javascript" src="%s"></script>');
        }
        
        return $this;
    }
    
    /**
     * @return TiiTemplate
     */ 
    private function ParseStyles(){
        if(($filename = $this->ParseFiles($this->styles,'css')) !== false){
            // add this cache file to the template
            $this->AddToHead(TII_URL_ROOT.$filename,'<link href="%s" rel="stylesheet" type="text/css">');
        }
        
        return $this;
    }
    
    private function ParseFiles($files=array(),$ext='txt'){
        // if not any script is included, then return without doing anything
        if(count($files) == 0) return false;
        
        // import the array helper
        Tii::Import('helper/array.php');
        
        // $this->scripts is multi-dimensional array, can hold multiple script file names at the same index
        // so flatten them to be one dimensional, so that we can iterate them easily. 
        $files = TiiHlpArray::Flatten($files);
        
        // generat the filename based on the included scripts using the md5
        $filename = TII_DIR_CACHE.'/'.md5(implode('*',$files)).'.'.$ext;
        
        // if cache is disabled or this file is already exist in the cache, use it
        if(! Tii::Config('use_cache') || !file_exists(TII_PATH_ROOT.$filename)){
            // include the file class
            Tii::Import('base/file.php');
            
            // instanciate the file class and create the script file in the cache 
            $F = new TiiFile(TII_PATH_ROOT.$filename, TiiFile::MODE_WRITE_ONLY);
            
            // loop through all the included scripts
            foreach($files as $file){
                // read the content of the script and add it to the created script file
                $F->Write(chr(10).chr(10).'/****| '.$file.' |****/'.chr(10).file_get_contents($file));
            }
        }
        
        return $filename;
    }
    
    private function AddToHead($html, $format=null){
        static $head;
        
        if (!isset($head)) $head = $this->DOM->find('head', 0);
        
        if(! is_null($format) && ! empty($format)) $html = Tii::Out($format,$html).chr(10);
        
        $head->innertext = $head->innertext() . $html;
        
        return $this;
    }
    
    /**
     *
     * @param object $prefix
     * @return TiiTemplate
     */
    public function PrefixRelativePaths($prefix) {
        foreach ($this->DOM->find('[src]') as $el) {
            if (strpos($el->src, 'http://'))
                continue;
            $el->src = $prefix.$el->src;
        }
        
        foreach ($this->DOM->find('[href]') as $el) {
            if (strpos($el->src, 'http://'))
                continue;
            $el->src = $prefix.$el->src;
        }
        
        return $this;
    }
}