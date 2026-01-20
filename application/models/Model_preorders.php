<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_preorders extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getPreOrders($user_id = null) {
        $this->db->select('*');
        $this->db->from('pre_orders');
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }
    
    public function getPreOrderById($id) {
        return $this->db->get_where('pre_orders', ['id' => $id])->row_array();
    }
    
    public function getPreOrderItems($pre_order_id) {
        return $this->db->get_where('pre_order_items', ['pre_order_id' => $pre_order_id])->result_array();
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('pre_orders', ['status' => $status], ['id' => $id]);
    }
    
    public function deletePreOrder($id) {
        return $this->db->delete('pre_orders', ['id' => $id]);
    }
    
    public function create($data) {
        return $this->db->insert('pre_orders', $data);
    }
    
    public function getLastInsertId() {
        return $this->db->insert_id();
    }
    
    public function insertItem($data) {
        return $this->db->insert('pre_order_items', $data);
    }
}
