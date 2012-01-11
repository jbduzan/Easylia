<?php

class Application_Model_PaypalAutorizedPayementMapper
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
           $this->setDbTable('Application_Model_DbTable_PaypalAutorizedPayment');
       }
       return $this->_dbTable;

    }
    
    public function save(Application_Model_PaypalAutorizedPayement $payement){
    	$data = array(
           	'id_payement' => $payement->getIdPayement(),
        	'id_transaction' => $payement->getIdTransaction(),
        	'date_honneur' => $payement->getDateHonneur(),
        	'date_validite' => $payement->getDateValidite() ,
        	'montant' => $payement->getMontant()
      	);

       if(null === ($id = $payement->getIdPayement())){
           unset($data['id_payement']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_payement = ?' => $id));
       }

    }
    
    public function find($id, Application_Model_PaypalAutorizedPayement $payement){
    	$result = $this->getDbTable()->find($id);
    	
    	if(count($result) == 0)
    		return;
    		
    	$row = $result->current();
    	$payement->setIdPayement($row->id_payement)
    			 ->setIdTransaction($row->id_transaction)
    			 ->setDateHonneur($row->date_honneur)
    			 ->setDateValidite($row->date_validite)
    			 ->setMontant($row->montant);
    }
    
    public function fetchALl(){
    	$resultSet = $this->getDbTable()->fetchAll();
    	
    	$entries = array();
    	
    	foreach($resultSet as $row){
    		$entry = new Application_Model_PaypalAutorizedPayement();
    		$entry->setIdPayement($row->id_payement)
    			  ->setIdTransaction($row->id_transaction)
    			  ->setDateHonneur($row->date_honneur)
    			  ->setDateValidite($row->date_validite)
    			  ->setMontant($row->montant);
    			  
    		array_push($entries, $entry);
    	}
    	
    	return $entries;
    }
    
    public function delete($id_payement){
    	$this->getDbTable()->delete("id_payement = $id_payement");
    }


}

