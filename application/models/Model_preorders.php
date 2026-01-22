<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_preorders extends CI_Model 
{
    public function get_all_preorders()
    {
        $this->db->select('*, customer_name, customer_phone');
        $this->db->from('pre_orders');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_preorder($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('pre_orders')->row_array();
    }

    public function get_preorder_items($pre_order_id)
    {
        $this->db->where('pre_order_id', $pre_order_id);
        return $this->db->get('pre_order_items')->result_array();
    }

    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('pre_orders', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function delete_preorder($id)
    {
        // Supprime items d'abord
        $this->db->where('pre_order_id', $id);
        $this->db->delete('pre_order_items');
        
        // Puis pré-commande
        $this->db->where('id', $id);
        $this->db->delete('pre_orders');
    }
}
