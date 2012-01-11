<?php

class Application_Form_Editreponse extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'edit_reponse_form'));
        
        $this->addElement('text', 'reponse', array(
            'label' => 'Réponse : '
        ));
        
        $this->addElement('checkbox', 'est_juste', array(
            'label' => 'Réponse juste : '
        ));
            
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));    
    }

}

