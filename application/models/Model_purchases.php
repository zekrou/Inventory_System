<?php 
class Model_purchases extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get purchase data
     */
    public function getPurchaseData($id = null)
    {
        if($id) {
            $sql = "SELECT p.*, s.name as supplier_name, s.phone as supplier_phone
                    FROM purchases p
                    LEFT JOIN suppliers s ON p.supplier_id = s.id
                    WHERE p.id = ?";
            $query = $this->db->query($sql, array($id));
            return $query->row_array();
        }

        $sql = "SELECT p.*, s.name as supplier_name
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                ORDER BY p.id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get purchase items
     */
    public function getPurchaseItems($purchase_id)
    {
        $sql = "SELECT pi.*, p.name as product_name, p.sku
                FROM purchase_items pi
                LEFT JOIN products p ON pi.product_id = p.id
                WHERE pi.purchase_id = ?";
        $query = $this->db->query($sql, array($purchase_id));
        return $query->result_array();
    }

    /**
     * Create purchase
     */
    public function create($data, $items)
    {
        if($data && $items) {
            $user_id = $this->session->userdata('id');
            
            // Generate purchase number
            $purchase_no = 'PUR-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            
            $purchase_data = array(
                'purchase_no' => $purchase_no,
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => date('Y-m-d H:i:s'),
                'total_amount' => $data['total_amount'],
                'status' => 'pending', // pending, received, cancelled
                'notes' => isset($data['notes']) ? $data['notes'] : '',
                'created_by' => $user_id
            );

            $insert = $this->db->insert('purchases', $purchase_data);
            $purchase_id = $this->db->insert_id();

            if($insert) {
                // Insert purchase items
                foreach($items as $item) {
                    $item_data = array(
                        'purchase_id' => $purchase_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price']
                    );
                    $this->db->insert('purchase_items', $item_data);
                }

                return $purchase_id;
            }
        }
        return false;
    }

    /**
     * Mark purchase as received and update product quantities
     */
    public function receivePurchase($purchase_id)
    {
        if($purchase_id) {
            $user_id = $this->session->userdata('id');
            
            // Get purchase items
            $items = $this->getPurchaseItems($purchase_id);
            
            if($items) {
                $this->load->model('model_products');
                
                // Update each product quantity
                foreach($items as $item) {
                    $product = $this->model_products->getProductData($item['product_id']);
                    if($product) {
                        $new_qty = $product['qty'] + $item['quantity'];
                        $update_data = array('qty' => $new_qty);
                        $this->model_products->update($update_data, $item['product_id']);
                        
                        // Record stock history
                        $this->recordStockHistory(
                            $item['product_id'],
                            $purchase_id,
                            'purchase_received',
                            $item['quantity'],
                            $product['qty'],
                            $new_qty,
                            $user_id
                        );
                    }
                }
                
                // Update purchase status
                $update_purchase = array(
                    'status' => 'received',
                    'received_date' => date('Y-m-d H:i:s'),
                    'received_by' => $user_id
                );
                $this->db->where('id', $purchase_id);
                return $this->db->update('purchases', $update_purchase);
            }
        }
        return false;
    }

    /**
     * Record stock movement
     */
    private function recordStockHistory($product_id, $purchase_id, $type, $qty, $qty_before, $qty_after, $user_id)
    {
        if($this->db->table_exists('stock_history')) {
            $data = array(
                'product_id' => $product_id,
                'purchase_id' => $purchase_id,
                'movement_type' => $type,
                'quantity' => $qty,
                'quantity_before' => $qty_before,
                'quantity_after' => $qty_after,
                'user_id' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('stock_history', $data);
        }
    }

    /**
     * Get purchases by supplier
     */
    public function getPurchasesBySupplier($supplier_id)
    {
        if($supplier_id) {
            $sql = "SELECT * FROM purchases WHERE supplier_id = ? ORDER BY id DESC";
            $query = $this->db->query($sql, array($supplier_id));
            return $query->result_array();
        }
        return array();
    }

    /**
     * Get purchases by product
     */
    public function getPurchasesByProduct($product_id)
    {
        if($product_id) {
            $sql = "SELECT p.*, s.name as supplier_name
                    FROM purchases p
                    INNER JOIN purchase_items pi ON p.id = pi.purchase_id
                    LEFT JOIN suppliers s ON p.supplier_id = s.id
                    WHERE pi.product_id = ?
                    ORDER BY p.purchase_date DESC";
            $query = $this->db->query($sql, array($product_id));
            return $query->result_array();
        }
        return array();
    }

    /**
     * Get purchase statistics
     */
    public function getPurchaseStatistics()
    {
        $sql = "SELECT 
                COUNT(*) as total_purchases,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_purchases,
                SUM(CASE WHEN status = 'received' THEN 1 ELSE 0 END) as received_purchases,
                SUM(total_amount) as total_spent,
                SUM(CASE WHEN status = 'received' THEN total_amount ELSE 0 END) as received_amount
                FROM purchases";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    /**
     * Cancel purchase
     */
    public function cancelPurchase($purchase_id)
    {
        if($purchase_id) {
            $purchase = $this->getPurchaseData($purchase_id);
            
            if($purchase && $purchase['status'] == 'pending') {
                $data = array('status' => 'cancelled');
                $this->db->where('id', $purchase_id);
                return $this->db->update('purchases', $data);
            }
        }
        return false;
    }
}