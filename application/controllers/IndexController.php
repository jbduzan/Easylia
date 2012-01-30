<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
            $this->_redirector = $this->_helper->getHelper('Redirector');
        $this->utilisateur = new Zend_Session_Namespace('user');   
        $this->commande = new Zend_Session_Namespace('commande');
    }

    public function indexAction()
    {
		$this->_redirector->goToUrl('http://spip.easylia.com');
    }

   /*
 public function expresscheckoutAction()
    {
		$authInfo = new Zend_Service_PayPal_Data_AuthInfo(
			'jbduza_1319099049_biz_api1.gmail.com',
			'1319099073',
			'AZ5Qm4qEIjcG7hc-HNVmIi-E3qTnAuLQy.pWX8p-SxdJEc6n.Il-qGWQ');
			
			
		$self = "http://localhost/formation/enregistrercommande";
		$amount = $this->utilisateur->montant;	
				
		$paypal = new Zend_Service_PayPal_Nvp($authInfo);
		$params = array('NOSHIPPING' => 1);
					
 		$reponse = $paypal->setExpressCheckout($this->commande->amount, $self.'?status=ok', $self.'?status=cancel', $params, $this->commande->payement_action, $this->commande->quantité, $this->commande->type, $this->commande->description);
 			 				
 		if($reponse->isSuccess() && ($token = $reponse->getValue('TOKEN'))){
			header("Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token");
 		}else
 			echo "error initialing paypal transaction";
							
    }

    public function testAction()
    {
                $mail = new Zend_Mail();
                $mail->setFrom('contact@easylia.com', 'contact');
                $mail->addTo('jbduzan@gmail.com');
                $mail->setSubject("Votre inscription sur Easylia");
                $mail->setBodyHtml("toto");
                $mail->send();
    }

    public function test2Action()
    {
        $authInfo = new Zend_Service_PayPal_Data_AuthInfo(
			'jbduza_1319099049_biz_api1.gmail.com',
			'1319099073',
			'AZ5Qm4qEIjcG7hc-HNVmIi-E3qTnAuLQy.pWX8p-SxdJEc6n.Il-qGWQ');
			
		$paypal = new Zend_Service_Paypal_Nvp($authInfo);
			
		$payement_mapper = new Application_Model_PaypalAutorizedPayementMapper();
		
		$result = $payement_mapper->fetchAll();
		
		foreach($result as $payement_info){
			
			// date à tester :
        	$now = date("d/m/Y");
	        $date_validite = $payement_info->getDateValidite();
	        $date_honneur = $payement_info->getDateHonneur();

    	    // Formatage pour compararaison de la date
     		$now = explode('/', $now);
       		$date_validite= explode('/', $date_validite);
       		$date_honneur = explode('/', $date_honneur);

	        $now = mktime(0,0,0,$now[1],$now[0],$now[2]);
    	    $date_validite = mktime(0,0,0,$date_validite[1], $date_validite[0], $date_validite[2]);     
    	    $date_honneur = mktime(0,0,0,$date_honneur[1], $date_honneur[0], $date_honneur[2]);
    	    
    	    if($now > $date_honneur){
    	    	$reponse = $paypal->setDoReauthorization($payement_info->getIdTransaction(), $payement_info->getMontant());
    	    }			
    	    
    	    if($now = $date_validite){
    	    	$reponse = $paypal->setDoCapture($payement_info->getIdTransaction(), $payement_info->getMontant());
			
				if($reponse->isSuccess()){
					$payement_mapper->delete($payement_info->getIdPayement());
				}
			}
		}
		

    }

    public function listedemandeAction()
    {
        // action body
    }
*/


}

















