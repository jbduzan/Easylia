<?php

class Application_Model_Faq
{
	protected $_id_faq;
	protected $_question;
	protected $_reponse;
	protected $_active;
	protected $_categorie;
	
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
    
    public function setIdFaq($id_faq){
    	$this->_id_faq = $id_faq;
    	return $this;
    }
    
    public function getIdFaq(){
    	return $this->_id_faq;
    }
    
    public function setQuestion($question){
    	$this->_question = $question;
    	return $this;
    }
    
    public function getQuestion(){
    	return $this->_question;
    }
    
    public function setReponse($reponse){
    	$this->_reponse = $reponse;
    	return $this;
    }
    
    public function getReponse(){
    	return $this->_reponse;
    }
    
    public function setActive($active){
    	$this->_active = $active;
    	return $this;
    }
    
    public function getActive(){
    	return $this->_active;
    }
    
    public function setCategorie($categorie){
    	$this->_categorie = $categorie;
    	return $this;
    }
    
    public function getCategorie(){
    	return $this->_categorie;
    }
}