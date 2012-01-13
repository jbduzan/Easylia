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
    
    public function autocompletequestionAction(){
		$this->_helper->layout->disableLayout();
        
        $request = $this->getRequest();
                
        $mapper = new Application_Model_QuestionsMapper();
        
        // On initialise les deux variables
        $id = null;
        $question = null;
        
        if($request->getParam('id') != null){
        	$id = $request->getParam('id');
        	$question = null;
        }
        
        if($request->getParam('question') != null){
        	$id = null;
        	$question = $request->getParam('question');
        }
        
        // On récupère les questions déjà présentes dans la certification pour les exclure plus tard
        $certification_mapper = new Application_Model_QuestionsCertificationsMapper();
        
        $list_certification = $certification_mapper->fetchAllWithId($request->getParam('id_certification'));
        
        // On effectue la requete pour récupérer les résultats     
        $list = $mapper->autoComplete($id, $question, $list_certification);
                
     	$this->view->list = $list;
    }


}

