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
             
        $list = $mapper->autoComplete($id, $question, $request->getParam('id_certification'));
        
        /*
// On enleve les questions qui sont deja dans la certifications
        $certification_mapper = new Application_Model_QuestionsCertificationsMapper();
        
        $list_certification = $certification_mapper->fetchAllWithId($request->getParam('id_certification'));
        
        $list_finale = array();
        
		foreach($list as $row){
			if(!in_array($row->id_question, $list_certification)){
				$list_finale['id_question'] = $row->id_question;
				$list_finale['question'] = $row->question;
			}
			
		}
        	
        $this->view->list = $list_finale;
*/
     	$this->view->list = $list;
    }


}

