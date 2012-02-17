<?php

class FactureController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');
        $this->commande = new Zend_Session_Namespace('commande');              
		$this->facture_mapper = new Application_Model_FacturesMapper();
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();
        $this->formation_mapper = new Application_Model_FormationsMapper();
        $this->groupe_mapper = new Application_Model_GroupesMapper();
        $this->nom_groupe = $this->groupe_mapper->getGroupeNameWithId($this->utilisateur->id_groupe);
    }

    public function indexAction()
    {
        // Liste des formations sur laquelle le client peut éditer une facture.
        
        $liste_facture = $this->facture_mapper->fetchAll();
               
        $liste = "";
        
        foreach($liste_facture as $row){
        
        	if($row->getIdUtilisateur() == $this->utilisateur->id_utilisateur){
        		$liste .= "<p><a href='facture/genererFacture'>Facture n° ".$row->getNumeroFacture().", ".$row->getMontant()." €";
        	}
        }
        
        $this->view->liste = $liste;
    }

    protected function genererFacture($id_utilisateur, $montant, $date)
    {    	
        // Génère une facture au format pdf
		$utilisateur = new Application_Model_Utilisateurs();
		
		$this->utilisateur_mapper->find($id_utilisateur, $utilisateur);
	
		$facture = new Zend_Service_LiveDocx_MailMerge(array(
			"username" => "jbduzan",
			"password" => "zorander33"
		));
		
		$invoice_number = rand(0, 9999);
						
		$facture->setLocalTemplate('invoice_template.doc');
	
        $date = explode(' ', $date);

		$facture->assign('phone', $utilisateur->getTelephone())
         		->assign('customer_number', $utilisateur->getIdUtilisateur())
         		->assign('invoice_number',  $invoice_number)
          		->assign('date', $date[1])
        		->assign('total',  $montant)
        		->assign('prénom',  'trucmuche');

		
		$facture->createDocument();
			
		
		$document = $facture->retrieveDocument('pdf');
		
		$filename = "documents/facture-".$invoice_number.'.pdf';
		
		file_put_contents($filename, $document);        
		
		$facture = new Application_Model_Factures();
		
		$facture->setNumeroFacture($invoice_number)
				->setIdUtilisateur($utilisateur->getIdUtilisateur())
				->setMontant($montant)
				->setNumeroFacture($invoice_number);
				
		//$this->facture_mapper->save($facture);
		$file = fopen($filename, 'r+');

		fwrite($file, $document);
		
    }
    
    public function genererfactureAction(){
    	// On génère une facture à partir de l'id transmis

    	// On vérifie que l'utilisateur est connecté est que c'est bien le bon utilisateur

    	$request = $this->getRequest();

    	$formation = new Application_Model_Formations();
    	$this->formation_mapper->find($request->getParam('id'), $formation);

    	if(!$this->utilisateur->is_logged || $this->utilisateur->id_utilisateur != $formation->getIdClient())
    		$this->_redirector->goToUrl('profil-utilisateur');

    	// On génère la facture par rapport au info sur la formation

    	$montant = $formation->getNombreHeure() * 55;
    	$this->genererfacture($this->utilisateur->id_utilisateur, $montant, $formation->getHeureDebut());
    }


}





