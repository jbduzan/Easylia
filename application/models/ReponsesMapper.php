<?php

class Application_Model_ReponsesMapper
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
           $this->setDbTable('Application_Model_DbTable_Reponses');
       }
       return $this->_dbTable;

    }

    // Insert ou update les données
    public function save(Application_Model_Reponses $reponse){
       $data = array(
           'id_reponse' => $reponse->getIdReponse(),
           'reponse' => utf8_decode($reponse->getReponse()),
           'est_juste' => $reponse->getEstJuste(),
           'id_question' => $reponse->getIdQuestion()
       );

       if(null === ($id = $reponse->getIdReponse())){
           unset($data['id_reponse']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_reponse = ?' => $id));
       }
    }

    // Trouve un enregistrement en fonction de son id
    public function find($id_reponse, Application_Model_Reponses $reponse){
       $result = $this->getDbTable()->find($id_reponse);

        if(0 == count($result)){
               return;
           }
           $row = $result->current();
           $reponse->setIdReponse($row->id_reponse);
           $reponse->setReponse(utf8_encode($row->reponse));
           $reponse->setEstJuste($row->est_juste);
           $reponse->setIdQuestion($row->id_question);
           
    }

    // Retourne tous les enregistrement
    public function fetchAll(){
      $resultSet = $this->getDbTable()->fetchAll();
      $entries = array();

      foreach($resultSet as $row){
          $entry = new Application_Model_Reponses();
          $entry->setIdReponse($row->id_reponse);
          $entry->setReponse(utf8_encode($row->reponse));
          $entry->setEstJuste($row->est_juste);
          $entry->setIdQuestion($row->id_question);
          $entries[] = $entry;
      }

      return $entries;
    }
    
    public function fetchAllWithId($id_question){
        $select = $this->getDbTable()->select()->where("id_question = ?", $id_question);
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach($resultSet as $row){
            $entry = new Application_Model_Reponses();
            $entry->setIdReponse($row->id_reponse);
            $entry->setReponse(utf8_encode($row->reponse));
            $entry->setEstJuste($row->est_juste);
            $entry->setIdQuestion($row->id_question);
            $entries[] = $entry;
        }

        return $entries;
    }
    
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){

          // Setup sort and search SQL
          $sort_sql = "$sort_name $sort_order";
          $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';

          // Get total count of records
          $sql = "select * from Reponses $search_sql";

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
              $estJuste = "non";
              if($row->est_juste == 1)
                $estJuste = "oui";
              $data['rows'][] = array(
                  'id' => $row->id_reponse,
                  'cell' => array(utf8_encode($row->reponse), $estJuste)
              );
          }

          return json_encode($data);        
    }
    
    public function fetchAllForFlexigridWithIdQuestion($page, $sort_name, $sort_order, $qtype, $query, $rp, $id_question){

          // Setup sort and search SQL
          $sort_sql = "$sort_name $sort_order";
          $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';

          // Get total count of records
          $sql = "select * from Reponses where id_question = $id_question";

          // Setup paging
          $page_start = ($page-1)*$rp;
          $limit_sql = "limit $page_start, $rp";

          // Return json Data
          $data = array();
          $data['page'] = $page;
          $data['total'] = $this->countWithIdQuestion($id_question);
          $data['rows'] = array();

          $select = $this->getDbTable()->select()->where("id_question = ?", $id_question)->limit($rp, $page_start)->order($sort_sql);

          if($search_sql != '')
              $select->where($search_sql);

          $result = $this->getDbTable()->fetchAll($select);

          foreach($result as $row){
              $estJuste = "non";
              if($row->est_juste == 1)
                $estJuste = "oui";
              $data['rows'][] = array(
                  'id' => utf8_encode($row->id_reponse),
                  'cell' => array($row->reponse, $estJuste)
              );
          }

          return json_encode($data);        
    }
    
    public function delete($id_reponse){
        $this->getDbTable()->delete("id_reponse = $id_reponse");
    }
    
    public function getNombreReponseJuste($id_question){
        // Renvoie le nombre de réponse juste à une question
        $select = $this->getDbTable()->select()->where("id_question = ?", $id_question)->where("est_juste = 1");
        $result = $this->getDbTable()->fetchAll($select);
        
        return count($result);
    }
    
    public function countWithIdQuestion($id_question){
        $select = $this->getDbTable()->select()->where('id_question = ?', $id_question);
        $result = $this->getDbTable()->fetchAll($select);
        return count($result);
    }
      
      


}

