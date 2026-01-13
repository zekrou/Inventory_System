<?php
class Model_stock extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active stock
     */
    public function getActiveStock()
    {
        $sql = "SELECT * FROM `stock` WHERE active = ? ORDER BY name ASC";
        $query = $this->db->query($sql, array(1));
        return $query->result_array();
    }

    /**
     * Get stock data
     */
    public function getStockData($id = null)
    {
        if ($id) {
            $sql = "SELECT * FROM `stock` WHERE id = ?";
            $query = $this->db->query($sql, array($id));
            return $query->row_array();
        }

        $sql = "SELECT * FROM `stock` ORDER BY name ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get stock with categories count
     */
    public function getStockWithCategories($stock_id)
    {
        $sql = "SELECT s.*, COUNT(c.id) as category_count 
                FROM `stock` s
                LEFT JOIN `categories` c ON s.id = c.stock_id
                WHERE s.id = ?
                GROUP BY s.id";
        $query = $this->db->query($sql, array($stock_id));
        return $query->row_array();
    }

    /**
     * Get stock statistics
     */
    public function getStockStatistics($stock_id = null)
    {
        if ($stock_id) {
            // Stats pour un stock spécifique
            $sql = "SELECT 
                    COUNT(DISTINCT p.id) as total_products,
                    SUM(p.qty) as total_quantity,
                    SUM(CASE WHEN p.qty > 10 THEN 1 ELSE 0 END) as good_stock,
                    SUM(CASE WHEN p.qty > 0 AND p.qty <= 10 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN p.qty = 0 THEN 1 ELSE 0 END) as out_of_stock
                    FROM `products` p
                    WHERE p.stock_id = ?";
            $query = $this->db->query($sql, array($stock_id));
        } else {
            // Stats globales
            $sql = "SELECT 
                    COUNT(DISTINCT p.id) as total_products,
                    SUM(p.qty) as total_quantity,
                    SUM(CASE WHEN p.qty > 10 THEN 1 ELSE 0 END) as good_stock,
                    SUM(CASE WHEN p.qty > 0 AND p.qty <= 10 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN p.qty = 0 THEN 1 ELSE 0 END) as out_of_stock
                    FROM `products` p";
            $query = $this->db->query($sql);
        }
        return $query->row_array();
    }

    /**
     * Create stock
     */
    public function create($data)
    {
        if ($data) {
            $insert = $this->db->insert('stock', $data);
            return ($insert == true) ? $this->db->insert_id() : false;
        }
        return false;
    }

    /**
     * Update stock
     */
    public function update($data, $id)
    {
        if ($data && $id) {
            $this->db->where('id', $id);
            $update = $this->db->update('stock', $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    /**
     * Remove stock
     */
    public function remove($id)
    {
        if ($id) {
            // Check if stock has categories
            $sql = "SELECT COUNT(*) as count FROM `categories` WHERE stock_id = ?";
            $query = $this->db->query($sql, array($id));
            $result = $query->row_array();

            if ($result['count'] > 0) {
                // Has categories, just deactivate
                $this->db->where('id', $id);
                return $this->db->update('stock', array('active' => 0));
            } else {
                // No categories, safe to delete
                $this->db->where('id', $id);
                return $this->db->delete('stock');
            }
        }
        return false;
    }

    /**
     * Count total stock
     */
    public function countTotalStock()
    {
        $sql = "SELECT COUNT(*) as total FROM `stock` WHERE active = ?";
        $query = $this->db->query($sql, array(1));
        $result = $query->row_array();
        return $result['total'];
    }
    public function getTodayMovements()
    {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) as count 
            FROM stock_history 
            WHERE DATE(created_at) = ?";

        $query = $this->db->query($sql, array($today));
        $result = $query->row_array();
        return $result['count'];
    }
}
