<?php

class Application_Model_DocumentMapper
{
	protected $_dbTable;
    
    public function setDbTable($dbTable){
        
        if(is_string($dbTable)){
            $dbTable = new $dbTable();
        }
        if(!$dbTable instanceof Zend_Db_Table_Abstract){
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
        
    }
    
    public function getDbTable(){
        
        if(null === $this->_dbTable){
            $this->setDbTable('Application_Model_DbTable_Documents');
        }
        return $this->_dbTable;
        
    }
    
    public function save(Application_Model_Document $document){
    	$data = array(
    		'id_document' => $document->getIdDocument(),
    		'titre' => $document->getTitre(),
    		'type' => $document->getType(),
    		'chemin' => $document->getChemin(),
    		'date_upload' => $document->getDateUpload(),
    		'date_validite' => $document->getDateValidite(),
    		'id_utilisateur' => $document->getIdUtilisateur(),
    		'id_facture' => $document->getIdFacture()
    	);
    	
    	if(null === ($id = $document->getIdDocument())){
            unset($data['id_document']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_document = ?' => $id));
        }    	
    	
    }
    
    public function find($id_document, Application_Model_Document $document){
    	$result = $this->getDbTable()->find($id_document);
    	
    	if(count($result) == 0)
    		return;
    	
    	$row = $result->current();
    	
    	$document->setIdDocument($row->id_document)
    			 ->setTitre($row->titre)
    			 ->setType($row->type)
    			 ->setChemin($row->chemin)
    			 ->setDateUpload($row->date_upload)
    			 ->setDateValidite($row->date_validite)
    			 ->setIdUtilisateur($row->id_utilisateur)
    			 ->setIdFacture($row->id_facture);
    }
    
    public function fetchAll($id_utilisateur = null){
    	$select = $this->getDbTable()->select();
    	
    	if($id_utilisateur != "")
    		$select->where('id_utilisateur = ?', $id_utilisateur);
    	
    	$result = $this->getDbTable()->fetchAll($select);
    	
    	if(count($result) == 0)
    		return;
    		
    	$entries = array();
    	    	
    	foreach($result as $row){
    		$document = new Application_Model_Document();
    		
    		$document->setIdDocument($row->id_document)
	    			 ->setTitre($row->titre)
	    			 ->setType($row->type)
	    			 ->setChemin($row->chemin)
	    			 ->setDateUpload($row->date_upload)
	    			 ->setDateValidite($row->date_validite)
	    			 ->setIdUtilisateur($row->id_utilisateur)
	    			 ->setIdFacture($row->id_facture);

			array_push($entries, $document);
    	}
    	
    	return $entries;
    }

	public function findWithIdAndType($id_utilisateur, $type, Application_Model_Document $document){
		$select = $this->getDbTable()->select()->where("id_utilisateur = ?", $id_utilisateur)->where("type = ?", $type);
		
		$result = $this->getDbTable()->fetchAll($select);
				
		$row = $result->current();
								
		$document->setIdDocument($row->id_document)
    			 ->setTitre($row->titre)
    			 ->setType($row->type)
    			 ->setChemin($row->chemin)
    			 ->setDateUpload($row->date_upload)
    			 ->setDateValidite($row->date_validite)
    			 ->setIdUtilisateur($row->id_utilisateur)
    			 ->setIdFacture($row->id_facture);

	}

}

