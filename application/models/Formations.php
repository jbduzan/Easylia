<?php

class Application_Model_Formations
{
    protected $_id_formation;
    protected $_nombre_heure;
    protected $_type;
    protected $_id_client;
    protected $_id_formateur;
    protected $_payee;
    protected $_date;
    protected $_heure_debut;
    protected $_id_formation_dispo;
    protected $_formation_effectue;
    protected $_raison_refus;
    
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
    
    public function setIdFormation($id_formation){
    	$this->_id_formation = $id_formation;
    	return $this;
    }
    
    public function getIdFormation(){
    	return $this->_id_formation;
    }
    
    public function setNombreHeure($nombre_heure){
    	$this->_nombre_heure = $nombre_heure;
    	return $this;
    }
    
    public function getNombreHeure(){
    	return $this->_nombre_heure;
    }
    
    public function setType($type){
    	$this->_type = $type;
    	return $this;
    }
    
    public function getType(){
    	return $this->_type;
    }
    
    public function setIdClient($id_client){
    	$this->_id_client = $id_client;
    	return $this;
    }
    
    public function getIdClient(){
    	return $this->_id_client;
    }
    
    public function setIdFormateur($id_formateur){
    	$this->_id_formateur = $id_formateur;
    	return $this;
    }
    
    public function getIdFormateur(){
    	return $this->_id_formateur;
    }
    
    public function setPayee($payee){
		$this->_payee = $payee;
		return $this;     
   	}
   	
   	public function getPayee(){
		return $this->_payee;   	
   	}

	public function setDate($date){
		$this->_date = $date;
		return $this;
	}
	
	public function getDate(){
		return $this->_date;
	}
	
	public function setHeureDebut($heure_debut){
		$this->_heure_debut = $heure_debut;
		return $this;
	}
	
	public function getHeureDebut(){
		return $this->_heure_debut;
	}
	
	public function setIdFormationDispo($id_formation_dispo){
		$this->_id_formation_dispo = $id_formation_dispo;
		return $this;
	}
	
	public function getIdFormationDispo(){
		return $this->_id_formation_dispo;
	}

    public function setFormationEffectue($formation_effectue){
        $this->_formation_effectue = $formation_effectue;
        return $this;
    }

    public function getFormationEffectue(){
        return $this->_formation_effectue;
    }

    public function setRaisonRefus($raison_refus){
        $this->_raison_refus = $raison_refus;
        return $this;
    }
	
    public function getRaisonRefus(){
        return $this->_raison_refus;
    }
}





