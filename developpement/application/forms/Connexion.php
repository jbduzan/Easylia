<?php

class Application_Form_Connexion extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
        $this->setMethod("post");
        
        $this->addElement('text', 'login', array(
            'label' => "Nom d'utilisateur : ",
            'required' => true
        ));
        
        $this->addElement('password', 'password', array(
            'label' => 'Mot de passe : ',
            'required' => true
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Connexion',
            'ignore' => true
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
        
    }


}

