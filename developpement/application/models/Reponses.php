<?php

class Application_Model_Reponses
{
    
    protected $_id_reponse;
    protected $_reponse;
    protected $_est_juste;
    protected $_id_question;
    
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
    
    // Getters et Setters
    
    public function setIdReponse($id_reponse){
        $this->_id_reponse = $id_reponse;
        return $this;
    }
    
    public function getIdReponse(){
        return $this->_id_reponse;
    }
    
    public function setReponse($reponse){
        $this->_reponse = (string) $reponse;
        return $this;
    }
    
    public function getReponse(){
        return $this->_reponse;
    }
    
    public function setEstJuste($est_juste){
        $this->_est_juste = $est_juste;
        return $this;
    }
    
    public function getEstJuste(){
        return $this->_est_juste;
    }

    public function setIdQuestion($id_question){
        $this->_id_question = $id_question;
        return $this;
    }
    
    public function getIdQuestion(){
        return $this->_id_question;
    }
    
}

