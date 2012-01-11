<?php

class Application_Model_QuestionsMapper
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
           $this->setDbTable('Application_Model_DbTable_Questions');
       }
       return $this->_dbTable;

    }

    // Insert ou update les données
    public function save(Application_Model_QuestionsReponses $question){
       $data = array(
           'id_question' => $question->getidQuestion(),
           'question' => $question->getQuestion(),
           'nbr_reponse' => $question->getNbrReponse(),
           'reponse_ouverte' => $question->getReponseOuverte()
       );

       if(null === ($id = $question->getidQuestion())){
           unset($data['id_question']);
           $this->getDbTable()->insert($data);
       }else{
           $this->getDbTable()->update($data, array('id_question = ?' => $id));
       }
    }

    // Trouve un enregistrement en fonction de son id
    public function find($id_question, Application_Model_QuestionsReponses $question){
       $result = $this->getDbTable()->find($id_question);

        if(0 == count($result)){
               return;
           }
           $row = $result->current();
           $question->setidQuestion($row->id_question);
           $question->setQuestion($row->question);
           $question->setNbrReponse($row->nbr_reponse);
           $question->setReponseOuverte($row->reponse_ouverte);
           
    }

    // Retourne tous les enregistrement
    public function fetchAll(){
      $resultSet = $this->getDbTable()->fetchAll();
      $entries = array();

      foreach($resultSet as $row){
          $entry = new Application_Model_QuestionsReponses();
          $entry->setidQuestion($row->id_question);
          $entry->setQuestion($row->question);
          $entry->setNbrReponse($row->nbr_reponse);
          $entry->setReponseOuverte($row->reponse_ouverte);
          $entries[] = $entry;
      }

      return $entries;
    }
    
    public function fetchAllForFlexigridWithIdQuestion($page, $sort_name, $sort_order, $qtype, $query, $rp, $id_question_array, $total){
          
          // Setup sort and search SQL
          $sort_sql = "$sort_name $sort_order";
          $search_sql = ($qtype != '' && $query != '') ? "$qtype LIKE '%$query%'" : '';
                              
          $id_question = array();
          
          foreach($id_question_array as $row){
              $id = $row->getidQuestion();
              $id_question[] = $id;
          }
                                        
          // Setup paging
          $page_start = ($page-1)*$rp;
          $limit_sql = "limit $page_start, $rp";

          // Return json Data
          $data = array();
          $data['page'] = $page;
          $data['total'] = $total;
          $data['rows'] = array();
                    
          $select = $this->getDbTable()->select()->where("id_question IN (?)", $id_question)->limit($rp, $page_start)->order($sort_sql);
         
          if($search_sql != '')
              $select->where($search_sql);

          $result = $this->getDbTable()->fetchAll($select);

          foreach($result as $row){		         
            $data['rows'][] = array(
                  'id' => $row->id_question,
                  'cell' => array($row->question, $row->nbr_reponse, $row->reponse_ouverte)
              );
          }

          return json_encode($data);        
      }
      
      public function fetchAllForFlexigrid($page, $sort_name, $sort_order, $qtype, $query, $rp){

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

            $select = $this->getDbTable()->select()->limit($rp, $page_start)->order($sort_sql);

            if($search_sql != '')
                $select->where($search_sql);

            $result = $this->getDbTable()->fetchAll($select);

            foreach($result as $row){
            	// Récupère le nombre de réponse pour une questions
 	          	$reponse_mapper = new Application_Model_ReponsesMapper();
 	          	$nbr_reponse = $reponse_mapper->countWithIdQuestion($row->id_question);	      
 	          		        	            
                $data['rows'][] = array(
                    'id' => $row->id_question,
                    'cell' => array($row->question, $nbr_reponse, $row->reponse_ouverte)
                );
            }

            return json_encode($data);        
        }
      
      public function delete($id_question){
          $this->getDbTable()->delete("id_question = $id_question");
      }
      
      public function fetchAllWithIdArray($id_questions){
          // Récupère tout les enregistrement avec un array d'id en paramètre, pour les afficher dans la certifications
          
          $id_question = array();
          
          foreach($id_questions as $key=>$row){
              $id_question[] = $row;
          }
                              
          $select = $this->getDbTable()->select()->where("id_question IN (?)", $id_question);
          $result = $this->getDbTable()->fetchAll($select);
                    
          $entries = array();
          
          foreach($result as $row){
              $entry = new Application_Model_Questions();
              $entry->setidQuestion($row->id_question);
              $entry->setQuestion($row->question);
              $entry->setNbrReponse($row->nbr_reponse);
              $entry->setReponseOuverte($row->reponse_ouverte);
              
              $entries[] = $entry;
          }
                    
          return $entries;
      }

}

