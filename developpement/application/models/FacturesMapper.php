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
    		"numero_facture" => $facture->getNumeroFacture()
    	);
    	
    	if(null === ($id = $facture->getIdFacture)){
            unset($data['id_facture']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_facture = ?' => $id));
        }
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
				->setNumeroFacture($row->numero_facture);
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
					->setNumeroFacture($row->numero_facture);
					
			array_push($entries, $facture);
		}
		
		return $entries;
	}

}

