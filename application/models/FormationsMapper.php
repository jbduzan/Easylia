<?php

class Application_Model_FormationsMapper
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
            $this->setDbTable('Application_Model_DbTable_Formations');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_Formations $formation){
        $data = array(
            'id_formation' => $formation->getIdFormation(),
            'nombre_heure' => $formation->getNombreHeure(),
            'type' => $formation->getType(),
            'id_client' => $formation->getIdClient(),
            'id_formateur' => $formation->getIdFormateur(),
            'payee' => $formation->getPayee(),
            'date' => $formation->getDate(),
            'heure_debut' => $formation->getHeureDebut(),
            'id_formation_dispo' => $formation->getIdFormationDispo()
        );
         
        $id = ""; 
                
        if(null === ($id = $formation->getIdFormation())){
            unset($data['id_formation']);
            $id = $this->getDbTable()->insert($data);
        }else{
            $id = $this->getDbTable()->update($data, array('id_formation = ?' => $id));
        }
        return $id;
    }
    
    // Trouve une formation en fonction de son id
    public function find($id_formation, Application_Model_Formations $formation){
        $result = $this->getDbTable()->find($id_formation);
        
         if(0 == count($result)){
                return;
            }
            $row = $result->current();
            $formation->setIdFormation($row->id_formation)
            		  ->setNombreHeure($row->nombre_heure)
            		  ->setType($row->type)
            		  ->setIdClient($row->id_client)
            		  ->setIdFormateur($row->id_formateur)
            		  ->setPayee($row->payee)
            		  ->setDate($row->date)
            		  ->setHeureDebut($row->heure_debut)
            		  ->setIdFormationDispo($row->id_formation_dispo);
    }
    
    public function fetchALl(){
    	$result = $this->getDbTable()->fetchAll();
    	
    	if(count($result) == 0)
    		return;
    	
    	$entries = array();
    		
    	foreach($result as $row){
    		$entry = new Application_Model_Formations();
            $entry->setIdFormation($row->id_formation)
            	  ->setNombreHeure($row->nombre_heure)
            	  ->setType($row->type)
            	  ->setIdClient($row->id_client)
            	  ->setIdFormateur($row->id_formateur)
            	  ->setPayee($row->payee)
            	  ->setDate($row->date)
            	  ->setHeureDebut($row->heure_debut)
            	  ->setIdFormationDispo($row->id_formation_dispo);
            	  
            array_push($entries, $entry);
    	}
    	
    	return $entries;
    	
    }
    
	// Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp, $sans_formateur, $type = null){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Formations $search_sql";
        
        if($sans_formateur == true)
        	$sql .= "and where id_formateur is null";
        
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
        
        $select = $this->getDbTable()->select()->from('Formations')->limit($rp, $page_start)->order($sort_sql);
        
        // Si on a un type de formation on filtre par type
        if(count($type) > 0){
 	       	$select->where("type in (?)", $type);
        }
                
        if($sans_formateur == "true")
        	$select->where('id_formateur is null');

        if($search_sql != '')
            $select->where($search_sql);
            
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
            // Le nom du client et du formateur
            $utilisateur_mapper = new Application_Model_UtilisateursMapper();
			$client = new Application_Model_Utilisateurs();
			$formateur = new Application_Model_Utilisateurs();
			$utilisateur_mapper->find($row->id_client, $client);
			$utilisateur_mapper->find($row->id_formateur, $formateur);
           	            
           	if($row->payee == 1)
           		$payee = "oui";
           	else
           		$payee = "non";
           		
           	$date = $row->date." - ".$row->heure_debut; 
           	            
            $data['rows'][] = array(
                'id' => $row->id_formation,
                'cell' => array($row->type, $row->nombre_heure, $client->getNom().' '.$client->getPrenom(), $formateur->getNom()." ".$formateur->getPrenom(), $date, $payee)
            );
        }
        
        return json_encode($data);        
    }
        
    public function delete($id_formation){
    	$this->getDbTable()->delete("id_formation = $id_formation");
    }
    
    public function fetchAllWithIdFormateur($id_formateur){
    	$select = $this->getDbTable()->select()->where('id_formateur = ?', $id_formateur);
    	$result = $this->getDbTable()->fetchAll($select);
    	
    	if(count($result) == 0)
    		return;
    	
    	$entries = array();
    		
    	foreach($result as $row){
    		$entry = new Application_Model_Formations();
            $entry->setIdFormation($row->id_formation)
            	  ->setNombreHeure($row->nombre_heure)
            	  ->setType($row->type)
            	  ->setIdClient($row->id_client)
            	  ->setIdFormateur($row->id_formateur)
            	  ->setPayee($row->payee)
            	  ->setDate($row->date)
            	  ->setHeureDebut($row->heure_debut)
            	  ->setIdFormationDispo($row->id_formation_dispo);
            	  
            array_push($entries, $entry);
    	}
    	
    	return $entries;
    }
    
    public function getFormationJson($id_formateur){
    	// Retourne la liste des formations au format Json
    	
    	$select = $this->getDbTable()->select()->where('id_formateur = ?', $id_formateur);
    	$result = $this->getDbTable()->fetchAll($select);
    	    	
    	$data = array();
    	
    	foreach($result as $row){
    		$date_formated = explode(' ', $row->heure_debut);
    		$date = explode('/', $date_formated[1]);
    		$heure = substr($date_formated[2], '0', '-1');
    		$timestamp_start = mktime($heure, 0, 0, $date[1], $date[0], 2012);
    		$timestamp_end = $timestamp_start + $row->nombre_heure * 3600;
    		$data[] = array(
    			'id' => $row->id_formation,
    			'title' => $row->type,
    			'start' => $timestamp_start,
    			'end' => $timestamp_end,
    			'allDay' => false
    		);
    	}
    	
    	return json_encode($data);
    	
    }
  
}

