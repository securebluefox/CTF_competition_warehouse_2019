<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop_m extends CI_Model
{

    public function commodity($data)
    {
        $page_size = 8;
        $page = $data['page'];
        $sql = "SELECT * FROM commodity ORDER BY id DESC LIMIT ?,?";
        $start = ($page - 1)*$page_size;
        $query = $this->db->query($sql,array($start,$page_size));
        $result = $query->result_array();
        return $result;
    }
    public function commodityNum()
    {
        $sql = "SELECT count(*) FROM commodity";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        return $result[0]['count(*)'];
    }

    public function commodityDetail($id)
    {
        $data = $this->db->get_where('commodity', ['id' => $id])->row();
        return $data;
    }

}




