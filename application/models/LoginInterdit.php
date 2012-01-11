<?php

class Application_Model_LoginInterdit
{
    
    // Liste des variables
	protected $_id_login_interdit;
	protected $_login;
    
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
 	
 	public function setIdLoginInterdit($id_login_interdit){
 		$this->_id_login_interdit = $id_login_interdit;
 		return $this;
 	}
 	
 	public function getIdLoginInterdit(){
 		return $this->_id_login_interdit;
 	}
 	
 	public function setLogin($login){
 		$this->_login = $login;
 		return $this;
 	}
 	
 	public function getLogin(){
 		return $this->_login;
 	}
 	   
}
