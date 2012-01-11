<?php

class Application_Model_ReponsesCertification
{
    
    protected $_id_reponse_certification;
    protected $_id_question;
    protected $_reponse;
    protected $_id_utilisateur;
    
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
    
    public function setIdReponseCertification($id_reponse_certification){
    	$this->_id_reponse_certification = $id_reponse_certification;
    	return $this;
    }
    
    public function getIdReponseCertification(){
    	return $this->_id_reponse_certification;
    }
    
    public function setIdQuestion($id_question){
    	$this->_id_question = $id_question;
    	return $this;
    }
    
    public function getIdQuestion(){
    	return $this->_id_question();
    }
    
    public function setReponse($reponse){
    	$this->_reponse = (string) $reponse;
    	return $this;
    }
    
    public function getReponse(){
    	return $this->_reponse;
    }
    
    public function setIdUtilisateur($id_utilisateur){
    	$this->_id_utilisateur = $id_utilisateur;
    	return $this;
    }
    
    public function getIdUtilisateur(){
    	return $this->_id_utilisateur;
    }
    

    
}

