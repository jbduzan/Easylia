<?php

class Application_Model_ReponsesCertificationMapper
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
            $this->setDbTable('Application_Model_DbTable_ReponsesCertification');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_ReponsesCertification $reponse){
        $data = array(
            'id_reponse_certification' => $reponse->getIdReponseCertification(),
            'id_question' => $reponse->getIdQuestion(),
            'reponse' => $reponse->getReponse(),
            'id_utilisateur' => $reponse->getIdUtilisateur()
        );
        
        if(null === ($id = $historique->getIdReponseCertification())){
            unset($data['id_reponse_certification']);
            $id = $this->getDbTable()->insert($data);
        }else{
            $id = $this->getDbTable()->update($data, array('id_reponse_certification = ?' => $id));
        }
        
        return $id;
    }
        
    // Trouve un enregistrement en fonction de son id
    public function find($id_reponse_certification, Application_Model_ReponsesCertification $reponse){
        $result = $this->getDbTable()->find($id_reponse_certification);
        
         if(0 == count($result)){
                return;
            }
            $row = $result->current();
            $reponse->setIdQuestion($row->id_question)
            	    ->setReponse($row->reponse)
            	    ->setIdUtilisateur($row->id_utilisateur);
    }
    
    // Retourne tous les enregistrement
    public function fetchAll(){
       $resultSet = $this->getDbTable()->fetchAll();
       $entries = array();
              
       foreach($resultSet as $row){
           $entry = new Application_Model_ReponsesCertification();
		   $entry->setIdReponseCertification($row->id_reponse_certification)
		   		 ->setIdQuestion($row->id_question)
		   		 ->setReponse($row->reponse)
		   		 ->setIdUtilisateur($row->id_utilisateur);
           $entries[] = $entry;
       }

       return $entries;
    }
        
    public function findByIdUtilisateur($id_utilisateur){
        $select = $this->getDbTable()->select()->where("id_utilisateur = ?", $id_utilisateur);
        $result = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        
        foreach($result as $row){
        	$reponse = new Application_Model_ReponsesCertification();
        	$reponse->setIdReponseCertification($row->id_reponse_certification)
        			->setIdQuestion($row->id_question)
        			->setReponse($row->reponse)
        			->setIdUtilisateur($row->id_utilisateur);
	        
	        array_push($entries, $reponse);
        }
        
        return $entries;
        
    }   

}

