<?php

class Application_Model_PageDynamique{
    
    protected $_id_page_dynamique;
    protected $_nom;
    protected $_description;
    protected $_url;
    protected $_contenu;
    
    // Constructeur
    
    public function __Construct(array $options = null){
        
        if(is_array($options)){
            $this->setOptions($options);
        }
        
    }
    
    // Getter et Setter Générique
    
    public function set($name, $value){
        $option = "_".$name;
        $this->$option = $value;
        return $this;   
    }

    public function get($name){
        $option = "_".$name;
        return $this->$option;
    }
    
    // Setters et Getters
    
    public function setOptions(array $options){
        foreach($options as $key => $value){
            $this->set($key, $value);
        }
        return $this;
        
    }
}

