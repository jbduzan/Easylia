<?php

class Application_Form_Uploaddocument extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'upload_document'));

        $this->addElement('file', 'file', array(
            'label' => 'Votre document : '
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

