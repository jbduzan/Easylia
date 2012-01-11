<?php

class Application_Model_GroupesPermissionsMapper
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
            $this->setDbTable('Application_Model_DbTable_GroupesPermissions');
        }
        return $this->_dbTable;
        
    }
    
    // Insert ou update les données
    public function save(Application_Model_GroupesPermissions $groupe_permission){
        $data = array(
            'id_groupe_permission' => $groupe_permission->getIdGroupePermission(),
            'id_groupe' => $groupe_permission->getIdGroupe(),
            'id_autorisation' => $groupe_permission->getIdAutorisation()
        );
        
        if(null === ($id = $groupe_permission->getIdGroupePermission())){
            unset($data['id_groupe_permission']);
            $this->getDbTable()->insert($data);
        }else{
            $this->getDbTable()->update($data, array('id_groupe_permission = ?' => $id));
        }
    }
    
    // Trouve un groupe en fonction de son id
    public function find($idGroupePermission, Application_Model_Groupes $groupe_permission){
        $result = $this->getDbTable()->find($idGroupePermission);
        
         if(0 == count($result)){
                return;
            }
            $row = $result->current();
            $groupe_permission->setIdGroupePermission($row->id_groupe_permission);
            $groupe_permission->setIdGroupe($row->id_groupe);
            $groupe_permission->setIdAutorisation($row->id_autorisation);
    }
    
    // Trouve en fonction de l'id autorisation
    public function findByIdAutorisation($id_autorisation, Application_Model_GroupesPermissions $groupe_permission){
        $db = $this->getDbTable();
        $select = $db->select()->where('id_autorisation = ?', $id_autorisation);
        $result = $db->fetchRow($select);
        
        if(count($result) == 0){
            return;
        }
        
        $row = $result;
        $groupe_permission->setIdGroupePermission($row->id_groupe_permission);
        $groupe_permission->setIdGroupe($row->id_groupe);
        $groupe_permission->setIdAutorisation($row->id_autorisation);
    }
    
    // Trouve en fonction de l'id groupe
    public function findByIdGroupe($id_groupe){
        $db = $this->getDbTable();
        $select = $db->select()->where('id_groupe = ?', $id_groupe);
        $result = $db->fetchAll($select);
        
        if(count($result) == 0){
            return;
        }
        
        $entries = array();
        foreach($result as $row){
            $entry = new Application_Model_GroupesPermissions();
            $entry->setIdGroupePermission($row->id_groupe_permission);
            $entry->setIdGroupe($row->id_groupe);
            $entry->setIdAutorisation($row->id_autorisation);
            $entries[] = $entry;
        }
        
        return $entries;
    }
    
    // Retourne tous les enregistrement
    public function fetchAll(){
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        
        foreach($resultSet as $row){
            $entry = new Application_Model_GroupesPermissions();
            $entry->setIdGroupePermission($row['id_groupe_permission']);
            $entry->setIdGroupe($row['id_groupe']);
            $entry->setIdAutorisation($row['id_autorisation']);
            $entries[] = $entry;
        }
        
        return $entries;
    }

    public function delete($id_autorisation, $id_groupe){
        $this->getDbTable()->delete(array("id_autorisation = ?" => $id_autorisation, 'id_groupe = ?' => $id_groupe));
    }

}

