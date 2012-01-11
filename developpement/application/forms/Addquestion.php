<?php

class Application_Form_Addquestion extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addAttribs(array('id' => 'add_question_form'));
        
        $this->addElement('text', 'question_add', array(
            'label' => 'Question : '
        ));
            
        // Protection anti CSRF
        $this->addElement('hash', 'csrf', array(
            'ignore' => true
        ));    
    }


}

