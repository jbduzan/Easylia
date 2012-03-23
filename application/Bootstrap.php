<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function run(){
        
        Zend_Session::start();
                
        $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'jbduzan@gmail.com', 'password' => 'Zorander33');
        $tr = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
        Zend_Mail::setDefaultTransport($tr);
        Zend_Mail::setDefaultFrom('no-reply@easylia.com', 'no-reply');
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
        
        require_once('MyAcl.php');
        $acl = new MyAcl();
                                
		//Zend_Controller_Action_HelperBroker::addPrefix('My_Helper'); 
		
        date_default_timezone_set("Europe/Paris");
               
        parent::run();        
    }

	// protected function _initForceSSL() {
	// 	if($_SERVER['SERVER_PORT'] != '443') {
 // 			header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
 //  			exit();
 //  		}
	// }

    protected function _initDoctype(){
        
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');        
    }
    
    /**
    * Initialize Module
    *
    * @return Zend_Application_Module_Autoloader
    */
    protected function _initAutoload()
    {
        $loader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => APPLICATION_PATH
        ));
        return $loader;
    }
    
    public function _initRoute(){
    	$FrontController = Zend_Controller_Front::getInstance(); 
    	$router = $FrontController->getRouter();
    	
		// retourne un routeur de réécriture par défaut
		$router->addRoute('inscription', new Zend_Controller_Router_Route('inscription-formateur', array('controller' => 'inscription', 'action' =>'index')));
		$router->addRoute('confirmation', new Zend_Controller_Router_Route('confirmation-inscription', array('controller' => 'inscription', 'action' => 'confirmation')));
		$router->addRoute('formateuravalider', new Zend_Controller_Router_Route('formateurs-a-valider', array('controller' => 'utilisateurs', 'action' => 'formateuravalider')));
		$router->addRoute('listeutilisateur', new Zend_Controller_Router_Route('liste-des-utilisateurs', array('controller' => 'utilisateurs', 'action' => 'listeutilisateurs')));
		$router->addRoute('profil', new Zend_Controller_Router_Route('profil-utilisateur', array('controller' => 'utilisateurs', 'action' => 'index')));		
		$router->addRoute('validerformateur', new Zend_Controller_Router_Route('valider-formateur', array('controller' => 'utilisateurs', 'action' => 'validerutilisateur')));		
		$router->addRoute('infouser', new Zend_Controller_Router_Route('modification-informations-personnelles', array('controller' => 'utilisateurs', 'action' => 'afficherinfo')));
		$router->addRoute('commanderformation', new Zend_Controller_Router_Route('commander-une-formation', array('controller' => 'formation', 'action' => 'commander')));
		$router->addRoute('passagecertification', new Zend_Controller_Router_Route('passer-une-certification', array('controller' => 'certifications', 'action' => 'passercertification')));
		$router->addRoute('parcourformateur', new Zend_Controller_Router_Route('renseigner-son-profil', array('controller' => 'utilisateurs', 'action' => 'parcoursformateur')));
		$router->addRoute('reponsetestmotivation', new Zend_Controller_Router_Route('reponses-test-formateur', array('controller' => 'questions', 'action' => 'reponsetestmotivation')));
		$router->addRoute('faq', new Zend_Controller_Router_Route('foire-aux-questions', array('controller' => 'faq', 'action' => 'index')));
		$router->addRoute('mesformations', new Zend_Controller_Router_Route('mes-formations', array('controller' => 'formation', 'action' => 'viewformation')));
		$router->addRoute('formationdispo', new Zend_Controller_Router_Route('formations-disponibles', array('controller' => 'formation', 'action' => 'listeformation')));
		$router->addRoute('changepassword', new Zend_Controller_Router_Route('changer-mot-de-passe', array('controller' => 'utilisateurs', 'action' => 'changepassword')));
		$router->addRoute('connexion', new Zend_Controller_Router_Route('connexion', array('controller' => 'utilisateurs', 'action' => 'connexion')));
		$router->addRoute('deconnexion', new Zend_Controller_Router_Route('deconnexion', array('controller' => 'utilisateurs', 'action' => 'deconnexion')));
		$router->addRoute('listegroupe', new Zend_Controller_Router_Route('liste-des-groupes', array('controller' => 'groupe', 'action' => 'index')));
		$router->addRoute('detailgroupe', new Zend_Controller_Router_Route('detail-groupe', array('controller' => 'groupe', 'action' => 'detailgroupe')));
		$router->addRoute('listequestion', new Zend_Controller_Router_Route('liste-des-questions', array('controller' => 'questions', 'action' => 'index')));
		$router->addRoute('listeformation', new Zend_Controller_Router_Route('liste-des-formations', array('controller' => 'formation', 'action' => 'listeformationdispo')));
		$router->addRoute('listequestionmotivation', new Zend_Controller_Router_Route('liste-questions-motivation', array('controller' => 'questions', 'action' => 'listequestionmotivation')));
		$router->addRoute('listelogin', new Zend_Controller_Router_Route('liste-logins-interdits', array('controller' => 'logininterdit', 'action' => 'index')));
		$router->addRoute('gererfaq', new Zend_Controller_Router_Route('gerer-faq', array('controller' => 'faq', 'action' => 'gestion')));
		$router->addRoute('certificationdispo', new Zend_Controller_Router_Route('certification-disponible', array('controller' => 'certifications', 'action' => 'listecertification')));
		$router->addRoute('listecertification', new Zend_Controller_Router_Route('liste-des-certifications', array('controller' => 'certifications', 'action' => 'index')));
		$router->addRoute('compteinactif', new Zend_Controller_Router_Route('compte-inactif', array('controller' => 'utilisateurs', 'action' => 'nonactive')));
		$router->addRoute('activation', new Zend_Controller_Router_Route('activation-compte', array('controller' => 'utilisateurs', 'action' => 'activation')));
		$router->addRoute('mdpoublie', new Zend_Controller_Router_Route('mot-de-passe-oublie', array('controller' => 'utilisateurs', 'action' => 'motdepasseoublie')));
		$router->addRoute('detailcertification', new Zend_Controller_Router_Route('detail-certification', array('controller' => 'certifications', 'action' => 'detailcertification')));
		$router->addRoute('gererformation', new Zend_Controller_Router_Route('administrer-formations', array('controller' => 'formation', 'action' => 'gererformation')));
		$router->addRoute('inscriptionclient', new Zend_Controller_Router_Route('inscription', array('controller' => 'inscription', 'action' => 'inscriptionclient')));
		$router->addRoute('voirmesformations', new Zend_Controller_Router_Route('formations-commandees', array('controller' => 'formation', 'action' => 'seeformation')));
		$router->addRoute('detailformation', new Zend_Controller_Router_Route('details-formation', array('controller' => 'formation', 'action' => 'detailformation')));
		$router->addRoute('detailquestion', new Zend_Controller_Router_Route('details-question', array('controller' => 'questions', 'action' => 'detailsquestion')));
		$router->addRoute('pagedynamique', new Zend_Controller_Router_Route('pages-dynamique', array('controller' => 'pagedynamique', 'action' => 'index')));
		$router->addRoute('genererfacture', new Zend_Controller_Router_Route('gerer-facture', array('controller' => 'formation', 'action' => 'selectformationfacturation')));
		$router->addRoute('gererfacture', new Zend_Controller_Router_Route('factures', array('controller' => 'facture', 'action' => 'gererfactureadmin')));
		$router->addRoute('document', new Zend_Controller_Router_Route('document', array('controller' => 'document', 'action' => 'index')));
		$router->addRoute('convention', new Zend_Controller_Router_Route('convention-formateur', array('controller' => 'document', 'action' => 'contratformateur')));
		$router->addRoute('presentation', new Zend_Controller_Router_Route('presentation-detaillee', array('controller' => 'utilisateurs', 'action' => 'presentationformateur')));
		$router->addRoute('resultat', new Zend_Controller_Router_Route('score-certification', array('controller' => 'certifications', 'action' => 'resultat')));
		$router->addRoute('mescertifications', new Zend_Controller_Router_Route('mes-certifications', array('controller' => 'certifications', 'action' => 'mescertifications')));
    }	
    
    public function _initSidebar(){
    	$this->bootstrap('view');
    	$view = $this->getResource('view');
    	$view->placeholder('menu-connecte');
    	$view->placeholder('sidebar');
    }
    
    protected function _initNavigation() {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
		$container = new Zend_Navigation($config);
		$view->navigation($container);
	}

}

