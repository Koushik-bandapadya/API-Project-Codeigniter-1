<?php
class Employee_wise_order_model extends CI_Model{

	public function getEmployeeWiseOrderDetails($designation, $start_date, $end_date, $order_type){
		$parentList=$this->getEmployeeList($designation);
		$response=array();
		if (empty($parentList)){
			$response['message']="No subordinate exist under this employee";
		}else
		{
			$resCount=0;
			foreach ($parentList as $parentItem){
				//$parentItem['forecast_order']=$this->getOrderData($parentItem['id'],1,$date);
				//echo $parentItem['employeeId'];
				$subord=$this->getEmployeeList($parentItem['id']);
				if (empty($subord)){
					$parentItem['subordinates']=false;
				}else{
					$parentItem['subordinates']=true;
				}
				$parentItem['orders']=$this->getClientOrderSum($parentItem['coded_employeeId'],$order_type,$start_date,$end_date,$designation);
				$response[$resCount]=$parentItem;
				$resCount++;
			}
		}
		return $response;
	}

	public function getEmployeeList($designation){
		$this->db->select('employees.*,employee_designation.designation as designationTitle,employee_info.*');
		$this->db->from('employees');
		$this->db->join('employee_designation','employee_designation.id=employees.designation', 'left');
		$this->db->join('employee_info','employee_info.id=employees.info_id', 'left');
		$this->db->where('employees.designation',$designation,'after');
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}

	private function getClientOrderSum($id,$orderType,$start_date,$end_date,$designation){
		$productList=$this->getProductList();
		$forecastOrder=array();
		$counter=0;
		foreach ($productList as $proItem){
			$productArray=array(
				"product_name"=>$proItem['p_name'],
				"product_type"=>$proItem['p_type'],
				"product_id"=>$proItem['id'],
				"order_id"=>"",
				"txid"=>"",
				"client_id"=>$id,
				"taker_id"=>"",
				"delevary_date"=>"",
				"plant"=>"",
				"taking_date"=>"",
				"order_type"=>"",
				"quantityes"=>0
			);
			$forecastOrder[$counter]=$productArray;
			$counter++;
		}
		$clientOrderList = $this->getEmployeeClientOrderDetails($id,$orderType,$start_date,$end_date,$designation);
		foreach ($clientOrderList as $orderItem){
			$productId=$orderItem['product_id'];
			$quan=$orderItem['quantityes'];
			$counter=0;
			foreach ($forecastOrder as $forOrderItem)
			{
				if ($forecastOrder[$counter]['product_id']==$productId){
					if (!empty($quan)){
						$forecastOrder[$counter]['quantityes']+=$quan;
						$forecastOrder[$counter]['txid']=$orderItem['txid'];
						$forecastOrder[$counter]['client_id']=$orderItem['client_id'];
						$forecastOrder[$counter]['taker_id']=$orderItem['taker_id'];
						$forecastOrder[$counter]['delevary_date']=$orderItem['delevary_date'];
						$forecastOrder[$counter]['plant']=$orderItem['plant'];
						$forecastOrder[$counter]['taking_date']=$orderItem['taking_date'];
						$forecastOrder[$counter]['order_id']=$orderItem['id'];
						$forecastOrder[$counter]['order_type']=$orderItem['order_type'];
					}
				}
				$counter++;
			}
		}
		return $forecastOrder;
	}

	private function getProductList(){
		$this->db->select('*');
		$this->db->from('product_details');
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}

	private function getEmployeeClientOrderDetails($id,$orderType,$start_date,$end_date,$designation){
		$this->db->select('*');
		$this->db->from('client_info');
		$this->db->join('order_details','order_details.client_id=client_info.id and order_details.order_type='.$orderType, 'right');
		//$this->db->where('client_info.handler_id', $id);
		if ($designation==6 || $designation==1) {
			$this->db->like('client_info.client_pairID',$id.".",'after');
		}else{
			$this->db->like('client_info.client_pairID',$id,'after');
		}

		//$this->db->like('client_info.client_pairID',$id,'after');

//        if ($orderType==1){
//			$this->db->where('taking_date >=', $start_date);
//			$this->db->where('taking_date <=', $end_date);
//		}
//		if ($orderType==2){
			$this->db->where('delevary_date >=', $start_date);
			$this->db->where('delevary_date <=', $end_date);
//		}
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		//echo print_r($result);
		return $result;
	}

}
