<?php

/**
 * This controller created by Shorif, 10/07/2019
 */

use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Save_order extends REST_Controller
{
	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('save_order_model');
		$this->load->helper('url');
		$this->load->model('login_model');
		$this->load->model('client_model');
		$this->load->library('order_forwarder');
	}
	protected $rest_format   = 'application/json';

	function index_post(){

		if( $this->request->body){
			$requestData = $this->request->body;
		}else{
			$requestData = $this->input->post();
		}
		$requestData = json_decode(file_get_contents('php://input'),true);

		$length = count($requestData);
		$i = 0;

		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');

		$isValidUser = $this->login_model->getUser($username, $password);

		$response = array();

		$orderData = array(
			'taking_date' => $requestData[0]['taking_date'],
			'delivery_date' => $requestData[0]['delevary_date'],
			//'insert_date_time' => "",
			'order_code' => $requestData[0]['txid'],
			'taker_id' => $requestData[0]['taker_id'],
			'order_for_client_id' => $requestData[0]['client_id'],
			);

		$clientDetails=$this->client_model->getClientById($requestData[0]['client_id']);
		$request_body=array();
		$request_body['client_code']=$clientDetails[0]['client_code'];
		$request_body['customer_order']=$orderData;
		$request_body['order_detail']=$requestData;
		// echo json_encode($request_body);
		// die();

		$order_index=$this->save_order_model->createdNewCustomerOrder($orderData);

		$orderData['id']=$order_index;


		if(!empty($isValidUser) && $order_index>0 && $order_index!=null){

			for ($i=0;$i<$length;$i++){

				$data = array('txid' => $requestData[$i]['txid'],
					'product_id' => $requestData[$i]['product_id'],
					'quantityes' => $requestData[$i]['quantityes'],
					'client_id' => $requestData[$i]['client_id'],
					'taker_id' => $requestData[$i]['taker_id'],
					'delevary_date' => $requestData[$i]['delevary_date'],
					'plant' => $requestData[$i]['plant'],
					'taking_date' => $requestData[$i]['taking_date'],
					'order_type' => $requestData[$i]['order_type'],
					'customer_order_id'=>$order_index
				);

				$res = $this->save_order_model->insertOrderTable($data);
			}

			if(!empty($res) ){
				$response['message'] = "Successfully saved data";
				// $url="http://demo.onuserver.com/payFlex/api/1v1/Save_orderflex_order";//demo
				// $this->order_forwarder->orderCurl($url,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$request_body);

				$urlLive="https://payflex.onukit.com/total/API/1v1.1/Save_orderflex_order";//live
				// $this->order_forwarder->orderCurl($urlLive,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$request_body);
			} else {
				$response['message'] = "Failed to save data";
				$this->save_order_model->deletCustomerOrder($order_index);
			}
		} else {
			$response['message'] = "Username or password not valid";
			$this->save_order_model->deletCustomerOrder($order_index);
		}

		$this->response(json_encode($response), 200);

		// if(!empty($isValidUser)){

		// 	for ($i=0;$i<$length;$i++){

		// 		$data = array('txid' => $requestData[$i]['txid'],
		// 			'product_id' => $requestData[$i]['product_id'],
		// 			'quantityes' => $requestData[$i]['quantityes'],
		// 			'client_id' => $requestData[$i]['client_id'],
		// 			'taker_id' => $requestData[$i]['taker_id'],
		// 			'delevary_date' => $requestData[$i]['delevary_date'],
		// 			'plant' => $requestData[$i]['plant'],
		// 			'taking_date' => $requestData[$i]['taking_date'],
		// 			'order_type' => $requestData[$i]['order_type']
		// 		);
		// 		$res = $this->save_order_model->insertOrderTable($data);
		// 	}

		// 	if(!empty($res) ){
		// 		$response['message'] = "Successfully saved data";
		// 	} else {
		// 		$response['message'] = "Failed to save data";
		// 	}
		// } else {
		// 	$response['message'] = "Username or password not valid";
		// }

		// $this->response(json_encode($response), 200);
	}

}