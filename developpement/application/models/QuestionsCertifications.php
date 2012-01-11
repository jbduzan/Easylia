<?php

class Application_Model_QuestionsCertifications
{
    protected $_id_question_certification;
    protected $_id_question;
    protected $_id_certification;
    protected $_question_obligatoire;
    
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
    
    public function setIdQuestionCertification($id_question_certification){
        $this->_id_question_certification = $id_question_certification;
        return $this;
    }
    
    public function getIdQuestionCertification(){
        return $this->_id_question_certification;
    }
    
    public function setidQuestion($id_question){
        $this->_id_question = $id_question;
        return $this;
    }
    
    public function getidQuestion(){
        return $this->_id_question;
    }
    
    public function setIdCertification($id_certification){
        $this->_id_certification = $id_certification;
        return $this;
    }
    
    public function getIdCertification(){
        return $this->_id_certification;
    }
    
    public function setQuestionObligatoire($question_obligatoire){
    	$this->_question_obligatoire = $question_obligatoire;
    	return $this;
    }
    
    public function getQuestionObligatoire(){
    	return $this->_question_obligatoire;
    }

}

