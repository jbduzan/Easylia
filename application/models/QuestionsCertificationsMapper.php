<?php

class Application_Model_QuestionsCertificationsMapper
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
           $this->setDbTable('Application_Model_DbTable_QuestionsCertifications');
       }
       return $this->_dbTable;

    }

    // Insert ou update les données
    public function save(Application_Model_QuestionsCertifications $question){
       $data = array(
           'id_question_certification' => $question->getIdQuestionCertification(),
           'id_question' => $question->getidQuestion(),
           'id_certification' => $question->getIdCertification(),
           'question_obligatoire' => $question->getQuestionObligatoire()
       );

       if(null === ($id = $question->getIdQuestionCertification())){
           unset($data['id_question_certification']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_question_certification = ?' => $id));
       }
    }

    // Trouve un enregistrement en fonction de son id
    public function find($id_question, Application_Model_QuestionsCertifications $question){
       $result = $this->getDbTable->find($id_question);

        if(0 == count($result)){
               return;
           }
           $row = $result->current();
           $question->setIdQuestionCertification($row->id_question_certification);
           $question->setidQuestion($row->id_question);
           $question->setIdCertification($row->id_certification);
           $question->setQuestionObligatoire($row->question_obligatoire);
    }

    // Retourne tous les enregistrement
    public function fetchAll(){
      $resultSet = $this->getDbTable()->fetchAll();
      $entries = array();

      foreach($resultSet as $row){
          $entry = new Application_Model_QuestionsCertifications();
          $entry->setIdQuestionCertification($row->id_question_certification);
          $entry->setidQuestion($row->id_question);
          $entry->setIdCertification($row->id_certification);
          $entry->setQuestionObligatoire($row->question_obligatoire);
          $entries[] = $entry;
      }

      return $entries;
    }
    
    // Retrouve tout les enregistrement d'une certification
    public function fetchAllWithId($id_certification){
        $select = $this->getDbTable()->select()->where("id_certification = ? ", $id_certification);
        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries = array();
                
        foreach($resultSet as $row){
            $entry = new Application_Model_QuestionsCertifications();
            $entry->setIdQuestionCertification($row->id_question_certification);
            $entry->setidQuestion($row->id_question);
            $entry->setIdCertification($row->id_certification);
            $entry->setQuestionObligatoire($row->question_obligatoire);
            $entries[] = $entry;
        }

        return $entries;
    }
    
    public function deleteWithIdQuestion($id_question){
        $this->getDbTable()->delete("id_question = $id_question");
    }

    public function countWithIdCertification($id_certification){
        $select = $this->getDbTable()->select()->where("id_certification = ?", $id_certification);
        $result = $this->getDbTable()->fetchAll($select);
        return count($result);
    }
}

