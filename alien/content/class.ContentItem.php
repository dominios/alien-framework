<?php

abstract class ContentItem {
    
    private $id;
    private $name;
    
    public function getId(){
        return $this->id;
    }
    
    public function getName(){
        return $this->name;
    }

    public abstract function getType();
}
?>
