<?php

class Application_Model_PaypalAutorizedPayement
{

	protected $_id_payement;
	protected $_id_transaction;
	protected $_date_honneur;
	protected $_date_validite;
	protected $_montant;
	
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

	public function setIdPayement($id_payement){
		$this->_id_payement = $id_payement;
		return $this;
	}
	
	public function getIdPayement(){
		return $this->_id_payement;
	}
	
	public function setIdTransaction($id_transaction){
		$this->_id_transaction = $id_transaction;
		return $this;
	}
	
	public function getIdTransaction(){
		return $this->_id_transaction;
	}
	
	public function setDateHonneur($date_honneur){
		$this->_date_honneur = $date_honneur;
		return $this;
	}
	
	public function getDateHonneur(){
		return $this->_date_honneur;
	}
	
	public function setDateValidite($date_validite){
		$this->_date_validite = $date_validite;
		return $this;
	}
	
	public function getDateValidite(){
		return $this->_date_validite;
	}
	
	public function setMontant($montant){
		$this->_montant = $montant;
		return $this;
	}
	
	public function getMontant(){
		return $this->_montant;
	}

}

