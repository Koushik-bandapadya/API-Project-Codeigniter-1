<?php

/**
 * This controller created by Shorif, 10/07/2019
 */

use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Clt_add extends REST_Controller
{
	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('client_model');
		$this->load->helper('url');
		$this->load->model('login_model');

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
		//$id=$this->input->get_request_header('id');
		$client_pairID=$this->input->get_request_header('clientPairID');

		$isValidUser = $this->login_model->getUser($username, $password);

		$response = array();

		if(!empty($isValidUser)){
			$existClient=$this->client_model->getClientByCode($requestData['client_code']);
			if (empty($existClient)) {
				$id=$this->client_model->saveClient($requestData);
				$res=$this->client_model->getClientById($id);
				$response['message'] = "Client Inserted!";
				$response['data'] = $res;
			}else{
				$response['message'] = "Duplicate Client";
				$response['data'] = null;
			}

		} else {
			$response['message'] = "Username or password not valid";
			$response['data'] = null;
		}

		echo json_encode($response);
	}

}