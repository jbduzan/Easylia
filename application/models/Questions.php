<?php

class Application_Model_Questions
{
    protected $_id_question;
    protected $_question;
    protected $_nbr_reponse;
    protected $_reponse_ouverte;
    protected $_motivation;
    
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
    
    public function setidQuestion($id_question){
        $this->_id_question = $id_question;
        return $this;
    }
    
    public function getidQuestion(){
        return $this->_id_question;
    }
    
    public function setQuestion($question){
        $this->_question = (string) $question;
        return $this;
    }
    
    public function getQuestion(){
        return $this->_question;
    }
    
    public function setNbrReponse($nbr_reponse){
        $this->_nbr_reponse = $nbr_reponse;
        return $this;
    }
    
    public function getNbrReponse(){
        return $this->_nbr_reponse;
    }
    
    public function setReponseOuverte($reponse_ouverte){
    	$this->_reponse_ouverte = $reponse_ouverte;
    	return $this;
    }
    
    public function getReponseOuverte(){
    	return $this->_reponse_ouverte;
    }
    
    public function setMotivation($motivation){
    	$this->_motivation = $motivation;
    	return $this;    
    }
    
    public function getMotivation(){
    	return $this->_motivation;
    }

}

