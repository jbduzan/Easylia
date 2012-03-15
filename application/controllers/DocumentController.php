<?php

class DocumentController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->acl = MyAcl::getInstance();
        $this->utilisateur = new Zend_Session_Namespace('user');      
        $this->utilisateur_mapper = new Application_Model_UtilisateursMapper();             
        $this->groupeMapper = new Application_Model_GroupesMapper();
        $this->facture_mapper = new Application_Model_FacturesMapper();
        $this->page_mapper = new Application_Model_PageDynamiqueMapper();
		$this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->utilisateur->id_groupe); 
		$this->document_mapper = new Application_Model_DocumentMapper();

    }

    public function preDispatch(){
        $this->view->render('utilisateurs/menu-connecte.phtml');
        $this->view->render('utilisateurs/sidebar.phtml');
    }

    public function indexAction()
    {
        $result = $this->document_mapper->fetchAll($id_utilisateur = $this->utilisateur->id_utilisateur);

        $this->view->rows = $result;
    }

    public function uploadAction()
    {
    	$this->getHelper('layout')->disableLayout();
    	$request = $this->getRequest();
    	
		$adapter = new Zend_File_Transfer_Adapter_Http();
		
		$path = "/home/easylia/production/public/documents/";
        //$path = "/Users/jbduzan/Sites/easylia/public/documents";

		// Si le fichier passe la validation
		if(!$this->validateFile($adapter, $request->getParam('type'))){
	    	// Sinon on affiche un message d'erreur
		    $this->view->type = $request->getParam('type');
			$this->view->result = 2;
		}
		else{	
			// On récupère le nom temporaire
			$temp_path = explode('/', $adapter->getFileName());
			
			// Et on le reinjecte avec l'id de l'utilisateur
            if($request->getParam('facture') == 'true')
                $type = $request->getParam('type').$request->getParam('numero_facture');
            else
                $type = $request->getParam('type');

			$path = $path.$this->utilisateur->id_utilisateur.'-'.$type."-".$temp_path[2];
            $path = str_replace(' ', '-', $path);
			
			$adapter->addFilter('Rename', array('target' => $path, 'overwrite' => true));
			if($adapter->receive()){
				// Si on a bien reçu le fichier, on renseigne les infos dans la bdd
				$document = new Application_Model_Document();
	    			
    			$document->setType($request->getParam('type'))
    					 ->setChemin($adapter->getFileName())
    					 ->setDateUpload(date("d/m/Y"))
    					 ->setIdUtilisateur($this->utilisateur->id_utilisateur);

                // Si le document est une facture
                if($request->getParam('facture') == 'true'){
                    // On enregistre les infos de la facture
                    $facture = new Application_Model_Factures();

                    $facture->setIdUtilisateur($this->utilisateur->id_utilisateur)
                            ->setMontant($request->getParam('montant_input'))
                            ->setNumeroFacture($request->getParam('numero_facture'))
                            ->setDateCreation(date('d/m/Y'));

                    $id = $this->facture_mapper->save($facture);

                    // On rajoute l'id de la facture dans les infos du document 

                    $document->setIdFacture($id);

                    $this->view->id_facture = $id;
                }
   					 
    			$this->document_mapper->save($document);
    			
    			$this->view->type = $request->getParam('type');
				$this->view->result = true;
    		}else{
	    		// Sinon on affiche un message d'erreur
			    $this->view->type = $request->getParam('type');
				$this->view->result = 0;
    		}
		}
    }
    
    public function validateFile($file, $type){
    	// Vérifie si un fichier est valide en fonction du fichier envoye
    	
    	if($type == "cv" || $type == "motivation"){
    		$file->addValidator('Extension', false, array('pdf', 'doc', 'docx'))
			 ->addValidator('Size', false, '100000000');
    	}else if($type == "casier"){
			$file->addValidator('Extension', false, array('pdf', 'doc', 'docx', 'jpg', 'bmp'))
			 ->addValidator('Size', false, '10000000');   		
    	}else if($type == "facture"){
            $file->addValidator('Extension', false, array('pdf'))
             ->addValidator('Size', false, '10000000');         
        }
			 
		if($file->isValid())
			return true;
		else{
			return false;
		}
    }

	public function checkAndValideDocument(){
		// Vérifie si les 4 docs on été uploadé et le valide en bdd si c'est le cas
	}

	public function downloadfileAction(){
		// Télécharge le fichier demandé
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = $this->getRequest()->getParam('chemin');
  
		$filepath = "https://in.easylia.com/documents/".$filename;

        $type = explode('.', $filename);

        $limit = count($type);

        $type = $type[$limit - 1];

        // Si c'est un pdf on l'affiche sinon on dl
        if($type == "pdf"){
            // Gestion du cache
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');

            // Informations sur le contenu à envoyer
            header('Content-Type: application/'.$type);
            readfile($filepath);
        }else{
            // Gestion du cache
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: must-revalidate, pre-check=0, post-check=0, max-age=0');

            // Informations sur le contenu à envoyer
            header('Content-Type: '.type);
            header('Content-Disposition: application/force-download; filename="'.$filename.'"');
            header('Content-Disposition: attachement; filename="' . $filename . '"');

            // Informations sur la réponse HTTP elle-même
            header('Date: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 1) . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
            readfile($filepath);
        }
	}

    public function contratformateurAction(){
        // Remplis un contrat pour le formateur

        if(!$this->utilisateur->is_logged || $this->utilisateur->id_groupe != 2)
            $this->_redirector->goToUrl('/profil-utilisateur');

        $utilisateur = new Application_Model_Utilisateurs();
        $this->utilisateur_mapper->find($this->utilisateur->id_utilisateur, $utilisateur);

        // On récupère le texte du contrat
        $contrat = new Application_Model_PageDynamique();
        $result = $this->page_mapper->fetchAll($nom = "Convention prestation de services");

        foreach($result as $row){
            $contrat = $row;
        }

        $contenu = $contrat->get('contenu');

        // On remplace les variables par les infos user
        $contenu = str_replace('{Civilite}', ucfirst($utilisateur->getType()), $contenu);
        $contenu = str_replace('{Prenom}', ucfirst($utilisateur->getPrenom()), $contenu);
        $contenu = str_replace('{NOM}', strtoupper($utilisateur->getNom()), $contenu);
        $contenu = str_replace('{siren}', $utilisateur->getSiren(), $contenu);
        $contenu = str_replace('{Adresse1}', ucfirst($utilisateur->getAdresse()), $contenu);
        
        if($utilisateur->getAdresse2() != '')        
            $contenu = str_replace('{, Adresse 2}', ', '.ucfirst($utilisateur->getAdresse2()), $contenu);
        else
            $contenu = str_replace('{, Adresse 2}', '', $contenu);

        $contenu = str_replace('{code postal}', $utilisateur->getCodePostal(), $contenu);
        $contenu = str_replace('{VILLE}', strtoupper($utilisateur->getVille()), $contenu);
        $contenu = str_replace('{telephone}', $utilisateur->getTelephone(), $contenu);
        $contenu = str_replace('{site internet}', '', $contenu);
        $contenu = str_replace('{email}', $utilisateur->getMail(), $contenu);
        $contenu = str_replace('{date du jour}', date('d/m/Y'), $contenu);
        $contenu= str_replace('{date du jour + 6 mois}', date('d/m/Y', strtotime('+6 month')), $contenu);


        $this->view->contrat = $contenu;
        $this->view->id_utilisateur = $this->utilisateur->id_utilisateur;

        // On regarde si l'utilisateur l'as déjà accepté pour enlever les bouttons
        $result = $this->document_mapper->fetchAll($id_utilisateur = $this->utilisateur->id_utilisateur);

        foreach($result as $row){
            if($row->getType() == 'convention'){
                // On vérifie bien que elle est encore valable
                $current_time = time();
                $date_validite = $row->getDateValidite();
                $date_validite = explode('/', $date_validite);
                $date_validite = mktime(0,0,0,$date_validite[1], $date_validite[0], $date_validite[2]);

                if($date_validite > $current_time){
                    $this->view->contrat_accepte = true;
                    break;
                }
            }
        }
    }


    public function setconventionsigneAction(){
        $this->getHelper('layout')->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        // Enregistre l'acceptation de la convention par un formateur
        $document = new Application_Model_Document();

        // On vérifie que elle n'existe pas sinon on la modifie
        $result = $this->document_mapper->fetchAll($id_utilisateur = $this->utilisateur->id_utilisateur);

        foreach($result as $row){
            $document = $row;
        }

        $document->setType('convention')
                 ->setDateUpload(date('d/m/Y'))
                 ->setDateValidite(date('d/m/Y', strtotime('+6 month')))
                 ->setIdUtilisateur($this->utilisateur->id_utilisateur);

        $this->document_mapper->save($document);

        echo "true";
    }   
}



