<?php

/*
    class : Application_Model_utilisateur
    date_création : 16/09/11
    date_update : 
    créateur : jbduzan

*/

class Application_Model_Utilisateurs
{
    
    // Liste des variables

    protected $_id_utilisateur;
    protected $_login;
    protected $_password;
    protected $_nom;
    protected $_prenom;
    protected $_type;
    protected $_titre;
    protected $_adresse;
    protected $_adresse2;
    protected $_codePostal;
    protected $_ville;
    protected $_telephone;
    protected $_mail;
    protected $_numeroSecu;
    protected $_dateNaissance;
    protected $_departementNaissance;
    protected $_paysNaissance;
    protected $_dateEntree;
    protected $_niveau;
    protected $_note;
    protected $_commentaire;
    protected $_id_groupe;
    protected $_document_envoye;
    protected $_document_valide;
    protected $_cle_activation;
    protected $_profil_actif;
    protected $_test_motivation;
    protected $_adresse_skype;
    protected $_date_entretien_skype;
    protected $_disponibilite_entretien;
    
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
    
    public function setIdUtilisateur($id_utilisateur){
        
        $this->_id_utilisateur = $id_utilisateur;
        return $this;
        
    }
    
    public function getIdUtilisateur(){
        
        return $this->_id_utilisateur;
        
    }
    
    public function setLogin($login){
        
        $this->_login = (string) $login;
        return $this;
        
    }
    
    public function getLogin(){
        
        return $this->_login;
        
    }
    
    public function setPassword($password){
        
        $this->_password = (string) $password;
        return $this;
        
    }
    
    public function getPassword(){
        
        return $this->_password;
        
    }

    public function setNom($nom){
        
        $this->_nom = (string) $nom;
        return $this;
        
    }
    
    public function getNom(){
        
        return $this->_nom;
        
    }
    
    public function setPrenom($prenom){
        
        $this->_prenom = (string) $prenom;
        return $this;
        
    }
    
    public function getPrenom(){
        
        return $this->_prenom;
        
    }
    
    public function setType($type){
        
        $this->_type = (string) $type;
        return $this;
        
    }
    
    public function getType(){
        
        return $this->_type;
        
    }
    
    public function setTitre($titre){
        
        $this->_titre = (string) $titre;
        return $this;
        
    }
    
    public function getTitre(){
        
        return $this->_titre;
        
    }
    
    public function setAdresse($adresse){
        
        $this->_adresse = (string) $adresse;
        return $this;
        
    }
    
    public function getAdresse(){
        
        return $this->_adresse;
        
    }
    
    public function setAdresse2($adresse2){
        
        $this->_adresse2 = (string) $adresse2;
        return $this;
        
    }
    
    public function getAdresse2(){
        
        return $this->_adresse2;
        
    }
    
    public function setCodePostal($code_postal){
        
        $this->_codePostal = (string) $code_postal;
        return $this;
        
    }
    
    public function getCodePostal(){
        
        return $this->_codePostal;
        
    }
    
    public function setVille($ville){
        
        $this->_ville = (string) $ville;
        return $this;
                
    }
    
    public function getVille(){
        
        return $this->_ville;
        
    }
    
    public function setTelephone($telephone){
        
        $this->_telephone = (string) $telephone;
        return $this;
        
    }
    
    public function getTelephone(){
        
        return $this->_telephone;
        
    }
    
    public function setMail($mail){
        
        $this->_mail = (string) $mail;
        return $this;
        
    }
    
    public function getMail(){
        
        return $this->_mail;
        
    }
    
    public function setNumeroSecu($numero_secu){
        
        $this->_numeroSecu = $numero_secu;
        return $this;
        
    }
    
    public function getNumeroSecu(){
        
        return $this->_numeroSecu;
        
    }
    
    public function setDateNaissance($date_naissance){
        
        $this->_dateNaissance = $date_naissance;
        return $this;
        
    }
    
    public function getDateNaissance(){
        
        return $this->_dateNaissance;
        
    }
    
    public function setDepartementNaissance($departement_naissance){
        
        $this->_departementNaissance = $departement_naissance;
        return $this;
        
    }
    
    public function getDepartementNaissance(){
        
        return $this->_departementNaissance;
        
    }
    
    public function setPaysNaissance($pays_naissance){
        
        $this->_paysNaissance = (string) $pays_naissance;
        return $this;
        
    }
    
    public function getPaysNaissance(){
        
        return $this->_paysNaissance;
        
    }
    
    public function setDateEntree($date_entree){
        
        $this->_dateEntree = $date_entree;
        return $this;
        
    }
    
    public function getDateEntree(){
        
        return $this->_dateEntree;
        
    }
    
    public function setNiveau($niveau){
        
        $this->_niveau = $niveau;
        return $this;
        
    }
    
    public function getNiveau(){
        
        return $this->_niveau;
        
    }
    
    public function setNote($note){
        
        $this->_note = (string) $note;
        return $this;
        
    }
    
    public function getNote(){
        
        return $this->_note;
        
    }
    
    public function setCommentaire($commentaire){
        
        $this->_commentaire = (string) $commentaire;
        return $this;
        
    }
    
    public function getCommentaire(){
        
        return $this->_commentaire;
        
    }
    
    public function setIdGroupe($id_groupe){
        
        $this->_id_groupe = $id_groupe;
        return $this;
        
    }
    
    public function getIdGroupe(){
        
        return $this->_id_groupe;
        
    }
    
    public function setDocumentEnvoye($document_envoye){
    	$this->_document_envoye = $document_envoye;
    	return $this;
    }
    
    public function getDocumentEnvoye(){
    	return $this->_document_envoye;
    }
    
    public function setDocumentValide($document_valide){
    	$this->_document_valide = $document_valide;
    	return $this;
    }
    
    public function getDocumentValide(){
    	return $this->_document_valide;
    }
    
    public function setCleActivation($cle_activation){
    	$this->_cle_activation = $cle_activation;
    	return $this;
    }

	public function getCleActivation(){
		return $this->_cle_activation;
	}
	
	public function setProfilActif($profil_actif){
		$this->_profil_actif = $profil_actif;
		return $this;
	}
	
	public function getProfilActif(){
		return $this->_profil_actif;
	}
	
	public function setTestMotivation($test_motivation){
		$this->_test_motivation = $test_motivation;
		return $this;
	}
	
	public function getTestMotivation(){
		return $this->_test_motivation;
	}
	
	public function setAdresseSkype($adresse_skype){
		$this->_adresse_skype = (string) $adresse_skype;
		return $this;
	}
	
	public function getAdresseSkype(){
		return $this->_adresse_skype;
	}
	
	public function setDateEntretienSkype($date_entretien){
		$this->_date_entretien_skype = $date_entretien;
		return $this;
	}
	
	public function getDateEntretienSkype(){
		return $this->_date_entretien_skype;
	}
	
	public function setDisponibiliteEntretien($disponibilite_entretien){
		$this->_disponibilite_entretien = $disponibilite_entretien;
		return $this;
	}
	
	public function getDisponibiliteEntretien(){
		return $this->_disponibilite_entretien;
	}
}	

