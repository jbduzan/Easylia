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
    }
    	
	public function indexAction(){
		// Inscription des formateurs
        
        $request = $this->getRequest();
        
        if($this->getRequest()->isPost()){
            if($request->getPost()){
                $client = new Application_Model_Utilisateurs($request->getPost());

                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'development');
                
                $password = sha1($config->salt.$request->getPost('password'));
                
                $client->setPassword($password);
                
                $client->setDateEntree(date("d/m/Y"));
                
                // On l'insère dans le groupe des formateurs non approuvé
                $client->setIdGroupe(3);
                                
                // Clé d'activation du compte à envoyer par mail;
                $cle_activation = substr(sha1(microtime(NULL)*100000),0,30);
                
                $client->setCleActivation($cle_activation);
                
                $id = $this->userMapper->save($client);

                $mail = new Zend_Mail();
                $mail->setFrom('no-reply@easylia.com', 'Easylia');
                $mail->addTo($client->getMail());
                $mail->setSubject("Bienvenue chez Easylia !");
                $mail->setBodyHtml(utf8_decode("<div><img src='https://in.easylia.com/images/logo.jpg'/></div><div><p>Bonjour, <br /><br />Vous venez de vous inscrire comme formateur chez Easylia, et nous vous en remercions. <br /><br /> Nous vous invitons tout d'abord à cliquer sur le lien suivant pour terminer votre préinscription : <br /><a href='http://in.easylia.com/activation-compte?id=".$id."&key=".$client->getCleActivation()."'>Activer votre compte</a><br /><br />Vous pourrez ensuite vous connecter sur votre <a href='http://in.easylia.com/profil-utilisateur'>espace personnel</a>, afin de poursuivre la procédure d'inscription.<br /><br />Une fois celle-ci achevée, vous pourrez accéder à la liste des formations disponibles et à donner.<br /><br />En cas de questions, nous vous invitons à consulter la FAQ, accessible depuis notre site Internet. <br /><br />Restant à votre écoute,<br />Nous vous souhaitons la bienvenue chez Easylia.<br /><br />Cordialement, <br />L'équipe Easylia. </p></div>"));
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

        $mail = new Zend_Mail();
        $mail->setFrom('no-reply@easylia.com', 'Easylia');
        $mail->addTo($client->getMail());
        $mail->setSubject("Bienvenue chez Easylia !");
        $mail->setBodyHtml(utf8_decode("<div><img src='https://in.easylia.com/images/logo.jpg'/></div><div><p>Bonjour, <br /><br />Vous venez de vous inscrire comme formateur chez Easylia, et nous vous en remercions. <br /><br /> Nous vous invitons tout d'abord à cliquer sur le lien suivant pour terminer votre préinscription : <br /><a href='http://in.easylia.com/activation-compte?id=".$id."&key=".$client->getCleActivation()."'>Activer votre compte</a><br /><br />Vous pourrez ensuite vous connecter sur votre <a href='http://in.easylia.com/profil-utilisateur'>espace personnel</a>, afin de poursuivre la procédure d'inscription.<br /><br />Une fois celle-ci achevée, vous pourrez accéder à la liste des formations disponibles et à donner.<br /><br />En cas de questions, nous vous invitons à consulter la FAQ, accessible depuis notre site Internet. <br /><br />Restant à votre écoute,<br />Nous vous souhaitons la bienvenue chez Easylia.<br /><br />Cordialement, <br />L'équipe Easylia. </p></div>"));
        $mail->send();
    }

}

?>