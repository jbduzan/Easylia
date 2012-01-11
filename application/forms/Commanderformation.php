<?php

class Application_Form_Commanderformation extends Zend_Form
{

    public function init()
    {
 		/* Form Elements & Other Definitions Here ... */
        
        $this->setMethod("post");
        
        $this->addElement('select', 'select_nbr_heures', array(
            'required' => true, 
            'option' => 1,
            'option' => 2,
            'option' => 3,
            'option' => 4,
            'option' => 5,
            'option' => 6,
            'option' => 7,
            'option' => 8,
            'option' => 9,
            'option' => 10
        ));
        
        $this->addElement('text', 'date', array(
            'required' => true
        ));
        
        $this->addElement('radio', 'date_payement', array(
            'label' => 'Connexion',
            'ignore' => true
        ));
        
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));

        
    }


}

