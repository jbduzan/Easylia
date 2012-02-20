<?php

class FormationController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');
        $this->commande = new Zend_Session_Namespace('commande');              
        $this->formation_mapper = new Application_Model_FormationsMapper();
        $this->formation_dispo_mapper = new Application_Model_FormationsDispoMapper();
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();
        $this->groupe_mapper = new Application_Model_GroupesMapper();
        $this->historique_mapper = new Application_Model_HistoriqueCertificationsMapper();
        $this->certification_mapper = new Application_Model_ListeCertificationMapper();
        $this->mail_mapper = new Application_Model_MailMapper();
        $this->nom_groupe = $this->groupe_mapper->getGroupeNameWithId($this->utilisateur->id_groupe);
        
        if(!$this->utilisateur->is_logged)
        	$this->_redirector->goToUrl('profil-utilisateur');
    }

    public function preDispatch(){
      	$this->view->render('utilisateurs/menu-connecte.phtml');
    	$this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        // Liste des formations et possibilité de les commander
        
    		//$this->_redirector->goToSimple('index', 'formations-disponible');
    }

    public function commanderAction()
    {
    	$request = $this->getRequest();
    	
    	if($request->getParam("id") == "")
    		$this->_redirector->goToSimple('index', 'formations-disponibles');
    
        // On commande une formation
        $formation_dispo = new Application_Model_FormationsDispo();
        $this->formation_dispo_mapper->find($request->getParam("id"), $formation_dispo);
        
        $this->view->formation_dispo = $formation_dispo;
    }

    public function enregistrercommandeAction()
    {
    	$request = $this->getRequest();
    	if( $request->getParam('id') == ""){
    		$this->_redirector->goToUrl('profil-utilisateur');
    	}
    	if($this->validePaiement()){

	        // Enregistre la commande de la formation dans la base de donnée
        	$formation = new Application_Model_Formations();
			
			$this->formation_mapper->find($request->getParam('id'), $formation);
			
			$formation->setPayee(1);
				        	
        	$this->formation_mapper->save($formation);

        	$this->view->success = true;
        	
        	// On envoie un mail au client pour confirmer la commande
    	    
    	    // On récupère les informations du mail à envoyer
    	    $utilisateur = new Application_Model_Utilisateurs();
    	    $this->utilisateur_mapper->find($this->utilisateur->id_utilisateur, $utilisateur);
            $mail_bdd = new Application_Model_Mail();
            $this->mail_mapper->find(4, $mail_bdd);
            $montant = $formation->getNombreHeure() * 55;
            $montant .= " euros";
            $contenu = $mail_bdd->getContenu();
            $contenu = str_replace('{FORMATION}', $formation->getType(), $contenu);
            $contenu = str_replace('{NOMBRE_HEURE}', $formation->getNombreHeure(), $contenu);
            $contenu = str_replace('{DATE_COMMANDE}', $formation->getDate(), $contenu);
            $contenu = str_replace('{MONTANT}', $montant, $contenu);

            $mail = new Zend_Mail();
            $mail->setFrom('no-reply@easylia.com', 'Easylia');
            $mail->addTo($utilisateur->getMail());
            $mail->setSubject($mail_bdd->getSujet());
            $mail->setBodyHtml(utf8_decode($contenu));
            $mail->send();

        	// Crée la facture associée
        	//$factureController = new FactureController();
        	
        	//$factureController->genererFacture($this->utilisateur->id_utilisateur, $this->commande->amount, $this->commande->date);
        	
    		Zend_Session::namespaceUnset('commande');
        }else{
        	$this->view->id = 'toto';
        }               
    }

    public function listeformationAction()
    {
		//  Redirige vers la page de connexion si pas connecté
		if(!$this->utilisateur->is_logged)
			$this->_redirector->goToUrl('/connexion');
    
        // Affiche la liste de toutes les formations en cours
        $this->view->sans_formateur = $this->getRequest()->getParam("sansFormateur");
        
        if($this->acl->isAllowed($this->nom_groupe, null, "ajouter_formateur_formation"))
        	$this->view->ajouter_formateur = "true";
        if($this->acl->isAllowed($this->nom_groupe, null, "modifier_formateur_formation"))
        	$this->view->modifier_formateur = "true";
        if($this->acl->isAllowed($this->nom_groupe, null, "supprimer_formateur_formation"))
        	$this->view->supprimer_formateur = "true";
        if($this->acl->isAllowed($this->nom_groupe, null, "ajouter_formation"))
        	$this->view->ajouter_formation = "true";
        if($this->acl->isAllowed($this->nom_groupe, null, "modifier_formation"))
        	$this->view->modifier_formation = "true";
        if($this->acl->isAllowed($this->nom_groupe, null, "supprimer_formation"))
        	$this->view->supprimer_formation = "true";
    }

    public function getlisteformationAction()
    {
        // Renvoie la liste de toutes les formations au format flexigrid
        
        $this->getHelper('layout')->disableLayout();
        
        $request = $this->getRequest();
                
         // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_formation'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        $id_groupe = "";
        
        $type = array();

		$this->view->rows = $this->formation_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit, $request->getParam('sansFormateur'));
        
    }

    public function deleteAction()
    {
        // Supprime une formation
        $this->getHelper("layout")->disableLayout();
        
        $request = $this->getRequest();
        
        $this->formation_mapper->delete($request->getParam("id_formation"));
    }

    public function payerAction()
    {    		
    	$this->getHelper('layout')->disableLayout();
    	
		// Récupère les paramètre de la commande
		$request = $this->getRequest();
				
		$quantite = $request->getParam("nbr_heures");

		$amount = 55 * $quantite;

		$payement_action = "Authorization";		
		$description = "Autorisation de paiement de la formation";			
					
		$formation = new Application_Model_Formations();
		
		$date = "";
		
		foreach($request->getParam('date_formation_commande') as $row){
			$date .= $row.',';
		}
		
		$date = substr($date, '0', '-1');
		
		$formation->setNombreHeure($quantite)
				  ->setType($request->getParam('nom_formation'))			
				  ->setIdClient($this->utilisateur->id_utilisateur)
				  ->setDate(date('d/m/yy'))
				  ->setHeureDebut($date)
				  ->setIdFormationDispo($request->getParam('id_formation_dispo'));
				  
		$id = $this->formation_mapper->save($formation);
		
		// Et les stocke dans la session		
				  
		$this->commande->date_commande = $request->getParam('date_formation_commande');
		$this->commande->amount = $amount;
		$this->commande->payement_action = $payement_action;
		$this->commande->quantite = $quantite;
		$this->commande->type = $request->getParam('nom_formation');
		$this->commande->description = $description;
		$this->commande->last_id = $id;

    }

    public function listedemandeAction()
    {
        // Liste les demande de formation sans formateurs
        $rows = $this->formation_mapper->fetchAllWithoutFormateur();
    }

    public function validePaiement()
    {
        $authInfo = new Zend_Service_PayPal_Data_AuthInfo(
			'jbduza_1319099049_biz_api1.gmail.com',
			'1319099073',
			'AZ5Qm4qEIjcG7hc-HNVmIi-E3qTnAuLQy.pWX8p-SxdJEc6n.Il-qGWQ');

		if($this->getRequest()->getParam('token')){
			$paypal = new Zend_Service_Paypal_Nvp($authInfo);
			$coDetails = $paypal->getExpressCheckoutDetails($this->getRequest()->getParam('token'));
						
			if($coDetails->isSuccess() && ($payerId = $coDetails->getValue('PAYERID'))){
				$reponse = $paypal->doExpressCheckoutPayment($this->getRequest()->getParam('token'), $coDetails->getValue('PAYERID'), $this->commande->amount, $this->commande->payement_action);
								
				if($this->commande->payement_action == "Authorization"){
			
					$autorized_payement = new Application_Model_PaypalAutorizedPayement();
					$payement_mapper = new Application_Model_PaypalAutorizedPayementMapper();
					
					$autorized_payement->setIdTransaction($reponse->getTransactionId());
					
					$timestamp_3jours = time() + (86400 * 3);
					$timestamp_29jours = time() + (86400 * 29);
					
					$date_3jours = date('d/m/Y', $timestamp_3jours);
					$date_29jours = date('d/m/Y', $timestamp_29jours);
					
					$autorized_payement->setDateHonneur($date_3jours);
					$autorized_payement->setDateValidite($date_29jours);
					$autorized_payement->setMontant($this->commande->amount);
					
					$payement_mapper->save($autorized_payement);
				}
							
			}
						
			if($reponse->isSuccess())
				return true;
			else
				return false;
			
		}   
    }

    public function addformateurtoformationAction()
    {
        // ajoute un formateur à une formation
        $request = $this->getRequest();
        
        $id_formateur = $this->utilisateur->id_utilisateur;
        
        if($request->getParam('id_formateur') != ''){
        	$id_formateur = $request->getParam('id_formateur');
        }
        	
        $formation = new Application_Model_Formations();
        $this->formation_mapper->find($request->getParam('id_formation'), $formation);
        
        $formation->setIdFormateur($id_formateur);
        $formation->setHeureDebut($request->getParam('heure'));
        
        $this->formation_mapper->save($formation);
    }

    public function getlistenomformateurAction()
    {
        // Retourne la liste des noms de tous les formateurs
        
        $this->getHelper('layout')->disableLayout();
        
        $result = $this->utilisateur_mapper->fetchAll();
        
        $option = "";
        
        foreach($result as $row){
        	if($row->getIdGroupe() == 2)
        		$option .= "<option value='".$row->getIdUtilisateur()."'>".$row->getNom()." ".$row->getPrenom()."</option>";
        }
        
        $this->view->option = $option;
    }

    public function listeformationdispoAction()
    {
        // Liste les formations disponible à afficher aux clients
        $result = $this->formation_dispo_mapper->fetchAll();
        
        $liste = "";
        
        if(count($result) == 0){
        	$this->view->liste = "<p>Il n'y a aucune formations disponible pour le moment.</p>";
        	return;
        }

        foreach($result as $row){
        	$liste .= "<br /><p><a href='commander-une-formation?id=".$row->getIdFormationDispo()."'>".$row->getNom()."</a></p>";
        }
        
        $this->view->liste = $liste;
    }
    
    public function sendcommandtopaypalAction(){
    	$authInfo = new Zend_Service_PayPal_Data_AuthInfo(
			'jbduza_1319099049_biz_api1.gmail.com',
			'1319099073',
			'AZ5Qm4qEIjcG7hc-HNVmIi-E3qTnAuLQy.pWX8p-SxdJEc6n.Il-qGWQ');
			
			
		$self = "http://localhost/formation/enregistrercommande?id=".$this->commande->last_id;
		$amount = $this->utilisateur->montant;	
				
		$paypal = new Zend_Service_PayPal_Nvp($authInfo);
		$params = array('NOSHIPPING' => 1);
					
 		$reponse = $paypal->setExpressCheckout($this->commande->amount, $self.'&status=ok', $self.'?status=cancel', $params, $this->commande->payement_action, $this->commande->quantite, $this->commande->type, $this->commande->description);

 			 				
 		if($reponse->isSuccess() && ($token = $reponse->getValue('TOKEN'))){
			header("Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token");
 		}else{
 			echo "Une erreur c'est produite lors de l'initialisation du paiement";
 			print_r($reponse);
 		}

    }
    
    public function viewformationAction(){
    	// Si l'utilisateur n'est pas connecté on le redirige
    	if(!$this->utilisateur->is_logged)
    		$this->_redirector->goToSimple('index', 'utilisateurs');

    }

	public function getcalendardataAction(){
	
		$this->getHelper('layout')->disableLayout();
		// Renvoie les formations au format json pour un formateur
		$data = $this->formation_mapper->getFormationJson($this->utilisateur->id_utilisateur);
		
		$this->view->event = $data;
		
	}
	
	public function geteventdataAction(){
		// Retourne les données d'une formation afin de les visualiser dans le calendrier formateur
		$request = $this->getRequest();
		$this->getHelper('layout')->disableLayout();
		
		if(!$request->isPost())
			return false;
			
		$formation = new Application_Model_Formations();
		$this->formation_mapper->find($request->getParam('id'), $formation);
		
		$utilisateur = new Application_Model_Utilisateurs();
		$this->utilisateur_mapper->find($formation->getIdClient(), $utilisateur);
		
		$typê = "";
		
		if($utilisateur->getType() == "mr")
			$type = "Mr";
		else if($utilisateur->getType() == "mme")
			$type = "Mme";
		else if($utilisateur->getType() == "mlle")
			$type = "Mlle";
		
		$this->view->nom_prenom = $type." ".$utilisateur->getNom()." ".$utilisateur->getPrenom();
		
		$adresse = $utilisateur->getAdresse();
		
		if($utilisateur->getAdresse2() != "")
			$adresse .= "- ".$utilisateur->getAdresse2();
			
		$adresse2 = $utilisateur->getCodePostal()." ".$utilisateur->getVille();
		
		$this->view->adresse = $adresse;
		$this->view->adresse2 = $adresse2;
		
		$this->view->telephone = $utilisateur->getTelephone();
		
		$this->view->date = $formation->getHeureDebut();
		$this->view->duree = $formation->getNombreHeure()."H";
		$this->view->formation = $formation->getType();
        $this->view->id_formation = $formation->getIdFormation();

        if($formation->getFormationEffectue() == 1)
            $this->view->formation_effectue = $formation->getFormationEffectue();
        else if($formation->getFormationEffectue() == 2){
            $this->view->formation_effectue = $formation->getFormationEffectue();
            $this->view->raison_refus = $formation->getRaisonRefus();
        }

	}
	
	public function getformationdataAction(){
		// Retourne les données d'une formation pour les visualiser au moment de l'acceptation par le formateur
		
		$this->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		$formation = new Application_Model_Formations();
		
		$this->formation_mapper->find($request->getParam('id'), $formation);
		
		$date_formated = explode(' ', $formation->getHeureDebut());
    	$date = explode('/', $date_formated[1]);
    	$heure = substr($date_formated[2], '0', '-1');
		
		// On récupère le code postal et la ville du client
		$utilisateur = new Application_Model_Utilisateurs();
		$this->utilisateur_mapper->find($formation->getIdClient(), $utilisateur);
		
		$this->view->code_postal = $utilisateur->getCodePostal();
		$this->view->ville = $utilisateur->getVille();		
		$this->view->date_formation = $date;
		
		$this->view->heure_formation = $formation->getHeureDebut();
		
		$this->view->type_formation = $formation->getType()." - ".$formation->getNombreHeure()."H";
	}

	public function checkcertificationAction(){
		// Vérifie si le formateur possède la certification pour accepter une formation
		       
		$this->getHelper('layout')->disableLayout();
		      
		// On récupère les certifications passées par le formateur
        $historique = $this->historique_mapper->findByIdUtilisateur($this->utilisateur->id_utilisateur);
        
        $id_certifications = array();
                
        foreach($historique as $row){
        	// Si il a réussi la certification, on la récupère
        	if($row->getScore() >= 70)
        	array_push($id_certifications, $row->getIdCertification());
        }
        
        // On récupère toutes les certifications
        $certifications = $this->certification_mapper->fetchAll();
        
        $type = array(); 
        
        // On récupère le type de la certification
        foreach($certifications as $row){
        	if(in_array($row->getidCertification(), $id_certifications)){
        		array_push($type, $row->getType());
        	}
        }
        
        // Et on vérifie que cela correspond à la certification demandée
        $formation = new Application_Model_Formations();
		$this->formation_mapper->find($this->getRequest()->getParam('id_formation'), $formation);
		
		if(in_array($formation->getType(), $type))
			$this->view->result = "true";
		else
			$this->view->result = "false";
			
	}

	public function gererformationAction(){
		// Affiche la liste des formations existante et permet de les administrer

		// Si l'utilisateur n'est pas connecté ou n'est pas administrateur
		if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 1)
			$this->_redirector->goToUrl('/profil-utilisateur');
	}

	public function getformationdispoAction(){
		// Renvoie la liste des formations existante au format flexigrid
		$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$request = $this->getRequest();
                
        // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_formation_dispo'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');

		echo $this->formation_dispo_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);
	}

	public function ajouterformationdispoAction(){
		// Ajoute une formation à partir d'une requete ajax
		$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$request = $this->getRequest();

		$formation = new Application_Model_FormationsDispo();
		
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
				$formation->__set($key,$temp);
				continue;
			}
			$formation->__set($key,$value);
		}
		
		// Une fois que on a fait toute les modifications, on sauvegarde
		$this->formation_dispo_mapper->save($formation);
	}

	public function editerformationdispoAction(){
		// Ajoute une formation à partir d'une requete ajax
		$this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$request = $this->getRequest();

		$formation = new Application_Model_FormationsDispo();
		$this->formation_dispo_mapper->find($request->getParam('idFormationDispo'), $formation);

		$proprietes = array_slice($request->getParams(), 3);
		
		// On les injecte à l'utilisateur
		foreach($proprietes as $key=>$value){
			// On saute l'id 
			if($key == "idFormationDispo")
				continue;

			// Si la value est un array
			if(is_array($value)){
				$temp = "";
				foreach($value as $row){
					$temp .= $row.",";
				}
				$temp = substr($temp, '0', '-1');
				$formation->__set($key,$temp);
				continue;
			}
			$formation->__set($key,$value);
		}
		
		// Une fois que on a fait toute les modifications, on sauvegarde
		$this->formation_dispo_mapper->save($formation);
	}

	public function deleteformationdispoAction()
    {
        // Supprime une formation
        $this->getHelper('layout')->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
        
        $request = $this->getRequest();
        
        $this->formation_dispo_mapper->delete($request->getParam("id_formation_dispo"));
    }

    public function seeformationAction(){
    	// Récupère la liste de toutes les formations commandés par un utilisateur
    	if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 5)
    		$this->_redirector->goToUrl('profil-utilisateur');

    	// On récupère la liste de toutes les formations
    	$formations = $this->formation_mapper->fetchAll($date_jour = null, $id_client = $this->utilisateur->id_utilisateur);
    	
    	$this->view->formations = $formations;
    }

    public function detailformationAction(){
    	// Affiche le détail d'une formation pour un client
    	if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 5)
    		$this->_redirector->goToUrl('profil-utilisateur');

    	// On récupère les informations de la formation
    	$formation = new Application_Model_Formations();
    	$this->formation_mapper->find($this->getRequest()->getParam('id'), $formation);

    	// On véfifie que c'est bien une formation pour cet utilisateur
    	if($this->utilisateur->id_utilisateur != $formation->getIdClient())
    		$this->_redirector->goToUrl('profil-utilisateur');
        
    	$this->view->formation = $formation;

    	// On récupère le nom du formateur
    	$formateur = new Application_Model_Utilisateurs();
    	$this->utilisateur_mapper->find($formation->getIdFormateur(), $formateur);

    	$this->view->formateur = $formateur;

        // On teste si la formation est déjà passé afin de proposer la facturation
        $date = $formation->getHeureDebut();
        if(count($date) == 1){
            $now = time();
            $date = explode(' ', $date);
            $date_formation = explode('/', $date[1]);
            $date_formation = mktime(0,0,0, $date_formation[1], $date_formation[0], $date_formation[2]);   

            if($date_formation < $now){
                $this->view->facture = true;
            }else
                $this->view->facture = false;
        }else
            $this->view->facture = false;
    }

    public function presenceformationAction(){
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        // On récupère la formation
        $formation = new Application_Model_Formations();
        $this->formation_mapper->find($request->getParam('id_formation'), $formation);

        $formation->setFormationEffectue($request->getParam('formation_effectue'))
                  ->setRaisonRefus($request->getParam('raison_refus'));

        $this->formation_mapper->save($formation);
    }
}





















