<?php

class Application_Form_Editgroupe extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'edit_form'));
        
        $this->addElement('text', 'nom', array(
            'label' => 'Nom : ',
            'disabled' => 'disabled'
        ));
        
        $this->addElement('text', 'description', array(
            'label' => 'Description :'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
    }


}

