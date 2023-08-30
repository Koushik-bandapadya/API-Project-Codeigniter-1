<?php
/**
 * Created by PhpStorm.
 * User: 1992b
 * Date: 7/2/2019
 * Time: 12:08 PM
 */
class Login_model extends CI_Model{
	public function getUser($username,$password){

		$this->db->select('*');
		$this->db->from('company_user');
		$this->db->where('username', $username);
		$this->db->where('password', $password);
		$rslt = $this->db->get();
		$result = $rslt->result_array();

		//$str =$this->db->last_query(); 
		//var_dump($str );
		
		return $result;
	}
	public function getUserInfo($employeeID){
		$this->db->select('*');
		$this->db->from('employees');
		$this->db->join('employee_info', 'employee_info.id=employees.info_id', 'right');
		$this->db->where('employees.id', $employeeID);
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}
	public function getClientUserInfo($clientID){
		$this->db->select('*');
		$this->db->from('company_user');
		$this->db->join('client_info', 'client_info.id=company_user.client_id', 'left');
		$this->db->where('company_user.client_id', $clientID);
		$rslt = $this->db->get();
		$result = $rslt->result_array();
		return $result;
	}
}
