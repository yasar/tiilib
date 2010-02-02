<?php

class TiiFile extends TiiBase{
    const MODE_READ_ONLY='r';
    const MODE_READ_WRITE='r+';
    const MODE_WRITE_ONLY='w';
    const MODE_READ_WRITE_CREATE='w+';
    const MODE_APPEND='a';
    const MODE_READ_APPEND='a+';
    
    private $filename,$mode,$handle;
    
    public function __construct($filename=null, $mode=MODE_READ_ONLY){
        parent::__construct();
        
        $this->mode = $mode;
        if (!is_null($filename)) {
            $this->filename = $filename;
            $this->Open();
        }
    }
    
    public function Open(){
        $this->handle = fopen($this->filename, $this->mode);
        return $this;
    }
    
    public function GetHandle(){
        return $this->handle;
    }
    
    public function Write($data){
        fwrite($this->handle, $data);
        return $this;
    }
}