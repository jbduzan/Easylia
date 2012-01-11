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
    
    	$utilisateur = new Application_Model_Utilisateurs();
    	
    	$this->utilisateur_mapper->find($this->utilisateur->id_utilisateur, $utilisateur);
    	
    	if($utilisateur->getDocumentEnvoye()){
    		$this->_redirector->goToSimple('index', 'utilisateurs');
    	}
    	
        // Affiche un formulaire pour uploader le document
        $form = new Application_Form_Uploaddocument();
        
        $array = array();
        
        if($this->getRequest()->getParam('type') == 'motivation'){
			$this->view->type = 'lettre de motivation';
			$file_extension = "motivation-".$this->utilisateur->id_utilisateur.".doc";
		}

		if($this->getRequest()->getParam('type') == 'photo'){
			$this->view->type = 'photo';
			$file_extension = "photo-".$this->utilisateur->id_utilisateur.".png";
		}
		
		if($this->getRequest()->getParam('type') == 'cv'){
			$this->view->type = 'cv';
			$file_extension = "cv-".$this->utilisateur->id_utilisateur.".doc";
		}
		
		if($this->getRequest()->getParam('type') == 'rib'){
			$this->view->type = 'rib';
			$file_extension = "rib-".$this->utilisateur->id_utilisateur.".png";
		}

		
      
        $form->populate($array);
        
        $this->view->form = $form;

		// On récupère le document envoyé et on le sauvegarde
		$request = $this->getRequest();
        
        if($request->isPost()){
           	if($form->isValid($request->getPost())){
        	
        		$adapter = new Zend_File_Transfer_Adapter_Http();
        		
        		$path = "/home/easylia/public/documents/".$file_extension;
        		       		
        		$adapter->addFilter('Rename', array('target' => $path , 'overwrite' => true));
        		
        		if($this->validateFile($request->getParam('type'), $adapter)){
        		
	        		if($adapter->receive()) {
	        			$document = new Application_Model_Document();
	        			
	        			$document->setType($request->getParam('type'))
	        					 ->setChemin($adapter->getFileName())
	        					 ->setDateUpload(date("d/m/Y"))
	        					 ->setIdUtilisateur($this->utilisateur->id_utilisateur);
	        					 
	        			$this->document_mapper->save($document);
	        			
	        			$this->_redirector->goToSimple('index', 'utilisateurs');
	        		}else{
					    $messages = $adapter->getMessages();
					    echo implode("\n", $messages);
					}
				}else{
					echo $this->validateFile($form->getValue('type'), $adapter);
				}
				
				
        	
			}	  
        }

        
        
    }
    
    public function validateFile($type, $file){
    	// Vérifie si un fichier est valide en fonction du fichier envoye
    	
    	if($type == "motivation" || $type == "cv"){
    		// Si l'on veut la lettre de motivation ou le cv
    		
    		$file->addValidator('Extension', false, array('doc', 'docx', 'pdf'))
    			 ->addValidator('Size', false, '500000');
    			 
    		if($file->isValid())
    			return true;
    		else
    			return $error = "L'extension de votre fichier ou sa taille n'est pas valide";
    			
    	}else if($type == "photo" || $type == "rib"){
    		// Si l'utilisateur veut uploader sa photo
    		
    		$file->addValidator('extension', false, array('jpg', 'jpeg', 'gif', 'bmp', 'png'))
    			 ->addValidator('size', false, '50000');
    			 
    		if($file->isValid())
    			return true;
   			else
    			return $error = "L'extension de votre fichier ou sa taille n'est pas valide";
    	}
    }

	public function checkAndValideDocument(){
		// Vérifie si les 4 docs on été uploadé et le valide en bdd si c'est le cas
	}

}



