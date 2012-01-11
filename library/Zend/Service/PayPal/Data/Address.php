<?php
class Zend_Service_PayPal_Data_Address
{
    /**
     * Street
     *
     * @var string
     */
    protected $street;
    
    /**
     * City
     *
     * @var string
     */
    protected $city;
    
    /**
     * State (2 character code)
     *
     * @var string
     */
    protected $state = 'ZZ';
    
    /**
     * Country (2 character code)
     *
     * @var string
     */
    protected $countryCode;
    
    /**
     * Zip code
     *
     * @var integer
     */
    protected $zip;
    
    public function __construct($street, $city, $countryCode, $state = null, $zip = null);
    /**
     * Get the value of street
     *
     * @return string
     */
    public function getStreet();
    
    /**
     * Get the value of city
     *
     * @return string
     */
    public function getCity();
    
    /**
     * Get the value of state
     *
     * @return string
     */
    public function getState();
    
    /**
     * Get the value of countryCode
     *
     * @return string
     */
    public function getCountryCode();
    
    /**
     * Get the value of zip
     *
     * @return integer
     */
    public function getZip();
} 