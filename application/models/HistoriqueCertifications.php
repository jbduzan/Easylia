<?php

class Application_Model_HistoriqueCertifications
{
    protected $_id_historique;
    protected $_date_passage;
    protected $_date_validite;
    protected $_score;
    protected $_id_utilisateur;
    protected $_id_certification;
    
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
    
    public function setIdHistorique($id_historique){
        $this->_id_historique = $id_historique;
        return $this;
    }
    
    public function getIdHistorique(){
        return $this->_id_historique;
    }
    
    public function setDatePassage($date_passage){
        $this->_date_passage = $date_passage;
        return $this;
    }
    
    public function getDatePassage(){
        return $this->_date_passage;
    }
    
    public function setDateValidite($date_validite){
        $this->_date_validite = $date_validite;
        return $this;
    }
    
    public function getDateValidite(){
        return $this->_date_validite;
    }
    
    public function setScore($score){
        $this->_score = $score;
        return $this;
    }
    
    public function getScore(){
        return $this->_score;
    }
    
    public function setIdUtilisateur($id_utilisateur){
        $this->_id_utilisateur = $id_utilisateur;
        return $this;
    }
    
    public function getIdUtilisateur(){
        return $this->_id_utilisateur;
    }
    
    public function setIdCertification($id_certification){
        $this->_id_certification = $id_certification;
        return $this;
    }
    
    public function getIdCertification(){
        return $this->_id_certification;
    }

}

