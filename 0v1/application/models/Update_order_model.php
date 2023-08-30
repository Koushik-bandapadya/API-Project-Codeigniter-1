<?php

/**
 * This model created by Shorif, 11/07/2019
 */
class Update_order_model extends CI_Model
{
	public function updateOrderTable($data, $txID){
		$this->db->where('txid', $txID);
		if ($this->db->update('order_details', $data)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function trxId($txID){
		$this->db->where('txid', $txID);
		$this->db->from('order_details');
		$rslt = $this->db->get();
		if ($rslt->num_rows() > 0){
			return true;
		}
		else {
			return false;
		}
	}
}
?>