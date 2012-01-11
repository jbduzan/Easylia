<?php

class Application_Form_Modifierinfo extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'modification_form'));
        
        $this->addElement('text', 'nom', array(
            'label' => 'Nom : '
        ));
        
        $this->addElement('text', 'prenom', array(
            'label' => 'Prenom :'
        ));
                
        $this->addElement('text', 'adresse', array(
            'label' => 'Adresse :'
        ));
        
        $this->addElement('text', 'adresse2', array(
            'label' => "Complement d'adresse :"
        ));
        
        $this->addElement('text', 'codePostal', array(
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
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Valider'
        ));
        
        $this->addElement('submit', 'annuler', array(
            'label' => 'Annuler'
        ));

        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));

    }


}

