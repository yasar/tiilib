<?php 
/********************************************************
 $Rev: 16 $:
 $Author: yasar $:
 $Date: 2009-09-01 00:29:25 -0700 (Tue, 01 Sep 2009) $:
 *********************************************************/
 
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
	
	
	public function AddScript($file, $idx=999){
		if ($this->IsIncluded($file)) return $this;
		
		if (! file_exists($file)){
			error_log(Tii::Out('File could not be found: %s. %s() in %s:  ',$file,__METHOD__,__FILE__), E_USER_WARNING);
			return true;
		}
		$this->scripts[$idx][]=$file;
		$this->includes[]=$file;
	}

	public function AddStyle($file, $idx=999){
		if ($this->IsIncluded($file)) return $this;
		
		if (! file_exists($file)){
			error_log(Tii::Out('File could not be found: %s. %s() in %s:  ',$file,__METHOD__,__FILE__), E_USER_WARNING);
			return true;
		}
		$this->styles[$idx][]=$file;
		$this->includes[]=$file;
	}
	
	public function IsIncluded($file){
		return in_array($file, $this->includes);
	}
	
    /**
     * @param string $s
     * @return TiiTemplate
     */
    public function SetTemplate($s, $absolute_path = false) {
        $this->template_file = $absolute_path ? $s : Tii::App()->Path() . '/templ/' . $s;
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
                $el->outertext = TiiHtml::GetTag($attributes);
                
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
                $el->outertext = TiiHtml::GetTag($attributes);
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
						// get all the attributes of the element: array(type,clas,...)
						// then extract them to local variables
						extract($_element->getAllAttributes());

						// fix the class name
						$class_name = 'TiiModule_Controller_'.$class;

						// include the module file which includes the class_name definition
						Tii::Import('modules/'.$class.'/module.php');
						break;

						
					// if type attribute does not match anything
					// then do not modify anything but return
					default: return;
				}

				// create a reflection class for the module class
				$_RClass = new ReflectionClass($class_name);

				// check if the required method is available in the class
				if($_RClass->hasMethod($method)) {
					// create a new instance of the module class
					$_CLASS = $_RClass->newInstance();

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
                    
					// invoke the requested method along with the parameters supplied
					$_return = $_CLASS->{$method}($params);
                    //echo '<hr />'.'='.Tii::App()->Template()->ID().'<hr/>';
                    //var_dump(Tii::App()->Template()->scripts);
                    //throw new Exception('qqq');
                    
					// replace the element with method's return value
					$_element->outertext=$_return;
				}else{
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
            $this->ParseScripts();
            $this->html = $this->DOM->root->innertext();
        }
        //$this->html = str_replace(chr(10), '', $this->html);
        
        return $this->html;
    }
    
    private function ParseScripts(){
        if(count($this->scripts) == 0) return;
        
        Tii::Import('helper/array.php');
        
        $scripts = TiiArray::Flatten($this->scripts);
        
        foreach($scripts as $script) $this->AddToHead($script,'<script type="text/javascript" src="%s"></script>');

        return $this;
    }
    
    private function ParseStyles(){
        foreach($this->styles as $styles) $this->AddToHead($script);
        
        return $this;
    }
    
    private function AddToHead($html, $format=null){
        static $head;
        
        if (!isset($head)) $head = $this->DOM->find('head', 0);
        
        if(! is_null($format) && ! empty($format)) $html = Tii::Out($format,$html);
        
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
