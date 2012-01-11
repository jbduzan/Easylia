<?php

class Application_Form_Editcertification extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'edit_form'));
        
        $this->addElement('text', 'nom_edit', array(
            'label' => 'Nom : '
        ));
        
        $this->addElement('text', 'type_edit', array(
            'label' => 'Description :'
        ));
        
        $this->addElement('text', 'nombre_question_edit', array(
            'label' => 'Nombre de question :'
        ));
        
        $this->addElement('text', 'temps_certification_edit', array(
            'label' => 'Temps certification (mn):'
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));  
       
    }


}

