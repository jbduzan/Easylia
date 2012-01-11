<?php

class Application_Form_InscriptionClient extends Zend_Form
{

    public function init()
    {
        
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'inscription_form'));
        
        $this->addElement('text', 'nom', array(
            'label' => ' * Nom: '
            
        ));
        
        $this->addElement('text', 'prenom', array(
            'label' => ' * Prenom: '
            
        ));
        
        $this->addElement('text', 'login', array(
            'label' => " * Nom d'utilisateur : "
            
        ));
        
        $this->addElement('password', 'password', array(
            'label' => ' * Mot de passe'
        ));
        
        $this->addElement('password', 'password_conf', array(
        	'label' => '* Confirmez le mot de passe'
        ));
        
        $this->addElement('text', 'adresse', array(
            'label' => ' * Adresse : '
            
        ));
        
        $this->addElement('text', 'adresse2', array(
            'label' => " Complément d'adresse : "
            
        ));
        
        $this->addElement('text', 'codePostal', array(
            'label' => ' * Code postal : '
            
        ));
        
        $this->addElement('text', 'ville', array(
            'label' => ' * Ville : '
            
        ));
        
        $this->addElement('text', 'telephone', array(
            'label' => ' * Téléphone : '
            
        ));
                
        $this->addElement('text', 'mail', array(
            'label' => ' * Adresse E-mail : '
            
        ));
        
        $this->addElement('text', 'dateNaissance', array(
            'label' => ' * Date de naissance'
            
        ));
        
        $this->addElement('text', 'departementNaissance', array(
            'label' => ' * Département de naissance'
            
        ));
        
        $this->addElement('text', 'paysNaissance', array(
            'label' => ' * Pays de naissance'
            
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Valider'
        ));
                
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
        
    }


}

