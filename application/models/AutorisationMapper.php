<?php

class Application_Model_AutorisationMapper
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
            $this->setDbTable('Application_Model_DbTable_Autorisations');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update un enregistrement
    public function save(Application_Model_Autorisation $autorisation){
        $data = array(
            'id_autorisation' => $autorisation->getIdAutorisation(),
            'nom' => $autorisation->getNom(),
            'droit_accorde' => $autorisation->getDroitAccorde()
        );
        
        if(null === ($id = $autorisation->getIdAutorisation())){
            unset($data['id_autorisation']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_autorisation = ?' => $id));
        }
    }
    
    // Trouve un enregistrement à partir de son id
    public function find($id_autorisation, Application_Model_Autorisation $autorisation){
        $result = $this->getDbTable()->find($id_autorisation);
        if(0 == count($result)){
            return;
        }
        $row = $result->current();
        
        $autorisation->setIdAutorisation($row->id_autorisation);
        $autorisation->setNom($row->nom);
        $autorisation->setDroitAccorde($row->droit_accorde);
    }
    
    // Tous les enregistrements
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        
        $entries = array();
        
        foreach($resultSet as $row){
            $entry = new Application_Model_Autorisation();
            $entry->setIdAutorisation($row['id_autorisation']);
            $entry->setNom($row['nom']);
            $entry->setDroitAccorde($row['droit_accorde']);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    public function fetchAllForFlexigridWithIdGroupe($page, $sort_name, $sort_order, $qtype, $query, $rp, $id_groupe){
          // Setup sort and search SQL
          $sort_sql = "$sort_name $sort_order";
          $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
                              
          // Get total count of records
          $sql = "select * from Autorisations $search_sql";

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
                    
          if($id_groupe == 1){
              $select = $this->getDbTable()->select()->limit($rp, $page_start)->order($sort_sql);
          }else{
              $gpMapper = new Application_Model_GroupesPermissionsMapper();
              $result = $gpMapper->findByIdGroupe($id_groupe);
              if(count($result) == 0)
                  return;

            $id_autorisation = array();

            foreach($result as $row){
                  $id = $row->getIdAutorisation();
                  $id_autorisation[] = $id;
              }
              $select = $this->getDbTable()->select()->where("id_autorisation IN (?)", $id_autorisation)->limit($rp, $page_start)->order($sort_sql);
          }

          if($search_sql != '')
              $select->where($search_sql);

          $result = $this->getDbTable()->fetchAll($select);

          foreach($result as $row){
              $data['rows'][] = array(
                  'id' => $row->id_autorisation,
                  'cell' => array($row->id_autorisation, $row->droit_accorde)
              );
          }

          return json_encode($data);        
      }

}

