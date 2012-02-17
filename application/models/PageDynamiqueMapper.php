<?php

class Application_Model_PageDynamiqueMapper
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
            $this->setDbTable('Application_Model_DbTable_PageDynamique');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update un enregistrement
    public function save(Application_Model_PageDynamique $page){
        $data = array(
            'id_page_dynamique' => $page->get('id_page_dynamique'),
            'nom' => $page->get('nom'),
            'description' => $page->get('description'),
            'url' => $page->get('url'),
            'contenu' => $page->get('contenu')
        );
        
        if(null === ($id = $page->get('id_page_dynamique'))){
            unset($data['id_page_dynamique']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_page_dynamique = ?' => $id));
        }
    }
    
    // Trouve un enregistrement à partir de son id
    public function find($id_page_dynamique, Application_Model_PageDynamique $page){
        $result = $this->getDbTable()->find($id_page_dynamique);
        if(0 == count($result)){
            return;
        }
        $row = $result->current();
        
        $page->set('id_page_dynamique', $row->id_page_dynamique)
             ->set('nom', $row->nom)
             ->set('description', $row->description)
             ->set('url', $row->url)
             ->set('contenu', $row->contenu);
    }
    
    // Tous les enregistrements
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        
        $entries = array();
        
        foreach($resultSet as $row){
            $entry = new Application_Model_PageDynamique();
            $entry->set('id_page_dynamique', $row->id_page_dynamique)
                  ->set('nom', $row->nom)
                  ->set('description', $row->description)
                  ->set('url', $row->url)
                  ->set('contenu', $row->contenu);
            $entries[] = $entry;
        }
        
        return $entries;
    }

    public function delete($id_page_dynamique){
        $this->getDbTable()->delete("id_page_dynamique = $id_page_dynamique");
    }
    
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
          // Setup sort and search SQL
          $sort_sql = "$sort_name $sort_order";
          $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
                              
          // Get total count of records
          $sql = "select * from page_dynamique $search_sql";

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
                    
          $select = $this->getDbTable()->select()->limit($rp, $page_start)->order($sort_sql);
          
          if($search_sql != '')
              $select->where($search_sql);

          $result = $this->getDbTable()->fetchAll($select);

          foreach($result as $row){
              $data['rows'][] = array(
                  'id' => $row->id_page_dynamique,
                  'cell' => array($row->nom, $row->description, $row->url, $row->contenu)
              );
          }

          return json_encode($data);        
      }

}

