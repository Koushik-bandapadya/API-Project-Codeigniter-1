<?php


use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class EmployeeWiseOrder extends REST_Controller{
	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('Employee_wise_order_model');
		$this->load->helper('url');
		$this->load->model('Login_model');
	}

	function index_get(){
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		$start_date = $this->input->get_request_header('startDate');
		$end_date = $this->input->get_request_header('endDate');
		$designation = $this->input->get_request_header('designation');
		$order_type = $this->input->get_request_header('orderType');

		$isValidUser = $this->Login_model->getUser($username, $password);

		$response = array();
		if(!empty($isValidUser)){
			$response = $this->Employee_wise_order_model->getEmployeeWiseOrderDetails($designation, $start_date, $end_date, $order_type);
		}
		else {
			$response['message'] = "User does not exist";
		}
		// echo json_encode($request);

		echo json_encode($response);
		
	}
}
