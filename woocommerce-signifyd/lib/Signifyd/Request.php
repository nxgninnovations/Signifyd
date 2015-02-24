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
                'browserIpAddress'  => parent::VALIDATE_IP,       //192.168.1.1
                'orderId'           => parent::VALIDATE_STRING,   //4fj58as
                'createdAt'         => parent::VALIDATE_DATETIME, //2013-01-18T17:54:31-05:00
                'paymentGateway'    => parent::VALIDATE_STRING,   //stripe
                'currency'          => parent::VALIDATE_CURRENCY, //USD
                'avsResponseCode'   => 'Y',
                'cvvResponseCode'   => 'M',
                'orderChannel'      => 'PHONE',
                'receivedBy'        => parent::VALIDATE_STRING,   //John Doe
                'totalPrice'        => parent::VALIDATE_DECIMAL,  //74.99
                'products'          => array(
                    array(
                        'itemId'         => parent::VALIDATE_STRING,  //1,2,3,...,n
                        'itemName'       => parent::VALIDATE_STRING,  //Sparkly sandals
                        'itemUrl'        => parent::VALIDATE_URL,     //http://mydomain.com/sparkly-sandals
                        'itemImage'      => parent::VALIDATE_URL,     //http://mydomain.com/images/sparkly-sandals.jpeg
                        'itemQuantity'   => parent::VALIDATE_INTEGER, //1
                        'itemPrice'      => parent::VALIDATE_DECIMAL, //49.99
                        'itemWeight'     => parent::VALIDATE_DECIMAL  //5
                    )
                ),
                'shipments'         => array(
                    array(
                        'shipper'        => parent::VALIDATE_STRING,  //USPS
                        'shippingMethod' => parent::VALIDATE_STRING,  //international
                        'shippingPrice'  => parent::VALIDATE_DECIMAL, //20.0
                        'trackingNumber' => parent::VALIDATE_STRING   //9201120200855113889012
                    )
                )
            ),
            'recipient'         => array(
                'fullName' => parent::VALIDATE_STRING, 			//'Bob Smith',
                'confirmationEmail' => parent::VALIDATE_STRING, //'bob@gmail.com',
                'confirmationPhone' => parent::VALIDATE_STRING, //'5047130000',
                'organization' => parent::VALIDATE_STRING, 		//'Signifyd',
                'deliveryAddress' => array(
                    'streetAddress' => parent::VALIDATE_STRING, //'123 State Street',
                    'unit' => parent::VALIDATE_STRING, 			//'2A',
                    'city' => parent::VALIDATE_STRING, 			//'Chicago',
                    'provinceCode' => parent::VALIDATE_STRING, 	//'IL',
                    'postalCode' => parent::VALIDATE_STRING, 	//'60622',
                    'countryCode' => parent::VALIDATE_STRING, 	//'US',
                    'latitude' => parent::VALIDATE_DECIMAL, 	//41.92,
                    'longitude' => parent::VALIDATE_DECIMAL, 	//-87.65
                )
            ),
            'card'              => array(
                'cardHolderName' => parent::VALIDATE_STRING, 	//'Robert Smith',
                'bin' => parent::VALIDATE_DECIMAL,		 		//407441,
                'last4' => parent::VALIDATE_STRING, 			//'1234',
                'expiryMonth' => parent::VALIDATE_DECIMAL, 		//12,
                'expiryYear' => parent::VALIDATE_DECIMAL, 		//2015,
                'hash' => parent::VALIDATE_STRING, 				//'sdfvbkel456hj',
                'billingAddress' => array(
                    'streetAddress' => null,
                    'unit' => parent::VALIDATE_STRING, 			//'2A',
                    'city' => parent::VALIDATE_STRING,	 		//'Chicago',
                    'provinceCode' => parent::VALIDATE_STRING, 	//'IL',
                    'postalCode' => parent::VALIDATE_STRING, 	//'60622',
                    'countryCode' => parent::VALIDATE_STRING, 	//'US',
                    'latitude' => parent::VALIDATE_DECIMAL, 	//41.92,
                    'longitude' => parent::VALIDATE_DECIMAL, 	//-87.65
                )
            ),
            'userAccount' => array(
                'email' => parent::VALIDATE_STRING, 					//'bob@gmail.com',
                'username' => parent::VALIDATE_STRING, 					//'bobbo',
                'phone' => parent::VALIDATE_STRING, 					//'5555551212',
                'createdDate' => parent::VALIDATE_DATETIME, 			//'2013-01-18T17:54:31-05:00',
                'accountNumber' => parent::VALIDATE_STRING, 			//'54321',
                'lastOrderId' => parent::VALIDATE_STRING, 				//'4321',
                'aggregateOrderCount' => parent::VALIDATE_INTEGER, 		//40,
                'aggregateOrderDollars' => parent::VALIDATE_INTEGER, 	//5000,
                'lastUpdateDate' => parent::VALIDATE_DATETIME, 			//'2013-01-18T17:54:31-05:00'
            ),
        );
    }
}
