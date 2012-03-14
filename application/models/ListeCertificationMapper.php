<?php

class Application_Model_ListeCertificationMapper
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
           $this->setDbTable('Application_Model_DbTable_ListeCertification');
       }
       return $this->_dbTable;

    }

    // Insert ou update les données
    public function save(Application_Model_ListeCertification $liste){
       $data = array(
           'id_certification' => $liste->getIdCertification(),
           'nom' => $liste->getNom(),
           'type' => $liste->getType(),
           'nombre_question' => $liste->getNombreQuestion(),
           'temps_certification' => $liste->getTempsCertification(),
           'score_minimum' => $liste->getScoreMinimum(),
           'duree_validite' => $liste->getDureeValidite(),
           'nombre_passage' => $liste->getNombrePassage()
       );

       if(null === ($id = $liste->getIdCertification())){
           unset($data['id_certification']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_certification = ?' => $id));
       }
    }

    // Trouve un enregistrement en fonction de son id
    public function find($id_liste, Application_Model_ListeCertification $liste){
       $result = $this->getDbTable()->find($id_liste);

        if(0 == count($result)){
               return;
           }
           $row = $result->current();
           $liste->setIdCertification($row->id_certification);
           $liste->setNom($row->nom);
           $liste->setType($row->type);
           $liste->setNombreQuestion($row->nombre_question);
           $liste->setTempsCertification($row->temps_certification);
           $liste->setScoreMinimum($row->score_minimum);
           $liste->setDureeValidite($row->duree_validite);
           $liste->setNombrePassage($row->nombre_passage);
    }

    // Retourne tous les enregistrement
    public function fetchAll(){
      $resultSet = $this->getDbTable()->fetchAll();
      $entries = array();

      foreach($resultSet as $row){
          $entry = new Application_Model_ListeCertification();
          $entry->setIdCertification($row->id_certification);
          $entry->setNom($row->nom);
          $entry->setType($row->type);
          $entry->setNombreQuestion($row->nombre_question);
          $entry->setTempsCertification($row->temps_certification);
          $entry->setScoreMinimum($row->score_minimum)
          		  ->setDureeValidite($row->duree_validite)
                ->setNombrePassage($row->nombre_passage);
          $entries[] = $entry;
      }

      return $entries;
    }    
    
    // Retourne les données pour une flexigrid
    public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){
        // Setup sort and search SQL
        $sort_sql = "$sort_name $sort_order";
        $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
        
        // Get total count of records
        $sql = "select * from Liste_certification $search_sql";
        
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

        $select = $this->getDbTable()->select()->from('Liste_certification')->limit($rp, $page_start)->order($sort_sql);

        if($search_sql != '')
            $select->where($search_sql);
                    
        $result = $this->getDbTable()->fetchAll($select);
   
        foreach($result as $row){           
            $data['rows'][] = array(
                'id' => $row->id_certification,
                'cell' => array($row->nom, $row->type, $row->nombre_question, $row->temps_certification, $row->score_minimum, $row->duree_validite, $row->nombre_passage)
            );
        }
        
        return json_encode($data);        
    }

    // Supprime un enregistrement
    public function delete($id_certification){
        $this->getDbTable()->delete("id_certification = $id_certification");
    }
}

