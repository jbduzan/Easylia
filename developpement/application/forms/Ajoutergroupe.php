<?php

class Application_Form_Ajoutergroupe extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'add_form'));
        
        $this->addElement('text', 'nom_groupe_add', array(
            'label' => 'Nom : '
        ));
        
        $this->addElement('text', 'description_groupe', array(
            'label' => 'Description :'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));
    }


}

