<?php

class AutocompleteController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        
        $request = $this->getRequest();
                
        $mapper = new Application_Model_CpAutoCompleteMapper();
        $code_postal = null;
        $ville = null;
        if($request->getPost('codePostal') != null){
            $code_postal = $request->getPost('codePostal');
            $ville = null;
        }
        if($request->getPost('ville') != null){
            $ville = $request->getPost('ville');
            $code_postal = null;
        }
        
        $list = array();
        
        if($code_postal != null && $ville == null){
            $list = $mapper->autoComplete($code_postal, $ville);
        }else if($code_postal == null && $ville != null){
            $list = $mapper->autoComplete($code_postal, $ville);
        }        
        
        $this->view->list = $list;
                                
    }


}

