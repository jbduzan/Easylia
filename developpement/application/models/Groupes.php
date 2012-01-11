<?php

class Application_Model_Groupes
{
    protected $_idGroupe;
    protected $_nom;
    protected $_description;
    
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
    
    public function setIdGroupe($idGroupe){
        $this->_idGroupe = $idGroupe;
        return $this;
    }
    
    public function getIdGroupe(){
        return $this->_idGroupe;
    }
    
    public function setNom($nom){
        $this->_nom = (string) $nom;
        return $this;
    }
    
    public function getNom(){
        return $this->_nom;
    }
    
    public function setDescription($description){
        $this->_description = (string) $description;
        return $this;
    }
    
    public function getDescription(){
        return $this->_description;
    }

}

