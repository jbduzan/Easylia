<?php

class Application_Model_FaqMapper
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
            $this->setDbTable('Application_Model_DbTable_Faq');
        }
        return $this->_dbTable;
        
    }
    
    public function save(Application_Model_Faq $faq){
    	$data = array(
    		"id_faq" => $faq->getIdFaq(),
    		"question" => $faq->getQuestion(),
    		"reponse" => $faq->getReponse(),
    		"active" => $faq->getActive(),
    		"categorie" => $faq->getCategorie()
    	);
    	
    	if(null === ($id = $faq->getIdFaq())){
            unset($data['id_faq']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_faq = ?' => $id));
        }
    }	


	public function find($id_faq, Application_Model_Faq $faq){
		$result = $this->getDbTable()->find($id_faq);
		
		if(count($result) == 0)
			return;
			
		$row = $result->current();
		
		$faq->setIdFaq($row->id_faq)
				->setQuestion($row->question)
				->setReponse($row->reponse)
				->setActive($row->active)
				->setCategorie($row->categorie);
	}
	
	public function fetchAll(){
		$result = $this->getDbTable()->fetchAll();
		
		$entries = array();
		
		foreach($result as $row){
			$faq = new Application_Model_Faq();
			
			$facture->setidFaq($row->id_faq)
					->setQuestion($row->question)
					->setReponse($row->reponse)
					->setActive($row->active)
					->setCategorie($row->categorie);
					
			array_push($entries, $faq);
		}
		
		return $entries;
	}
	
	public function fetchAllActive(){
		$select = $this->getDbTable()->select()->where("active = 1");
		$result = $this->getDbTable()->fetchAll($select);
		
		$entries = array();
		
		foreach($result as $row){
			$faq = new Application_Model_Faq();
			
			$faq->setidFaq($row->id_faq)
					->setQuestion($row->question)
					->setReponse($row->reponse)
					->setActive($row->active)
					->setCategorie($row->categorie);
					
			array_push($entries, $faq);
		}
		
		return $entries;
	}
	
		// Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Faq $search_sql";
        
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
        
        $select = $this->getDbTable()->select()->limit($rp, $page_start);
        
        if($sort_sql != "")
	        $select->order($sort_sql);
                
        if($search_sql != '')
            $select->where($search_sql);
            
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
        	if($row->active == 1)
        		$active = "oui";
        	else
        		$active = "non";
        	
            $data['rows'][] = array(
                'id' => $row->id_faq,
                'cell' => array($row->question, $row->reponse, $active, $row->categorie)
            );
        }
        
        return json_encode($data);        
    }

	public function delete($id_faq){
		$this->getDbTable()->delete("id_faq = $id_faq");
	}

}

