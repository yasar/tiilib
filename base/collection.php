<?php

class TiiCollection implements ArrayAccess, Iterator, Countable {
    private $container = array();

    public function __construct() {}
    
    protected function ClearCollection(){
        $this->container = array();
    }
    
    protected function GetCollection(){
        return $this->container;
    }
    
    public function offsetSet($offset,$value) {
        if ($offset == "") {
            $this->container[] = $value;
        }else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
     return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function rewind() {
        reset($this->container);
    }

    public function current() {
        return current($this->container);
    }

    public function key() {
        return key($this->container);
    }

    public function next() {
        return next($this->container);
    }

    public function valid() {
        return $this->current() !== false;
    }    

    public function count() {
     return count($this->container);
    }

}