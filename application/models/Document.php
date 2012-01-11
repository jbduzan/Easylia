<?php

class Application_Model_Document
{
	protected $_id_document;
	protected $_titre;
	protected $_type;
	protected $_chemin;
	protected $_date_upload;
	protected $_date_validite;
	protected $_id_utilisateur;
	protected $_id_facture;

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

	public function setIdDocument($id_document){
		$this->_id_document = $id_document;
		return $this;
	}
	
	public function getIdDocument(){
		return $this->_id_document;
	}
	
	public function setTitre($titre){
		$this->_titre = $titre;
		return $this;
	}
	
	public function getTitre(){
		return $this->_titre;
	}
	
	public function setType($type){
		$this->_type = $type;
		return $this;
	}
	
	public function getType(){
		return $this->_type;
	}
	
	public function setChemin($chemin){
		$this->_chemin = $chemin;
		return $this;
	}
	
	public function getChemin(){
		return $this->_chemin;
	}
	
	public function setDateUpload($date_upload){
		$this->_date_upload = $date_upload;
		return $this;
	}
	
	public function getDateUpload(){
		return $this->_date_upload;
	}
	
	public function setDateValidite($date_validite){
		$this->_date_validite = $date_validite;
		return $this;
	}
	
	public function getDateValidite(){
		return $this->_date_validite;
	}
	
	public function setIdUtilisateur($id_utilisateur){
		$this->_id_utilisateur = $id_utilisateur;
		return $this;
	}
	
	public function getIdUtilisateur(){
		return $this->_id_utilisateur;
	}
	
	public function setIdFacture($id_facture){
		$this->_id_facture = $id_facture;
		return $this;
	}
	
	public function getIdFacture(){
		return $this->_id_facture;
	}

}

