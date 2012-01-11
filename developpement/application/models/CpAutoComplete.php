<?php

class Application_Model_CpAutoComplete
{
    
    // Liste des variables
    protected $_code_pays;
    protected $_code_postal;
    protected $_ville;
    
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
    
    public function setCodePays($code_pays){
        $this->_code_pays = $code_pays;
        return $this;
    }
    
    public function getCodePays(){
        return $this->_code_pays;
    }
    
    public function setCodePostal($code_postal){
        $this->_code_postal = (string) $code_postal;
        return $this;
    }
    
    public function getCodePostal(){
        return $this->_code_postal;
    }
    
    public function setVille($ville){
        $this->_ville = (string) $ville;
        return $this;
    }
    
    public function getVille(){
        return $this->_ville;
    }

}

