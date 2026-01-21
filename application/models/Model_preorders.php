<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_preorders extends CI_Model
{
    private $db_conn;
    
    public function __construct($tenant_db = null)
    {
        parent::__construct();
        // Si un tenant_db est fourni, on l'utilise, sinon fallback sur master
        $this->db_conn = $tenant_db ?? $this->load->database('master', TRUE);
    }

    public function setTenantDb($tenant_db)
    {
        $this->db_conn = $tenant_db;
    }

    public function getPreOrders()
    {
        $query = $this->db_conn->order_by('created_at', 'DESC')->get('pre_orders');
        return $query->result_array();
    }

    public function getPreOrderById($id)
    {
        return $this->db_conn->get_where('pre_orders', ['id'=>$id])->row_array();
    }

    public function getPreOrderItems($pre_order_id)
    {
        $this->db_conn->order_by('id', 'ASC');
        return $this->db_conn->get_where('pre_order_items', ['pre_order_id'=>$pre_order_id])->result_array();
    }

    public function updateStatus($id, $status)
    {
        $data = ['status'=>$status, 'updated_at'=>date('Y-m-d H:i:s')];
        $this->db_conn->where('id', $id);
        return $this->db_conn->update('pre_orders', $data);
    }

    public function deletePreOrder($id)
    {
        $this->db_conn->where('pre_order_id', $id);
        $this->db_conn->delete('pre_order_items');

        $this->db_conn->where('id', $id);
        return $this->db_conn->delete('pre_orders');
    }

    public function getStatistics()
    {
        $this->db_conn->select('
            COUNT(*) as total_orders,
            SUM(CASE WHEN status="pending" THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status="approved" THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status="rejected" THEN 1 ELSE 0 END) as rejected_count,
            SUM(CASE WHEN status="completed" THEN 1 ELSE 0 END) as completed_count,
            SUM(total_amount) as total_revenue
        ', FALSE);
        $query = $this->db_conn->get('pre_orders');
        $result = $query->row_array();
        return $result ?: [
            'total_orders'=>0,'pending_count'=>0,'approved_count'=>0,
            'rejected_count'=>0,'completed_count'=>0,'total_revenue'=>0
        ];
    }
}
