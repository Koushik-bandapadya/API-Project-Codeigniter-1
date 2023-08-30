<?php

/**
 * This controller created by Shorif, 11/07/2019
 */

use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Update_order extends REST_Controller
{
	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('update_order_model');
		$this->load->helper('url');
		$this->load->model('login_model');
		$this->load->library('order_forwarder');
		$this->load->model('client_model');
	}
	protected $rest_format   = 'application/json';

	function index_post(){

		if( $this->request->body){
			$requestData = $this->request->body;
		}else{
			$requestData = $this->input->post();
		}

		// var_dump($requestData);
		$requestData = json_decode(file_get_contents('php://input'),true);
		$length = count($requestData);
		$i = 0;
		$res = false;

		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');

		$isValidUser = $this->login_model->getUser($username, $password);
		$response = array();
		if(!empty($isValidUser)){
			for ($i=0;$i<$length;$i++){
				$data = array(
					'product_id' => $requestData[$i]['product_id'],
					'quantityes' => $requestData[$i]['quantityes'],
					'client_id' => $requestData[$i]['client_id'],
					'taker_id' => $requestData[$i]['taker_id'],
					'delevary_date' => $requestData[$i]['delevary_date'],
					'plant' => $requestData[$i]['plant'],
					'taking_date' => $requestData[$i]['taking_date'],
					'order_type' => $requestData[$i]['order_type']
				);

				$txID = $requestData[$i]['txid'];

				$isValidTxid = $this->update_order_model->trxId($txID);
				if($isValidTxid == true) {
					// var_dump("data:  " ,$data);

					$res = $this->update_order_model->updateOrderTable($data, $txID);
				}
				else {
					$response['message'] = "Transaction id does not match";
				}

			}

			if(!empty($res)){
				$response['message'] = "Successfully updated data";
				// $url="http://demo.onuserver.com/payFlex/api/1v1/Update_orderflex_order";
				// $this->order_forwarder->orderCurl($url,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$requestData);

				$urlLive="https://payflex.onukit.com/total/API/1v1.1/Update_orderflex_order";//live
				// $this->order_forwarder->orderCurl($urlLive,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$requestData);
			} else {
				$response['message'] = "Failed to updated data";
			}
		} else {
			$response['message'] = "Username or password not valid";
		}

		$this->response(json_encode($response), 200);
	}
}