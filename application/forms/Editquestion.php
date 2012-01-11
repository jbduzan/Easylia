<?php

class Application_Form_Editquestion extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'edit_question_form'));
        
        $this->addElement('text', 'question', array(
            'label' => 'Question : '
        ));

        $this->addElement('text', 'nbr_reponse', array(
            'label' => 'Nombre de réponse'
        ));

        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));    
    }



}

