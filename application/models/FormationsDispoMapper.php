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
    public function save(Application_Model_FormationsDispo $formation_dispo){
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

    // Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Formations_dispo $search_sql";
               
        $select = $this->getDbTable()->select($sql);
        $result = $this->getDbTable()->fetchAll($select);
        $total = count($result);
        
        // Setup paging
        $page_start = ($page-1)*$rp;
        $limit_sql = "limit $page_start, $rp";
        
        // Return json Data
        $data = array();
        $data['page'] = $page;
        $data['total'] = $total;
        $data['rows'] = array();
        
        $select = $this->getDbTable()->select()->from('Formations_dispo')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
            
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
            
            $data['rows'][] = array(
                'id' => $row->id_formation_dispo,
                'cell' => array($row->nom, $row->type)
            );
        }
        
        return json_encode($data);        
    }

    public function delete($id_formation_dispo){ 
        $this->getDbTable()->delete("id_formation_dispo = $id_formation_dispo");
    }


}

