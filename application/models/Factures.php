<?php

class Application_Model_Factures
{
	protected $_id_facture;
	protected $_id_utilisateur;
	protected $_id_fournisseur;
	protected $_montant;
	protected $_numero_facture;
    protected $_date_creation;
    protected $_paye;
	
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
    
    public function setIdFacture($id_facture){
    	$this->_id_facture = $id_facture;
    	return $this;
    }
    
    public function getIdFacture(){
    	return $this->_id_facture;
    }
    
    public function setIdUtilisateur($id_utilisateur){
    	$this->_id_utilisateur = $id_utilisateur;
    	return $this;
    }
    
    public function getIdUtilisateur(){
    	return $this->_id_utilisateur;
    }
    
    public function setIdFournisseur($id_fournisseur){
    	$this->_id_fournisseur = $id_fournisseur;
    	return $this;
    }
    
    public function getIdFournisseur(){
    	return $this->_id_fournisseur;
    }
    
    public function setMontant($montant){
    	$this->_montant = $montant;
    	return $this;
    }
    
    public function getMontant(){
    	return $this->_montant;
    }
    
    public function setNumeroFacture($numero_facture){
    	$this->_numero_facture = $numero_facture;
    	return $this;
    }
    
    public function getNumeroFacture(){
    	return $this->_numero_facture;
    }

    public function setDateCreation($date_creation){
        $this->_date_creation = $date_creation;
        return $this;
    }

    public function getDateCreation(){
        return $this->_date_creation;
    }

    public function setPaye($paye){
        $this->_paye = $paye;
        return $this;
    }

    public function getPaye(){
        return $this->_paye;
    }
}

