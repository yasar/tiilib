<?php
Class TiiDomDriver_Tii implements TiiDomdriver{
	private $html='';
    
    /**
     * @var DOMDocument
     */
    private $DOM = null;
    
    /**
     * @var DOMXPath
     */
    private $DOMXPath = null;
    
    /**
     * @var DOMNodeList
     */
    private $DOMNodeList = null;
    
    /**
     * @var DOMNode
     */
    private $DOMNode = null;
    
    public function __construct(){
        $this->DOM = new DOMDocument();
	}
    
    public function LoadFile($path){
        @$this->DOM->loadHTMLFile($path);
        $this->DOMXPath = new DOMXPath($this->DOM);
        //var_dump($this->DOM->saveHTML());exit;
        return $this;
    }
    
    public function LoadHTML($html){
        $this->DOM->loadHTML($html);
        $this->DOMXPath = new DOMXPath($this->DOM);
        return $this;
    }
    
    public function FindNodes($xpath){
        $this->DOMNodeList = $this->DOMXPath->query($xpath);
        $this->DOMNode = null;
        return $this;
    }
    
    public function FindNode($xpath, $idx=0){
        $this->DOMNodeList = null;
        $this->DOMNode = $this->DOMXPath->query($xpath)->item($idx);
        return $this;
    }
/*    
    public function ReplaceAll($html){
        $node = new DOMNode();
        foreach($this->DOMNodeList as $node){
            $this->Replace($html, $node);
        }
        return $this;
    }
    
    public function Replace($html, $node=null){
        $node = tii_setnot($node, $this->node);
        $node->removeChild($node->firstChild);
        $node->appendChild(new DOMText($html));
        return $this;
    }
    
    public function AppendAll($html){
        $node = new DOMNode();
        foreach($this->DOMNodeList as $node){
            $node->appendChild(new DOMText($html));
        }
        return $this;
    }
*/    
    public function Append($html){
        $this->DOMNode->nodeValue .= $html;
        return $this;
    }
    
    public function InnerHTML($html=null){
        if(is_null($html)){
            $D = new DOMDocument();
            $D->appendChild($D->importNode($this->DOMNode->cloneNode(true), true));
            return $D->saveHTML();
        }
        
        //$this->DOMNode->removeChild($this->DOMNode->firstChild);
        //$this->DOMNode->appendChild(new DOM)
        $this->DOMNode->nodeValue = $html;
        return $this;
    }
}