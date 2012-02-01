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
		$this->historique_mapper = new Application_Model_HistoriqueCertificationsMapper();
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
            
            // Si l'utilisateur est formateur non approuvé
            if($this->user->id_groupe == 3){
            	// On redirige sur la page d'approuvation
	           	$this->_redirector->goToSimple('index','renseigner-son-profil');            	
            }else if($this->utilisateur->id_groupe = 4){
            	// Si l'utilisateur est :
            }
            
            // Compte le nombre de formation en attente de formateur
            $result = $this->formation_mapper->fetchAll($date_jour = true);
            
           	$nombre_formation = 0;
           	
           	foreach($result as $row){
           		if($row->getIdFormateur() == null)
           			$nombre_formation += 1;
           	}
           	
           	$this->view->nombre_formation = $nombre_formation;
            
            $this->view->utilisateur = $utilisateur->getPrenom()." ".$utilisateur->getNom();
            
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
				$this->user->prenom = $client->getPrenom();
                $this->user->nom = $client->getNom();
                $this->user->id_utilisateur = $client->getIdUtilisateur();
                $this->user->id_groupe = $client->getIdGroupe();
                $this->user->is_logged = true;
                                    
                //return $this->_redirector->goToSimple($this->user->requested_action,$this->user->requested_controller);
                return $this->_redirector->goToUrl('/profil-utilisateur');
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
		$this->getResponse()->setHeader("Access-Control-Allow-Origin", "http://www.easylia.com", true);
		
		
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
                $this->user->prenom = $client->getPrenom();
                $this->user->nom = $client->getNom();
                $this->user->id_utilisateur = $client->getIdUtilisateur();
                $this->user->id_groupe = $client->getIdGroupe();
                $this->user->is_logged = true;
                $this->view->is_logged = $client->getLogin();
            }
            if($request->getParam('remember') == "on"){
                $this->user->setExpirationSeconds('7200');
            }
            
            $this->view->test = json_encode('toto');
        }else{
            $erreur = "Le nom d'utilisateur ou le mot de passe ne correspondent pas à ceux enregistrés";
            return $erreur;
        }
        
    }

    public function deconnexionAction()
    {
        Zend_Session::destroy();
        $this->_helper->redirector->goToUrl('http://www.easylia.com');
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
        
        return $this->_redirector->goToUrl('/connexion');
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
    
    public function edituserAction(){
    	// Edite l'utilisateur en fonction des paramètres envoyés
    	$this->getHelper('layout')->disableLayout();
		$request = $this->getRequest();
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($request->getParam('id_utilisateur'), $utilisateur);
		
		$proprietes = array_slice($request->getParams(), 3);
		
		// On les injecte à l'utilisateur
		foreach($proprietes as $key=>$value){
			// On saute l'id 
			if($key == "id_utilisateur")
				continue;

			// Si la value est un array
			if(is_array($value)){
				$temp = "";
				foreach($value as $row){
					$temp .= $row.",";
				}
				$temp = substr($temp, '0', '-1');
				$utilisateur->__set($key,$temp);
				continue;
			}
			$utilisateur->__set($key,$value);
		}
		
		// Une fois que on a fait toute les modifications, on sauvegarde
		$this->userMapper->save($utilisateur);
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
                
    }
    
    public function setchangepasswordAction(){
    	$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$request = $this->getRequest();
        if($this->getRequest()->isPost()){
            $client = new Application_Model_Utilisateurs();
        
            $this->userMapper->find($this->user->id_utilisateur, $client);
                            
            $path = APPLICATION_PATH."/configs/application.ini";
            $config = new Zend_Config_Ini($path, 'development');
            
            $password = sha1($config->salt.$request->getParam('old'));              
                                        
            if($client->getPassword() == $password && $client->getPassword() != $request->getParam('new')){
                $client->setPassword(sha1($config->salt.$request->getParam('new')));
                
                $this->userMapper->save($client);
                
                echo 'yes';
            }else{
                echo 'no';
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
		
    	// On récupère l'adresse skype de l'utilisateur
  		$this->view->adresse_skype = $utilisateur->getAdresseSkype();
  		
  		// On regarde si l'entretien skype est passé
  		if($utilisateur->getDateEntretienSkype() != "")
  			$this->view->entretien_skype = true;

		// On regarde si l'utilisateur à déjà renseigné ses disponibilité
    	if($utilisateur->getDisponibiliteEntretien() != "")
    		$this->view->disponibilite = true; 
		
		// On regarde les documents que il a déjà uploadé
    	$document = $this->checkDocumentExist();

    	// Si il n'as pas encore uploadé ses documents
       	$document_present = array();
		
		// Si il n'y as aucun documents
		if(!$document){
			$this->view->cv = false;   
			$this->view->casier = false; 
			$this->view->lettre =  false;
		}else if($document == "true"){
			// Si les 3 documents on été uploadé
			$this->view->cv = true;   
			$this->view->casier = true; 
			$this->view->lettre = true;
			
			// Et si le test de motivation à été passé
			if($utilisateur->getTestMotivation() == 1){
				$this->view->test_motivation = true;
				$this->view->waiting = true;
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
        		     
        	if(!in_array('casier', $document_present))		     
         		$this->view->casier = false;   
        	else
        		$this->view->casier = true;
        		
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
			
			// On envoie un mail au formateur pour lui signifier son acceptation
			$mail = new Zend_Mail();
			$mail->setFrom('no-reply@easylia.com', 'Easylia');
			$mail->addTo($utilisateur->getMail());
			$mail->setSubject(utf8_decode("Réponse à votre entretien"));
			$mail->setBodyHtml(utf8_decode("<div><img src='http://dev.easylia.com/images/logo.jpg'/><br /><br/><br/></div><div><p>Ceci est un mail automatique, merci de ne pas y r&eacute;pondre</p><p>Après avoir examiné votre candidature, il a été décidé de l'accepter. <br /> Merci de votre participation</p></div>"));
			$mail->send();

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
				$this->view->message = "<p>Votre compte a correctement été activé.<br /><br />Nous vous invitons maintenant à vous connecter à votre profil, afin de poursuivre la procédure d'inscription, avec les identifiants fournis lors de votre préinscription (si vous avez oublié vos identifiants, nous pouvons vous les renvoyer par e-mail.<br /> <br />Pour cela, cliquez <a href='/mot-de-passe-oublie'>ici</a>  </p>";
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
		
			// On vérifie si l'utilisateur a activé son compte
			if($utilisateur->getProfilActif() != 1)
				$this->_redirector->goToUrl('/compte-inactif');
			
			// On vérifie si il a déjà passé la certification pédagogique
			$id_certification = $this->checkCertificationPedagogique($utilisateur->getIdUtilisateur());
			
			if(is_int($id_certification))
				$this->view->certification_pedagogique = $id_certification;				
			
			// On appelle toutes les actions à effectuer
	    	$this->formateurNonValide(); 
	    	
	    	// Les questions pour le test de motivation 
	    	$this->view->test = $this->prepareMotivationTest();
	    	$this->view->id_utilisateur = $this->user->id_utilisateur;
	    	      
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
            $question = "<div class='question' style='display : none' id='question".$row->getidQuestion()."'><p class='enonce'><span class='enonce-question'>".$row->getQuestion()."</span><span class='enonce-numero'>Question N° $i sur $nombre_reponse</span></p><div class='separateur'></div>";
            
            // Si c'est une question ouverte on affiche un textarea pour répondre
            $question .= "<div class='reponse'><p><i>Veuillez inscrire votre réponse ci-dessous : </i></p><textarea rows='10' cols='80' name='".$row->getIdQuestion()."'></textarea>";
            $question .= "</div></div>";
            
            array_push($questions_motivation, $question);
        }
		
		return $questions_motivation;
	}
	
	protected function checkCertificationPedagogique($id_utilisateur){
		// Vérifie si l'utilisateur à déjà passé et obtenue la certification pédagogique	
		
		// On récupère l'id de la certification pédagogique et son score minimum
		$result = $this->certification_mapper->fetchAll();
		
		$id_certification = "";
		$score_minimum;
		
		foreach($result as $row){
			if($row->getType() == "Certification Pedagogique"){
				$id_certification = $row->getIdCertification();
				$score_minimum = $row->getScoreMinimum();
				break;
			}
		}
		
		// On récupère dabord toutes les certifications passé de l'utilisateur
		$result = $this->historique_mapper->fetchAll();

		// Si l'utilisateur a passé la certification et que il a passé le score mini on valide		
		foreach($result as $row){
			if($row->getIdUtilisateur() == $id_utilisateur){
				if($row->getScore() >= $score_minimum){
					return true;
				}
			}
		}
		
		// Sinon on renvoie l'id de la certification pour la faire passer
		return intval($id_certification);
	}
	
	public function getentretiendataAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		// On récupère les infos sur l'utilisateur
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($request->getParam('id'), $utilisateur);

		$result = "<h5 class='h5_modifie'>Notes sur l'entretien</h5><textarea id='note_skype' rows='15' cols='128'>";
		
		// On on remplis les notes si elles ont déjà été renseignées
		if($utilisateur->getNote() != ''){
			$result .= $utilisateur->getNote();
		}
			
		$result .= "</textarea>";		
		// On affiche les informations
		echo "<p>Nom d'utilisateur Skype du formateur : <a href='skype:".$utilisateur->getAdresseSkype()."?call'>".$utilisateur->getAdresseSkype()."</a></p>";
		echo "<p>Date de l'entretien : <span id='date_entretien'>".$utilisateur->getDateEntretienSkype()."</span></p>";
		echo $result;
		echo "<br />";
		echo "<br />";
		echo "<span id='info_entretien' style='display : none'></span>";
		echo "<br />";
		
		// On récupère les disponibilitée de l'utilisateur si il n'as pas encore passé l'entretien
		if($utilisateur->getDateEntretienSkype() == ""){
			$disponibilite_left = "";
			$disponibilite_right = "";
			$temp = $utilisateur->getDisponibiliteEntretien();
			$temp = explode(',', $temp);
			$i = 0;
			
			foreach($temp as $row){
				$i++;
				if($i <= round((count($temp) / 2)))
					$disponibilite_left .= "<p class='dispo_left'>- ".$row."<p>";
				else
					$disponibilite_right .= "<p class='dispo_right'>- ".$row."<p>";
			}

			echo "<h5 class='h5_modifie'>Disponibilitées du formateur</h5>";
			echo "<div id='dispo_left'>".$disponibilite_left."</div>";
			echo "<div id='dispo_right'>".$disponibilite_right."</div>";
		}
	}
	
	public function refuserformateurAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		$this->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($request->getParam('id_utilisateur'), $utilisateur);
		
		// On bascule le formateur dans le groupe des formateurs refusés
		$utilisateur->setIdGroupe(4);
		$this->userMapper->save($utilisateur);
		
		// On envoie un mail au formateur pour lui signifier son refus
		$mail = new Zend_Mail();
		$mail->setFrom('no-reply@easylia.com', 'Easylia');
		$mail->addTo($utilisateur->getMail());
		$mail->setSubject(utf8_decode("Réponse à votre entretien"));
		$mail->setBodyHtml(utf8_decode("<div><img src='http://dev.easylia.com/images/logo.jpg'/><br /><br/><br/></div><div><p>Ceci est un mail automatique, merci de ne pas y r&eacute;pondre</p><p>Après avoir examiné votre candidature, il a été décidé de ne pas l'accepter. <br /> Merci de votre participation</p></div>"));
		$mail->send();
	}
	
	public function nonactiveAction(){
		// Si l'utilisateur n'as pas activé son compte.
		// On récupère la clé d'activation de l'utilisateur et on lui propose dans le lien		
		
		if($this->user->is_logged != true)
			$this->_redirector->goToUrl('/connexion');
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->find($this->user->id_utilisateur, $utilisateur);
		
		$this->view->cle_activation = $utilisateur->getCleActivation();
		$this->view->id_utilisateur = $utilisateur->getIdUtilisateur();
	}
		
	public function motdepasseoublieAction(){
	
	}
	
	public function setpasswordAction(){
		// Renvoie le mot de passe à un utilisateur
		$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		// On vérifie si l'utilisateur à bien rentré son adresse mail et login
		$request = $this->getRequest();
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->userMapper->findByLogin($request->getParam('login'), $utilisateur);
		
		if($utilisateur->getMail() != $request->getParam('mail') || $request->getParam('mail') == "" && $request->getParam('login') == ''){
			echo "false";
			return false;
		}
		else{
			// On générère un nouveau mot de passe aléatoire
			$string = "";
			
			$chaine = "abcdefghijklmnpqrstuvwxyz123456789";
			srand((double)microtime()*1000000);
			
			for($i=0; $i<8; $i++) {
				$string .= $chaine[rand()%strlen($chaine)];
			}
						
			$path = APPLICATION_PATH."/configs/application.ini";
            $config = new Zend_Config_Ini($path, 'development');
            
            $password = sha1($config->salt.$string);
            
            $utilisateur->setPassword($password);
            $this->userMapper->save($utilisateur);
			
			$mail = new Zend_Mail();
			$mail->setFrom('no-reply@easylia.com', 'Easylia');
			$mail->addTo($utilisateur->getMail());
			$mail->setSubject(utf8_decode("Renvoi de vos identifiants"));
			$mail->setBodyHtml(utf8_decode("<div><img src='http://dev.easylia.com/images/logo.jpg'/><br /><br/><br/></div><div><p>Vous avez demandé un renvoi de vos identifiants de connexion.<br /><br />Nom d'utilisateur : ".$utilisateur->getLogin()." , mot de passe : ".$string."<br /><br />Nous vous conseillons de changer votre mot de passe lors de votre prochaine connexion<br /><br />Cordialement,<br />L'équipe d'Easylia.</p></div>"));
			$mail->send();
			
			echo "true";
		}
	}

    public function voirdocumentAction(){
        // Affiche la liste des documents d'un utilisateur

        // On récupère son nom et son prénom
        $utilisateur = new Application_Model_Utilisateurs();
        $this->userMapper->find($this->getRequest()->getParam('id'), $utilisateur);
        
        $this->view->prenom = $utilisateur->getPrenom();
        $this->view->nom = $utilisateur->getNom();   

        // On récupère la liste des documents de cet utilisateur et le chemin des documents
        $document = new Application_Model_Document();
        $result = $this->document_mapper->fetchAll($id_utilisateur = $this->getRequest()->getParam('id'));
        
        $liste_document = "<p>";

        foreach ($result as $row) {
            if($row->getType() == 'cv')
                $nom = "Curriculum Vitae";
            elseif($row->getType() == 'casier') 
                $nom = "Extrait de casier judiciaire";
            elseif($row->getType() == 'motivation')
                $nom = "Lettre de motivation";
            
            $chemin = explode('/', $row->getChemin());
            //$liste_document .= "<span><a href='/document/downloadfile?chemin=".$chemin[6]."'>$nom</a></span><br />";
            $liste_document .= "<span><a href='/document/downloadfile?chemin=".$chemin[6]."'>$nom</a></span><br />";
        }

        $this->view->liste = $liste_document;
    }
}	
