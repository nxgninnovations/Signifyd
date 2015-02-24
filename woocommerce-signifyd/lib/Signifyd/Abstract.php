<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

global $post;
require_once dirname(__FILE__).'/../Varien/Object.php';


abstract class Signifyd_Case_Abstract extends Varien_Object{

    const PURCHASE = 'purchase';
    const API_CASES_URL = 'https://api.signifyd.com/v2/cases';
	const PASSWORD = '';
    
    //VALIDATION
    const VALIDATE_IP = 'VALIDATION_IP'; 
    const VALIDATE_STRING = 'VALIDATION_STRING'; 
    const VALIDATE_DATETIME = 'VALIDATION_DATETIME'; 
    const VALIDATE_CURRENCY = 'VALIDATION_CURRENCY'; 
    const VALIDATE_DECIMAL = 'VALIDATION_DECIMAL'; 
    const VALIDATE_URL = 'VALIDATION_URL';
    const VALIDATE_INTEGER = 'VALIDATION_INTEGER'; 

    protected $_type = null;
    protected $_url = null;

    protected function _getTemplate(){
        return array();
    }

    protected function _validateData($validation ,$value){
        switch($validation){
            case self::VALIDATE_IP:
            case self::VALIDATE_STRING:
            case self::VALIDATE_DATETIME:
            case self::VALIDATE_CURRENCY:
            case self::VALIDATE_DECIMAL:
            case self::VALIDATE_URL:
            case self::VALIDATE_INTEGER:
                if(empty($value)){
                    throw new Exception('Wrong value for "'.$validation.'" =>"'.$value.'"');
                }
        }
        return TRUE;
    }

    protected function _iterate($data,$template){
        $_tmpData = array();
        foreach($template as $idx => $_data){
            if(is_array($_data) && isset($_data[0]) && is_array($_data[0])){
                foreach($data[$idx] as $idxItem => $_dataItem){
                    $_tmpData[$idx][$idxItem] = $this->_iterate($_dataItem,$_data[0]); //RECURSIVE
                }
            }else{
                if($this->_validateData($_data, $data[$idx])){
                    $_tmpData[$idx] = $data[$idx];
                }
            }
        }
        return $_tmpData;
    }

    public function send(){
        return $this->_send();
    }

    protected function _send(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getJson());
        curl_setopt($ch, CURLOPT_USERPWD, get_site_option( 'signifyd_api' ).':'.self::PASSWORD);
        $response = curl_exec($ch);
        curl_close($ch);
        $jsonResponse = json_decode($response);
        if(!is_object($jsonResponse)){
            throw new Exception($response);
        }
        $this->setResponse($jsonResponse);
        return $jsonResponse;
    }

	public function get($caseID){
        return $this->_get($caseID);
    }

    protected function _get($caseID){
		$geturl = 'https://api.signifyd.com/v2/cases/'.$caseID;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $geturl);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_USERPWD, get_site_option( 'signifyd_api' ).':'.self::PASSWORD);
        $response = curl_exec($ch);
        curl_close($ch);
  
        $this->setResponse($jsonResponse);
		return json_decode($response,true)[score];
    }
	
    protected function getJson(){
        if(!$this->hasJson()){
            $this->setData('json',json_encode($this->_prepareData()));
        }
        return $this->getData('json');
    }

    protected function _prepareData(){
        $tmp = $this->_iterate($this->getData(),$this->_getTemplate());
        return $tmp;
    }

}
