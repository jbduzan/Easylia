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
		$this->nom_groupe = $this->groupeMapper->getGroupeNameWithId($this->utilisateur->id_groupe); 
		$this->document_mapper = new Application_Model_DocumentMapper();

    }

    public function indexAction()
    {
        // action body
    }

    public function uploadAction()
    {
    	$this->getHelper('layout')->disableLayout();
    	$request = $this->getRequest();
    	
		$adapter = new Zend_File_Transfer_Adapter_Http();
		
		$path = "/home/easylia/production/public/documents/";
						
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
			$path = $path.$this->utilisateur->id_utilisateur.'-'.$request->getParam('type')."-".$temp_path[2];
			
			$adapter->addFilter('Rename', array('target' => $path, 'overwrite' => true));
			if($adapter->receive()){
				// Si on a bien reçu le fichier, on renseigne les infos dans la bdd
				$document = new Application_Model_Document();
	    			
    			$document->setType($request->getParam('type'))
    					 ->setChemin($adapter->getFileName())
    					 ->setDateUpload(date("d/m/Y"))
    					 ->setIdUtilisateur($this->utilisateur->id_utilisateur);
    					 
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
			 ->addValidator('Size', false, '10000000');
    	}else if($type == "casier"){
			$file->addValidator('Extension', false, array('pdf', 'doc', 'docx', 'jpg', 'bmp'))
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

}



