<?php

class QuestionsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->user = new Zend_Session_Namespace('user');
        $this->certification_liste_mapper = new Application_Model_ListeCertificationMapper();
        $this->question_reponse_mapper = new Application_Model_QuestionsMapper();
        $this->question_certification_mapper = new Application_Model_QuestionsCertificationsMapper();
        $this->reponse_mapper = new Application_Model_ReponsesMapper();
        $this->reponse_certification_mapper = new Application_Model_ReponsesCertificationMapper();
        $this->groupeMapper = new Application_Model_GroupesMapper();            
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();
        $this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe);
    }

    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        // Vérification des permissions
        if(!$this->acl->isAllowed($this->nom_groupe, null, "voir_liste_questions"))
            $this->_redirector->goToSimple('index', 'Utilisateurs');
        
        if($this->acl->isAllowed($this->nom_groupe, null, 'ajouter_question'))
            $this->view->ajouter_question = "true";
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'editer_question'))
            $this->view->editer_question = "true";
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'supprimer_question'))
            $this->view->supprimer_question = "true";
        
        // Formulaire d'ajout de certification
        $this->view->add_form = new Application_Form_Addquestion();
                
        // Formulaire d'édition de certification
        $this->view->edit_form = new Application_Form_Editquestion();
    }

    public function deleteAction()
    {
    	$this->getHelper('layout')->disableLayout();
    	
        // Supprime une question
        $request = $this->getRequest();
        if($request->isPost()){
        	 // Si c'est une question de motivation
            if($request->getParam('motivation') ==1){
            	$this->question_reponse_mapper->delete($request->getParam('id_question'));
            }else{
            	$this->question_certification_mapper->deleteWithIdQuestion($request->getParam('id_question'));
            
         		// Supprime les réponses associées
	            $reponse = $this->reponse_mapper->fetchAllWithId($request->getParam('id_question'));
	            
	            foreach($reponse as $row){
	            	$this->reponse_mapper->delete($row->getIdReponse());
	            }            
            }
        }    
    
    }

    public function ajouterAction()
    {
        // Ajoute une question
        
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
        if($request->isPost()){
        
        	if($request->getParam('motivation') == 1){
        		// Si c'est une question de motivation
        		
        		$question = new Application_Model_Questions();
        		$question->setQuestion($request->getParam('question'))
        				 ->setMotivation(1);
        				 
        		$this->question_reponse_mapper->save($question);
        	}else{
        		// On ajoute dabord la question dans la base
        	
	        	if($request->getParam('reponse_ouverte') == "checked")
	        		$reponse_ouverte = 1;
	        	else
	        		$reponse_ouverte = 0;
	        		
	            $question = new Application_Model_Questions();
	            $question->setQuestion($request->getParam('question'))
	            		 ->setNbrReponse(count($request->getParam('reponse')))
	            		 ->setReponseOuverte($reponse_ouverte);
	            
	            $id_question = $this->question_reponse_mapper->save($question);
	            
	            if($id_question == null)
	            	return;
	            
	            // Ensuite chaque réponse
	            foreach($request->getParam('reponse') as $row){
	           		$row = explode(',', $row); 	
	            	$reponse = new Application_Model_Reponses();
	            	
	            	if($row[1] == "checked")
	            		$checked = 1;
	            	else
	            		$checked = 0;
	            	
	            	$reponse->setReponse($row[0])
	            			->setIdQuestion($id_question)
	            			->setEstJuste($checked);
	            	
	            	$this->reponse_mapper->save($reponse);
	            }
	            
	            // Et on lie le tout à la certification
	            if($request->getParam('question_obligatoire') == "checked")
	            	$question_obligatoire = 1;
	            else
	            	$question_obligatoire = 0;
	                        	
	            $question_certification = new Application_Model_QuestionsCertifications();
	            $question_certification->setIdQuestion($id_question)
	            					   ->setIdCertification($request->getParam('id_certification'))
	            					   ->setQuestionObligatoire($question_obligatoire);
	            					   
				$this->question_certification_mapper->save($question_certification);
        	}
        }        
    }

    public function editerAction()
    {
        $this->getHelper('layout')->disableLayout();
        // Edite une question
        $request = $this->getRequest();
        echo "toto";
        if($request->isPost()){
            $question = new Application_Model_Questions();
            $certification = new Application_Model_QuestionsCertifications();
          
            $this->question_reponse_mapper->find($request->getParam('id_question'), $question);
            
            if($request->getParam('motivation') == 1){
            	// Si c'est une question de motivation
            	$question->setQuestion($request->getParam('question'));
            }else{
            	$question->setQuestion($request->getParam('question'));
	            $certification->setQuestionObligatoire($request->getParam('question_obligatoire'))
                              ->setIdQuestion($request->getParam('id_question'))
                              ->setIdCertification($request->getParam('certification_demande'));
            }
            
            $this->question_certification_mapper->save($certification);
            $this->question_reponse_mapper->save($question);
        }
        
    }

    public function getlistenomformateAction()
    {
        // Renvoie la liste des question qui ne sont pas encore dans la certification
        
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
        
        if($request->isPost()){
            // Récupère la liste des questions de la certification
            $questions = $this->question_certification_mapper->fetchAllWithId($request->getParam('id_certification'));
            $liste = '';
            $question_exclure = array();
            
            // Exclue celle qui sont déjà présente dans la certifications
            foreach($questions as $question){
                $question_exclure[] = $question->getIdQuestionReponse();
            }
            
            $question_reponses = $this->question_reponse_mapper->fetchAll();
            
            // Et ajoute celle qui ne le sont pas dans la liste pour le select
            foreach($question_reponses as $question_reponse){
                if(!in_array($question_reponse->getIdQuestionReponse(), $question_exclure))
                    $liste .= "<option value=".$question_reponse->getIdQuestionReponse().">".$question_reponse->getQuestion()."</option>";                        
            }

            $this->view->liste = $liste;
        }
        
    }

    public function getlistequestionAction()
    {
        // Retourne la liste des questions au format flexigrid
        
        $this->getHelper('layout')->disableLayout();
        
        // Retourne les questions/reponses au format flexigrid
        $request = $this->getRequest();
        
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_question'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
                
        $this->view->row = $this->question_reponse_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function detailsquestionAction(){
        // Affiche la liste des réponse d'une questions
        $this->view->id_demande = $this->getRequest()->getParam('id');
        
        $this->view->add_form = new Application_Form_Addreponse();
        
        $this->view->edit_form = new Application_Form_Editreponse();
    }
    
    public function listequestionmotivationAction(){
    	// Affichage du tableaux liste des questions de motivation
    }
    
    public function getquestionmotivationAction(){
    	// Retourne la liste des questions de motivation pour flexigrid
    	
    	$this->getHelper('layout')->disableLayout();
        
        // Retourne les questions/reponses au format flexigrid
        $request = $this->getRequest();
        
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_question'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
                
        $this->view->row = $this->question_reponse_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit, 1);

    }
    
    public function reponsetestmotivationAction(){
    	// Affiche les réponses du test de motivation pour un formateur
    	$request = $this->getRequest();
    	
    	if($request->getParam('id') == "")
    		$this->_redirector->goToSimple('index', 'formateurs-à-valider');
    		
    	// On récupère toutes les réponses de l'utilisateur
		$result = $this->reponse_certification_mapper->fetchAll($id_utilisateur = $request->getParam('id'));
		
		$reponse = "<dl id='reponses'>";
		
		// Que on associe avec les questions 
		foreach($result as $row){
			$question = new Application_Model_Questions();
			$this->question_reponse_mapper->find($row->getIdQuestion(), $question);
			$reponse .= "<dt class='question_motivation'>".$question->getQuestion()."</dt>";
			$reponse .= "<dd class='reponse_motivation'>".$row->getReponse()."</dt>";			
		}
		
		$reponse .= "</dl>";
		
		$this->view->reponse = $reponse;
		
		// On récupère le nom de l'utilisateur
		$utilisateur = new Application_Model_Utilisateurs();
		$this->utilisateur_mapper->find($request->getParam('id'), $utilisateur);
		
		$this->view->utilisateur = $utilisateur->getNom()." ".$utilisateur->getPrenom();
    }
    
}















