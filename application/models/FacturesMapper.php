<?php

class Application_Model_FacturesMapper
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
            $this->setDbTable('Application_Model_DbTable_Factures');
        }
        return $this->_dbTable;
        
    }
    
    public function save(Application_Model_Factures $facture){
    	$data = array(
    		"id_facture" => $facture->getIdFacture(),
    		"id_utilisateur" => $facture->getIdUtilisateur(),
    		"id_fournisseur" => $facture->getIdFournisseur(),
    		"montant" => $facture->getMontant(),
    		"numero_facture" => $facture->getNumeroFacture(),
    		"date_creation" => $facture->getDateCreation(),
    		"paye" => $facture->getPaye()
    	);
    	
    	$id = '';

    	if(null === ($id = $facture->getIdFacture())){
            unset($data['id_facture']);
            $id = $this->getDbTable()->insert($data);
        }else{
            $id = $this->getDbTable()->update($data, array('id_facture = ?' => $id));
        }
        return $id;
    }	


	public function find($id_facture, Application_Model_Factures $facture){
		$result = $this->getDbTable()->find($id_facture);
		
		if(count($result) == 0)
			return;
			
		$row = $result->current();
		
		$facture->setIdFacture($row->id_facture)
				->setIdUtilisateur($row->id_utilisateur)
				->setIdFournisseur($row->id_fournisseur)
				->setMontant($row->montant)
				->setNumeroFacture($row->numero_facture)
				->setDateCreation($row->date_creation)
				->setPaye($row->paye);
	}
	
	public function fetchAll(){
		$result = $this->getDbTable()->fetchAll();
		
		$entries = array();
		
		foreach($result as $row){
			$facture = new Application_Model_Factures();
			
			$facture->setIdFacture($row->id_facture)
					->setIdUtilisateur($row->id_utilisateur)
					->setIdFournisseur($row->id_fournisseur)
					->setMontant($row->montant)
					->setNumeroFacture($row->numero_facture)
					->setDateCreation($row->date_creation)
					->setPaye($row->paye);
					
			array_push($entries, $facture);
		}
		
		return $entries;
	}

	// Ressort les données au format flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
       // Setup sort and search SQL
       $sort_sql = "$sort_name $sort_order";
       $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';

       // Get total count of records
       $sql = "select * from Factures $search_sql";

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

       $select = $this->getDbTable()->select()->from('Factures')->limit($rp, $page_start)->order($sort_sql);

       if($search_sql != '')
           $select->where($search_sql);

       $result = $this->getDbTable()->fetchAll($select);

       foreach($result as $row){

       		if($row->paye == 1)
				$paye = "<img class='icone_ok' src='/images/icone_ok_16.png' />";
	        else
	        	$paye = "<img class='icone_erreur' src='/images/icone_erreur_16.png' />";

           $data['rows'][] = array(
               'id' => $row->id_facture,
               'cell' => array($row->numero_facture, $row->montant." €", $row->date_creation, $paye)
           );
       }

       return json_encode($data);        
    }

}

