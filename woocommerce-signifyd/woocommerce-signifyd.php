<?php
/*
Plugin Name: Signifyd - Fraud and Chargeback Protection
Plugin URI: http://www.signifyd.com/
Description: Signifyd Integration with WooCommerce
Version: 1.0.2
Author: Antony Marceles
Author URI: http://www.signifyd.com/
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Begins execution of the plugin.
	 */
	add_action( 'plugins_loaded', 'run_WC_signifyd' );

	function run_WC_signifyd() {

		// Add Signifyd Metabox to the Order Edit Page 
		add_action( 'add_meta_boxes', 'signified_order_meta_boxes' );
		function signified_order_meta_boxes()
		{
			add_meta_box(
				'woocommerce-order-signifyd_nxgn',
				__( 'Signifyd Analysis' ),
				'order_meta_box_signifyd',
				'shop_order',
				'side',
				'high'
			);
		}

		// Add Signifyd Score & Button to Signifyd Metabox 
		function order_meta_box_signifyd()
		{	
				global $post;
				$id = $post->ID;

				echo '<div style="width:100%; text-align:center; margin-bottom:5px" id="signifyd_request" post_id="'.$id.'">';
				echo '<h2><a id="signifyd_score" href="">  </a></h2>';
				echo '<h4 id="signifyd_recommendation" style="margin:-20px 0px 6px 0px;"> </h4>';
				echo '</div>';

		}

		// Adding the JavaScript file to wp-ajax
		function ajax_load_scripts() {
			if (is_admin()) {
				// load our jquery file that sends the $.post request
				wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/signifyd.js', array( 'jquery' ) );
			 
				// make the ajaxurl var available to the above script
				wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
			}
		}
		add_action('wp_print_scripts', 'ajax_load_scripts');

		// PHP function that is called by the JavaScript File to perform external requests
		function ajax_get_score() {

				global $wpdb;
				$post_id = $_POST["postid"];
				$caseID = get_post_meta( $post_id, '_signifyd_case_id', true );
			
				require_once dirname(__FILE__).'/lib/Signifyd/Retrieve.php';
				$request = new Signifyd_Case_Retrieve();
				$responseObject = $request->get($caseID);
				echo json_encode($responseObject);
				die();
			
		} 
		add_action('wp_ajax_get_score', 'ajax_get_score');


		// Create Signifyd Case Request on Woocommerce Payment Complete
		if ( ! class_exists( 'WC_NxGn_Signifyd' ) ) {
			
			class WC_NxGn_Signifyd {
			
				public function __construct() {
					add_filter( 'woocommerce_thankyou', array( &$this,'text_ajax_create_case'), 10, 1 );
				}
				
				function text_ajax_create_case($order_id) {
						
						global $wpdb;
						$time = new DateTime;
						$post_id = $order_id;
				
						require_once dirname(__FILE__).'/lib/Signifyd/Request.php';

						$request = new Signifyd_Case_Request();

						//ADD ORDER INFORMATION
						$request->addOrderInformation(array(
							'browserIpAddress'  => get_post_meta( $post_id, '_customer_ip_address', true ),
							'orderId'           => $post_id,
							'createdAt'         => $time->format(DateTime::ATOM),
							'paymentGateway'    => 'paypal',
							'currency'          => 'USD',
							'orderChannel'      => 'WEB',
							'receivedBy'        => '',
							'totalPrice'        => get_post_meta( $post_id, '_order_total', true)
						));

						//ADD PRODUCT INFORMATION
						$order = new WC_Order( $post_id );
						$items = $order->get_items();

						foreach ( $items as $item ) {
						$request->addProduct(array(
									'itemId'=> $item['product_id'],
									'itemName'=>  $item['name'],
									'itemQuantity'=> $item['qty'],
									'itemPrice'=> $item['line_total'],			
								));
						}						
						//ADD RECIPIENT INFORMATION
						$request->addRecipient(array(
								'fullName' => get_post_meta( $post_id, '_shipping_first_name', true ).' '.get_post_meta( $post_id, '_shipping_last_name', true ),
								'confirmationEmail' => get_post_meta( $post_id, '_billing_email', true ),
								'confirmationPhone' => get_post_meta( $post_id, '_billing_phone', true ),
								'organization' => get_post_meta( $post_id, '_shipping_company', true ),
								'deliveryAddress' => array(
									'streetAddress' =>  get_post_meta( $post_id, '_shipping_address_1', true ),
									'unit' =>  get_post_meta( $post_id, '_shipping_address_2', true ),
									'city' =>  get_post_meta( $post_id, '_shipping_city', true ),
									'provinceCode' =>  get_post_meta( $post_id, '_shipping_state', true ),
									'postalCode' => get_post_meta( $post_id, '_shipping_postcode', true ),
									'countryCode' => get_post_meta( $post_id, '_shipping_country', true ),
								))
						);
						

						//ADD CARD INFORMATION
						$request->addCard(array(
								'cardHolderName' => get_post_meta( $post_id, '_billing_first_name', true ).' '.get_post_meta( $post_id, '_billing_last_name', true ),
								'billingAddress' => array(
									'streetAddress' => get_post_meta( $post_id, '_billing_address_1', true ),
									'unit' => get_post_meta( $post_id, '_billing_address_2', true ),
									'city' => get_post_meta( $post_id, '_billing_city', true ),
									'provinceCode' => get_post_meta( $post_id, '_billing_state', true ),
									'postalCode' => get_post_meta( $post_id, '_billing_postcode', true ),
									'countryCode' => get_post_meta( $post_id, '_billing_country', true ),
								))
						); 

						$responseObject = $request->send();
						add_post_meta( $post_id, '_signifyd_case_id', $responseObject->investigationId, true );
				}
			}
			
			// instantiate plugin class, add it to the set of globals
			$GLOBALS['wc_nxgn_signifyd'] = new WC_NxGn_Signifyd();
		}

		include('lib/admin-page.php'); // the plugin options page HTML and save functions

	}
}