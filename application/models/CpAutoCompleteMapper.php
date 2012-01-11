<?php

class Application_Model_CpAutoCompleteMapper
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
            $this->setDbTable('Application_Model_DbTable_CpAutoComplete');
        }
        return $this->_dbTable;
        
    }
    
    public function autoComplete($code_postal = null, $ville = null){
        
        $list = array();
        
        $db = $this->getDbTable();
        
        $where = "code_pays = 'FR'";
        
        if($code_postal != null && $ville == null){
            $where .= "AND code_postal like '".$code_postal."%'";
        }else if($code_postal == null && $ville != null){
            $where .= "AND ville like '".$ville."%'";
        }
                                
        $select = $db->select('code_postal', 'ville')->distinct('code_postal')->where($where)->limit(10,0);
                
        $list = $db->fetchAll($select);
        
        return $list;
        
        
    } 

}

