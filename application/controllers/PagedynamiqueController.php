<?php

class PagedynamiqueController extends Zend_Controller_Action
{

    protected $_redirector = null;

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');      
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();             
        $this->page_dynamique_mapper = new Application_Model_PageDynamiqueMapper();        
    }
    
    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction(){
        // On vérifie si l'utilisateur est loggé et si il est admin
        if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 1)
            $this->_redirector->goToUrl('/profil-utilisateur');
    }

    public function getpagedynamiqueAction(){
        // Renvoie la liste de toutes les pages au format flexigrid

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        // renvoie les donnée à la grid sous le bon format
        $request = $this->getRequest();
                
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_page_dynamique'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_groupe = "";
                
        echo $this->page_dynamique_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function ajouterAction(){
        // On ajoute une page dans la bdd
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        $page = new Application_Model_PageDynamique();

        $page->set('nom', $request->getParam('nom'))
             ->set('description', $request->getParam('description'))
             ->set('url', $request->getParam('url'))
             ->set('contenu', $request->getParam('contenu'));
    
        $this->page_dynamique_mapper->save($page);
    }

    public function editerAction(){
        // Modifie une page
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        // On récupère l'enregistrement à modifier
        $page = new Application_Model_PageDynamique();
        $this->page_dynamique_mapper->find($request->getParam('id'), $page);

        $page->set('nom', $request->getParam('nom'))
             ->set('description', $request->getParam('description'))
             ->set('url', $request->getParam('url'))
             ->set('contenu', $request->getParam('contenu'));
    
        $this->page_dynamique_mapper->save($page);
    }

    public function deleteAction(){
        // Supprime une page
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        $this->page_dynamique_mapper->delete($request->getParam('id'));
    }
}



