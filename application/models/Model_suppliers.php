<?php 
class Model_suppliers extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active suppliers
     */
    public function getActiveSuppliers()
    {
        $sql = "SELECT * FROM `suppliers` WHERE active = 1 ORDER BY name ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get supplier data
     */
    public function getSupplierData($id = null)
    {
        if($id) {
            $sql = "SELECT * FROM `suppliers` WHERE id = ?";
            $query = $this->db->query($sql, array($id));
            return $query->row_array();
        }

        $sql = "SELECT * FROM `suppliers` ORDER BY id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get products from a supplier
     */
    public function getSupplierProducts($supplier_id)
    {
        if($supplier_id) {
            $sql = "SELECT p.*, sp.supplier_price, sp.lead_time_days 
                    FROM products p
                    INNER JOIN supplier_product sp ON p.id = sp.product_id
                    WHERE sp.supplier_id = ?
                    ORDER BY p.name ASC";
            $query = $this->db->query($sql, array($supplier_id));
            return $query->result_array();
        }
        return array();
    }

    /**
     * Link product to supplier
     */
    public function linkProductToSupplier($supplier_id, $product_id, $supplier_price, $lead_time_days = 0)
    {
        // Check if link already exists
        $sql = "SELECT * FROM supplier_product WHERE supplier_id = ? AND product_id = ?";
        $query = $this->db->query($sql, array($supplier_id, $product_id));
        
        if($query->num_rows() > 0) {
            // Update existing link
            $data = array(
                'supplier_price' => $supplier_price,
                'lead_time_days' => $lead_time_days
            );
            $this->db->where('supplier_id', $supplier_id);
            $this->db->where('product_id', $product_id);
            return $this->db->update('supplier_product', $data);
        } else {
            // Create new link
            $data = array(
                'supplier_id' => $supplier_id,
                'product_id' => $product_id,
                'supplier_price' => $supplier_price,
                'lead_time_days' => $lead_time_days
            );
            return $this->db->insert('supplier_product', $data);
        }
    }

    /**
     * Create supplier
     */
    public function create($data)
    {
        if($data) {
            $insert = $this->db->insert('suppliers', $data);
            return ($insert == true) ? $this->db->insert_id() : false;
        }
        return false;
    }

    /**
     * Update supplier
     */
    public function update($data, $id)
    {
        if($data && $id) {
            $this->db->where('id', $id);
            $update = $this->db->update('suppliers', $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    /**
     * Remove supplier
     */
    public function remove($id)
    {
        if($id) {
            // Check if supplier has purchases
            $sql = "SELECT COUNT(*) as count FROM purchases WHERE supplier_id = ?";
            $query = $this->db->query($sql, array($id));
            $result = $query->row_array();
            
            if($result['count'] > 0) {
                // Don't delete, just deactivate
                $data = array('active' => 0);
                $this->db->where('id', $id);
                return $this->db->update('suppliers', $data);
            } else {
                // Safe to delete
                $this->db->where('id', $id);
                return $this->db->delete('suppliers');
            }
        }
        return false;
    }

    /**
     * Get supplier statistics
     */
    public function getSupplierStats($supplier_id)
    {
        $sql = "SELECT 
                COUNT(DISTINCT p.id) as total_purchases,
                SUM(p.total_amount) as total_spent,
                MAX(p.purchase_date) as last_purchase_date
                FROM purchases p
                WHERE p.supplier_id = ?";
        $query = $this->db->query($sql, array($supplier_id));
        return $query->row_array();
    }

    /**
     * Count total suppliers
     */
    public function countTotalSuppliers()
    {
        $sql = "SELECT COUNT(*) as total FROM suppliers WHERE active = 1";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        return $result['total'];
    }
}