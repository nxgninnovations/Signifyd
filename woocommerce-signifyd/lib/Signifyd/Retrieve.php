<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once dirname(__FILE__).'/Abstract.php';

class Signifyd_Case_Retrieve extends Signifyd_Case_Abstract{

    public function __construct(){
        $this->_urlscore = API_CASES_URL;
    }
   
}
