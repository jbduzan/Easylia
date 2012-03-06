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
        $this->document_mapper = new Application_Model_DocumentMapper();
        $this->groupe_mapper = new Application_Model_GroupesMapper();
        $this->nom_groupe = $this->groupe_mapper->getGroupeNameWithId($this->utilisateur->id_groupe);
    }

    public function preDispatch(){
        $this->view->render('utilisateurs/menu-connecte.phtml');
        $this->view->render('utilisateurs/sidebar.phtml');
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
				
		$this->facture_mapper->save($facture);
		$file = fopen($filename, 'r+');

		fwrite($file, $document);
		
    }
    
    public function genererfactureAction(){
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    	// On génère une facture à partir de l'id transmis

    	$request = $this->getRequest();
                
        // On vérifie que l'utilisateur est connecté et que c'est bien le bon utilisateur
        $formation = new Application_Model_Formations();
        $this->formation_mapper->find($request->getParam('id'), $formation);

        if(!$this->utilisateur->is_logged || $this->utilisateur->id_utilisateur != $formation->getIdClient())
            $this->_redirector->goToUrl('profil-utilisateur');

        // On génère la facture par rapport au info sur la formation

        $montant = $formation->getNombreHeure() * 55;
        $this->genererfacture($this->utilisateur->id_utilisateur, $montant, $formation->getHeureDebut());
    }

    public function downloadfactureAction(){
        // Permet de télécharger une facture
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();

        // On récupère la facture correcpondante au numéro
        $result = $this->facture_mapper->fetchAll();

        $chemin_facture = "";

        foreach($result as $row){
            if($row->getNumeroFacture() == $request->getParam('numero_facture') && $row->getIdUtilisateur() == $this->utilisateur->id_utilisateur){
                $result2 = $this->document_mapper->fetchAll();

                foreach($result2 as $row2){
                    if($row2->getIdFacture() == $row->getIdFacture()){
                        $chemin_facture = $row2->getChemin();
                        break;
                    }
                } 
                break;
            }
        }

        $chemin = explode('/', $chemin_facture);
        echo $chemin[sizeof($chemin) - 1];
    }

    public function gererfactureadminAction(){
        // Page de gestion des facture pour les admins

        // Liste toutes les factures non payées
        $result = $this->facture_mapper->fetchAll();

        //print_r($result);
    }

    public function getfactureadminAction(){
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        
        // Request parameters received via GET from flexigrid.
        $sort_column = $this->_getParam('sortname','id_facture'); 
        $sort_order = $this->_getParam('sortorder','asc'); 
        $page = $this->_getParam('page',1);
        $limit = $this->_getParam('rp',17);
        $offset = (($page - 1) * $limit);
        $search_column = $this->_getParam('qtype');
        $search_for = $this->_getParam('query');
        
        $result = $this->facture_mapper->fetchAllForFlexigrid($page, $sort_column, $sort_order, $search_column, $search_for, $limit);

        echo $result;
    }

    public function setfacturepayeAction(){
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 

        $request = $this->getRequest();

        // On marque la facture selectionnée comme payée

        $facture = new Application_Model_Factures();
        $this->facture_mapper->find($request->getParam('id'), $facture);

        $facture->setPaye(1);

        $this->facture_mapper->save($facture);
    }

}





