<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once dirname(__FILE__).'/Abstract.php';

class Signifyd_Case_Request extends Signifyd_Case_Abstract{

    public function __construct(){
        $this->_type = self::PURCHASE;
        $this->_url = self::API_CASES_URL;
    }

    public function addOrderInformation($orderInfo){
        $this->setData($this->_type,array_merge(
            $orderInfo,
            array(
                'products'   => array(),
                'shipments'  => array(),
            )
        ));
    }

    public function addProduct($productInfo){
        $orderInfo = $this->getData($this->_type);
        $orderInfo['products'][] = $productInfo;
        $this->setData($this->_type, $orderInfo);
    }

    public function addShipment($shipmentInfo){
        $orderInfo = $this->getData($this->_type);
        $orderInfo['shipments'][] = $shipmentInfo;
        $this->setData($this->_type, $orderInfo);
    }

    public function addUserAccount($userAccountInfo){
        $this->setData('userAccount',$userAccountInfo);
    }

    public function addCard($cardInfo){
        $this->setCard($cardInfo);
    }

    public function addRecipient($recipientInfo){
        $this->setRecipient($recipientInfo);
    }

    protected function _getTemplate(){
        return array(
            $this->_type => array(
                'browserIpAddress'  => parent::VALIDATE_IP,      
                'orderId'           => parent::VALIDATE_STRING,   
                'createdAt'         => parent::VALIDATE_DATETIME, 
                'paymentGateway'    => parent::VALIDATE_STRING,   
                'currency'          => parent::VALIDATE_CURRENCY, 
                'avsResponseCode'   => 'Y',
                'cvvResponseCode'   => 'M',
                'orderChannel'      => 'PHONE',
                'receivedBy'        => parent::VALIDATE_STRING,   
                'totalPrice'        => parent::VALIDATE_DECIMAL,  
                'products'          => array(
                    array(
                        'itemId'         => parent::VALIDATE_STRING,  
                        'itemName'       => parent::VALIDATE_STRING,  
                        'itemUrl'        => parent::VALIDATE_URL,     
                        'itemImage'      => parent::VALIDATE_URL,     
                        'itemQuantity'   => parent::VALIDATE_INTEGER, 
                        'itemPrice'      => parent::VALIDATE_DECIMAL, 
                        'itemWeight'     => parent::VALIDATE_DECIMAL  
                    )
                ),
                'shipments'         => array(
                    array(
                        'shipper'        => parent::VALIDATE_STRING,  
                        'shippingMethod' => parent::VALIDATE_STRING,  
                        'shippingPrice'  => parent::VALIDATE_DECIMAL, 
                        'trackingNumber' => parent::VALIDATE_STRING   
                    )
                )
            ),
            'recipient'         => array(
                'fullName' => parent::VALIDATE_STRING, 			
                'confirmationEmail' => parent::VALIDATE_STRING, 
                'confirmationPhone' => parent::VALIDATE_STRING, 
                'organization' => parent::VALIDATE_STRING, 		
                'deliveryAddress' => array(
                    'streetAddress' => parent::VALIDATE_STRING, 
                    'unit' => parent::VALIDATE_STRING, 		
                    'city' => parent::VALIDATE_STRING, 			
                    'provinceCode' => parent::VALIDATE_STRING, 	
                    'postalCode' => parent::VALIDATE_STRING, 	
                    'countryCode' => parent::VALIDATE_STRING, 	
                    'latitude' => parent::VALIDATE_DECIMAL, 	
                    'longitude' => parent::VALIDATE_DECIMAL, 	
                )
            ),
            'card'              => array(
                'cardHolderName' => parent::VALIDATE_STRING, 	
                'bin' => parent::VALIDATE_DECIMAL,		 		
                'last4' => parent::VALIDATE_STRING, 			
                'expiryMonth' => parent::VALIDATE_DECIMAL, 		
                'expiryYear' => parent::VALIDATE_DECIMAL, 		
                'hash' => parent::VALIDATE_STRING, 				
                'billingAddress' => array(
                    'streetAddress' => null,
                    'unit' => parent::VALIDATE_STRING, 			
                    'city' => parent::VALIDATE_STRING,	 		
                    'provinceCode' => parent::VALIDATE_STRING, 	
                    'postalCode' => parent::VALIDATE_STRING, 	
                    'countryCode' => parent::VALIDATE_STRING, 	
                    'latitude' => parent::VALIDATE_DECIMAL, 	
                    'longitude' => parent::VALIDATE_DECIMAL, 	
                )
            ),
            'userAccount' => array(
                'email' => parent::VALIDATE_STRING, 					
                'username' => parent::VALIDATE_STRING, 					
                'phone' => parent::VALIDATE_STRING, 					
                'createdDate' => parent::VALIDATE_DATETIME, 			
                'accountNumber' => parent::VALIDATE_STRING, 			
                'lastOrderId' => parent::VALIDATE_STRING, 				
                'aggregateOrderCount' => parent::VALIDATE_INTEGER, 		
                'aggregateOrderDollars' => parent::VALIDATE_INTEGER, 	
                'lastUpdateDate' => parent::VALIDATE_DATETIME, 			
            ),
        );
    }
}
