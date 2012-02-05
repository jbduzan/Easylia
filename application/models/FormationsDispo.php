<?php

class Application_Model_FormationsDispo
{
    protected $_id_formation_dispo;
    protected $_nom;
    protected $_type;
    
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

	public function setIdFormationDispo($id_formation_dispo){
		$this->_id_formation_dispo = $id_formation_dispo;
		return $this;
	}
	
	public function getIdFormationDispo(){
		return $this->_id_formation_dispo;
	}
	
	public function setNom($nom){
		$this->_nom = (string) $nom;
		return $this;
	}
	
	public function getNom(){
		return $this->_nom;
	}
	
	public function setType($type){
		$this->_type = (string) $type;
		return $this;
	}
	
	public function getType(){
		return $this->_type;
	}
		
}

