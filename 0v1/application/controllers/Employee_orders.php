<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Employee_orders extends REST_Controller
{
	var $data;

	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('employee_model');
		$this->load->helper('url');
		$this->load->model('login_model');
	}
	protected $rest_format   = 'application/json';

	public function index_get(){
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		$employeeId = $this->input->get_request_header('codedEmployeeId');
		$designation = $this->input->get_request_header('designation');
		$start_date = $this->input->get_request_header('startDate');
		$end_date = $this->input->get_request_header('endDate');

		//echo "employeeId: ".$employeeId;

		$userData=$this->login_model->getUser($username,$password);
		$response=array();
		if (empty($userData)){
			$response['message']="Invalid username or password";
			echo json_encode($response);
		}else{
			$response=$this->employee_model->getEmployeeOrderDetails($employeeId,$designation,$start_date,$end_date);
			echo json_encode($response);
		}
	}
}
