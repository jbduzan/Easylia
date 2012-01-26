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
                $mail->setSubject("Votre inscription sur Easylia");
                $mail->setBodyHtml("<div><img src='http://dev.easylia.com/images/logo.jpg'/></div><div><p>Merci de vous etre enregistre sur Easylia.<br />Votre nom d'utilisateur est : '".$client->getLogin()."', et votre mot de passe : '".$request->getPost('password')."'.</p><p>Vous devez maintenant activer votre compte avant de vous connecter. Vous pouvez le faire en cliquant sur ce <a href='/activation-compte?id=".$this->id_utilisateur."&key=".$this->cle_activation."'>lien</a>.</p></div>");
                $mail->send();

                                                             
                return $this->_redirector->goToSimple('confirmation', 'inscription');
            }
        }
	}
	
	public function confirmationAction(){
	}

}

?>