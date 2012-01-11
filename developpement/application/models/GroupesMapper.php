<?php

class Application_Model_GroupesMapper
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
            $this->setDbTable('Application_Model_DbTable_Groupes');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_Groupes $groupe){
        $data = array(
            'id_groupe' => $groupe->getIdGroupe(),
            'nom' => $groupe->getNom(),
            'description' => $groupe->getDescription()
        );
        
        if(null === ($id = $groupe->getIdGroupe())){
            unset($data['id_groupe']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_groupe = ?' => $id));
        }
    }
    
    // Trouve un groupe en fonction de son id
    public function find($idGroupe, Application_Model_Groupes $groupe){
        $result = $this->getDbTable()->find($idGroupe);
        
        if(0 == count($result)){
            return;
        }
        $row = $result->current();
        $groupe->setIdGroupe($row->id_groupe);
        $groupe->setNom($row->nom);
        $groupe->setDescription($row->description);
    }
    
    // Retourne tous les enregistrement
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        
        foreach($resultSet as $row){
            $entry = new Application_Model_Groupes();
            $entry->setIdGroupe($row['id_groupe']);
            $entry->setNom($row['nom']);
            $entry->setDescription($row['description']);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    // Retrouve le nom d'un groupe à partir de son id
    public function getGroupeNameWithId($id_groupe){
        $result = $this->getDbTable()->find($id_groupe);
        
        if(0 == count($result)){
            return;
        }
        $row = $result->current();
        return $row->nom;
    }

    // Ressort les données au format flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
           // Setup sort and search SQL
           $sort_sql = "$sort_name $sort_order";
           $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';

           // Get total count of records
           $sql = "select * from Groupes $search_sql";

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

           $select = $this->getDbTable()->select()->from('Groupes')->limit($rp, $page_start)->order($sort_sql);

           if($search_sql != '')
               $select->where($search_sql);

           $result = $this->getDbTable()->fetchAll($select);

           foreach($result as $row){
               $data['rows'][] = array(
                   'id' => $row->id_groupe,
                   'cell' => array($row->id_groupe, $row->nom, $row->description)
               );
           }

           return json_encode($data);        
    }
    
    // Ressort les autorisations au format flexigrid
    public function fetchAutorisationForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
           // Setup sort and search SQL
           $sort_sql = "$sort_name $sort_order";
           $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';

           // Get total count of records
           $sql = "select * from Groupes_permissions $search_sql";

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
           
           $select = "select a.id_autorisation, a.droit_accorde from Groupes g join Groupes_permissions gp on g.id_groupe = gp.id_groupe join Autorisation on gp.id_autorisation = a.id_autorisation";

           $select = $this->getDbTable()->select($select)
                                        ->limit($rp, $page_start);//->order($sort_sql);

           if($search_sql != '')
               $select->where($search_sql);
               
           $result = $this->getDbTable()->fetchAll($select);

           foreach($result as $row){
               $data['rows'][] = array(
                   'id' => $row->id_groupe,
                   'cell' => array($row->id_groupe, $row->nom, $row->description)
               );
           }

           return json_encode($data);
    }
    
    // Supprime un groupe
    public function deleteGroupe($id_groupe){
        $this->getDbTable()->delete("id_groupe = $id_groupe");
    }

}

