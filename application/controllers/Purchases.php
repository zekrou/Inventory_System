<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property Model_orders $model_orders
 * @property Model_products $model_products
 * @property Model_customers $model_customers
 * @property Model_orders $model_orders
 * @property Model_company $model_company
 * @property CI_Output $output
 * 
 * Custom Models
 * @property Model_purchases $model_purchases
 * @property Model_suppliers $model_suppliers
 */
class Purchases extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Purchases';
        $this->load->model('model_purchases');
        $this->load->model('model_suppliers');
        $this->load->model('model_products');
    }

    public function index()
    {
        if (!isset($this->permission['viewPurchase'])) {
            redirect('dashboard', 'refresh');
        }

        $this->data['purchase_stats'] = $this->model_purchases->getPurchaseStatistics();
        $this->render_template('purchases/index', $this->data);
    }

    public function fetchPurchasesData()
    {
        try {
            $result = array('data' => array());
            $searchTerm = $this->input->get('search_term');
            $data = $this->model_purchases->getPurchasesDataWithSearch($searchTerm);

            foreach ($data as $key => $value) {
                $buttons = '';

                // View button
                if (isset($this->permission['viewPurchase'])) {
                    $buttons .= '<a href="' . base_url('purchases/view/' . $value['id']) . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a> ';
                }

                // Receive button - Only for pending
                if (isset($this->permission['updatePurchase']) && $value['status'] == 'pending') {
                    $buttons .= '<button class="btn btn-sm btn-success" onclick="receivePurchase(' . $value['id'] . ')"><i class="fa fa-check"></i> Receive</button> ';
                }

                // Cancel button - Only for pending
                if (isset($this->permission['updatePurchase']) && $value['status'] == 'pending') {
                    $buttons .= '<button class="btn btn-sm btn-warning" onclick="cancelPurchase(' . $value['id'] . ')"><i class="fa fa-ban"></i></button> ';
                }

                // ✅ Delete button - TOUJOURS VISIBLE (pour tous les statuts)
                if (isset($this->permission['deletePurchase'])) {
                    $buttons .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeFunc(' . $value['id'] . ')"><i class="fa fa-trash"></i></button>';
                }

                // Status badges
                $status_badges = array(
                    'pending' => '<span class="label label-warning">Pending</span>',
                    'received' => '<span class="label label-success">Received</span>',
                    'cancelled' => '<span class="label label-danger">Cancelled</span>'
                );
                $status = isset($status_badges[$value['status']]) ? $status_badges[$value['status']] : $value['status'];

                $date = date('d-m-Y', strtotime($value['purchase_date']));
                $supplier_phone = !empty($value['supplier_phone']) ? $value['supplier_phone'] : '-';

                $result['data'][$key] = array(
                    $value['purchase_no'],
                    $value['supplier_name'],
                    $supplier_phone,
                    $date,
                    number_format($value['total_amount'], 2) . ' DZD',
                    $status,
                    $buttons
                );
            }

            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(200);
            echo json_encode(array(
                'error' => true,
                'message' => $e->getMessage(),
                'data' => array()
            ));
        }
    }






    public function create()
    {
        if (!isset($this->permission['createPurchase'])) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $supplier_id = $this->input->post('supplier_id');

            // Auto-create supplier if "new"
            if ($supplier_id === 'new') {
                $this->load->model('model_suppliers');

                $last = $this->db->select('id')->order_by('id', 'DESC')->limit(1)->get('suppliers')->row();
                $number = $last ? ($last->id + 1) : 1;
                $supplier_code = 'SUP-' . str_pad($number, 4, '0', STR_PAD_LEFT);

                $supplier_data = array(
                    'supplier_code' => $supplier_code,
                    'name' => $this->input->post('new_supplier_name'),
                    'contact_person' => $this->input->post('new_contact_person'),
                    'phone' => $this->input->post('new_phone'),
                    'email' => $this->input->post('new_email'),
                    'address' => $this->input->post('new_address'),
                    'payment_terms' => $this->input->post('new_payment_terms'),
                    'active' => 1
                );

                $this->db->insert('suppliers', $supplier_data);
                $supplier_id = $this->db->insert_id();

                if (!$supplier_id) {
                    $this->session->set_flashdata('error', 'Error creating supplier!');
                    redirect('purchases/create', 'refresh');
                    return;
                }
            }

            $products = $this->input->post('product');
            $quantities = $this->input->post('qty');
            $prices = $this->input->post('price');

            $items = array();
            $total = 0;

            for ($i = 0; $i < count($products); $i++) {
                if ($products[$i] && $quantities[$i] && $prices[$i]) {
                    $subtotal = $quantities[$i] * $prices[$i];
                    $product = $this->model_products->getProductData($products[$i]);

                    $stock_id = (isset($product['stock_id']) && !empty($product['stock_id']))
                        ? $product['stock_id']
                        : NULL;

                    $items[] = array(
                        'product_id' => $products[$i],
                        'quantity' => $quantities[$i],
                        'unit_price' => $prices[$i],
                        'stock_id' => $stock_id,
                    );

                    $total += $subtotal;
                }
            }

            // Payment data
            $paid_amount = $this->input->post('paid_amount') ? floatval($this->input->post('paid_amount')) : 0;
            $due_amount = $total - $paid_amount;

            if ($paid_amount == 0) {
                $payment_status = 'unpaid';
            } elseif ($paid_amount >= $total) {
                $payment_status = 'paid';
                $paid_amount = $total;
                $due_amount = 0;
            } else {
                $payment_status = 'partial';
            }

            $data = array(
                'supplier_id' => $supplier_id,
                'total_amount' => $total,
                'paid_amount' => $paid_amount,
                'due_amount' => $due_amount,
                'payment_status' => $payment_status,
                'payment_method' => $this->input->post('payment_method'),
                'expected_delivery_date' => $this->input->post('expected_delivery_date') ?: NULL,
                'notes' => $this->input->post('notes')
            );

            $purchase_id = $this->model_purchases->create($data, $items);

            if ($purchase_id) {
                // Record initial payment if any
                if ($paid_amount > 0) {
                    $payment_data = array(
                        'purchase_id' => $purchase_id,
                        'payment_date' => date('Y-m-d H:i:s'),
                        'amount_paid' => $paid_amount,
                        'payment_method' => $this->input->post('payment_method'),
                        'reference_number' => $this->input->post('reference_number'),
                        'notes' => 'Initial payment',
                        'created_by' => $this->session->userdata('id')
                    );
                    $this->db->insert('purchase_payments', $payment_data);
                }

                $this->session->set_flashdata('success', 'Purchase created successfully!');
                redirect('purchases/view/' . $purchase_id, 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error creating purchase!');
                redirect('purchases/create', 'refresh');
            }
        } else {
            $this->data['suppliers'] = $this->model_suppliers->getActiveSuppliers();
            $this->data['products'] = $this->model_products->getActiveProductData();

            $this->render_template('purchases/create', $this->data);
        }
    }

    public function view($id = null)
    {
        if (!isset($this->permission['viewPurchase'])) {
            redirect('dashboard', 'refresh');
        }

        if ($id) {
            $purchase = $this->model_purchases->getPurchaseData($id);

            if (!$purchase) {
                $this->session->set_flashdata('error', 'Purchase not found!');
                redirect('purchases', 'refresh');
            }

            $items = $this->model_purchases->getPurchaseItems($id);
            $payment_history = $this->model_purchases->getPaymentHistory($id);

            $this->data['purchase'] = $purchase;
            $this->data['items'] = $items;
            $this->data['payment_history'] = $payment_history;

            $this->render_template('purchases/view', $this->data);
        } else {
            redirect('purchases', 'refresh');
        }
    }


    public function receive($id)
    {
        if (!isset($this->permission['updatePurchase'])) {
            $this->session->set_flashdata('error', 'Permission denied');
            redirect('purchases', 'refresh');
        }

        if ($id) {
            $receive = $this->model_purchases->receivePurchase($id);

            if ($receive) {
                // ✅ AJOUTEZ: Recalculate average cost for all products in this purchase
                $items = $this->model_purchases->getPurchaseItems($id);

                foreach ($items as $item) {
                    // Recalculate average cost for each product
                    $this->recalculateProductAverageCost($item['product_id']);
                }

                $this->session->set_flashdata('success', 'Purchase received! Stock and prices updated.');
                redirect('purchases/view/' . $id, 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error receiving purchase');
                redirect('purchases/view/' . $id, 'refresh');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid purchase ID');
            redirect('purchases', 'refresh');
        }
    }

    /**
     * Recalculate average cost for a product based on purchase history
     * @param int $product_id
     * @return bool
     */
    private function recalculateProductAverageCost($product_id)
    {
        if (!$product_id) {
            return false;
        }

        // Calculate average cost and last purchase price from purchase_items
        $sql = "SELECT 
                AVG(pi.unit_price) as avg_cost, 
                MAX(pi.unit_price) as last_price,
                COUNT(*) as purchase_count
            FROM purchase_items pi
            JOIN purchases p ON pi.purchase_id = p.id
            WHERE pi.product_id = ? 
            AND p.status = 'received'";

        $query = $this->db->query($sql, array($product_id));
        $result = $query->row_array();

        if ($result && $result['purchase_count'] > 0) {
            $avg_cost = $result['avg_cost'] ? floatval($result['avg_cost']) : 0;
            $last_price = $result['last_price'] ? floatval($result['last_price']) : 0;

            // Update product
            $update_sql = "UPDATE products 
                       SET average_cost = ?, 
                           last_purchase_price = ?,
                           purchase_price_updated_at = NOW()
                       WHERE id = ?";

            $this->db->query($update_sql, array($avg_cost, $last_price, $product_id));

            return true;
        }

        return false;
    }


    public function cancel($id)
    {
        if (!isset($this->permission['updatePurchase'])) {
            $this->session->set_flashdata('error', 'Permission denied');
            redirect('purchases', 'refresh');
        }

        if ($id) {
            $cancel = $this->model_purchases->cancelPurchase($id);

            if ($cancel) {
                $this->session->set_flashdata('success', 'Purchase cancelled successfully');
                redirect('purchases/view/' . $id, 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error: Cannot cancel this purchase');
                redirect('purchases/view/' . $id, 'refresh');
            }
        } else {
            $this->session->set_flashdata('error', 'Invalid purchase ID');
            redirect('purchases', 'refresh');
        }
    }

    public function remove()
    {
        // ✅ ACTIVER LES ERREURS
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        header('Content-Type: application/json');

        if (!isset($this->permission['deletePurchase'])) {
            echo json_encode(array('success' => false, 'messages' => 'Permission denied'));
            return;
        }

        $purchase_id = $this->input->post('purchase_id');
        $force_delete = $this->input->post('force_delete');

        $response = array();

        if ($purchase_id) {
            try {
                // ✅ LOG pour debug
                log_message('debug', 'Attempting to delete purchase: ' . $purchase_id);
                log_message('debug', 'Force delete: ' . ($force_delete == 'yes' ? 'YES' : 'NO'));

                $result = $this->model_purchases->remove($purchase_id, ($force_delete == 'yes'));

                $response['success'] = $result['success'];
                $response['messages'] = $result['message'];

                if (!$result['success'] && isset($result['type'])) {
                    $response['type'] = $result['type'];
                }
            } catch (Exception $e) {
                log_message('error', 'Delete purchase error: ' . $e->getMessage());
                $response['success'] = false;
                $response['messages'] = 'Exception: ' . $e->getMessage();
            }
        } else {
            $response['success'] = false;
            $response['messages'] = 'Invalid request';
        }

        echo json_encode($response);
    }





    public function getProductPrice()
    {
        $product_id = $this->input->post('product_id');

        if ($product_id) {
            $product = $this->model_products->getProductData($product_id);

            if ($product) {
                $response = array(
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'qty' => $product['qty'],
                    'price_default' => $product['price_default'],
                    'last_purchase_price' => isset($product['last_purchase_price']) ? $product['last_purchase_price'] : 0,
                    'average_cost' => isset($product['average_cost']) ? $product['average_cost'] : 0,
                );

                echo json_encode($response);
            } else {
                echo json_encode(array('error' => 'Product not found'));
            }
        } else {
            echo json_encode(array('error' => 'No product ID provided'));
        }
    }
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
    public function addPayment()
    {
        if (!isset($this->permission['updatePurchase'])) {
            $this->session->set_flashdata('error', 'Permission denied!');
            redirect('purchases', 'refresh');
        }

        if ($this->input->post()) {
            $purchase_id = $this->input->post('purchase_id');
            $amount_paid = floatval($this->input->post('amount_paid'));
            $payment_date = $this->input->post('payment_date');
            $payment_method = $this->input->post('payment_method');
            $reference_number = $this->input->post('reference_number');
            $notes = $this->input->post('payment_notes');

            $user_id = $this->session->userdata('id');

            // Get current purchase data
            $purchase = $this->model_purchases->getPurchaseData($purchase_id);

            if (!$purchase) {
                $this->session->set_flashdata('error', 'Purchase not found!');
                redirect('purchases', 'refresh');
            }

            // Calculate new totals
            $current_paid = isset($purchase['paid_amount']) ? floatval($purchase['paid_amount']) : 0;
            $new_paid_amount = $current_paid + $amount_paid;
            $new_due_amount = $purchase['total_amount'] - $new_paid_amount;

            // Determine payment status
            if ($new_due_amount <= 0) {
                $payment_status = 'paid';
                $new_paid_amount = $purchase['total_amount'];
                $new_due_amount = 0;
            } elseif ($new_paid_amount > 0) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'unpaid';
            }

            // Insert payment record
            $payment_data = array(
                'purchase_id' => $purchase_id,
                'payment_date' => date('Y-m-d H:i:s', strtotime($payment_date)),
                'amount_paid' => $amount_paid,
                'payment_method' => $payment_method,
                'reference_number' => $reference_number,
                'notes' => $notes,
                'created_by' => $user_id
            );

            $this->db->insert('purchase_payments', $payment_data);

            // Update purchase totals
            $update_data = array(
                'paid_amount' => $new_paid_amount,
                'due_amount' => $new_due_amount,
                'payment_status' => $payment_status
            );

            $this->db->where('id', $purchase_id);
            $this->db->update('purchases', $update_data);

            $this->session->set_flashdata('success', 'Payment added successfully!');
            redirect('purchases/view/' . $purchase_id, 'refresh');
        }
    }

    public function invoice($id = null)
    {
        if (!isset($this->permission['viewPurchase'])) {
            redirect('dashboard', 'refresh');
        }

        if (!$id) {
            redirect('purchases', 'refresh');
        }

        $purchase_data = $this->model_purchases->getPurchaseData($id);
        $purchase_items = $this->model_purchases->getPurchaseItems($id);
        $payment_history = $this->model_purchases->getPaymentHistory($id);

        if (!$purchase_data) {
            show_404();
        }

        $this->load->model('model_company');
        $company = $this->model_company->getCompanyData(1);

        $data = array(
            'purchase_data' => $purchase_data,
            'purchase_items' => $purchase_items,
            'payment_history' => $payment_history,
            'company' => $company
        );

        // ✅ Load view WITHOUT template
        $this->load->view('purchases/invoice', $data);
    }
}
