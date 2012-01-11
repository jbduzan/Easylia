<?php

class Application_Form_Addreponse extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'add_reponse_form'));
        
        $this->addElement('text', 'reponse_add', array(
            'label' => 'Réponse : '
        ));
        
        $this->addElement('checkbox', 'est_juste_add', array(
            'label' => 'Réponse juste : '
        ));
            
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));    
    }


}

