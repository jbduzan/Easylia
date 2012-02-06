<?php

class Application_Model_MailMapper
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
            $this->setDbTable('Application_Model_DbTable_Mail');
        }
        return $this->_dbTable;
        
    }
    
    public function save(Application_Model_Mail $mail){
    	$data = array(
    		'id_mail' => $mail->getIdMail(),
            'sujet' => utf8_decode($mail->getSujet()),
            'contenu' => utf8_decode($mail->getContenu()),
            'description' => utf8_decode($mail->getDescription())
    	);
    	
    	if(null === ($id = $mail->getIdMail())){
            unset($data['id_mail']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_mail = ?' => $id));
        }    	
    	
    }
    
    public function find($id_mail, Application_Model_Mail $mail){
    	$result = $this->getDbTable()->find($id_mail);
    	
    	if(count($result) == 0)
    		return;
    	
    	$row = $result->current();
    	
    	$mail->setIdMail($row->id_mail)
             ->setSujet(utf8_encode($row->sujet))
             ->setContenu(utf8_encode($row->contenu))
             ->setDescription(utf8_encode($row->description));
    }
    
    public function fetchAll(){
    	$select = $this->getDbTable()->select();
    	
    	$result = $this->getDbTable()->fetchAll($select);
    	
    	if(count($result) == 0)
    		return;
    		
    	$entries = array();
    	    	
    	foreach($result as $row){
    		$mail = new Application_Model_Mail();
    		
        $mail->setIdMail($row->id_mail)
             ->setSujet(utf8_encode($row->sujet))
             ->setContenu(utf8_encode($row->contenu))
             ->setDescription(utf8_encode($row->description));	

			array_push($entries, $mail);
    	}
    	
    	return $entries;
    }

    // Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Mail";
    
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

        $select = $this->getDbTable()->select()->from('Mail')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
            
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){
           
            $data['rows'][] = array(
                'id' => $row->id_mail,
                'cell' => array(utf8_encode($row->description), utf8_encode($row->sujet), utf8_encode($row->contenu))
            );
        }
        
        return json_encode($data);        
    }

}

