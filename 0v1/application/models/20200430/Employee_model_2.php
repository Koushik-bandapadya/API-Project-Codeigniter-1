<?php
/**
 * Created by PhpStorm.
 * User: 1992b
 * Date: 7/2/2019
 * Time: 6:23 PM
 */
class Employee_model_2 extends CI_Model{

    //call function
    public function getEmployeeOrderDetails($employeeId,$designation,$start_date,$end_date){
        $parentList=$this->getEmployeeList($employeeId);
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
                $parentItem['forecast_order']=$this->getClientOrderSum($parentItem['coded_employeeId'],1,$start_date,$end_date,$designation);
                //$parentItem['schedule_order']=$this->getClientOrderSum($parentItem['coded_employeeId'],2,$start_date,$end_date,$designation);
                $response[$resCount]=$parentItem;
                $resCount++;
            }
        }
        return $response;
    }

    public function getAllEmployeeList($employeeId){
        $this->db->select('employees.*,employee_designation.designation as designationTitle,employee_info.*');
        $this->db->from('employees');
        $this->db->join('employee_designation','employee_designation.id=employees.designation', 'left');
        $this->db->join('employee_info','employee_info.id=employees.info_id', 'left');
        $this->db->like('employees.coded_employeeId',$employeeId,'after');
        $rslt = $this->db->get();
        $result = $rslt->result_array();
        return $result;
    }
    //custom function

    private function getClientOrderSum($id,$orderType,$start_date,$end_date,$designation){
        //echo "</br>";
       // echo "Employee ID: ".$id;
        $productList=$this->getProductList();
        $forecastOrder=array();
        $counter=0;
        $ton=0;
        foreach ($productList as $proItem){
//			$productArray=array("product_name"=>$proItem['p_name'],"product_id"=>$proItem['id'],"quantities"=>0);
            $productArray=array(
                "product_name"=>$proItem['p_name'],
                "product_type"=>$proItem['p_type'],
                "product_id"=>$proItem['id'],
                "product_amount"=>$proItem['p_amount'],
                "order_id"=>"",
                "txid"=>"",
                "client_id"=>$id,
                "taker_id"=>"",
                "delevary_date"=>"",
                "plant"=>"",
                "taking_date"=>"",
                "order_type"=>"",
                "quantities"=>0
            );
            $forecastOrder[$counter]=$productArray;
            $counter++;
        }
        $clientOrderList = $this->getDsrClientDetails($id,$orderType,$start_date,$end_date,$designation);
        foreach ($clientOrderList as $orderItem){
            $productId=$orderItem['product_id'];
//            $productName=$orderItem['product_name'];
//            $productAmount=$orderItem['p_amount'];
//            echo "</br>";
//            echo "Product name: ".$productName;
//            echo " Amount: ".$productAmount;
            $quan=$orderItem['quantityes'];
            $counter=0;
            foreach ($forecastOrder as $forOrderItem)
            {
                if ($forecastOrder[$counter]['product_id']==$productId){
                    if (!empty($quan)){
                        //echo $quan;
                        $ton=$ton+($forecastOrder[$counter]['product_amount']*$quan)/1000;
                        $forecastOrder[$counter]['quantities']+=$quan;
                    }else{
                        //echo "empty";
                    }
                }
                if ($forecastOrder[$counter]['product_name']=='TON'){
                    $forecastOrder[$counter]['quantities']=$ton;
                }
                $counter++;
            }
            //$ton=0;

        }
//        echo "</br>";
//        echo $ton;
        //$forecastOrder[$counter]['quantities']=$ton/1000;
        return $forecastOrder;
    }
//	private function getClientOrderSum($id,$orderType,$start_date,$end_date){
//		$productList=$this->getProductList();
//		$forecastOrder=array();
//		$counter=0;
//		if ($this->avgFlag){
//		}else{
//		}
//		foreach ($productList as $proItem){
//			if ($this->avgFlag){
//				$productArray=array(
//					"product_name"=>$proItem['p_name'],
//					"product_type"=>$proItem['p_type'],
//					"product_id"=>$proItem['id'],
//					"order_id"=>"",
//					"txid"=>"",
//					"client_id"=>$id,
//					"taker_id"=>"",
//					"delevary_date"=>"",
//					"plant"=>"",
//					"taking_date"=>"",
//					"order_type"=>"",
//					"quantityes"=>0
//				);
//			}else{
//
//				$productArray=array(
//					"product_name"=>$proItem['p_name'],
//					"product_type"=>$proItem['p_type'],
//					"product_id"=>$proItem['id'],
//					"order_id"=>"",
//					"txid"=>"",
//					"client_id"=>$id,
//					"taker_id"=>"",
//					"delevary_date"=>"",
//					"plant"=>"",
//					"taking_date"=>"",
//					"order_type"=>"",
//					"quantityes"=>0
//				);
//			}
//
//			$forecastOrder[$counter]=$productArray;
//			$counter++;
//		}
//		$clientOrderList = $this->getClientOrders($id,$orderType,$start_date,$end_date);
//		foreach ($clientOrderList as $orderItem){
//			$productId=$orderItem['product_id'];
//			$quan=$orderItem['quantityes'];
//			$counter=0;
//			foreach ($forecastOrder as $forOrderItem)
//			{
//				if ($forecastOrder[$counter]['product_id']==$productId){
//					if (!empty($quan)){
//						$forecastOrder[$counter]['quantityes']+=$quan;
//						$forecastOrder[$counter]['txid']=$orderItem['txid'];
//						$forecastOrder[$counter]['client_id']=$orderItem['client_id'];
//						$forecastOrder[$counter]['taker_id']=$orderItem['taker_id'];
//						$forecastOrder[$counter]['delevary_date']=$orderItem['delevary_date'];
//						$forecastOrder[$counter]['plant']=$orderItem['plant'];
//						$forecastOrder[$counter]['taking_date']=$orderItem['taking_date'];
//						$forecastOrder[$counter]['order_id']=$orderItem['id'];
//						$forecastOrder[$counter]['order_type']=$orderItem['order_type'];
//					}
//				}
//				$counter++;
//			}
//		}
//		return $forecastOrder;
//	}
    private function getEmployeeList($employeeId){
        $this->db->select('*');
        $this->db->from('employees');
        $this->db->join('employee_info','employee_info.id=employees.info_id', 'right');
        $this->db->where('employees.parent_id', $employeeId);
        $rslt = $this->db->get();
        $result = $rslt->result_array();
        return $result;
    }

    private function getProductList(){
        $this->db->select('*');
        $this->db->from('product_details');
        $rslt = $this->db->get();
        $result = $rslt->result_array();
        return $result;
    }

    private function getDsrClientDetails($id,$orderType,$start_date,$end_date,$designation){
        $this->db->select('*');
        $this->db->from('client_info');
        $this->db->join('order_table','order_table.client_id=client_info.id and order_table.order_type='.$orderType, 'right');
        //$this->db->where('client_info.handler_id', $id);
        if ($designation==6 || $designation==1) {
            $this->db->like('client_info.client_pairID',$id.".",'after');
        }else{
            $this->db->like('client_info.client_pairID',$id,'after');
        }
        //$this->db->like('client_info.client_pairID',$id.".",'both');
//		if ($orderType==1){
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


















//	private function getOrderData($employeeId,$orderType,$date)
//	{
//		$orderSammary = array();
//		$listRes = $this->getEmployeeList($employeeId);
//		if (empty($listRes)) {
//			$orderSammary=$this->getClientOrder($employeeId,$orderType,$date);
//		} else
//			{
//			$whileCount=0;
//			while (!empty($listRes)) {
//				$tempList = array();
//				foreach ($listRes as $listItem) {
//					$subList=$this->getEmployeeList($listItem['id']);
//					$tempList=array_merge($tempList,$subList);
//				}
//				$whileCount++;
//				if (empty($tempList)){
//					$orderSammary=$this->getClientOrderSum($listRes,$orderType,$date);
//					$listRes=$tempList;
//				}else{
//					$listRes=$tempList;
//				}
//			}
//		}
//		return $orderSammary;
//	}


//	private function getClientOrder($dsrID,$orderType,$date){
//		$productList=$this->getProductList();
//		$forecastOrder=array();
//		$counter=0;
//		foreach ($productList as $proItem){
//			$productArray=array("product_name"=>$proItem['p_name'],"product_type"=>$proItem['id'],"quantities"=>0);
//			$forecastOrder[$counter]=$productArray;
//			$counter++;
//		}
//
//		$clientOrderList = $this->getDsrClientDetails($dsrID,$orderType,$date);
//		foreach ($clientOrderList as $orderItem){
//			$productId=$orderItem['product_id'];
//			$quan=$orderItem['quantityes'];
//			$counter=0;
//			foreach ($forecastOrder as $forOrderItem)
//			{
//				if ($forecastOrder[$counter]['product_type']==$productId){
//					if (!empty($quan)){
//						//echo $quan;
//						$forecastOrder[$counter]['quantities']+=$quan;
//					}else{
//						//echo "empty";
//					}
//				}
//				$counter++;
//			}
//		}
//		return $forecastOrder;
//	}

?>
