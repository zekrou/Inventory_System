<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_purchases extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPurchaseData($id = null)
    {
        if ($id) {
            $sql = "SELECT p.*, 
                s.name as supplier_name, 
                s.phone as supplier_phone,
                s.email as supplier_email,
                s.address as supplier_address
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


    public function getPurchaseItems($purchase_id)
    {
        $sql = "SELECT pi.*, p.name as product_name, p.sku, st.name as stock_name
                FROM purchase_items pi
                LEFT JOIN products p ON pi.product_id = p.id
                LEFT JOIN stock st ON pi.stock_id = st.id
                WHERE pi.purchase_id = ?";
        $query = $this->db->query($sql, array($purchase_id));
        return $query->result_array();
    }

    public function create($data, $items)
    {
        if ($data && $items) {
            // ✅ valider user_id pour le tenant
            $user_id = $this->session->userdata('id');
            $user_check = $this->db->where('id', $user_id)->get('users');
            if ($user_check->num_rows() == 0) {
                $admin   = $this->db->select('id')
                    ->order_by('id', 'ASC')
                    ->limit(1)
                    ->get('users')
                    ->row();
                $user_id = $admin ? $admin->id : 1;
            }

            // Generate unique purchase number
            $last   = $this->db->select('id')->order_by('id', 'DESC')->limit(1)->get('purchases')->row();
            $number = $last ? ($last->id + 1) : 1;
            $purchase_no = 'PUR-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // Check if purchase_no exists
            $check = $this->db->where('purchase_no', $purchase_no)->get('purchases');
            while ($check->num_rows() > 0) {
                $number++;
                $purchase_no = 'PUR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
                $check = $this->db->where('purchase_no', $purchase_no)->get('purchases');
            }

            $purchase_data = array(
                'purchase_no'            => $purchase_no,
                'supplier_id'            => $data['supplier_id'],
                'purchase_date'          => date('Y-m-d H:i:s'),
                'expected_delivery_date' => isset($data['expected_delivery_date']) ? $data['expected_delivery_date'] : NULL,
                'total_amount'           => $data['total_amount'],
                'paid_amount'            => isset($data['paid_amount']) ? $data['paid_amount'] : 0,
                'due_amount'             => isset($data['due_amount']) ? $data['due_amount'] : $data['total_amount'],
                'payment_status'         => isset($data['payment_status']) ? $data['payment_status'] : 'unpaid',
                'payment_method'         => isset($data['payment_method']) ? $data['payment_method'] : NULL,
                'status'                 => 'pending',
                'notes'                  => isset($data['notes']) ? $data['notes'] : NULL,
                'created_by'             => $user_id,   // ✅ id valide dans ce tenant
            );

            $insert      = $this->db->insert('purchases', $purchase_data);
            $purchase_id = $this->db->insert_id();

            if ($insert && $purchase_id) {
                foreach ($items as $item) {
                    $itemdata = array(
                        'purchase_id' => $purchase_id,
                        'product_id'  => $item['product_id'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'stock_id'    => isset($item['stock_id']) && !empty($item['stock_id']) ? $item['stock_id'] : NULL,
                    );
                    $this->db->insert('purchase_items', $itemdata);
                }

                return $purchase_id;
            }
        }


        return false;
    }

    /**
     * Get purchases data with optional search term
     * @param string|null $searchTerm - Search in supplier name or purchase number
     * @return array
     */
    public function getPurchasesDataWithSearch($searchTerm = null)
    {
        // 🟢 Ajoute s.phone dans le SELECT
        $this->db->select('p.*, s.name as supplier_name, s.phone as supplier_phone');
        $this->db->from('purchases p');
        $this->db->join('suppliers s', 'p.supplier_id = s.id', 'LEFT');

        // Search filter
        if (!empty($searchTerm)) {
            $this->db->group_start();
            $this->db->like('s.name', $searchTerm);
            $this->db->or_like('p.purchase_no', $searchTerm);
            $this->db->or_like('s.phone', $searchTerm);
            $this->db->group_end();
        }

        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();

        if (!$query) {
            log_message('error', 'Database error: ' . $this->db->error()['message']);
            return array();
        }

        return $query->result_array();
    }


    public function receivePurchase($purchase_id)
    {
        if ($purchase_id) {
            $user_id = $this->session->userdata('id');

            // ✅ AJOUTE CES 7 LIGNES ICI
            $user_check = $this->db->where('id', $user_id)->get('users');

            if ($user_check->num_rows() == 0) {
                // L'utilisateur n'existe pas dans ce tenant, utiliser l'admin du tenant
                $admin = $this->db->select('id')->order_by('id', 'ASC')->limit(1)->get('users')->row();
                $user_id = $admin ? $admin->id : 1;
            }
            // ✅ FIN DE L'AJOUT

            $items = $this->getPurchaseItems($purchase_id);

            if ($items) {
                $this->load->model('model_products');

                foreach ($items as $item) {
                    $product = $this->model_products->getProductData($item['product_id']);

                    if ($product) {
                        // ✅ CALCULATE NEW AVERAGE COST
                        $old_qty = $product['qty'];
                        $old_avg_cost = $product['average_cost'] ?: 0;
                        $new_qty_purchased = $item['quantity'];
                        $new_purchase_price = $item['unit_price'];

                        $total_qty = $old_qty + $new_qty_purchased;

                        // Formula: (Old Stock × Old Avg + New Stock × New Price) / Total Stock
                        if ($total_qty > 0) {
                            $new_average_cost = (($old_qty * $old_avg_cost) + ($new_qty_purchased * $new_purchase_price)) / $total_qty;
                        } else {
                            $new_average_cost = $new_purchase_price;
                        }

                        // ✅ UPDATE PRODUCT
                        $update_data = array(
                            'qty' => $total_qty,
                            'average_cost' => round($new_average_cost, 2),
                            'last_purchase_price' => $new_purchase_price,
                            'purchase_price_updated_at' => date('Y-m-d H:i:s')
                        );

                        $this->model_products->update($update_data, $item['product_id']);
                    }
                }

                // Update purchase status
                $update_purchase = array(
                    'status' => 'received',
                    'received_date' => date('Y-m-d H:i:s'),
                    'received_by' => $user_id  // ✅ Maintenant c'est l'ID correct
                );

                $this->db->where('id', $purchase_id);
                return $this->db->update('purchases', $update_purchase);
            }
        }

        return false;
    }




    public function cancelPurchase($purchase_id)
    {
        if ($purchase_id) {
            $purchase = $this->getPurchaseData($purchase_id);

            if ($purchase && $purchase['status'] == 'pending') {
                $data = array('status' => 'cancelled');
                $this->db->where('id', $purchase_id);
                return $this->db->update('purchases', $data);
            }
        }

        return false;
    }
    /**
     * Get payment history for a purchase
     */
    public function getPaymentHistory($purchase_id)
    {
        if ($purchase_id) {
            $sql = "SELECT pp.*, u.username 
                FROM purchase_payments pp
                LEFT JOIN users u ON pp.created_by = u.id
                WHERE pp.purchase_id = ?
                ORDER BY pp.payment_date DESC";
            $query = $this->db->query($sql, array($purchase_id));
            return $query->result_array();
        }
        return array();
    }

    public function remove($purchase_id, $force_delete = false)
    {
        if (!$purchase_id) {
            return array('success' => false, 'message' => 'ID invalide');
        }

        $purchase = $this->getPurchaseData($purchase_id);

        if (!$purchase) {
            return array('success' => false, 'message' => 'Achat introuvable');
        }

        // Si l'achat est reçu, demander confirmation
        if ($purchase['status'] == 'received' && !$force_delete) {
            return array(
                'success' => false,
                'type' => 'is_received',
                'message' => 'Cet achat est deja recu. Voulez-vous forcer la suppression? Le stock sera restaure.'
            );
        }

        // ✅ SI L'ACHAT EST REÇU, RESTAURER LE STOCK
        if ($purchase['status'] == 'received') {
            $items = $this->getPurchaseItems($purchase_id);

            if (!empty($items)) {
                // ✅ valider user_id pour le tenant
                $user_id = $this->session->userdata('id');
                $user_check = $this->db->where('id', $user_id)->get('users');
                if ($user_check->num_rows() == 0) {
                    $admin   = $this->db->select('id')
                        ->order_by('id', 'ASC')
                        ->limit(1)
                        ->get('users')
                        ->row();
                    $user_id = $admin ? $admin->id : 1;
                }

                foreach ($items as $item) {
                    // Get current product stock
                    $sql   = "SELECT qty FROM products WHERE id = ?";
                    $query = $this->db->query($sql, array($item['product_id']));
                    $product = $query->row_array();

                    if ($product) {
                        $quantity_before = $product['qty'];

                        // ✅ SOUSTRAIRE la quantité achetée du stock
                        $new_qty = $quantity_before - $item['quantity'];

                        // Ne pas avoir de stock négatif
                        if ($new_qty < 0) {
                            $new_qty = 0;
                        }

                        // Update stock
                        $this->db->where('id', $item['product_id']);
                        $this->db->update('products', array('qty' => $new_qty));

                        // ✅ ENREGISTRER dans stock_history avec la bonne structure
                        $stock_history_data = array(
                            'product_id'      => $item['product_id'],
                            'purchase_id'     => $purchase_id,
                            'movement_type'   => 'adjustment', // ou 'return' selon ta logique
                            'quantity'        => -$item['quantity'], // Négatif car on retire
                            'quantity_before' => $quantity_before,
                            'quantity_after'  => $new_qty,
                            'unit_price'      => $item['unit_price'],
                            'user_id'         => $user_id, // ✅ déjà validé
                            'notes'           => 'Purchase ' . $purchase['purchase_no'] . ' deleted - Stock restored',
                            'created_at'      => date('Y-m-d H:i:s'),
                        );

                        $this->db->insert('stock_history', $stock_history_data);
                    }
                }
            }
        }


        // Delete purchase_items
        $this->db->where('purchase_id', $purchase_id);
        $this->db->delete('purchase_items');

        // Delete purchase_payments
        $this->db->where('purchase_id', $purchase_id);
        $this->db->delete('purchase_payments');

        // Delete purchase
        $this->db->where('id', $purchase_id);
        $delete = $this->db->delete('purchases');

        if ($delete) {
            return array(
                'success' => true,
                'message' => 'Achat supprime avec succes' . ($purchase['status'] == 'received' ? '. Stock restaure.' : '')
            );
        }

        return array('success' => false, 'message' => 'Erreur lors de la suppression');
    }





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

    public function getPurchasesBySupplier($supplier_id)
    {
        if ($supplier_id) {
            $sql = "SELECT * FROM purchases WHERE supplier_id = ? ORDER BY id DESC";
            $query = $this->db->query($sql, array($supplier_id));
            return $query->result_array();
        }
        return array();
    }
}
