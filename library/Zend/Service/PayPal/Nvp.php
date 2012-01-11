<?php


/** @see Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/** @see Zend_Service_PayPal_Data_AuthInfo*/
require_once 'Zend/Service/PayPal/Data/AuthInfo.php';

class Zend_Service_PayPal_Nvp
{
    
    /**
     * Authentication info for the PayPal API service
     *
     * @var Zend_Service_PayPal_Data_AuthInfo
     */
    protected $_authInfo;
    
    /**
     * Zend_Http_Client to use for the service
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient;
    
    protected $_config = array(
        'endpoint'	=> 'https://api-3t.sandbox.paypal.com/nvp',
        'version'	=> 70.0
    );

    /**
     * TODO: description.
     * 
     * @var string  Defaults to ''. 
     */
    protected $_lastResponse;
    
    /**
     * Enter description here...
     * 
     * @todo should we allow $authInfo to be passed in as an array?
     * 
     * @todo was there a good reason for (originally) passing in a Zend_Http_Client?
     *
     * @param Zend_Service_PayPal_Data_AuthInfo $auth_info
     * @param Zend_Http_Client                  $httpClient
     */
    public function __construct(Zend_Service_PayPal_Data_AuthInfo $authInfo, array $options = array())
    {
        $this->_authInfo    = $authInfo;
        $this->_httpClient = new Zend_Http_Client();
        
        $this->_config = array_merge( $this->_config, $options ); 
    }
    
    /**
     * HTTP client preparation procedures - should be called before every API
     * call.
     *
     * Will clean up the HTTP client parameters, set the request method to POST
     * and add the always-required authentication information
     *
     * @param string $method The API method we are about to use
     * @return void
     */
    protected function _prepare( $apiMethod )
    {
        
    }
    
    /**
     * Preform the request and return a response object
     *
     * @return Zend_Service_PayPal_Response
     */
    protected function _process()
    {
        
    }
    
    /**
     * Get the HTTP client to be used for this service
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }
    
    /**
     * Set the HTTP client to be used for this service
     *
     * @param Zend_Http_Client $client
     */
    public function setHttpClient( Zend_Http_Client $client )
    {
        $this->_httpClient = $client;
    }
    
    /**
     * Preform a DoDirectPayment call
     *
     * This call preforms a direct payment, directly charging a credit card
     * using PayPal's services. It does not require a valid PayPal user name,
     * but does require the credit card details and billing address of the
     * customer.
     *
     * @param  Zend_Service_PayPal_Data_CreditCard $creditCard
     * @param  Zend_Service_PayPal_Data_Address    $address
     * @param  float                               $ammount
     * @param  string                              $remoteAddr
     * @param  string                              $paymentAction
     * @param  array                               $params
     * @return Zend_Service_PayPal_Response
     */
    public function doDirectPayment($creditCard, $address, $ammount, $remoteAddr = null, $paymentAction = 'Sale', array $params = array())
    {
        
    }
    
    /**
     * Preform a SetExpressCheckout PayPal API call, starting an Express
     * Checkout process.
     *
     * This call is expected to return a token which can be used to redirect
     * the user to PayPal's transaction approval page
     *
     * @param  float  $amount
     * @param  string $returnUrl
     * @param  string $cancelUrl
     * @param  array  $params    Additional parameters
     * @return Zend_Service_PayPal_Response
     */
    public function setExpressCheckout($amount, $returnUrl, $cancelUrl, array $params = array(), $payement_action, $quantité, $type, $description)
    {
        //----------------------------------
        // TODO Verify return url and cancel url
        //----------------------------------
        $data = $this->_parseParams( $params );
        $data['METHOD'] = 'SetExpressCheckout';
        $data['RETURNURL'] = $returnUrl;
        $data['CANCELURL'] = $cancelUrl;
        $data['PAYMENTREQUEST_0_PAYMENTACTION'] = $payement_action;

        $data['PAYMENTREQUEST_0_AMT'] = $amount;
        $data['L_PAYMENTREQUEST_0_AMT0'] = 55;
        $data['L_PAYMENTREQUEST_0_QTY0'] = $quantité;
        $data['L_PAYMENTREQUEST_0_NAME0'] = $type;
        $data['L_PAYMENTREQUEST_0_DESC0'] = $description;    
        $data['PAYMENTREQUEST_0_CURRENCYCODE'] = "EUR"; 
        $data['HDRIMG'] = "http://easylia.safitec.com/Easylia/Prendre_des_cours_files/shapeimage_1.png";   
        $data['PAYFLOWCOLOR'] = "blue";

        $response = $this->_doMethodCall( $data );

        return new Zend_Service_PayPal_Nvp_Response( $response );

    }
    
    public function setDoCapture($authorization_id, $amount, array $params = array()){
    	$data = $this->_parseParams($params);
    	$data['METHOD'] = "DoCapture";
    	$data['AUTHORIZATIONID'] = $authorization_id;
    	$data['AMT'] = $amount;
    	$data['COMPLETETYPE'] = 'Complete';
    	
    	$reponse = $this->_doMethodCall($data);
    	
    	return new Zend_Service_PayPal_Nvp_Response($reponse);
    }
    
    public function setDoReauthorization($authorization_id, $amount){
    	$data = array();
    	$data['METHOD'] = 'DoReauthorization';
    	$data['AUTHORIZATIONID'] = $authorization_id;
    	$data['AMT'] = $amount;
    	
    	$reponse = $this->_doMethodCall($data);
    	
    	return new Zend_Service_PayPal_Nvp_Response($reponse);
    }
        
    /**
     * Preform a GetExpressCheckoutDetails PayPal API call, requesting info
     * about a started Express Checkout transaction
     *
     * @param  string $token Transaction identifier token
     * @return Zend_Service_PayPal_Response
     */
    
    public function getExpressCheckoutDetails( $token )
    {
        $data = array( 'TOKEN' => $token );
        $data['METHOD'] = 'GetExpressCheckoutDetails';

        $response = $this->_doMethodCall( $data );
        return new Zend_Service_PayPal_Nvp_Response( $response );
    }
    
    /**
     * Preform a DoExpressCheckoutPayment PayPal API call, finalizing a
     * transaction.
     *
     * @param  string $token
     * @param  string $payerId
     * @param  string $ammount
     * @param  string $paymentAction Payment action - 'Sale' or 'Authorization'
     * @return Zend_Service_PayPal_Response
     */
    public function doExpressCheckoutPayment($token, $payerId, $ammount, $paymentAction = 'Sale')
    {
        $data = array();
        $data['METHOD'] = 'DoExpressCheckoutPayment';
        $data['PAYMENTACTION'] = $paymentAction;
        $data['TOKEN'] = $token;
        $data['PAYERID'] = $payerId;
        $data['AMT'] = $ammount;
 

        $response = $this->_doMethodCall( $data );

        return new Zend_Service_PayPal_Nvp_Response( $response );

    }
    
    /**
     * Preform a Mass Payment API call
     *
     * @param  array  $receivers    Array of Zend_Service_PayPal_Data_MassPayReceiver objects
     * @param  string $rcpttype     Receiver type
     * @param  string $emailSubject Email subject for all receivers
     * @param  string $currency     3 letter currency code, default is USD
     * @return Zend_Service_PayPal_Response
     */
    public function doMassPay(array $receivers, $rcpttype = Zend_Service_PayPal_Data_MassPayReceiver::RT_EMAIL, $emailSubject = '', $currency = 'USD')
    {

    }
    
    /**
     * Preform a Get Transaction Details API call
     *
     * @param  string $transactionId Transaction ID (17 Alphanumeric single-byte characters)
     * @return Zend_Service_PayPal_Response
     */
    public function doGetTransactionDetails($transactionId)
    {
        
    }
    
    /**
     * Sets the version of the NVP API to use
     *
     * @param float $version
     */
    public function setVersion( $version )
    {
        $this->_config[ 'version' ] = $version;
    }

    /**
     * 
     * @return array
     */
    protected function _parseParams( array $aParams )
    {
        $data = array();
        foreach( $aParams as $name => $value ) {
            $data[strtoupper($name)] = urlencode($value);
        }

        return $data;
    }

    /**
     * TODO: short description.
     * 
     * @param array $aParams 
     * 
     * @return TODO
     */
    protected function _doMethodCall( array $aParams )
    {
        //--------------------------------------
        // Add auth details to params
        //--------------------------------------
        $aParams['USER']      = $this->_authInfo->getUsername();
        $aParams['PWD']       = $this->_authInfo->getPassword();
        $aParams['SIGNATURE'] = $this->_authInfo->getSignature();
        $aParams['VERSION']   = $this->_config[ 'version' ];
        $aParams['LOCALECODE'] = 'FR';
        $aParams['CURRENCYCODE'] = "EUR";
        
      //  print_r($aParams);
        
		$this->_httpClient->setUri('https://api-3t.sandbox.paypal.com/nvp');
        $this->_httpClient->setParameterPost( $aParams );
        return $this->_httpClient->request( Zend_Http_Client::POST );
    }

} 
