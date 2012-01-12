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
        $this->nom_groupe = $this->groupe_mapper->getGroupeNameWithId($this->utilisateur->id_groupe);
        
        if(!$this->utilisateur->is_logged)
        	$this->_redirector->goToSimple('index', 'utilisateurs');
    }

    public function indexAction()
    {
        // Liste des formations et possibilité de les commander
        
        $this->_redirector->goToSimple('listeformationdispo', 'formation');
    }

    public function commanderAction()
    {
    	$request = $this->getRequest();
    	
    	if($request->getParam("id") == "")
    		$this->_redirector->goToSimple('listeformationdispo', 'formation');
    
        // On commande une formation
        $formation_dispo = new Application_Model_FormationsDispo();
        $this->formation_dispo_mapper->find($request->getParam("id"), $formation_dispo);
        
        $this->view->formation_dispo = $formation_dispo;
    }

    public function enregistrercommandeAction()
    {
    	if($this->commande->id_last_commande == ""){
    		$this->_redirector->goToSimple('index', 'formation');
    	}else if($this->validePaiement()){
    	
	        // Enregistre la commande de la formation dans la base de donnée
        	$formation = new Application_Model_Formations();
			
			$this->formation_mapper->find($this->commande->id_last_commande, $formation);
			
			$formation->setPayee(1);
				        	
        	$this->formation_mapper->save($formation);
        	
        	// Crée la facture associée
        	//$factureController = new FactureController();
        	
        	//$factureController->genererFacture($this->utilisateur->id_utilisateur, $this->commande->amount, $this->commande->date);
        	
    		Zend_Session::namespaceUnset('commande');
        }                
    }

    public function listeformationAction()
    {
		//  Redirige vers la page de connexion si pas connecté
		if(!$this->utilisateur->is_logged)
			$this->_redirector->goToSimple('index','utilisateurs');
    
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
		
/*
		if($request->getParam("date_payement") == "maintenant"){
			$payement_action = "Sale";
			$description = "Paiement de la formation";
		}
		else{
			$payement_action = "Authorization";		
			$description = "Autorisation de paiement de la formation";			
		}
*/

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
				  ->setDate(date('d/m/y'))
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
        
        foreach($result as $row){
        	$liste .= "<br /><p><a href='commander/id/".$row->getIdFormationDispo()."'>".$row->getNom()."</a></p>";
        }
        
        $this->view->liste = $liste;
    }
    
    public function sendcommandtopaypalAction(){
    	$authInfo = new Zend_Service_PayPal_Data_AuthInfo(
			'jbduza_1319099049_biz_api1.gmail.com',
			'1319099073',
			'AZ5Qm4qEIjcG7hc-HNVmIi-E3qTnAuLQy.pWX8p-SxdJEc6n.Il-qGWQ');
			
			
		$self = "http://dev.easylia.com/formation/enregistrercommande";
		$amount = $this->utilisateur->montant;	
				
		$paypal = new Zend_Service_PayPal_Nvp($authInfo);
		$params = array('NOSHIPPING' => 1);
					
 		$reponse = $paypal->setExpressCheckout($this->commande->amount, $self.'?status=ok', $self.'?status=cancel', $params, $this->commande->payement_action, $this->commande->quantite, $this->commande->type, $this->commande->description);

 			 				
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
		
		$this->view->date_formation = $date;
		
		$this->view->heure_formation = $formation->getHeureDebut();
		
		$this->view->type_formation = $formation->getType()." - ".$formation->getNombreHeure()."H";
	}

	public function checkcertificationAction(){
		// Vérifie si le formateur possède la certification pour accepter une formation
		       
		$this->getHelper('layout')->disableLayout();
		       
        $historique = $this->historique_mapper->findByIdUtilisateur($this->utilisateur->id_utilisateur);
        
        $id_certifications = array();
        
        foreach($historique as $row){
        	if($row->getScore() >= 70)
        	array_push($id_certifications, $row->getIdCertification());
        }
        
        $certifications = $this->certification_mapper->fetchAll();
        
        $type = array(); 
        
        foreach($certifications as $row){
        	if(in_array($row->getidCertification(), $id_certifications)){
        		array_push($type, $row->getType());
        	}
        }
        
        $formation = new Application_Model_Formations();
		$this->formation_mapper->find($this->getRequest()->getParam('id_formation'), $formation);
		
		if(in_array($formation->getType(), $type))
			$this->view->result = "true";
		else
			$this->view->result = "false";
			
	}

}





















