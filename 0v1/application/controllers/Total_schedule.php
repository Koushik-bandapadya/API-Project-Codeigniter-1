<?php
/**
 * Created by PhpStorm.
 * User: bidyut
 * Date: 8/5/2019
 * Time: 2:57 PM
 */
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
class Total_schedule extends REST_Controller
{
	var $data;

	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('order_model');
		$this->load->helper('url');
		$this->load->model('login_model');
	}
	protected $rest_format = 'application/json';

	public function index_get()
	{
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		$start_date = $this->input->get_request_header('startDate');
		$end_date = $this->input->get_request_header('endDate');

		$userData=$this->login_model->getUser($username,$password);
		$response=array();
		if (empty($userData)){
			$response['message']="Invalid username or password";
			echo json_encode($response);
		}else{
			$employeeData=$this->login_model->getUserInfo($userData[0]['employee_id']);
			if($employeeData[0]['designation']==6){
				$response= $this->order_model->getTotalSchedule($employeeData[0]['id'],$start_date,$end_date);
				echo json_encode($response);
			}else{
				$response['message']="Ops! You have no permission to see.";
				echo json_encode($response);
			}

		}
	}
}
