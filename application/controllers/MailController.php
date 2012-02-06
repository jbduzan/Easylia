<?php

class MailController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');      
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();             
        $this->mail_mapper = new Application_Model_MailMapper();
    }

    public function preDispatch(){
        $this->view->render('utilisateurs/menu-connecte.phtml');
        $this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        // On vérifie que l'utilisateur est loggué est que il est admin
        if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 1)
            $this->_redirector->goToUrl('/profil-utilisateur');
    }

    public function getmailAction(){
        // Renvoie la liste des mail au format flexigrid

        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
                
        // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_mail'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');

        echo $this->mail_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
    }

    public function editerAction(){
        // Edit un mail à partir d'une requete ajax
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        $mail = new Application_Model_Mail();
        $this->mail_mapper->find($request->getParam('idMail'), $mail);

        $proprietes = array_slice($request->getParams(), 3);
        
        // On les injecte à l'utilisateur
        foreach($proprietes as $key=>$value){
            // On saute l'id 
            if($key == "idMail")
                continue;

            // Si la value est un array
            if(is_array($value)){
                $temp = "";
                foreach($value as $row){
                    $temp .= $row.",";
                }
                $temp = substr($temp, '0', '-1');
                $mail->__set($key,$temp);
                continue;
            }
            $mail->__set($key,$value);
        }
        
        // Une fois que on a fait toute les modifications, on sauvegarde
        $this->mail_mapper->save($mail);
    }
}

?>