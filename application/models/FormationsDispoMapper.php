<?php

class Application_Model_FormationsDispoMapper
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
            $this->setDbTable('Application_Model_DbTable_FormationsDispo');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_FormationsDispos $formation_dispo){
        $data = array(
            'id_formation_dispo' => $formation_dispo->getIdFormationDispo(),
            'nom' => $formation_dispo->getNom(),
            'type' => $formation_dispo->getType()
        );
        
        if(null === ($id = $formation_dispo->getIdFormationDispo())){
            unset($data['id_formation_dispo']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_formation_dispo = ?' => $id));
        }
    }
    
    // Trouve une formation_dispo en fonction de son id
    public function find($id_formation_dispo, Application_Model_FormationsDispo $formation_dispo){
        $result = $this->getDbTable()->find($id_formation_dispo);
        
         if(0 == count($result)){
                return;
            }
            $row = $result->current();
            $formation_dispo->setIdFormationDispo($row->id_formation_dispo)
		            		->setNom($row->nom)
		            		->setType($row->type);
    }
    
    public function fetchALl(){
    	$result = $this->getDbTable()->fetchAll();
    	
    	if(count($result) == 0)
    		return;
    	
    	$entries = array();
    		
    	foreach($result as $row){
    		$entry = new Application_Model_FormationsDispo();
            $entry->setIdformationDispo($row->id_formation_dispo)
		          ->setNom($row->nom)
		          ->setType($row->type);            	  
            array_push($entries, $entry);
    	}
    	
    	return $entries;
    	
    }


}

