<?php

class Application_Form_Changepassword extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'change_password'));
        
        $this->addElement('password', 'old', array(
            'label' => 'Ancien mot de passe : '
        ));
        
        $this->addElement('password', 'new', array(
            'label' => 'Nouveau mot de passe :'
        ));
        
        $this->addElement('password', 'confirmation', array(
            'label' => 'Confirmer le nouveau mot de passe :'
        ));
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Valider'
        ));
        
        $this->addElement('submit', 'cancel', array(
            'label' => 'Annuler'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
    }
    


}

