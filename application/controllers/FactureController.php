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

    public function genererFacture($id_utilisateur, $montant, $date)
    {    	
        // Génère une facture au format pdf
		$utilisateur = new Application_Model_Utilisateurs();
		
		$this->utilisateur_mapper->find($id_utilisateur, $utilisateur);
	
		$test = new Zend_Service_LiveDocx_MailMerge(array(
			"username" => "jbduzan",
			"password" => "zorander33"
		));
		
		$invoice_number = rand(0, 9999);
						
		$facture->setLocalTemplate('invoice_template.docx');
	
		$facture->assign('phone', $utilisateur->getTelephone())
         		 ->assign('customer_number', $utilisateur->getIdUtilisateur())
         		 ->assign('invoice_number',  $invoice_number)
          		 ->assign('date', $date)
        		 ->assign('total',  $montant);

		
		$facture->createDocument();
			
		
		$document = $facture->retrieveDocument('pdf');
		
		$filename = "documents/facture-".$invoice_number;
		
		file_put_contents($filename, $document);        
		
		$facture = new Application_Model_Factures();
		
		$facture->setNumeroFacture($invoice_number)
				->setIdUtilisateur($utilisateur->getIdUtilisateur())
				->setMontant($monteant)
				->setNumeroFacture($invoice_number);
				
		$this->facture_mapper->save($facture);
		
		fwrite($filename, $document);
		
    }
    
    public function genererfactureAction(){
    	$this->genererfacture();
    }


}





