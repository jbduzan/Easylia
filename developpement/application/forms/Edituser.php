<?php

class Application_Form_Edituser extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'edit_form'));
        
        $this->addElement('text', 'nom', array(
            'label' => 'Nom : '
        ));
        
        $this->addElement('text', 'prenom', array(
            'label' => 'Prenom :'
        ));
        
        $this->addElement('text', 'login', array(
            'label' => 'Nom d\'utilisateur'
        ));
        
        $this->addElement('select', 'nom_groupe', array(
            'label' => 'Groupe'
        ));
        
        $this->addElement('text', 'adresse', array(
            'label' => 'Adresse :'
        ));
        
        $this->addElement('text', 'adresse2-edit', array(
            'label' => "Complement d'adresse :"
        ));
        
        $this->addElement('text', 'code_postal', array(
            'label' => "Code postal : "
        ));
        
        $this->addElement('text', 'ville', array(
            'label' => 'ville : '
        ));
        
        $this->addElement('text', 'telephone', array(
            'label' => 'Téléphone :'
        ));
        
        $this->addElement('text', 'mail', array(
            'label' => 'Email :'
        ));
        
        $this->addElement('text', 'date_naissance', array(
            'label' => 'Date de naissance :'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
    }


}

