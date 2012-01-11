<?php

class Application_Model_LoginInterditMapper
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
            $this->setDbTable('Application_Model_DbTable_LoginInterdit');
        }
        return $this->_dbTable;
        
    }
    
    public function save(Application_Model_LoginInterdit $login_interdit){
   		$data = array(
            'login' => utf8_decode($login_interdit->getLogin()),
        );
        
        if(null === ($id = $login_interdit->getIdLoginInterdit())){
            unset($data['id_login_interdit']);
            $this->getDbTable()->insert($data);
        }else{
           $this->getDbTable()->update($data, array('id_login_interdit = ?' => $id));
        }

    }
    
    public function find($id_login, Application_Model_LoginInterdit $login_interdit){
    	$result = $this->getDbTable()->find($id_login);
	    if(0 == count($result)){
            return;
        }
        $row = $result->current();
        $login_interdit->setIdLoginInterdit($row->id_login_interdit)
        			   ->setLogin($row->login);
        
    }
    
    public function fetchAll(){
    	$result = $this->getDbTable()->fetchALl();
    	
    	if(count($result) == 0){
    		return;
    	}
    	
    	$entries = array();
    	foreach($result as $row){
    		$entry = new Application_Model_LoginInterdit();
    		$entry->setIdLoginInterdit($row->id_login_interdit)
    			  ->setLogin($row->login);
    			  
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
        $sql = "select * from Login_interdit $search_sql";
        
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

        $select = $this->getDbTable()->select()->from('Login_interdit')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
                    
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
            $data['rows'][] = array(
                'id' => $row->id_login_interdit,
                'cell' => array($row->login)
            );
        }
        
        return json_encode($data);        
    }

    
    public function delete($id_login){
    	$this->getDbTable()->delete("id_login_interdit = $id_login");
    }

}

?>