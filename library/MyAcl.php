<?php

require_once ('Zend/Acl.php');

class MyAcl extends Zend_Acl{
    
    const INVITE = 'invite';

    protected static $_defaultRole = self::INVITE;
    protected static $_instance;
    
    final public function __construct() {
        $this->acl = new Zend_Acl();
        $this->addRole(new Zend_Acl_Role($this->getDefaultRole()));
        $this->_init();
    }
        
    public static function getInstance() {
       if(self::$_instance == null) {
           self::$_instance = new self ( );
       }
       return self::$_instance;
    }
       
    public static function setParams(array $params) {
        if (isset ( $params ['defaultRole'] )) {
            self::$_defaultRole = $params ['defaultRole'];
        }
     }
    
    public static function getDefaultRole() {
        return self::$_defaultRole;
    }       
    
    public function _init(){
     // On récupère les groupes et les autorisations
        $groupeMapper = new Application_Model_GroupesMapper();
        $groupes = $groupeMapper->fetchAll();

        $autorisationMapper = new Application_Model_AutorisationMapper();
        $autorisations = $autorisationMapper->fetchAll();

        $groupePermissionMapper = new Application_Model_GroupesPermissionsMapper();
        $groupePermission = $groupePermissionMapper->fetchAll();

        // Puis on crée les acl à partir de ça
        foreach($groupes as $roles){
            $this->acl->addRole(new Zend_Acl_Role($roles->nom));
        }

        foreach($groupePermission as $permission){
            $tempGroupe = new Application_Model_Groupes();
            $tempAutorisation = new Application_Model_Autorisation();

            $groupeMapper->find($permission->idGroupe, $tempGroupe);
            $autorisationMapper->find($permission->idAutorisation, $tempAutorisation);

            $nom_role = $tempGroupe->getNom();
            $droit = $tempAutorisation->getNom();

            $this->acl->allow($nom_role, null, $droit);
        }
        
        $this->acl->allow('Administrateur');
        
    }
    
    public function isAllowed($role, $resource, $privilege){
        return $this->acl->isAllowed($role, $resource, $privilege);
    }
        
}

?>