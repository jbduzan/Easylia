<?php

class Application_Form_Addcertification extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'add_form'));
        
        $this->addElement('text', 'nom', array(
            'label' => 'Nom : '
        ));
        
        $this->addElement('text', 'type', array(
            'label' => 'Description :'
        ));
        
        $this->addElement('text', 'nombre_question', array(
            'label' => 'Nombre de question :'
        ));
        
        $this->addElement('text', 'temps_certification', array(
            'label' => 'Temps certification (mn) :'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));    
    }

}

