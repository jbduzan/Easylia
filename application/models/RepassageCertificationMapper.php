<?php

class Application_Model_RepassageCertificationMapper
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
           $this->setDbTable('Application_Model_DbTable_RepassageCertification');
       }
       return $this->_dbTable;

    }

    // Insert ou update les données
    public function save(Application_Model_RepassageCertification $repassage){
       $data = array(
           'id_repassage' => $repassage->getIdRepassage(),
           'id_utilisateur' => $repassage->getIdUtilisateur(),
           'id_certification' => $repassage->getIdCertification(),
           'date_validite' => $repassage->getDateValidite()
       );

       if(null === ($id = $repassage->getIdRepassage())){
           unset($data['id_repassage']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_repassage = ?' => $id));
       }
    }

    // Trouve un enregistrement en fonction de son id
    public function find($id_utilisateur, $id_certification, Application_Model_RepassageCertification $repassage){
       $select = $this->getDbTable()->select()->where("id_utilisateur = ?", $id_utilisateur)->where("id_certification = ?", $id_certification);
       $result = $this->getDbTable()->fetchAll($select);

        if(0 == count($result)){
               return;
        }
        $row = $result->current();
        $repassage->setIdRepassage($row->id_repassage)
                  ->setIdUtilisateur($row->id_utilisateur)
                  ->setIdCertification($row->id_certification)
                  ->setDateValidite($row->date_validite);
           
    }

    // Retourne tous les enregistrement
    public function fetchAll(){
      $resultSet = $this->getDbTable()->fetchAll();
      $entries = array();

      foreach($resultSet as $row){
          $entry = new Application_Model_RepassageCertification();
          $entry->setIdRepassage($row->id_repassage);
          $entry->setIdUtilisateur($row->id_utilisateur);
          $entry->setIdCertification($row->id_certification);
          $entry->setDateValidite($row->date_validite);
          $entries[] = $entry;
      }

      return $entries;
    }

}

