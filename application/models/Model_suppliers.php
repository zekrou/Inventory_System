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
        if ($id) {
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
        if ($supplier_id) {
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

        if ($query->num_rows() > 0) {
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
        if ($data) {
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
        if ($data && $id) {
            $this->db->where('id', $id);
            $update = $this->db->update('suppliers', $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    // Remove supplier - with detailed response
    public function remove($id, $force_delete = false)
    {
        if ($id) {
            // Check if supplier has purchases
            $sql = "SELECT COUNT(*) as count FROM purchases WHERE supplier_id = ?";
            $query = $this->db->query($sql, array($id));
            $result = $query->row_array();

            if ($result['count'] > 0) {
                if ($force_delete) {
                    // ✅ Force delete - AVEC SUPPRESSION CASCADE

                    // 1. Get all purchase IDs from this supplier
                    $sql_purchases = "SELECT id FROM purchases WHERE supplier_id = ?";
                    $purchases = $this->db->query($sql_purchases, array($id))->result_array();

                    // 2. Delete purchase_items for each purchase
                    foreach ($purchases as $purchase) {
                        $this->db->where('purchase_id', $purchase['id']);
                        $this->db->delete('purchase_items');
                    }

                    // 3. Delete purchases
                    $this->db->where('supplier_id', $id);
                    $this->db->delete('purchases');

                    // 4. Delete from supplier_product (if exists)
                    if ($this->db->table_exists('supplier_product')) {
                        $this->db->where('supplier_id', $id);
                        $this->db->delete('supplier_product');
                    }

                    // 5. Finally delete the supplier
                    $this->db->where('id', $id);
                    $delete = $this->db->delete('suppliers');

                    return array(
                        'success' => true,
                        'type' => 'force_deleted',
                        'message' => 'Fournisseur et tous ses achats supprimés définitivement',
                        'purchases_count' => $result['count']
                    );
                } else {
                    // Cannot delete - has purchases
                    return array(
                        'success' => false,
                        'type' => 'has_purchases',
                        'message' => 'Ce fournisseur a ' . $result['count'] . ' achat(s). Voulez-vous le désactiver ou forcer la suppression ?',
                        'purchases_count' => $result['count']
                    );
                }
            } else {
                // Safe to delete
                // Delete supplier links (if exists)
                if ($this->db->table_exists('supplier_product')) {
                    $this->db->where('supplier_id', $id);
                    $this->db->delete('supplier_product');
                }

                // Delete supplier
                $this->db->where('id', $id);
                $delete = $this->db->delete('suppliers');

                return array(
                    'success' => true,
                    'type' => 'deleted',
                    'message' => 'Fournisseur supprimé avec succès'
                );
            }
        }
        return array('success' => false, 'type' => 'error', 'message' => 'ID invalide');
    }


    // Deactivate supplier instead of deleting
    public function deactivate($id)
    {
        if ($id) {
            $data = array('active' => 0);
            $this->db->where('id', $id);
            $update = $this->db->update('suppliers', $data);
            return ($update == true) ? true : false;
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
    public function getActiveSuppliersThisMonth()
    {
        $month = date('Y-m');
        $sql = "SELECT COUNT(DISTINCT supplier_id) as count 
            FROM purchases 
            WHERE DATE_FORMAT(purchase_date, '%Y-%m') = ?";

        $query = $this->db->query($sql, array($month));
        $result = $query->row_array();
        return $result['count'];
    }
}
