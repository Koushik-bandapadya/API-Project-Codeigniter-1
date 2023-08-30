<?php
/**
 * Created by PhpStorm.
 * User: 1992b
 * Date: 9/15/2019
* Time: 12:13 PM
*/
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class DesignationWiseEmployeeOrder extends REST_Controller{
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
		//$designation = $this->input->get_request_header('designation');
		$order_type = $this->input->get_request_header('orderType');

		$isValidUser = $this->Login_model->getUser($username, $password);

		$response = array();
		if(!empty($isValidUser)){
			$response['manager'] = $this->Employee_wise_order_model->getEmployeeWiseOrderDetails(1, $start_date, $end_date, $order_type);
			$response['officer'] = $this->Employee_wise_order_model->getEmployeeWiseOrderDetails(4, $start_date, $end_date, $order_type);
			$response['dsr'] = $this->Employee_wise_order_model->getEmployeeWiseOrderDetails(5, $start_date, $end_date, $order_type);
		}
		else {
			$response['message'] = "User does not exist";
		}
		// echo json_encode($request);

		echo json_encode($response);

	}
}
