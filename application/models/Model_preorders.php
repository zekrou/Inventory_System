<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_preorders extends CI_Model
{
    private $master_db;
    
    public function __construct()
    {
        parent::__construct();
        
        // ✅ Connexion à la base MASTER (stock_master)
        $this->master_db = $this->load->database('master', TRUE);
    }
    
    /**
     * Get all pre-orders avec infos utilisateur
     */
    public function getPreOrders()
    {
        $sql = "SELECT 
            po.*, 
            u.username as created_by_username,
            u.firstname as created_by_firstname,
            u.lastname as created_by_lastname
        FROM pre_orders po
        LEFT JOIN users u ON po.user_id = u.id
        ORDER BY po.created_at DESC";
        
        $query = $this->master_db->query($sql);
        return $query->result_array();
    }
    
    /**
     * Get pre-order by ID
     */
    public function getPreOrderById($id)
    {
        $sql = "SELECT 
            po.*, 
            u.username as created_by_username,
            u.firstname as created_by_firstname,
            u.lastname as created_by_lastname,
            u.phone as created_by_phone
        FROM pre_orders po
        LEFT JOIN users u ON po.user_id = u.id
        WHERE po.id = ?";
        
        $query = $this->master_db->query($sql, array($id));
        return $query->row_array();
    }
    
    /**
     * Get pre-order items
     */
    public function getPreOrderItems($pre_order_id)
    {
        $sql = "SELECT 
            poi.*
        FROM pre_order_items poi
        WHERE poi.pre_order_id = ?
        ORDER BY poi.id";
        
        $query = $this->master_db->query($sql, array($pre_order_id));
        return $query->result_array();
    }
    
    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $sql = "SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(total_amount) as total_revenue
        FROM pre_orders";
        
        $query = $this->master_db->query($sql);
        $result = $query->row_array();
        
        // Valeurs par défaut si aucune donnée
        if (!$result || $result['total_orders'] == 0) {
            return [
                'total_orders' => 0,
                'pending_count' => 0,
                'approved_count' => 0,
                'rejected_count' => 0,
                'completed_count' => 0,
                'total_revenue' => 0
            ];
        }
        
        return $result;
    }
    
    /**
     * Update status
     */
    public function updateStatus($id, $status)
    {
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $this->master_db->where('id', $id);
        return $this->master_db->update('pre_orders', $data);
    }
    
    /**
     * Delete pre-order
     */
    public function deletePreOrder($id)
    {
        // Delete items first
        $this->master_db->where('pre_order_id', $id);
        $this->master_db->delete('pre_order_items');
        
        // Delete pre-order
        $this->master_db->where('id', $id);
        return $this->master_db->delete('pre_orders');
    }
}
