<?php

class Application_Model_ListeCertification
{
    protected $_id_certification;
    protected $_nom;
    protected $_type;
    protected $_nombre_question;
    protected $_temps_certification;
    
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
    
    public function setIdCertification($id_certification){
        $this->_id_certification = $id_certification;
        return $this;
    }
    
    public function getIdCertification(){
        return $this->_id_certification;
    }
    
    public function setNom($nom){
        $this->_nom = (string) $nom;
        return $this;
    }
    
    public function getNom(){
        return $this->_nom;
    }
    
    public function setType($type){
        $this->_type = $type;
        return $this;
    }
    
    public function getType(){
        return $this->_type;
    }
    
    public function setNombreQuestion($nombre_question){
        $this->_nombre_question = $nombre_question;
        return $this;
    }
    
    public function getNombreQuestion(){
        return $this->_nombre_question;
    }
    
    public function setTempsCertification($temps_certification){
        $this->_temps_certification = $temps_certification;
        return $this;
    }
    
    public function getTempsCertification(){
        return $this->_temps_certification;
    }
    

}

