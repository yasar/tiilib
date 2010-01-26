<?php 
/********************************************************
 $Rev: 16 $:
 $Author: yasar $:
 $Date: 2009-09-01 00:29:25 -0700 (Tue, 01 Sep 2009) $:
 *********************************************************/
 
class TiiTemplate extends TiiCore {
    /**
     * @var simple_html_dom_node
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
    
    public function __construct($template_file = null) {
        if (! empty($template_file))
            $this->SetTemplate($template_file);
    }
    
    public function __destruct() {
        unset($this->DOM);
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
    
    private function LoadDOM() {
        if ( empty($this->template_file))
            throw new Exception('Template file is not set.');
			
        $this->DOM = file_get_html($this->template_file);
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
        if (! empty($node_selector))
            $this->html = $this->DOM->find('[id='.$node_selector.']', 0)->outertext();
        else
            $this->html = $this->DOM->root->innertext();
        //$this->html = str_replace(chr(10), '', $this->html);
        return $this->html;
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
