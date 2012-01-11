<?php

class Application_Model_Autorisation
{
    protected $_idAutorisation;
    protected $_nom;
    protected $_droitAccorde;
    
    // Constructeur
    
    public function __Construct(array $options = null){
        
        if(is_array($options)){
            $this->setOptions($options);
        }
        
    }
    
    // Getter et Setter Générique
    
    public function __set($name, $value){
        
        $method = 'set'.$name;
        if(('mapper' == $name) || !method_exists($this, $method)){
            throw new Exception('Invalid Utilisateurs property');
        }
        return $this->$method();
        
    }

    public function __get($name){
        
        $method = 'get'.$name;
        if(('mapper' == $name) || !method_exists($this, $method)){
            throw new Exception('Invalid Utilisateurs property');
        }
        return $this->$method();
        
    }
    
    // Setters et Getters
    
    public function setOptions(array $options){
        
        $methods = get_class_methods($this);
        foreach($options as $key => $value){
            $method = 'set'.ucfirst($key);
            if(in_array($method, $methods)){
                $this->$method($value);
            }
        }
        return $this;
        
    }
    
    public function setIdAutorisation($id_autorisation){
        $this->_idAutorisation = $id_autorisation;
        return $this;
    }
    
    public function getIdAutorisation(){
        return $this->_idAutorisation;
    }
    
    public function setNom($nom){
        $this->_nom = (string) $nom;
        return $this;
    }
    
    public function getNom(){
        return $this->_nom;
    }
    
    public function setDroitAccorde($droit_accorde){
        $this->_droitAccorde = (string) $droit_accorde;
        return $this;
    }
    
    public function getDroitAccorde(){
        return $this->_droitAccorde;
    }

}

