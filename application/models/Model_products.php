<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_preorders extends CI_Model
{
    private $db_conn;

    public function __construct($tenant_db = null)
    {
        parent::__construct();
        $this->db_conn = $tenant_db ?? $this->db;
    }

    public function setTenantDb($tenant_db)
    {
        $this->db_conn = $tenant_db;
    }

    /**
     * Get all pre-orders with optional status filter
     */
    public function getPreOrders($status = null)
    {
        $this->db_conn->select('po.*, u.username, u.firstname, u.lastname')
            ->from('pre_orders po')
            ->join('users u', 'po.user_id = u.id', 'left')
            ->order_by('po.created_at', 'DESC');

        if ($status) {
            $this->db_conn->where('po.status', $status);
        }

        $query = $this->db_conn->get();
        return $query->result_array();
    }

    /**
     * Get single pre-order by ID
     */
    public function getPreOrderById($id)
    {
        $this->db_conn->select('po.*, u.username, u.firstname, u.lastname')
            ->from('pre_orders po')
            ->join('users u', 'po.user_id = u.id', 'left')
            ->where('po.id', $id);
        
        return $this->db_conn->get()->row_array();
    }

    /**
     * Get pre-order items
     */
    public function getPreOrderItems($pre_order_id)
    {
        $this->db_conn->select('poi.*, p.sku, p.image')
            ->from('pre_order_items poi')
            ->join('products p', 'poi.product_id = p.id', 'left')
            ->where('poi.pre_order_id', $pre_order_id)
            ->order_by('poi.id', 'ASC');
        
        return $this->db_conn->get()->result_array();
    }

    /**
     * Update pre-order status
     */
    public function updateStatus($id, $status)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db_conn->where('id', $id);
        return $this->db_conn->update('pre_orders', $data);
    }

    /**
     * Delete pre-order and items
     */
    public function deletePreOrder($id)
    {
        // Delete items first
        $this->db_conn->where('pre_order_id', $id);
        $this->db_conn->delete('pre_order_items');
        
        // Delete order
        $this->db_conn->where('id', $id);
        return $this->db_conn->delete('pre_orders');
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $this->db_conn->select('
            COUNT(*) as total_orders,
            SUM(CASE WHEN status="pending" THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status="confirmed" THEN 1 ELSE 0 END) as confirmed_count,
            SUM(CASE WHEN status="cancelled" THEN 1 ELSE 0 END) as cancelled_count,
            SUM(total_amount) as total_revenue
        ', FALSE);
        
        $query = $this->db_conn->get('pre_orders');
        $result = $query->row_array();
        
        return $result ?: [
            'total_orders' => 0,
            'pending_count' => 0,
            'confirmed_count' => 0,
            'cancelled_count' => 0,
            'total_revenue' => 0
        ];
    }
}
