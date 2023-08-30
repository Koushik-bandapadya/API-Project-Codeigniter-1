<?php

class Payment_model extends CI_Model

{
	public function getBankList(){
		$this->db->select('*');
		$this->db->from('tbl_financial_institution_list');
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}
	public function getPaymentMode(){
		$this->db->select('*');
		$this->db->from('tbl_payment_mode');
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}
	public function getClientOrderDetails($client_id,$delivery_date){
		$this->db->select('tbl_customer_order.*,
			order_details.txid,
			order_details.id as product_order_id, 
			order_details.order_type, 
			order_details.plant,
			order_details.quantityes,
			product_details.id as product_id,
			product_details.p_name,
			product_details.p_type,
			tbl_product_price.p_retailPrice,
			tbl_product_price.p_wholesalePrice,
			tbl_product_price.p_specialPrice,
			product_details.p_discription');
		$this->db->from('`tbl_customer_order`');
		$this->db->join('order_details','tbl_customer_order.id=order_details.customer_order_id', 'left');
		$this->db->join('product_details','order_details.product_id=product_details.id', 'left');
		$this->db->join('tbl_product_price','tbl_product_price.product_id=product_details.id', 'left');
		$this->db->where('order_for_client_id', $client_id);
		$this->db->where('delivery_date', $delivery_date);
		$this->db->where('order_type', 2);
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		//echo print_r($result);
		//echo json_encode($result);
		return $result;
	}

	public function savePayment($data){
		$this->db->insert('tbl_payment', $data);
		$Info = $this->db->insert_id();
		return $Info;
	}

	public function getPaymentList($order_code){
		$this->db->select('tbl_payment.*,
			tbl_financial_institution_list.bank_name,
			tbl_payment_mode.methode_name,tbl_payment_mode.custom_methode');
		$this->db->from("tbl_payment");
		$this->db->join('tbl_financial_institution_list','tbl_payment.financial_institution_id=tbl_financial_institution_list.id', 'left');
		$this->db->join('tbl_payment_mode','tbl_payment.payment_mode_id=tbl_payment_mode.id', 'left');
		$this->db->where('tbl_payment.order_code',$order_code);
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		//echo print_r($result);
		//echo json_encode($result);
		return $result;
	}

// 	SELECT tbl_payment.*,
// tbl_financial_institution_list.bank_name,
// tbl_payment_mode.methode_name,tbl_payment_mode.custom_methode
// FROM tbl_payment
// LEFT JOIN tbl_financial_institution_list ON tbl_payment.financial_institution_id=tbl_financial_institution_list.id
// LEFT JOIN tbl_payment_mode ON tbl_payment.payment_mode_id=tbl_payment_mode.id
// WHERE order_code='15763202870030'

// SELECT 
// tbl_customer_order.*,
// order_details.txid,order_details.id as product_order_id, order_details.order_type, order_details.plant,order_details.quantityes,
// product_details.id as product_id,product_details.p_name,product_details.p_type,product_details.p_retailPrice,product_details.p_wholesalePrice,product_details.p_specialPrice
// FROM tbl_customer_order 
// LEFT JOIN order_details ON tbl_customer_order.id=order_details.customer_order_id
// LEFT JOIN product_details ON order_details.product_id=product_details.id
// where delivery_date='2019-10-17' 
// AND order_for_client_id='572'
// AND order_type='2'
}
?>