<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_preorders extends CI_Model
{
    public function get_all_preorders()
    {
        $this->db->select('p.*, CONCAT(u.firstname, " ", u.lastname) as customer_name, u.phone');
        $this->db->from('pre_orders p');
        $this->db->join('users u', 'u.id = p.user_id', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result_array();
    }


    public function get_preorder($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('pre_orders')->row_array();
    }

    public function get_preorder_items($preorder_id)
    {
        $this->db->where('preorder_id', $preorder_id);
        return $this->db->get('pre_order_items')->result_array();
    }

    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('pre_orders', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function delete_preorder($id)
    {
        // Supprime les items d'abord
        $this->db->where('preorder_id', $id);
        $this->db->delete('pre_order_items');

        // Puis la pré-commande
        $this->db->where('id', $id);
        $this->db->delete('pre_orders');
    }
}
