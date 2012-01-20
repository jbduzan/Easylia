<?php

class UtilisateursController extends Zend_Controller_Action
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
		$this->formation_mapper = new Application_Model_FormationsMapper();
		$this->question_mapper = new Application_Model_QuestionsMapper();
    }
    
    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        // On vérifie si l'utilisateur est loggué
        if(!empty($this->user->is_logged) && $this->user->is_logged === true){        	
            $utilisateur = new Application_Model_Utilisateurs();
            $mapper = new Application_Model_UtilisateursMapper();
            
            $mapper->find($this->user->id_utilisateur, $utilisateur);
            
            // Compte le nombre de formation en attente de formateur
            $result = $this->formation_mapper->fetchAll();
            
           	$nombre_formation = 0;
           	
           	foreach($result as $row){
           		if($row->getIdFormateur() == null)
           			$nombre_formation += 1;
           	}
           	
           	$this->view->nombre_formation = $nombre_formation;
            
            $this->view->utilisateur = $utilisateur->getPrenom()." ".$utilisateur->getNom();

			// Si l'utilisateur est formateur non approuvé
            if($this->user->id_groupe == 3){
            	// On redirige sur la page d'approuvation
	           	$this->_redirector->goToSimple('index','renseigner-son-profil');            	
            }else if($this->utilisateur->id_groupe = 4){
            	// Si l'utilisateur est :
            }
            
            // On récupère les permissions de l'utilisateur
           	$permissions = $this->setPermissions();
           	         
           	// et on envoie le résultat à la vue         	
           	foreach($permissions as $key=>$value){
           		$this->view->$key = $value;
           	}
           	            
        }else{
            // Sinon redirection vers la page de connexion
            $this->redirectToConnexion();
        }
            
    }

    public function inscriptionclientAction()
    {
     	
     	// Inscription des clients   
        $request = $this->getRequest();
        $form = new Application_Form_InscriptionClient();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($request->getPost())){
                $client = new Application_Model_Utilisateurs($form->getValues());

                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'development');
                
                $password = sha1($config->salt.$form->getValue('password'));
                
                $client->setPassword($password);
                $mapper = new Application_Model_UtilisateursMapper();
                $mapper->save($client);

                $mail = new Zend_Mail();
                $mail->addTo($client->getMail());
                $mail->setSubject("Votre inscription sur Easylia");
                $mail->setBodyText("Merci de vous etre enregistré sur Easylia, votre login est : ".$client->getLogin().", et votre mot de passe : ".$client->getPassword());
                //$mail->send();
                                             
                return $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function inscriptionformateurAction()
    {
        // Inscription des formateurs
        
        $request = $this->getRequest();
        $form = new Application_Form_Inscriptionformateur();
        
        if($this->getRequest()->isPost()){
            if($request->getPost()){
                $client = new Application_Model_Utilisateurs($request->getPost());

                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'development');
                
                $password = sha1($config->salt.$request->getPost('password'));
                
                $client->setPassword($password);
                
                // On l'insère dans le groupe des formateurs non approuvé
                $client->setIdGroupe(3);
                
                // Clé d'activation du compte à envoyer par mail;
                $cle_activation = substr(sha1(microtime(NULL)*100000),0,30);
                
                $client->setCleActivation($cle_activation);
                
                $id = $this->userMapper->save($client);

                $mail = new Zend_Mail();
                $mail->addTo($client->getMail());
                $mail->setSubject("Votre inscription sur Easylia");
                $mail->setBodyHtml("<div><img src='http://dev.easylia.com/images/logo.jpg'/></div><div><p>Merci de vous etre enregistre sur Easylia.<br />Votre nom d'utilisateur est : '".$client->getLogin()."', et votre mot de passe : '".$request->getPost('password')."'.</p><p>Vous devez maintenant activer votre compte avant de vous connecter. Vous pouvez le faire en cliquant sur ce <a href='dev.easylia.com/utilisateurs/activation/id/".$id."/key/".$cle_activation."'>lien</a>.</p></div>");
                $mail->send();
                                                             
                return $this->_redirector->goToSimple('inscriptionsuccess', 'utilisateurs');
            }
        }
        $this->view->form = $form;

    }
    
    public function inscriptionsuccessAction(){
    
    }

    public function connexionAction()
    {
                
        // Connexion depuis la page connexion
        $request = $this->getRequest();
    
        if($this->getRequest()->isPost()){
            $client = new Application_Model_Utilisateurs();
        
            $mapper = new Application_Model_UtilisateursMapper();
            $mapper->findByLogin($request->getParam('login'), $client);
                            
            $path = APPLICATION_PATH."/configs/application.ini";
            $config = new Zend_Config_Ini($path, 'development');
            
            $password = sha1($config->salt.$request->getParam('password'));
                                        
            if($client->getPassword() == $password){
                $this->user->login = $client->getLogin();
                $this->user->id_utilisateur = $client->getIdUtilisateur();
                $this->user->id_groupe = $client->getIdGroupe();
                $this->user->is_logged = true;
                                    
                return $this->_redirector->goToSimple($this->user->requested_action,$this->user->requested_controller);
            }else{
                echo "<p id='error_connexion' style='color:red;margin-top : 5em;margin-left:2em;'>Le nom d'utilisateur ou le mot de passe ne correspondent pas à ceux enregistrés</p>";
            }
        }
    }

    public function connexionfromindexAction()
    {
        $this->_helper->layout->disableLayout();
        
        
        // Connexion par la requetes ajax depuis n'importe quelle page
        $request = $this->getRequest();
                        
        if($request->getPost('loginFromIndex') == true){
            
            if($request->getPost('remember') == true){
                Zend_Session::RememberMe(86400);
            }else{
                Zend_Session::ForgetMe();
            }
            
            $client = new Application_Model_Utilisateurs();

            $mapper = new Application_Model_UtilisateursMapper();
            $mapper->findByLogin($request->getPost('login'), $client);
            
            $path = APPLICATION_PATH."/configs/application.ini";
            $config = new Zend_Config_Ini($path, 'development');
            
            $password = sha1($config->salt.$request->getPost('password'));
                
            if($client->getPassword() == $password){
                $this->user->login = $client->getLogin();
                $this->user->id_utilisateur = $client->getIdUtilisateur();
                $this->user->id_groupe = $client->getIdGroupe();
                $this->user->is_logged = true;
                $this->view->is_logged = $client->getLogin();
            }
            if($request->getParam('remember') == "on"){
                $this->user->setExpirationSeconds('7200');
            }
        }else{
            $erreur = "Le nom d'utilisateur ou le mot de passe ne correspondent pas à ceux enregistrés";
            return $erreur;
        }
        
    }

    public function deconnexionAction()
    {
        Zend_Session::destroy();
        $this->_helper->redirector->goToSimple('index','index');
    }

    public function testloginexistAction()
    {

        // Vérifie lors de l'inscription si le login existe
        
        $this->_helper->layout->disableLayout();
        
        $login = $this->getRequest()->getPost('login');
        
        $client = new Application_Model_Utilisateurs();
        $mapper = new Application_Model_UtilisateursMapper();
        $login_interdit_mapper = new Application_Model_LoginInterditMapper();
        
        $login_interdit = $login_interdit_mapper->fetchAll();
        
        $mapper->findByLogin($login, $client);
        
        // On vérifie que le login n'est pas un login interdit quelque soit la casse
		foreach($login_interdit as $row){
			if($login == ucfirst($row->getLogin()) || $login == strtoupper($row->getLogin()) || $login == $row->getLogin()){
				$this->view->loginExist = "true";
				return;
			}
		}
        
        if($client->getLogin() != null)
            // Si le login existe déjà
            $this->view->loginExist = 'true';
        else 
            // Si il n'existe pas.
            $this->view->loginExist = 'false';

    }

    public function testemailexistAction()
    {
        // Vérifie lors de l'inscription si le mail existe
        
        $this->_helper->layout->disableLayout();
        
        $mail = $this->getRequest()->getPost('mail');
        
        $mapper = new Application_Model_UtilisateursMapper();
        
        $utilisateurs = $mapper->fetchAll();
        
        $emailExist = 'false';
        
        foreach($utilisateurs as $utilisateur){
            if($utilisateur->getMail() == $mail)
                $emailExist = 'true';
        }
        
        $this->view->emailExist = $emailExist;
        
    }

    public function modifierinfoAction()
    {
        $this->_helper->layout->disableLayout();
        
        
        // Si le formulaire est posté on enregistre et refresh    
        $request = $this->getRequest();
        if($this->getRequest()->isPost()){
            
            $client = new Application_Model_Utilisateurs();
                            
            $mapper = new Application_Model_UtilisateursMapper();
            
            $request = $request;
            
            // On récupère les infos user, les modifie et save
            $mapper->find($this->user->id_utilisateur, $client);
            
            $client->setNom($request->getPost('nom'));
            $client->setPrenom($request->getPost('prenom'));
            $client->setAdresse($request->getPost('adresse'));
            $client->setAdresse2($request->getPost('adresse2'));
            $client->setCodePostal($request->getPost('codePostal'));
            $client->setVille($request->getPost('ville'));
            $client->setTelephone($request->getPost('telephone'));
            $client->setMail($request->getPost('mail'));
            
            $mapper->save($client);
            
            // On reaffiche les données modifiée
            // Création et pleuplement du form de modification
	        $form_data = array(
	            'nom' => $client->getNom(),
	            'prenom' => $client->getPrenom(),
	            'adresse' => $client->getAdresse(),
	            'adresse2' => $client->getAdresse2(),
	            'codePostal' => $client->getCodePostal(),
	            'ville' => $client->getVille(),
	            'telephone' => $client->getTelephone(),
	            'mail' => $client->getMail()
	            );
	        
	        $form = new Application_Form_Modifierinfo();    
	        $form->populate($form_data);
	        $this->view->form = $form;
        }
        
    }

    public function redirectToConnexion()
    {
        // Redirige vers l'action connexion en settant la page demandée pour la redirection        
        $request = $this->getRequest();
        $this->user->requested_controller = $request->getControllerName();
        $this->user->requested_action = $request->getActionName();
        
        return $this->_helper->redirector('connexion');
    }

    public function afficherinfoAction()
    {
        
        // Redirige vers la page de connexion si pas loggué
        if(empty($this->user->is_logged) || $this->user->is_logged != true)
            $this->redirectToConnexion();
        
        // On vérifie que l'utilisateur à la permission de voir cette fiche
        $groupeMapper = new Application_Model_GroupesMapper();
                    
        $nom_groupe = $groupeMapper->getGroupeNameWithId($this->user->id_groupe);
                            
        $form = new Application_Form_Modifierinfo();    
        
        // On affiche les infos
        $utilisateur = new Application_Model_Utilisateurs();
    
        // On regarde si c'est l'utilisateur ou quelqu'un d'autre
        $request = $this->getRequest();
        if($request->getParam('id')){
			if(!$this->acl->isAllowed($nom_groupe, null, 'voir_fiche_client')){
	           // Sinon redirect sur l'espace user
    	       $this->_helper->redirector('index');
        	}
			$this->view->canEdit = true;
            $this->userMapper->find($request->getParam('id'), $utilisateur);
        }
        else{
            $this->userMapper->find($this->user->id_utilisateur, $utilisateur);
        }
    
        // Vérifie si c'est bien la fiche de l'utilisateur
    
        $canEdit = 'false';
            
        // Si oui on lui permet de l'édit    
        if($utilisateur->getIdUtilisateur() == $this->user->id_utilisateur){
            $canEdit = 'true';
        }
    
        $this->view->canEdit = $canEdit;
    
        // Création et pleuplement du form de modification
        $form_data = array(
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'adresse' => $utilisateur->getAdresse(),
            'adresse2' => $utilisateur->getAdresse2(),
            'codePostal' => $utilisateur->getCodePostal(),
            'ville' => $utilisateur->getVille(),
            'telephone' => $utilisateur->getTelephone(),
            'mail' => $utilisateur->getMail()
            );
		
		foreach($form_data as $key=>$value){
			$this->view->$key = $value;
		}
		
    }

    public function listeutilisateursAction()
    {
        $form = new Application_Form_Edituser();
        
        // On vérifie si l'utilisateur à le droit d'être la
        $groupeMapper = new Application_Model_GroupesMapper();
                    
        $nom_groupe = $groupeMapper->getGroupeNameWithId($this->user->id_groupe);
        
        // Sinon on le redirige
        if(!$this->acl->isAllowed($nom_groupe, null, 'voir_liste_utilisateurs'))
            $this->_helper->redirector('index');
            
        // On voit si l'utilisateur peut editer et supprimer
        if($this->acl->isAllowed($nom_groupe, null, 'editer_info_utilisateurs'))
            $this->view->editer_info = "true";
            
        if($this->acl->isAllowed($nom_groupe, null, 'supprimer_utilisateurs'))
            $this->view->supprimer_utilisateur = "true";
        
        $request = $this->getRequest();
        
        // Si on demande un groupe spécifique
        if($request->getParam('id')){
            $this->view->groupe_demande = $request->getParam('id');
        }
        
        // Si le formulaire est posté on édit l'utilisateur
        if($request->isPost()){
        	if($request->getParam('edit') != "true")
        		return false;
        
            $utilisateur = new Application_Model_Utilisateurs();
            $utilisateurMapper = new Application_Model_UtilisateursMapper();
            
            $utilisateurMapper->find($request->getParam('id_utilisateur'), $utilisateur);
            
            $utilisateur->setNom($request->getParam('nom'))
                        ->setPrenom($request->getParam('prenom'))
                        ->setLogin($request->getParam('login'))
                        ->setAdresse($request->getParam('adresse'))
                        ->setAdresse2($request->getParam('adresse2'))
                        ->setCodePostal($request->getParam('code_postal'))
                        ->setVille($request->getParam('ville'))
                        ->setTelephone($request->getParam('telephone'))
                        ->setMail($request->getParam('mail'))
                        ->setDateNaissance($request->getParam('date_naissance'))
                        ->setIdGroupe($request->getParam('id_groupe'));
                        
            $utilisateurMapper->save($utilisateur);
            
        }    
        $this->view->form = $form;
                                        
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
        $id_groupe = "";
        
        if($request->getParam('id_groupe')){
            $id_groupe = $request->getParam('id_groupe');
        }
        
        // Récupération de la liste des utilisateurs
        $mapper = new Application_Model_UtilisateursMapper();
        
        $this->view->rows = $mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $id_groupe);
        
    }

    public function deleteuserAction()
    { 			   
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
        
        if($request->isPost()){
	        // On vérifie quye l'utilisateur à l'autorisation d'effectuer cette action
    		if($request->getParam('delete') != "true")
    			return false;
    		    	
            $id_utilisateur = $request->getParam('id_utilisateur');
            
            $mapper = new Application_Model_UtilisateursMapper();
            
            $mapper->delete($id_utilisateur);
        }
        
    }

    public function getlistenomformateAction()
    {
        // Retourne la liste des Nom et prénom formaté
        if($this->getRequest()->isPost()){
            $this->getHelper('layout')->disableLayout();
            
            $utilisateurs = $this->userMapper->fetchAll();
            
            $liste ='';
            
            foreach($utilisateurs as $utilisateur){
                $id_groupe = $utilisateur->getIdGroupe();
                if($id_groupe != $this->getRequest()->getParam('id_groupe')){
                    $id_utilisateur = $utilisateur->getIdUtilisateur();
                    $nom = $utilisateur->getNom();
                    $prenom = $utilisateur->getPrenom();
                
                    $liste .= "<option value='$id_utilisateur'>$nom&nbsp;$prenom</option>";
                }
            }
            
            $this->view->liste = $liste;
        }
        
    }

    public function changepasswordAction()
    {
        $form = new Application_Form_Changepassword();
        
        $this->view->form = $form;
        
        $request = $this->getRequest();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($request->getPost())){
                $client = new Application_Model_Utilisateurs();
            
                $this->userMapper->find($this->user->id_utilisateur, $client);
                                
                $path = APPLICATION_PATH."/configs/application.ini";
                $config = new Zend_Config_Ini($path, 'development');
                
                $password = sha1($config->salt.$form->getValue('old'));
                                            
                if($client->getPassword() == $password && $client->getPassword() != $form->getValue('new')){
                    $client->setPassword(sha1($config->salt.$form->getValue('new')));
                    
                    $this->userMapper->save($client);
                    
                    $this->view->password_changed = 'yes';
                }else{
                    $this->view->password_changed = 'no';
                }
            }
        }
        
    }

    public function testpasswordAction()
    {
        // Vérifie si c'est le bon password lors du changement de mot de passe
        
        if($this->getRequest()->isPost()){
            $this->_helper->layout->disableLayout();
        
            $path = APPLICATION_PATH."/configs/application.ini";
            $config = new Zend_Config_Ini($path, 'development');
        
            $password = sha1($config->salt.$this->getRequest()->getParam('old'));
        
            $client = new Application_Model_Utilisateurs();
        
            $this->userMapper->find($this->user->id_utilisateur, $client);
        
            if($client->getPassword() == $password)
                // Si le login existe déjà
                $this->view->password_correct = 'true';
            else 
                // Si il n'existe pas.
                $this->view->password_correct = 'false';
        }
        
    }

    public function checkDocumentExist()
    {
		// Vérifie si le formateur à bien uploadé tous les documents nécessaires
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
		
		if($utilisateur->getDocumentEnvoye())
			return true;
		
		// Si il ne l'as pas fait on fait la liste de ceux qu'il a déjà uploadé
		$liste_document = array();
		
		$result = $this->document_mapper->fetchAll();
		
		if(count($result) == 0)
			return false;
		
		foreach($result as $row){
			if($row->getIdUtilisateur() == $this->user->id_utilisateur){
				array_push($liste_document, $row->getType());
			}
		}
		
		if(count($liste_document) >= 3)
			return true;

		return $liste_document;
    }

    public function voirdocumentAction()
    {
    
		$utilisateur = new Application_Model_Utilisateurs();
		
		$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
		
		$this->view->document_envoye = $utilisateur->getDocumentEnvoye();
		    
        // Voir la liste des documents uploadé par l'utilisateur
        $document_array = $this->document_mapper->fetchALl();
        
        $liste = "";
        
        foreach($document_array as $row){
        	if($row->getIdUtilisateur() == $this->user->id_utilisateur){
        		$path = explode("/", $row->getChemin());
            		
            	$number = count($path) - 1;
            	
            	$path = $path[$number];
        		
        		$liste .= "<p>Vous avez uploadé votre ".$row->getType().", vous pouvez <span class='modifier'>le <a href='/document/upload/type/".$row->getType()."'>modifier</a> ou </span>le <a href='/documents/".$path."'>télécharger</a> ";
        	}
        }
        
        $this->view->liste = $liste;
    }

    public function validerdocumentAction()
    {
    
    	$this->getHelper('layout')->disableLayout();
        // Envoie les documents d'un formateur dans le processessus de validation
        
        $request = $this->getRequest();
        
        if($request->getParam('valid_doc') == true){
        	$utilisateur = new Application_Model_Utilisateurs();
        	
        	$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
        	
        	$utilisateur->setDocumentEnvoye("1");
        	
        	$this->userMapper->save($utilisateur);
        	
        	$this->view->retour = true;
        }
        
    }

    public function certificationPedagogique()
    {
    	// Vérifie si l'utilisateur a déjà passé la certification pédagogique et sinon lui affiche le lien pour le faire
    	
    	$certification_mapper = new Application_Model_ListeCertificationMapper();
    	$historique_mapper = new Application_Model_HistoriqueCertificationsMapper();
    	
    	$result = $certification_mapper->fetchALl();
    	
    	$certification_id = "";
    	
    	// On récupère l'id de la certification pédagogique
    	foreach($result as $row){
    		if($row->getNom() == "Certification Pedagogique"){
    			$certification_id = $row->getIdCertification();
    		}
    	}
    	
    	// Pour voir si il existe dans l'historique avec l'id de l'utilisateur
    	$result = $historique_mapper->fetchAll();
    	    	
    	foreach($result as $row){
    		if($row->getIdCertification() == $certification_id){
    			if($row->getIdUtilisateur() == $this->user->id_utilisateur && $row->getScore() >= 70){
    				return true;
    			}
       		}
    	}
    	
    	return $certification_id;
    	
    }

	public function formateuravaliderAction(){
	
	}

    public function getformateuravaliderAction()
    {
        // Liste les formateurs en attente de validation
        
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
        $id_groupe = "";
        
        if($request->getParam('id_groupe')){
            $id_groupe = $request->getParam('id_groupe');
        }
        
        // Récupération de la liste des utilisateurs       
                
        $this->view->rows = $this->userMapper->fetchAllForFlexigridWithDocuments($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $id_groupe);
    }

    public function validerutilisateurAction()
    {
        // Affiche la page de validation pour un utilisateur en particulier.
        $request = $this->getRequest();
        $id_utilisateur = $request->getParam('id');
        
        $filepath = "/home/easylia/public/documents/";
        
        // On vérifie si l'utilisateur à uploadé les 4 documents
        if(file_exists($filepath."cv-".$id_utilisateur.".doc"))
        	$this->view->cv = "/documents/cv-".$id_utilisateur.".doc";
        	
        if(file_exists($filepath."photo-".$id_utilisateur.".png"))
        	$this->view->photo = "/documents/photo-".$id_utilisateur.".png";
        	
        if(file_exists($filepath."motivation-".$id_utilisateur.".doc"))
        	$this->view->motivation = "/documents/motivation-".$id_utilisateur.".doc";
        	
        if(file_exists($filepath."rib-".$id_utilisateur.".png"))
        	$this->view->rib = "/documents/rib-".$id_utilisateur.".png";
        	
        $this->view->id_utilisateur = $id_utilisateur;
        
        $utilisateur = new Application_Model_Utilisateurs;
        $this->userMapper->find($id_utilisateur, $utilisateur);
        
        $this->view->utilisateur = $utilisateur;

    }

    public function setPermissions()
    {
    	// paramètre les permissions de l'utilisateur
		
		$permissions = array();
		
    	if($this->acl->isAllowed($this->nom_groupe,null,'voir_liste_utilisateurs'))
			$permissions['voir_user'] = true;
            
    	if($this->acl->isAllowed($this->nom_groupe,null,'voir_liste_formateurs'))
        	$permissions['voir_formateur'] = true;
        
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_liste_groupe'))
            $permissions['voir_groupe'] = true;
                            
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_liste_certifications'))
            $permissions['voir_certification'] = true;
                
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_liste_question'))
            $permissions['voir_question'] = true;
            
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_formation'))
        	$permissions['voir_formation'] = true;
		
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_liste_login_interdit'))
        	$permissions['voir_login_interdit'] = true;
        	
        if($this->acl->isAllowed($this->nom_groupe, null, 'voir_liste_faq'))
        	$permissions['voir_liste_faq'] = true;
		
		if($this->user->id_groupe == 2){
			$permissions['voir_formation_dispo'] = true;
			$permissions['creer_facture'] = true;
			$permissions['is_formateur'] = true;
		}
		
		if($this->user->id_groupe == 1)
			$permissions['is_admin'] = true;
		
		return $permissions;
		
    }

    public function formateurNonValide()
    {
    	// Action à effectuer lorsque un formateur pas encore validé se loggue
		
		// On récupère les infos de l'utilisateur
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
		
		// On regarde les documents que il a déjà uploadé
    	$document = $this->checkDocumentExist();

    	// Si il n'as pas encore uploadé ses documents
       	$document_present = array();
		
		// Si il n'y as aucun documents
		if(!$document){
			$this->view->cv = false;   
			$this->view->rib = false; 
			$this->view->lettre =  false;
		}else if($document == "true"){
			// Si les 3 documents on été uploadé
			$this->view->cv = true;   
			$this->view->rib = true; 
			$this->view->lettre = true;
			
			// Et si le test de motivation à été passé
			if($utilisateur->getTestMotivation() == 1){
				$this->view->waiting = true;
				$this->view->test_motivation = true;
				return;
			}
		}else if(count($document) > 0 && count($document) < 3){
			// Si tous les documents non pas été uploadé
        	foreach($document as $row){
        		array_push($document_present, $row);
        	}	
        	
        	if(!in_array('cv', $document_present))
        		$this->view->cv = false;   
        	else
        		$this->view->cv = true;
        		     
        	if(!in_array('rib', $document_present))		     
         		$this->view->rib = false;   
        	else
        		$this->view->rib = true;
        		
			if(!in_array('motivation', $document_present))
        		$this->view->lettre =  false;  
        	else
        		$this->view->lettre = true;         	
    	}
    	
    	// On vérifie le test de motivation
    	if($utilisateur->getTestMotivation() == 1)
    		$this->view->test_motivation = true;
    	else
    		$this->view->test_motivation = false;    	
    }

    public function checkFormateurValide()
    {
    	// return true si le formatter a été validé
    	$utilisateur = new Application_Model_Utilisateurs();
    	
    	$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
    	
    	if($utilisateur->getDocumentValide() == 1)
    		return "valide";
    	else if($utilisateur->getDocumentEnvoye() == 1)
    		return "invalide";
    	else 
    		return false;
    }

    public function valideuserajaxAction()
    {
		// Valide l'utilisateur via un post ajax
		$this->getHelper('layout')->disableLayout();
		$request = $this->getRequest();		
				
		if($request->getParam('valider_utilisateur')){
			$utilisateur = new Application_Model_Utilisateurs();
			 
			$this->userMapper->find($request->getParam('id_utilisateur'), $utilisateur);
			
			$utilisateur->setDocumentValide(1);
			$utilisateur->setDocumentEnvoye(1);
			$utilisateur->setIdGroupe(2);
			
			$this->userMapper->save($utilisateur);
		}
    }
	
	public function activationAction(){
		// Active un utilisateur via le lien de l'adresse mail.
		$request = $this->getRequest();
		
		$utilisateur = new Application_Model_Utilisateurs();
		
		$this->userMapper->find($request->getParam('id'), $utilisateur);
		
		// Si le profil est déjà actif
		if($utilisateur->getProfilActif() == 1){
			$this->view->message = "<p>Votre profil a déjà été activé. <br />Vous pouvez vous <a href='/utilisateurs/'>connecter</a> à votre profil</p>";
		}else{
			// On test la clé d'activation
			$cle = $utilisateur->getCleActivation();
			
			if($request->getParam('key') == $cle){
				$utilisateur->setProfilActif(1);
				$this->userMapper->save($utilisateur);
				$this->view->message = "<p>Votre profil a été activé avec success. <br />Vous pouvez vous <a href='/utilisateurs/'>connecter</a> à votre profil</p>";
			}else{
				$this->view->message = "Une erreur c'est produite pendant le processus d'activation";
			}
		}
	}

	public function parcoursformateurAction(){
		// On vérifie que l'utilisateur est bien connecte
		if(!empty($this->user->is_logged) && $this->user->is_logged === true){
			
			// On vérifie que l'utilisateur n'as pas déjà remplis ça
			$utilisateur = new Application_Model_Utilisateurs();
			$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
			
			if($utilisateur->getProfilActif() == 1)
				$this->_redirector->goToSimple('index', 'profil-utilisateur');
			
			// On appelle toutes les actions à effectuer
	    	$this->formateurNonValide(); 
	    	
	    	// Les questions pour le test de motivation 
	    	$this->view->test = $this->prepareMotivationTest();
	    	      
		}else{
			$this->redirectToConnexion();
		}
	}
	
	protected function prepareMotivationTest(){
		$result = $this->question_mapper->fetchAll($test_motivation = true);
		
		$i = 0;
		$nombre_reponse = count($result);
		$questions_motivation = array();

		foreach($result as $row){    
        	$i ++;
            $question = "<div class='question' style='display : none' id='question".$row->getidQuestion()."'><p class='enonce'><span class='enonce-question'>".$row->getQuestion()." ?</span><span class='enonce-numero'>Question N° $i sur $nombre_reponse</span></p><div class='separateur'></div>";
            
            // Si c'est une question ouverte on affiche un textarea pour répondre
            $question .= "<div class='reponse'><p><i>Veuillez inscrire votre réponse ci-dessous : </i></p><textarea rows='10' cols='80' name='".$row->getIdQuestion()."'></textarea>";
            $question .= "</div></div>";
            
            array_push($questions_motivation, $question);
        }
		
		return $questions_motivation;
	}
}
