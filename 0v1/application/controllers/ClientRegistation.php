<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class ClientRegistation extends REST_Controller
{
	var $data;

	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->helper('url');
		$this->load->model('login_model');
		$this->load->model('client_model');
		$this->load->model('camp_Slab_model');
	}
	protected $rest_format   = 'application/json';

	public function index_post(){
    $requestData = json_decode(file_get_contents('php://input'),true);
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		$isValidUser = $this->login_model->getUser($username, $password);
		$response = array();

		if(!empty($isValidUser)){
			$Info=$this->client_model->saveNewClient($requestData['client_info']);
			$index=$Info;
			$requestData['client_camp_relation']['client_id']=$index;
			$this->camp_Slab_model->saveClientCampSlab($requestData['client_camp_relation']);
			$response['code'] =200;
			$response['message']="Successfully inserted";
		}else {
			$response['code'] =204;
			$response['message']="Username or Password error";
		}
		echo json_encode($response);
  }
}
?>
