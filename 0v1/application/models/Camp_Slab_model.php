<?php
class Camp_Slab_model extends CI_Model
{
  public function saveClientCampSlab($relationInfo){
    $this->db->insert('client_camp_slab', $relationInfo);
    $Info = $this->db->insert_id();
    return $Info;
  }
}
 ?>
