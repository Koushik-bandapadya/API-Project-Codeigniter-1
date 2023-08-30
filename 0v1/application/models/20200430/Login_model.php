<?php
/**
 * Created by PhpStorm.
 * User: 1992b
 * Date: 7/2/2019
 * Time: 12:08 PM
 */
class Login_model extends CI_Model{
	public function getUser($username,$password){

		$this->db->select('company_user.id,
		company_user.username,
		company_user.password,
		company_user.created_time,
		company_user.employee_id');
		$this->db->from('company_user');
		$this->db->where('username', $username);
		$this->db->where('password', $password);
		$rslt = $this->db->get();
		$result = $rslt->result_array();
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
}
