<?php

class Application_Model_HistoriqueCertificationsMapper
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
            $this->setDbTable('Application_Model_DbTable_HistoriqueCertifications');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_HistoriqueCertifications $historique){
        $data = array(
            'id_historique' => $historique->getIdHistorique(),
            'date_passage' => $historique->getDatePassage(),
            'date_validite' => $historique->getDateValidite(),
            'score' => $historique->getScore(),
            'id_utilisateur' => $historique->getIdUtilisateur(),
            'id_certification' => $historique->getIdCertification()
        );
        
        if(null === ($id = $historique->getIdHistorique())){
            unset($data['id_historique']);
            $id = $this->getDbTable()->insert($data);
        }else{
            $id = $this->getDbTable()->update($data, array('id_historique = ?' => $id));
        }
        
        return $id;
    }
        
    // Trouve un enregistrement en fonction de son id
    public function find($id_historique, Application_Model_HistoriqueCertifications $historique){
        $result = $this->getDbTable()->find($id_historique);
        
         if(0 == count($result)){
                return;
            }
            $row = $result->current();
            $historique->setIdHistorique($row->id_historique);
            $historique->setDatePassage($row->date_passage);
            $historique->setDateValidite($row->date_validite);
            $historique->setScore($row->score);
            $historique->setIdUtilisateur($row->id_utilisateur);
            $historique->setIdCertification($row->id_certification);
    }
    
    // Retourne tous les enregistrement
    public function fetchAll(){
       $resultSet = $this->getDbTable()->fetchAll();
       $entries = array();
              
       foreach($resultSet as $row){
           $entry = new Application_Model_HistoriqueCertifications();
           $entry->setIdHistorique($row->id_historique);
           $entry->setDatePassage($row->date_passage);
           $entry->setDateValidite($row->date_validite);
           $entry->setScore($row->score);
           $entry->setIdUtilisateur($row->id_utilisateur);
           $entry->setIdCertification($row->id_certification);
           $entries[] = $entry;
       }

       return $entries;
    }
    
    public function findByIdUtilisateurAndCertification($id_utilisateur, $id_certification, Application_Model_HistoriqueCertifications $historique){
        $select = $this->getDbTable()->select()->where("id_utilisateur = ?", $id_utilisateur)->where("id_certification = ?", $id_certification)->order('date_passage desc');
        $result = $this->getDbTable()->fetchAll($select);
        
        $row = $result->current();
        $historique->setIdHistorique($row->id_historique);
        $historique->setDatePassage($row->date_passage);
        $historique->setDateValidite($row->date_validite);
        $historique->setScore($row->score);
        $historique->setIdUtilisateur($row->id_utilisateur);
        $historique->setIdCertification($row->id_certification);
    }
    
    public function findByIdUtilisateur($id_utilisateur){
        $select = $this->getDbTable()->select()->where("id_utilisateur = ?", $id_utilisateur)->order('date_passage desc');
        $result = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        
        foreach($result as $row){
        	$historique = new Application_Model_HistoriqueCertifications();
        	$historique->setIdHistorique($row->id_historique);
	        $historique->setDatePassage($row->date_passage);
	        $historique->setDateValidite($row->date_validite);
	        $historique->setScore($row->score);
	        $historique->setIdUtilisateur($row->id_utilisateur);
	        $historique->setIdCertification($row->id_certification);
	        
	        array_push($entries, $historique);
        }
        
        return $entries;
        
    }   

}

