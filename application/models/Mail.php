<?php

class Application_Model_Mail
{
	protected $_id_mail;
	protected $_sujet;
	protected $_contenu;
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
        return $this->$method($value);
        
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

	public function setIdMail($id_mail){
		$this->_id_mail = $id_mail;
		return $this;
	}

	public function getIdMail(){
		return $this->_id_mail;
	}

	public function setSujet($sujet){
		$this->_sujet = $sujet;
		return $this;
	}

	public function getSujet(){
		return $this->_sujet;
	}

	public function setContenu($contenu){
		$this->_contenu = $contenu;
		return $this;
	}

	public function getContenu(){
		return $this->_contenu;
	}

	public function setDescription($description){
		$this->_description = $description;
		return $this;
	}

	public function getDescription(){
		return $this->_description;
	}

}

