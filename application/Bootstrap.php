<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function run(){
        
        Zend_Session::start();
                
        $config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => 'no-reply@easylia.com', 'password' => 'Tg67zBv1');
        $tr = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
        Zend_Mail::setDefaultTransport($tr);
        Zend_Mail::setDefaultFrom('no-reply@easylia.com', 'no-reply');
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
        
        require_once('MyAcl.php');
        $acl = new MyAcl();
                               
        date_default_timezone_set("Europe/Paris");
        
        parent::run();        
    }

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
		$router->addRoute('formation',new Zend_Controller_Router_Route('liste-des-formations-disponibles',array('controller' => 'formation','action' => 'listeformationdispo')));
		$router->addRoute('inscription', new Zend_Controller_Router_Route('inscription-formateur', array('controller' => 'inscription', 'action' =>'index')));
		$router->addRoute('formateuravalider', new Zend_Controller_Router_Route('formateurs-à-valider', array('controller' => 'utilisateurs', 'action' => 'formateuravalider')));
		$router->addRoute('listeutilisateur', new Zend_Controller_Router_Route('liste-des-utilisateurs', array('controller' => 'utilisateurs', 'action' => 'listeutilisateurs')));
		$router->addRoute('profil', new Zend_Controller_Router_Route('profil-utilisateur', array('controller' => 'utilisateurs', 'action' => 'index')));		
		$router->addRoute('validerformateur', new Zend_Controller_Router_Route('valider-formateur', array('controller' => 'utilisateurs', 'action' => 'validerutilisateur')));		
		$router->addRoute('infouser', new Zend_Controller_Router_Route('information-utilisateur', array('controller' => 'utilisateurs', 'action' => 'afficherinfo')));
		$router->addRoute('formationdispo', new Zend_Controller_Router_Route('formations-disponibles', array('controller' => 'formation', 'action' => 'listeformationdispo')));		
		$router->addRoute('commanderformation', new Zend_Controller_Router_Route('commander-une-formation', array('controller' => 'formation', 'action' => 'commander')));
		$router->addRoute('listecertification', new Zend_Controller_Router_Route('certifications-disponible', array('controller' => 'certifications', 'action' => 'listecertification')));
		$router->addRoute('passagecertification', new Zend_Controller_Router_Route('passer-une-certification', array('controller' => 'certifications', 'action' => 'passercertification')));
		$router->addRoute('parcourformateur', new Zend_Controller_Router_Route('renseigner-son-profil', array('controller' => 'utilisateurs', 'action' => 'parcoursformateur')));
		$router->addRoute('reponsetestmotivation', new Zend_Controller_Router_Route('réponses-test-formateur', array('controller' => 'questions', 'action' => 'reponsetestmotivation')));
		//$router->addRoute('deconnexion', new Zend_Controller_Router_Route('deconnexion', array('controller' => 'utilisateurs', 'action' => 'deconnexion')));
    }
    
    public function _initSidebar(){
    	$this->bootstrap('view');
    	$view = $this->getResource('view');
    	$view->placeholder('menu-connecte');
    	$view->placeholder('sidebar');
    }
    
    protected function _initNavigation() {
    
    $this->bootstrap('view');
        $view = $this->getResource('view');

		//parsage du fichier xml en lui indiquant la section à utiliser, ici "nav"
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
		
		//initialisation de Zend_Navigation
		$navigation = new Zend_Navigation($config);
		
		//initialisation de l'aide vue navigation()
		$view->navigation($navigation);
		
		//initialisation du breadcrumbs
		$view->navigation()->breadcrumbs()
		->setMinDepth(0)->setSeparator(' >> ');
      }

}

