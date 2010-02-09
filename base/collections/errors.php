<?php

class TiiErrors extends TiiCollection{
    
    public function __toString(){
        return $this->count().'';
    }
    
    public function AddError(TiiError $error){
        $this[]=$error;
        return $this;
    }
    
    public function HasError(){
        return $this->count() > 0;
    }

    public function ClearErrors(){
        $this->ClearCollection();
        return $this;
    }   
    
    /**
    * put your comment there...
    * 
    * @param integer $offset
    * @param TiiError $value
    */
    public function offsetSet($offset, $value) {
        if (! $value instanceof TiiError) throw new Exception("Value have to be a instance of TiiError");
        parent::offsetSet($offset, $value);
    }
    
    /**
    * @param TiiError $offset
    */
    public function offsetGet($offset) {
        return parent::offsetGet($offset);
    }
    
    public function GetMessages(){
        return '<ol><li>'.implode('</li><li>',$this->GetCollection()).'</li></ol>';
    }
}