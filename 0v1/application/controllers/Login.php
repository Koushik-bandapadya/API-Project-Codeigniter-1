<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Login extends REST_Controller
{
	var $data;

	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('login_model');
		$this->load->helper('url');
	}

	protected $rest_format   = 'application/json';

	public function index_post(){
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		//echo $username." ; ".$password;
		//die();
		$userData=$this->login_model->getUser($username,$password);
		if (empty($userData)){
			$response=array();
			$response['message']="Invalid username or password";
			echo json_encode($response);
		}else{
			foreach ($userData as $item){
				$employeeID=$item['employee_id'];
				$employeeData=$this->login_model->getUserInfo($employeeID);

				foreach ($employeeData as $item){
					$response=array();
					$response['message']="Successfully login";
					$response['employeeId']=$item['id'];
					$response['designation']=$item['designation'];
					$response['employeeName']=$item['name'];
					$response['userName']=$username;
					$response['password']=$password;
					$response['email']=$item['email'];
					$response['office_email']=$item['office_email'];
					$response['contact_number']=$item['contact_number'];
					$response['home_address']=$item['home_address'];
					$response['emergency_contact']=$item['emergency_contact'];
					$response['coded_employeeId']=$item['coded_employeeId'];
					$response['created_date']=$item['created_date'];
					if ($item['is_client_handler']=="1"){
						$response['is_clientHandler']=true;
					}else{
						$response['is_clientHandler']=false;
					}
					echo json_encode($response);
				}
			}
		}
	}
}
