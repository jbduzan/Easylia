<?php

class InscriptionController extends Zend_Controller_Action
{

    protected $_redirector = null;

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->user = new Zend_Session_Namespace('user');      
        $this->userMapper = new Application_Model_UtilisateursMapper();             
        $this->groupeMapper = new Application_Model_GroupesMapper();
	$this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->user->id_groupe); 
	$this->document_mapper = new Application_Model_DocumentMapper();
	$this->certification_mapper = new Application_Model_ListeCertificationMapper();
        $this->mail_mapper = new Application_Model_MailMapper();
    }
    	
public function indexAction(){
		// Inscription des formateurs
        
        $request = $this->getRequest();
        
        if($this->getRequest()->isPost()){
            if($request->getPost()){
                $client = new Application_Model_Utilisateurs($request->getPost());

                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'production');
                
                $password = sha1($config->salt.$request->getPost('password'));
                
                $client->setPassword($password);
                
                $client->setDateEntree(date("d/m/Y"));
                
                // On l'insère dans le groupe des formateurs non approuvé
                $client->setIdGroupe(3);
                                
                // Clé d'activation du compte à envoyer par mail;
                $cle_activation = substr(sha1(microtime(NULL)*100000),0,30);
                
                $client->setCleActivation($cle_activation);
                
                $id = $this->userMapper->save($client);

                // On récupère les informations du mail à envoyer
                $mail_bdd = new Application_Model_Mail();
                $this->mail_mapper->find(1, $mail_bdd);
                $contenu = $mail_bdd->getContenu();
                $contenu = str_replace('{ID}', $id, $contenu);
                $contenu = str_replace('{KEY}', $client->getCleActivation(), $contenu);

                $mail = new Zend_Mail();
                $mail->setFrom('no-reply@easylia.com', 'Easylia');
                $mail->addTo($client->getMail());
                $mail->setSubject($mail_bdd->getSujet());
                $mail->setBodyHtml(utf8_decode($contenu));
                $mail->send();
                                        
                return $this->_redirector->goToUrl('/confirmation-inscription?id='.$id);
            }
        }
}
	
        public function confirmationAction(){
                // Récupère l'adresse mail de l'utilisateur
        	$utilisateur = new Application_Model_Utilisateurs();
        	$this->userMapper->find($this->getRequest()->getParam('id'), $utilisateur);
        	
        	$this->view->email = $utilisateur->getMail();
                $this->view->id_utilisateur = $utilisateur->getIdUtilisateur();
        }

    public function renvoiemailactivationAction(){
        // Renvoie le mail d'activation
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->getParam('id');
        $client = new Application_Model_Utilisateurs();
        $this->userMapper->find($id, $client);

        // On récupère les informations du mail à envoyer
        $mail_bdd = new Application_Model_Mail();
        $this->mail_mapper->find(3, $mail_bdd);
        $contenu = $mail_bdd->getContenu();
        $contenu = str_replace('{ID}', $id, $contenu);
        $contenu = str_replace('{KEY}', $client->getCleActivation(), $contenu);

        $mail = new Zend_Mail();
        $mail->setFrom('no-reply@easylia.com', 'Easylia');
        $mail->addTo($client->getMail());
        $mail->setSubject($mail_bdd->getSujet());
        $mail->setBodyHtml(utf8_decode($contenu));
        $mail->send();
    }

    public function inscriptionclientAction(){
        // Inscription des clients
        
        $request = $this->getRequest();
        
        if($this->getRequest()->isPost()){
            if($request->getPost()){
                $client = new Application_Model_Utilisateurs($request->getPost());

                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'production');
                
                $password = sha1($config->salt.$request->getPost('password'));
                
                $client->setPassword($password);
                
                $client->setDateEntree(date("d/m/Y"));
                
                // On l'insère dans le groupe des clients
                $client->setIdGroupe(5);
                                
                // Clé d'activation du compte à envoyer par mail;
                $cle_activation = substr(sha1(microtime(NULL)*100000),0,30);
                
                $client->setCleActivation($cle_activation);
                
                $id = $this->userMapper->save($client);

                       // On récupère les informations du mail à envoyer
                $mail_bdd = new Application_Model_Mail();
                $this->mail_mapper->find(, $mail_bdd);
                $contenu = $mail_bdd->getContenu();
                $contenu = str_replace('{ID}', $id, $contenu);
                $contenu = str_replace('{KEY}', $client->getCleActivation(), $contenu);

                $mail = new Zend_Mail();
                $mail->setFrom('no-reply@easylia.com', 'Easylia');
                $mail->addTo($client->getMail());
                $mail->setSubject($mail_bdd->getSujet());
                $mail->setBodyHtml(utf8_decode($contenu));
                $mail->send();

                                                             
                return $this->_redirector->goToUrl('/confirmation-inscription?id='.$id);
            }
        }  
    }

}

?>