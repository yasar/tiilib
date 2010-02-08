<?php

class TiiError{
    public $description;
    
    public function __construct($description){
        $this->description = $description;
    }
    
    public function __toString(){
        return $this->description;
    }
}