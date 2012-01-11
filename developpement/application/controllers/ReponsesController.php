<?php

class ReponsesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->user = new Zend_Session_Namespace('user');
        $this->reponse_mapper = new Application_Model_ReponsesMapper();
        $this->groupeMapper = new Application_Model_GroupesMapper();            
        $this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe);    
    }

    public function indexAction()
    {
        // action body
    }

    public function getlistereponseAction()
    {
        // Retourne la liste des réponses à une question
        
        $this->getHelper('layout')->disableLayout();
        
        // renvoie les donnée à la grid sous le bon format
        $request = $this->getRequest();
                
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_reponse'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_question = $request->getParam('id');
                        
        $this->view->data = $this->reponse_mapper->fetchAllForFlexigridWithIdQuestion($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $id_question);
    }

    public function ajouterreponseAction()
    {
        // Ajoute une reponse à une question
        
        $request = $this->getRequest();
        
        if($request->isPost()){
            $reponse = new Application_Model_Reponses();
        
            $reponse->setReponse($request->getParam('reponse'))
                    ->setEstJuste($request->getParam('est_juste'))
                    ->setIdQuestion($request->getParam('id_question'));
                
                $this->reponse_mapper->save($reponse);
        }
    }

    public function deleteAction()
    {
        // Supprime une réponse
        if($this->getRequest()->isPost()){
            $this->reponse_mapper->delete($this->getRequest()->getParam('id_reponse'));
        }
    }

    public function editerAction()
    {
        // edite une réponse
        
        $request = $this->getRequest();
        
        if($request->isPost()){
            $reponse = new Application_Model_Reponses();
            
            $this->reponse_mapper->find($request->getParam('id_reponse'), $reponse);
            
            $reponse->setReponse($request->getParam('reponse'))
                    ->setEstJuste($request->getParam('est_juste'))
                    ->setIdQuestion($request->getParam('id_question'));
                    
            $this->reponse_mapper->save($reponse);
        }
    }


}









