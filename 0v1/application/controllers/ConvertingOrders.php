<?php
/**
 * Created by PhpStorm.
 * User: 1992b
 * Date: 9/8/2019
 * Time: 12:42 PM
 */
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class ConvertingOrders extends REST_Controller
{
	function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model('save_order_model');
		$this->load->helper('url');
		$this->load->model('login_model');
		$this->load->model('order_model');
		$this->load->library('order_forwarder');
	}
	function index_post(){
		$username = $this->input->get_request_header('username');
		$password = $this->input->get_request_header('password');
		$date = $this->input->get_request_header('todayDate');

		$today= date('mdY', time());

		$requestBody=array();
		$requestBody['date']=$date;
		// $url="http://demo.onuserver.com/payFlex/api/1v1/convertingOrders";
		// $this->order_forwarder->orderCurl($url,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$requestBody);

		$urlLive="https://payflex.onukit.com/total/API/1v1.1/convertingOrders";//live
		// $this->order_forwarder->orderCurl($urlLive,"orderflex.admin@payflex.com","t0t@l@dm1nP@$5",$requestBody);

		$isValidUser = $this->login_model->getUser($username, $password);

		if(!empty($isValidUser)){
			$requestData =$this->order_model->getTodayForecast($date, 1);
			$length = count($requestData);

			for ($i=0;$i<$length;$i++){

				$data = array('txid' => $requestData[$i]['txid'].$date,
					'product_id' => $requestData[$i]['product_id'],
					'quantityes' => $requestData[$i]['quantityes'],
					'client_id' => $requestData[$i]['client_id'],
					'taker_id' => $requestData[$i]['taker_id'],
					'delevary_date' => $requestData[$i]['delevary_date'],
					'plant' => $requestData[$i]['plant'],
					'taking_date' => $requestData[$i]['taking_date'],
					'customer_order_id' => $requestData[$i]['customer_order_id'],
					'order_type' => 2
				);

				$check=$this->order_model->getProductBytxid($requestData[$i]['txid'].$date,2);
				if (empty($check)){
					$res = $this->save_order_model->insertOrderTable($data);
				}else{
					$response['message'] = "already converted";
				}
			}

			if(!empty($res) ){
				$response['message'] = "Successfully convert data";

			} else {
				$response['message'] = "Failed to convert data";
			}
		} else {
			$response['message'] = "Username or password not valid";
		}
		echo json_encode($response);
	}

}
