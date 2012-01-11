<?php

class Application_Model_GroupesPermissions
{
    protected $_idGroupePermission;
    protected $_idGroupe;
    protected $_idAutorisation;
    
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
    
    public function setIdGroupePermission($id_groupe_permission){
        $this->_idGroupePermission = $id_groupe_permission;
        return $this;
    }
    
    public function getIdGroupePermission(){
        return $this->_idGroupePermission;
    }
    
    public function setIdGroupe($id_groupe){
        $this->_idGroupe = $id_groupe;
        return $this;
    }
    
    public function getIdGroupe(){
        return $this->_idGroupe;
    }
    
    public function setIdAutorisation($id_autorisation){
        $this->_idAutorisation = $id_autorisation;
        return $this;
    }
    
    public function getIdAutorisation(){
        return $this->_idAutorisation;
    }

}

