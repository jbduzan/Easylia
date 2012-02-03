<?php

class GroupeController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->user = new Zend_Session_Namespace('user');
        $this->groupeMapper = new Application_Model_GroupesMapper();
    }
    
    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        // On vérifie si l'utilisateur à le droit d'être la
        $nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe);
        
        // Sinon on le redirige
        if(!$this->acl->isAllowed($nom_groupe, null, 'voir_liste_groupe'))
            $this->_redirector->goToSimple('index', 'Utilisateurs');
            
        // On voit si l'utilisateur peut ajouter, editer et supprimer
        if($this->acl->isAllowed($nom_groupe, null, 'editer_groupe'))
            $this->view->editer_groupe = "true";
            
        if($this->acl->isAllowed($nom_groupe, null, 'supprimer_groupe'))
            $this->view->supprimer_groupe = "true";
            
        if($this->acl->isAllowed($nom_groupe, null, 'ajouter_groupe'))
            $this->view->ajouter_groupe = "true";
        
        if($this->acl->isAllowed($nom_groupe, null, 'ajouter_utilisateur_groupe'))
            $this->view->ajouter_utilisateur_groupe = "true";
            
        $form_edit = new Application_Form_Editgroupe();
                
        $request = $this->getRequest();
        
        // Si le formulaire est posté on édit l'utilisateur
        if($request->isPost()){
            $groupe = new Application_Model_Groupes();
                        
            $this->groupeMapper->find($request->getParam('id_groupe'), $groupe);
            
            $groupe->setNom($request->getParam('nom'))
                   ->setDescription($request->getParam('description'));
                        
            $this->groupeMapper->save($groupe);
            
        }    
        $this->view->form = $form_edit;
        
        $form_ajout = new Application_Form_Ajoutergroupe();
        $this->view->form_ajout = $form_ajout;
    }

    public function getlistegroupeAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
        
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_groupe'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        
        // Récupération de la liste des utilisateurs
        $mapper = new Application_Model_GroupesMapper();
        
        $this->view->rows = $mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function getnomgroupeAction()
    {
        if($this->getRequest()->isPost()){
            $this->getHelper('layout')->disableLayout();
            
            $this->view->liste_groupe = $this->groupeMapper->fetchAll();
        }
        
    }

    public function deletegroupeAction()
    {
         $this->getHelper('layout')->disableLayout();

            $request = $this->getRequest();

            if($request->isPost()){
                $id_groupe = $request->getParam('id_groupe');

                $this->groupeMapper->deleteGroupe($id_groupe);
            }
            
    }

    public function ajoututilisateurAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $utilisateur = new Application_Model_Utilisateurs();
            
            $utilisateurMapper = new Application_Model_UtilisateursMapper();
            
            $utilisateurMapper->find($request->getParam('id_utilisateur'), $utilisateur);
            $utilisateur->setIdGroupe($request->getParam('id_groupe'));
            
            $utilisateurMapper->save($utilisateur);
        }
        
    }

    public function ajoutergroupeAction()
    {
        $request = $this->getRequest();
        
        if($request->isPost()){
            $groupe = new Application_Model_Groupes();
            $groupe->setNom($request->getParam('nom'))
                   ->setDescription($request->getParam('description'));
            
            $this->groupeMapper->save($groupe);
        }
        
    }

    public function getlisteautorisationAction()
    {
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
        
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        
        // Récupération de la liste des utilisateurs
        
        $this->view->rows = $this->groupeMapper->fetchAutorisationForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function detailgroupeAction()
    {
        // On vérifie si l'utilisateur à les droits
        $nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe);
        
        if($this->acl->isAllowed($nom_groupe, null, 'retirer_utilisateur_groupe'))
            $this->view->retirer_utilisateur = "true";
            
        if($this->acl->isAllowed($nom_groupe, null, 'retirer_autorisation_groupe'))
            $this->view->retirer_autorisation = "true";
        
        if($this->acl->isAllowed($nom_groupe, null, 'ajouter_utilisateur_groupe'))
            $this->view->ajouter_utilisateur = "true";
        
        if($this->acl->isAllowed($nom_groupe, null, 'ajouter_autorisation_groupe'))
            $this->view->ajouter_autorisation = "true";
                    
        $this->user->id_groupe_detail = $this->getRequest()->getParam('id_groupe');
        $this->view->id_groupe = $this->user->id_groupe_detail;
    }

    public function getlisteutilisateursAction()
    {
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
        $id_groupe = $this->user->id_groupe_detail;

        // Récupération de la liste des utilisateurs
        $mapper = new Application_Model_UtilisateursMapper();

        $this->view->row = $mapper->fetchAllForFlexigridWithIdGroupe($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $id_groupe);
        
    }

    public function getlisteautorisationsAction()
    {
        $this->getHelper('layout')->disableLayout();

        // renvoie les donnée à la grid sous le bon format
        $request = $this->getRequest();

         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_autorisation'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_groupe = $this->user->id_groupe_detail;

        // Récupération de la liste des utilisateurs
        $mapper = new Application_Model_AutorisationMapper();

        $this->view->row = $mapper->fetchAllForFlexigridWithIdGroupe($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $id_groupe);
    }

    public function removeuserfromgroupeAction()
    {
            $this->getHelper('layout')->disableLayout();

            $request = $this->getRequest();

            if($request->isPost()){
                $id_utilisateur = $request->getParam('id_utilisateur');

                $utilisateur = new Application_Model_Utilisateurs();
                $mapper = new Application_Model_UtilisateursMapper();

                $mapper->find($id_utilisateur, $utilisateur);
                
                $utilisateur->setIdGroupe();
                
                $mapper->save($utilisateur);
            }            
    }

    public function removeautorisationfromgroupeAction()
    {
        $this->getHelper('layout')->disableLayout();

        $request = $this->getRequest();

        if($request->isPost()){
            $id_autorisation = $request->getParam('id_autorisation');
            $id_groupe = $request->getParam('id_groupe');

            $mapper = new Application_Model_GroupesPermissionsMapper();

            $mapper->delete($id_autorisation, $id_groupe);
            
        }        
    }

    public function getlistenomautorisationAction()
    {
        $this->getHelper('layout')->disableLayout();

        $request = $this->getRequest();
        
        if($request->isPost()){
            $autorisation_mapper = new Application_Model_AutorisationMapper();
            $id_groupe = $request->getParam('id_groupe');
            
            $gpMapper = new Application_Model_GroupesPermissionsMapper();
            
            $dataGp = $gpMapper->findByIdGroupe($id_groupe);
            
            $gp = array();
            foreach($dataGp as $data){
                $gp[] = $data->getIdAutorisation();
            }
                        
            $data = $autorisation_mapper->fetchAll();
            
            $liste = '';
            
            foreach($data as $autorisation){
                if(!in_array($autorisation->getIdAutorisation(), $gp))
                $liste .= "<option value=".$autorisation->getIdAutorisation().">".utf8_encode($autorisation->getDroitAccorde())."</option>";
            }
        }
         $this->view->liste = $liste;
        
    }

    public function ajoutautorisationAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
            $groupe_permission = new Application_Model_GroupesPermissions();
            
            $gpMapper = new Application_Model_GroupesPermissionsMapper();
            
            $groupe_permission->setIdGroupe($request->getParam('id_groupe'));
            $groupe_permission->setIdAutorisation($request->getParam('id_autorisation'));
            
            $gpMapper->save($groupe_permission);
        }
    }

    public function editautorisationAction(){
        $this->_helper->viewRenderer->setNoRender(true);
        
        $request = $this->getRequest();
        
        $autorisation = new Application_Model_Autorisation();
        $autorisation_mapper= new Application_Model_AutorisationMapper();
        
        $autorisation_mapper->find($request->getParam('id'), $autorisation);
        
        $autorisation->setDroitAccorde($request->getParam('droit_accorde'));
        
        $autorisation_mapper->save($autorisation);    
    }
}



























