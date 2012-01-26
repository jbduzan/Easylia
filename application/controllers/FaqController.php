<?php

class FaqController extends Zend_Controller_Action
{

    protected $_redirector = null;

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->user = new Zend_Session_Namespace('user');      
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();             
        $this->groupeMapper = new Application_Model_GroupesMapper();
		$this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe);
		$this->faq_mapper = new Application_Model_FaqMapper();
    }

    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }
    
    public function indexAction(){
    	// Récupère la liste des question actives de la faq et les affiches
    	$result = $this->faq_mapper->fetchAllActive();
    	
    	$faq = "<dl id='faq'>";
    	
    	$i = 0;
    	
    	if($this->user->is_logged){
    		if($this->user->id_groupe == 1)
    			$categorie = "Administrateur";
    		else if($this->user->id_groupe == 2)
    			$categorie = "Formateur";    		
    		else if($this->user->id_groupe != 1 || $this->user->id_groupe != 2)
    			$categorie = "Client";
    	}else
    		$categorie = "Non-connecté";
    	
    	foreach($result as $row){
    		$categories = explode(',', $row->getCategorie());
    		if(in_array($categorie, $categories) || $row->getCategorie() == "Non-connecté"){
	    		$i++;
	    		$faq .= "<dt class='question-faq'>".$i.". ".ucfirst($row->getQuestion())." ?</dt>";
	    		$faq .= "<dd class='reponse-faq'>".ucfirst($row->getReponse())."</dd>";
    		}
    	}
    	
    	$faq .= "</dl>";
    	   	
    	$this->view->faq = $faq;
    }
    
    public function gestionAction(){
    	// Affichage de la pge de gestion des questions de la FAQ
    	
    	// On vérifie si l'utilisateur à les droits
    	if(!$this->acl->isAllowed($this->nom_groupe, null, "voir_liste_faq"))
    		// Sinon on redirige
    		$this->_redirector->goToSimple("index", "utilisateurs");
    		
    	// Véfication des droits
    	if($this->acl->isAllowed($this->nom_groupe, null, 'ajouter_liste_faq'))
    		$this->view->ajouter_faq = "true";
    		
    	if($this->acl->isAllowed($this->nom_groupe, null, 'modifier_liste_faq'))
			$this->view->modifier_faq = "true";
		
		if($this->acl->isAllowed($this->nom_groupe, null, 'supprimer_liste_faq'))
			$this->view->supprimer_faq = "true";
    }
    
    public function getlistequestionAction(){
    	// Retourne la liste des questions/reponses au format flexigrid
    	$this->getHelper('layout')->disableLayout();
                       
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','question'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');

    	
    	$this->view->rows = $this->faq_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }
    
    public function addAction(){
    	$this->getHelper('layout')->disableLayout();
    	$request = $this->getRequest();
    	$faq = new Application_Model_Faq();
    	
    	if($request->getParam('active') == "checked")
    		$active = 1;
    	else
    		$active = 0;
    	
    	$categorie = "";
    	
    	foreach($request->getParam('categorie') as $row){
    		$categorie .= $row.',';
    	}
    	
    	$categorie = substr($categorie, '0', '-1');
    	
    	$faq->setQuestion($request->getParam('question'))
    		->setReponse($request->getParam('reponse'))
    		->setCategorie($categorie)
    		->setActive($active);
    	
    	$this->faq_mapper->save($faq);
    }
    
    public function editAction(){
    	$this->getHelper('layout')->disableLayout();
    	$request = $this->getRequest();
    	
    	$faq = new Application_Model_Faq();
    	$this->faq_mapper->find($request->getParam('id'), $faq);
    	
    	if($request->getParam('active') == "checked")
    		$active = 1;
    	else
    		$active = 0;
    		
    	$categorie = "";
    	
    	foreach($request->getParam('categorie') as $row){
    		$categorie .= $row.',';
    	}
    	
    	$categorie = substr($categorie, '0', '-1');
    	
    	$faq->setQuestion($request->getParam('question'))
    		->setReponse($request->getParam('reponse'))
    		->setCategorie($categorie)
    		->setActive($active);
    		
    	$this->faq_mapper->save($faq);
    }
    
    public function deleteAction(){
	    $this->getHelper('layout')->disableLayout();
    	$this->faq_mapper->delete($this->getRequest()->getParam('id'));
    }
    
}