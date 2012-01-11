<?php

class LogininterditController extends Zend_Controller_Action
{

    protected $_redirector = null;

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
    	$this->login_mapper = new Application_Model_LoginInterditMapper();   	
    	$this->user = new Zend_Session_Namespace('user');      
        $this->userMapper = new Application_Model_UtilisateursMapper();             
        $this->groupeMapper = new Application_Model_GroupesMapper();
		$this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe); 
    }
    
    public function indexAction(){
    	// On vérifie si l'utilisateur est loggué
        if(!empty($this->user->is_logged) && $this->user->is_logged === true){   
			if($this->acl->isAllowed($this->nom_groupe, null, 'ajouter_login_interdit'))
				$this->view->ajouter_login = 'true';
				
			if($this->acl->isAllowed($this->nom_groupe, null, 'modifier_login_interdit'))
				$this->view->modifier_login = 'true';
				
			if($this->acl->isAllowed($this->nom_groupe, null, 'supprimer_login_interdit'))
				$this->view->supprimer_login = 'true';   
		}else
			$this->_redirector->goToSimple('connexion', 'utilisateurs');
    }
    
    public function getlisteloginAction(){
   		$this->getHelper('layout')->disableLayout();
        
        // renvoie les donnée à la grid sous le bon format
        $request = $this->getRequest();
                
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_utilisateur'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        
        // Récupération de la liste des logins        
        $this->view->rows = $this->login_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);

    }
    
	public function addAction(){
		$this->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		$login = new Application_Model_LoginInterdit();
		
		$login->setLogin($request->getParam('login'));
		
		$this->login_mapper->save($login);
	}
	
	public function deleteAction(){
		$this->getHelper('layout')->disableLayout();
	
		$request = $this->getRequest();
		
		$this->login_mapper->delete($request->getParam('id'));
	}
	
	public function editAction(){
		$this->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		$login = new Application_Model_LoginInterdit();
		
		$login->setIdLoginInterdit($request->getParam('id'))
			  ->setLogin($request->getParam('login'));
			  
		$this->login_mapper->save($login);
	}

}

?>