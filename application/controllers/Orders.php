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
 */
class Orders extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Orders';
        $this->load->model('model_orders');
        $this->load->model('model_products');
        $this->load->model('model_company');
        $this->load->model('model_customers');
    }

    public function index()
    {
        if (!isset($this->permission['viewOrder'])) {
            redirect('dashboard', 'refresh');
        }

        $this->data['order_stats'] = $this->model_orders->getOrderStats();
        $this->render_template('orders/index', $this->data);
    }

    public function fetchOrdersData()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // ✅ DEBUG: Affiche les permissions
        echo "<h3>DEBUG PERMISSIONS:</h3>";
        echo "Permission isset: " . (isset($this->permission) ? 'YES' : 'NO') . "<br>";
        echo "Permission is_array: " . (is_array($this->permission) ? 'YES' : 'NO') . "<br>";
        echo "Permission count: " . count($this->permission) . "<br>";
        echo "viewOrder isset: " . (isset($this->permission['viewOrder']) ? 'YES' : 'NO') . "<br>";
        echo "viewOrder value: " . (isset($this->permission['viewOrder']) ? $this->permission['viewOrder'] : 'NULL') . "<br>";
        echo "<pre>" . print_r($this->permission, true) . "</pre>";

        // ✅ TEST DIRECT DE LA BASE
        $test = $this->db->query("SELECT permission FROM `groups` WHERE id = 1")->row_array();
        $test_perm = unserialize($test['permission']);
        echo "<h3>TEST DIRECT DB:</h3>";
        echo "viewOrder in DB: " . (isset($test_perm['viewOrder']) ? $test_perm['viewOrder'] : 'NOT FOUND') . "<br>";
        echo "<pre>" . print_r($test_perm, true) . "</pre>";
        die();

        try {
            $result = array('data' => array());
            $status = $this->input->get('status');
            $searchTerm = $this->input->get('search_term');

            // 🔴 LOG pour debug
            log_message('debug', 'Status: ' . $status);
            log_message('debug', 'Search term: ' . $searchTerm);

            if ($status && $status != 'all') {
                $status_map = array('paid' => 1, 'unpaid' => 2, 'partial' => 3);
                $data = $this->model_orders->getOrdersDataWithSearch($status_map[$status], $searchTerm);
            } else {
                $data = $this->model_orders->getOrdersDataWithSearch(null, $searchTerm);
            }

            // 🔴 Vérifie si $data existe
            if (!$data) {
                $data = array();
            }

            foreach ($data as $key => $value) {
                $count_total_item = $this->model_orders->countOrderItem($value['id']);

                // Gère les deux formats de date
                $timestamp = is_numeric($value['date_time']) ? $value['date_time'] : strtotime($value['date_time']);
                $date = date('d-m-Y', $timestamp);
                $time = date('h:i a', $timestamp);
                $date_time = $date . ' ' . $time;

                $buttons = '';
                if (isset($this->permission['viewOrder'])) {
                    $buttons .= '<button class="btn btn-info btn-sm" onclick="viewOrderDetails(' . $value['id'] . ')"><i class="fa fa-eye"></i></button> ';
                    $buttons .= '<a target="__blank" href="' . base_url('orders/invoice/' . $value['id']) . '" class="btn btn-default btn-sm"><i class="fa fa-print"></i></a> ';
                }
                if (isset($this->permission['updateOrder'])) {
                    $buttons .= '<a href="' . base_url('orders/update/' . $value['id']) . '" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a> ';
                }
                if (isset($this->permission['deleteOrder'])) {
                    $buttons .= '<button type="button" class="btn btn-danger btn-sm" onclick="removeFunc(' . $value['id'] . ')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
                }

                if ($value['paid_status'] == 1) {
                    $paid_status = '<span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span>';
                } elseif ($value['paid_status'] == 3) {
                    $due = number_format($value['due_amount'], 2);
                    $paid_status = '<span class="label label-warning"><i class="fa fa-clock-o"></i> Partial</span><br><small class="text-danger">Due: ' . $due . ' DZD</small>';
                } else {
                    $paid_status = '<span class="label label-danger"><i class="fa fa-times"></i> Unpaid</span>';
                }

                $result['data'][$key] = array(
                    $value['bill_no'],
                    $value['customer_name'],
                    $value['customer_phone'],
                    $date_time,
                    $count_total_item,
                    number_format($value['net_amount'], 2) . ' DZD',
                    number_format($value['paid_amount'], 2) . ' DZD',
                    '<span class="' . ($value['due_amount'] > 0 ? 'text-danger' : 'text-success') . '">' . number_format($value['due_amount'], 2) . ' DZD</span>',
                    $paid_status,
                    $buttons
                );
            }

            echo json_encode($result);
        } catch (Exception $e) {
            // 🔴 Retourne un JSON valide avec détails de l'erreur
            http_response_code(200); // Force 200 pour éviter l'alerte
            echo json_encode(array(
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => array()
            ));
        }
    }




    /**
     * Check for duplicate customers (AJAX)
     */
    public function checkDuplicateCustomer()
    {
        $customer_name = $this->input->post('customer_name');
        $customer_phone = $this->input->post('customer_phone');

        $response = array('exists' => false, 'suggestions' => array());

        if (empty($customer_name) && empty($customer_phone)) {
            echo json_encode($response);
            return;
        }

        // Check for exact or similar matches
        $duplicates = $this->model_customers->checkDuplicates($customer_name, $customer_phone);

        if (!empty($duplicates)) {
            $response['exists'] = true;
            $response['suggestions'] = $duplicates;
        }

        echo json_encode($response);
    }

    public function create()
    {
        if (!isset($this->permission['createOrder'])) {
            redirect('dashboard', 'refresh');
        }

        // VALIDATION
        $this->form_validation->set_rules('customer_id', 'Customer', 'trim|required');
        $this->form_validation->set_rules('product[]', 'Product', 'required');

        // Conditional validation for new customer
        if ($this->input->post('customer_id') === 'new') {
            $this->form_validation->set_rules('new_customer_name', 'Customer Name', 'trim|required');
            $this->form_validation->set_rules('new_customer_phone', 'Customer Phone', 'trim|required');
            $this->form_validation->set_rules('new_customer_type', 'Customer Type', 'trim|required');
        }

        if ($this->form_validation->run() == TRUE) {

            $customer_id = $this->input->post('customer_id');

            // AUTO-CREATE NEW CUSTOMER IF SELECTED
            if ($customer_id === 'new') {
                $this->load->model('model_customers');

                $customer_data = array(
                    'customer_code' => $this->model_customers->generateCustomerCode(),
                    'customer_name' => $this->input->post('new_customer_name'),
                    'phone' => $this->input->post('new_customer_phone'),
                    'address' => $this->input->post('new_customer_address'),
                    'email' => $this->input->post('new_customer_email'),
                    'customer_type' => $this->input->post('new_customer_type'),
                    'active' => 1
                );

                $customer_id = $this->model_customers->create($customer_data);

                if (!$customer_id) {
                    $this->session->set_flashdata('error', 'Error creating customer!');
                    redirect('orders/create', 'refresh');
                    return;
                }
            }

            // Get customer data
            $customer = $this->model_customers->getCustomerData($customer_id);

            if (!$customer) {
                $this->session->set_flashdata('error', 'Customer not found!');
                redirect('orders/create', 'refresh');
                return;
            }

            // Get price type (can be overridden)
            $price_type_override = $this->input->post('customer_type_override');
            $original_type = $customer['customer_type'];
            $override_reason = $this->input->post('override_reason');

            // Prepare products and items
            $products = $this->input->post('product');
            $quantities = $this->input->post('qty');
            $rates = $this->input->post('rate_value');
            $amounts = $this->input->post('amount_value');

            // Validate stock BEFORE creating order
            $this->load->model('model_products');
            for ($i = 0; $i < count($products); $i++) {
                if ($products[$i] && $quantities[$i]) {
                    $product_data = $this->model_products->getProductData($products[$i]);
                    if ($product_data['qty'] < $quantities[$i]) {
                        $this->session->set_flashdata('error', 'Insufficient stock for product: ' . $product_data['name']);
                        redirect('orders/create', 'refresh');
                        return;
                    }
                }
            }

            // Calculate totals
            $gross_amount = floatval($this->input->post('gross_amount_value'));
            $discount = $this->input->post('discount') ? floatval($this->input->post('discount')) : 0;
            $net_amount = floatval($this->input->post('net_amount_value'));
            $paid_amount = $this->input->post('paid_amount') ? floatval($this->input->post('paid_amount')) : 0;
            $due_amount = $net_amount - $paid_amount;

            // Payment status
            if ($paid_amount == 0) {
                $paid_status = 2; // Unpaid
            } elseif ($paid_amount >= $net_amount) {
                $paid_status = 1; // Fully paid
            } else {
                $paid_status = 3; // Partial
            }

            $user_id = $this->get_tenant_user_id();
            $bill_no = 'BILPR-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

            // Prepare order data for Model
            $data = array(
                'bill_no' => $bill_no,
                'customer_id' => $customer_id,
                'customer_type' => $original_type,
                'price_type_override' => ($price_type_override && $price_type_override != $original_type) ? $price_type_override : '',
                'override_reason' => ($price_type_override && $price_type_override != $original_type) ? $override_reason : '',
                'customer_name' => $customer['customer_name'],
                'customer_address' => $customer['address'],
                'customer_phone' => $customer['phone'],
                'date_time' => date('Y-m-d H:i:s'),
                'gross_amount' => $gross_amount,
                'service_charge_rate' => 0,
                'service_charge' => 0,
                'vat_charge_rate' => 0,
                'vat_charge' => 0,
                'net_amount' => $net_amount,
                'paid_amount' => $paid_amount,
                'due_amount' => $due_amount,
                'discount' => $discount,
                'paid_status' => $paid_status,
                'payment_method' => $this->input->post('payment_method'),
                'payment_notes' => $this->input->post('payment_notes'),
                'user_id' => $user_id
            );

            // Insert order
            $this->db->insert('orders', $data);
            $order_id = $this->db->insert_id();

            if ($order_id) {
                // Insert order items and update stock
                for ($i = 0; $i < count($products); $i++) {
                    if ($products[$i] && $quantities[$i]) {

                        $product_id = $products[$i];
                        $qty = $quantities[$i];
                        $rate = $rates[$i];
                        $amount = $amounts[$i];

                        // Insert item
                        $item_data = array(
                            'order_id' => $order_id,
                            'product_id' => $product_id,
                            'qty' => $qty,
                            'rate' => $rate,
                            'amount' => $amount
                        );
                        $this->db->insert('orders_item', $item_data);

                        // Update product stock
                        $product_data = $this->model_products->getProductData($product_id);
                        $qty_before = $product_data['qty'];
                        $qty_after = $qty_before - $qty;

                        $update_product = array('qty' => $qty_after);
                        $this->model_products->update($update_product, $product_id);

                        // Record stock history if table exists
                        if ($this->db->table_exists('stock_history')) {
                            $stock_data = array(
                                'product_id' => $product_id,
                                'order_id' => $order_id,
                                'movement_type' => 'sale',
                                'quantity' => $qty,
                                'quantity_before' => $qty_before,
                                'quantity_after' => $qty_after,
                                'user_id' => $user_id
                            );
                            $this->db->insert('stock_history', $stock_data);
                        }
                    }
                }

                // Record initial payment if any
                if ($paid_amount > 0) {
                    $payment_data = array(
                        'order_id' => $order_id,
                        'installment_number' => 1,
                        'payment_date' => date('Y-m-d H:i:s'),
                        'payment_amount' => $paid_amount,
                        'payment_method' => $this->input->post('payment_method'),
                        'reference_number' => NULL,
                        'notes' => $this->input->post('payment_notes') ?: 'Initial payment',
                        'remaining_balance' => $due_amount,
                        'received_by' => $user_id
                    );
                    $this->db->insert('order_payments', $payment_data);
                }

                // Update customer balance
                if ($customer_id) {
                    $this->load->model('model_customers');
                    $this->model_customers->updateBalance($customer_id, $due_amount);
                }

                // APRÈS:
                $this->session->set_flashdata('success', 'Order created successfully! Order #' . $bill_no);
                redirect('orders', 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error creating order!');
                redirect('orders/create', 'refresh');
            }
        } else {
            // Load form - validation failed or first load
            $customers = $this->model_customers->getActiveCustomers();
            $this->data['customers'] = $customers;

            $products = $this->model_products->getActiveProductData();
            $this->data['products'] = $products;

            $company_data = $this->model_company->getCompanyData(1);
            $this->data['company_data'] = $company_data;

            $this->data['is_vat_enabled'] = false;
            $this->data['is_service_enabled'] = false;

            $this->render_template('orders/create', $this->data);
        }
    }



    public function update($id)
    {
        if (!isset($this->permission['updateOrder'])) {
            redirect('dashboard', 'refresh');
        }
        if (!$id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {

            $user_id = $this->get_tenant_user_id();
            $update = $this->model_orders->update($id, $user_id);
            if ($update == true) {
                $this->session->set_flashdata('success', 'Successfully updated');
                redirect('orders/update/' . $id, 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error occurred!!');
                redirect('orders/update/' . $id, 'refresh');
            }
        } else {
            $customers = $this->model_customers->getActiveCustomers();
            $this->data['customers'] = $customers;
            $products = $this->model_products->getActiveProductData();
            $this->data['products'] = $products;
            $company_data = $this->model_company->getCompanyData(1);
            $this->data['company_data'] = $company_data;
            $this->data['is_vat_enabled'] = false;
            $this->data['is_service_enabled'] = false;

            $result = array();
            $orders_data = $this->model_orders->getOrdersData($id);
            $result['order'] = $orders_data;
            $orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);
            foreach ($orders_item as $k => $v) {
                $result['order_item'][] = $v;
            }
            $this->data['order_data'] = $result;
            $this->render_template('orders/edit', $this->data);
        }
    }

    public function remove()
    {
        if (!isset($this->permission['deleteOrder'])) {
            redirect('dashboard', 'refresh');
        }
        $order_id = $this->input->post('order_id');
        $response = array();
        if ($order_id) {
            $user_id = $this->get_tenant_user_id();
            $delete = $this->model_orders->remove($order_id, $user_id);
            if ($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed. Stock has been restored.";
            } else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the order information";
            }
        } else {
            $response['success'] = false;
            $response['messages'] = "Refresh the page again!!";
        }
        echo json_encode($response);
    }

    // 🔴 FONCTION AJAX AMÉLIORÉE - Gère le type de client + prix manuel
    public function getProductValueById()
    {
        $product_id = $this->input->post('product_id');
        $customer_type = $this->input->post('customer_type');

        if ($product_id) {
            // Si customer_type est fourni, utilise-le (peut être manuel ou type client)
            if ($customer_type) {
                $product_data = $this->model_products->getProductDataWithPrice($product_id, $customer_type);
            } else {
                // Par défaut: prix retail
                $product_data = $this->model_products->getProductDataWithPrice($product_id, 'retail');
            }
            echo json_encode($product_data);
        }
    }

    public function getTableProductRow()
    {
        $customer_type = $this->input->post('customer_type');
        if ($customer_type) {
            $products = $this->model_products->getProductsWithPricing($customer_type);
        } else {
            $products = $this->model_products->getProductsWithPricing('retail');
        }
        echo json_encode($products);
    }

    public function getOrderDetails($id)
    {
        $order = $this->model_orders->getOrdersData($id);
        $items = $this->model_orders->getOrdersItemData($id);
        $payments = $this->model_orders->getOrderPayments($id);

        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<h4><i class="fa fa-file-text"></i> Order Information</h4>';
        echo '<table class="table table-condensed">';
        echo '<tr><th width="40%">Bill No:</th><td><strong>' . $order['bill_no'] . '</strong></td></tr>';
        echo '<tr><th>Customer:</th><td>' . $order['customer_name'] . '</td></tr>';
        echo '<tr><th>Phone:</th><td>' . $order['customer_phone'] . '</td></tr>';
        echo '<tr><th>Address:</th><td>' . $order['customer_address'] . '</td></tr>';
        $timestamp = is_numeric($order['date_time']) ? $order['date_time'] : strtotime($order['date_time']);
        $formatted_date = date('d-m-Y h:i a', $timestamp);
        echo '<tr><th>Date:</th><td>' . date('d-m-Y h:i a', $timestamp) . '</td></tr>';


        // 🔴 AFFICHER PRIX MANUEL SI OVERRIDE
        if (!empty($order['price_type_override']) && $order['price_type_override'] != $order['customer_type']) {
            $type_labels = array(
                'super_wholesale' => 'Super Gros',
                'wholesale' => 'Gros',
                'retail' => 'Détail'
            );
            echo '<tr><th>Price Override:</th><td><span class="label label-warning">' . ($type_labels[$order['price_type_override']] ?? $order['price_type_override']) . '</span><br><small class="text-muted">' . $order['override_reason'] . '</small></td></tr>';
        }

        echo '</table>';
        echo '</div>';

        echo '<div class="col-md-6">';
        echo '<h4><i class="fa fa-money"></i> Payment Summary</h4>';
        echo '<table class="table table-condensed">';
        echo '<tr><th width="40%">Total Amount:</th><td><strong>' . number_format($order['net_amount'], 2) . ' DZD</strong></td></tr>';
        echo '<tr><th>Paid Amount:</th><td class="text-success"><strong>' . number_format($order['paid_amount'], 2) . ' DZD</strong></td></tr>';
        echo '<tr><th>Due Amount:</th><td class="text-danger"><strong>' . number_format($order['due_amount'], 2) . ' DZD</strong></td></tr>';

        if ($order['paid_status'] == 1) {
            echo '<tr><th>Status:</th><td><span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span></td></tr>';
        } elseif ($order['paid_status'] == 3) {
            echo '<tr><th>Status:</th><td><span class="label label-warning"><i class="fa fa-clock-o"></i> Partially Paid</span></td></tr>';
        } else {
            echo '<tr><th>Status:</th><td><span class="label label-danger"><i class="fa fa-times"></i> Unpaid</span></td></tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';

        echo '<hr>';

        echo '<h4><i class="fa fa-shopping-cart"></i> Order Items</h4>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead><tr style="background: #f8f9fa;"><th>Product</th><th width="15%" class="text-center">Quantity</th><th width="20%" class="text-right">Rate</th><th width="20%" class="text-right">Amount</th></tr></thead>';
        echo '<tbody>';
        foreach ($items as $item) {
            $product = $this->model_products->getProductData($item['product_id']);
            echo '<tr>';
            echo '<td>' . $product['name'] . '</td>';
            echo '<td class="text-center">' . $item['qty'] . '</td>';
            echo '<td class="text-right">' . number_format($item['rate'], 2) . ' DZD</td>';
            echo '<td class="text-right"><strong>' . number_format($item['amount'], 2) . ' DZD</strong></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo '<hr>';

        echo '<h4><i class="fa fa-history"></i> Payment Installments History</h4>';

        if (!empty($payments)) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover">';
            echo '<thead>';
            echo '<tr style="background: #3c8dbc; color: white;">';
            echo '<th width="10%" class="text-center">Install. #</th>';
            echo '<th width="20%">Date & Time</th>';
            echo '<th width="15%" class="text-right">Amount Paid</th>';
            echo '<th width="15%" class="text-right">Balance After</th>';
            echo '<th width="15%">Payment Method</th>';
            echo '<th width="25%">Notes</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($payments as $payment) {
                echo '<tr>';
                echo '<td class="text-center">';
                echo '<span class="label label-primary" style="font-size: 14px;">#' . $payment['installment_number'] . '</span>';
                if ($payment['remaining_balance'] == 0) {
                    echo '<br><small class="text-success"><i class="fa fa-check-circle"></i> Final</small>';
                }
                echo '</td>';
                echo '<td><i class="fa fa-calendar"></i> ' . date('d-m-Y', $payment['payment_date']) . '<br><i class="fa fa-clock-o"></i> ' . date('h:i A', $payment['payment_date']) . '</td>';
                echo '<td class="text-right"><strong style="color: #00a65a; font-size: 16px;">' . number_format($payment['payment_amount'], 2) . ' DZD</strong></td>';

                if ($payment['remaining_balance'] > 0) {
                    echo '<td class="text-right"><span class="text-danger"><strong>' . number_format($payment['remaining_balance'], 2) . ' DZD</strong></span></td>';
                } else {
                    echo '<td class="text-right"><span class="text-success"><i class="fa fa-check"></i> <strong>Paid Off</strong></span></td>';
                }

                $method_badge_colors = array(
                    'cash' => 'success',
                    'bank_transfer' => 'info',
                    'cheque' => 'warning',
                    'credit_card' => 'primary',
                    'mobile_payment' => 'info'
                );
                $badge_color = isset($method_badge_colors[$payment['payment_method']]) ? $method_badge_colors[$payment['payment_method']] : 'default';
                echo '<td><span class="label label-' . $badge_color . '">' . ucfirst(str_replace('_', ' ', $payment['payment_method'])) . '</span></td>';

                echo '<td>' . (!empty($payment['payment_notes']) ? $payment['payment_notes'] : '<em class="text-muted">No notes</em>') . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            $progress_percentage = ($order['paid_amount'] / $order['net_amount']) * 100;
            echo '<div style="margin-top: 20px;">';
            echo '<h5><i class="fa fa-bar-chart"></i> Payment Progress</h5>';
            echo '<div class="progress" style="height: 30px; margin-bottom: 10px;">';
            echo '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: ' . $progress_percentage . '%; line-height: 30px; font-size: 14px;">';
            echo number_format($progress_percentage, 1) . '% Paid';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-sm-4 text-center">';
            echo '<div style="padding: 10px; background: #d4edda; border-radius: 5px;">';
            echo '<small>Total Paid</small><br><strong style="color: #155724; font-size: 18px;">' . number_format($order['paid_amount'], 2) . ' DZD</strong>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-sm-4 text-center">';
            echo '<div style="padding: 10px; background: #f8d7da; border-radius: 5px;">';
            echo '<small>Remaining</small><br><strong style="color: #721c24; font-size: 18px;">' . number_format($order['due_amount'], 2) . ' DZD</strong>';
            echo '</div>';
            echo '</div>';
            echo '<div class="col-sm-4 text-center">';
            echo '<div style="padding: 10px; background: #d1ecf1; border-radius: 5px;">';
            echo '<small>Installments</small><br><strong style="color: #0c5460; font-size: 18px;">' . count($payments) . '</strong>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">';
            echo '<i class="fa fa-exclamation-triangle"></i> <strong>No payments recorded yet.</strong>';
            echo '<br>This order has not received any payments.';
            echo '</div>';
        }

        if ($order['due_amount'] > 0 && isset($this->permission['updateOrder'])) {
            echo '<div class="text-center" style="margin-top: 20px;">';
            echo '<button type="button" class="btn btn-success btn-lg" onclick="openAddPaymentModal(' . $order['id'] . ', ' . $order['due_amount'] . ')">';
            echo '<i class="fa fa-plus-circle"></i> Add Payment Installment';
            echo '</button>';
            echo '</div>';
        }
    }

    public function addPayment()
    {
        if (!isset($this->permission['updateOrder'])) {
            echo json_encode(array('success' => false, 'message' => 'Permission denied'));
            return;
        }

        $order_id = $this->input->post('order_id');
        $payment_amount = $this->input->post('payment_amount');
        $payment_method = $this->input->post('payment_method');
        $payment_notes = $this->input->post('payment_notes');

        $response = array();

        if (!$order_id || !$payment_amount || $payment_amount <= 0) {
            $response['success'] = false;
            $response['message'] = 'Invalid payment amount';
            echo json_encode($response);
            return;
        }

        $user_id = $this->get_tenant_user_id();
        $result = $this->model_orders->addPaymentInstallment($order_id, $payment_amount, $payment_method, $payment_notes, $user_id);
        if ($result['success']) {
            $response['success'] = true;
            $response['message'] = 'Payment recorded successfully!';
            $response['new_due_amount'] = $result['new_due_amount'];
            $response['paid_status'] = $result['paid_status'];
        } else {
            $response['success'] = false;
            $response['message'] = $result['message'];
        }

        echo json_encode($response);
    }

    /**
     * Invoice with Payment Installments
     * Complete version with all payment details
     */
    /**
     * Professional Black & White Invoice Design
     */
    public function invoice($id)
    {
        if (!isset($this->permission['viewOrder'])) {
            redirect('dashboard', 'refresh');
        }

        if ($id) {
            $order_data = $this->model_orders->getOrdersData($id);
            $orders_items = $this->model_orders->getOrdersItemData($id);
            $company_info = $this->model_company->getCompanyData(1);
            $payments = $this->model_orders->getOrderPayments($id);

            // Convert order date
            if (is_numeric($order_data['date_time'])) {
                $order_timestamp = $order_data['date_time'];
            } else {
                $datetime_obj = DateTime::createFromFormat('Y-m-d H:i:s', $order_data['date_time']);
                if ($datetime_obj) {
                    $order_timestamp = $datetime_obj->getTimestamp();
                } else {
                    $order_timestamp = strtotime($order_data['date_time']);
                    if ($order_timestamp === false || $order_timestamp === 0) {
                        $order_timestamp = time();
                    }
                }
            }

            $order_date = date('d/m/Y', $order_timestamp);
            $order_time = date('h:i A', $order_timestamp);

            // Payment status
            if ($order_data['paid_status'] == 1) {
                $paid_status = 'PAID';
                $status_class = 'status-received';
            } elseif ($order_data['paid_status'] == 3) {
                $paid_status = 'PARTIALLY PAID';
                $status_class = 'status-pending';
            } else {
                $paid_status = 'UNPAID';
                $status_class = 'status-cancelled';
            }

            // ========================================
            // NOUVEAU DESIGN HTML (COMME PURCHASE)
            // ========================================
            $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - ' . $order_data['bill_no'] . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: \'Segoe UI\', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #2c3e50;
            background: #fff;
            padding: 20mm;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 4px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-section {
            flex: 1;
        }
        
        .company-name {
            font-size: 26pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .company-details {
            font-size: 10pt;
            color: #555;
            line-height: 1.5;
        }
        
        .invoice-section {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 32pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 13pt;
            font-weight: bold;
            color: #e74c3c;
            background: #fff3cd;
            padding: 5px 15px;
            display: inline-block;
            border-radius: 4px;
        }
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 4px;
        }
        
        .info-box.customer {
            border-left-color: #9b59b6;
        }
        
        .info-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .info-row {
            display: flex;
            padding: 5px 0;
            font-size: 10pt;
        }
        
        .info-label {
            font-weight: 600;
            width: 140px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
            color: #2c3e50;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-received {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Table */
        .items-section {
            margin: 30px 0;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        thead {
            background: #34495e;
            color: white;
        }
        
        th {
            padding: 12px 10px;
            text-align: left;
            font-size: 10pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        tbody tr:last-child {
            border-bottom: 3px solid #34495e;
        }
        
        td {
            padding: 12px 10px;
            font-size: 10pt;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        /* Payment Summary Box */
        .payment-summary {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #27ae60;
            margin-top: 30px;
            border-radius: 4px;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11pt;
        }
        
        .payment-row.total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            margin: 10px -10px;
            border-radius: 8px;
            font-size: 14pt;
            font-weight: bold;
        }
        
        .payment-row.paid {
            color: #27ae60;
            font-weight: 600;
        }
        
        .payment-row.due {
            color: #e74c3c;
            font-weight: 600;
        }
        
        /* Payment History */
        .payment-history {
            margin-top: 30px;
        }
        
        .history-title {
            font-size: 12pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #27ae60;
        }
        
        /* Print Button */
        .print-button {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn-print {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 15px 50px;
            font-size: 14pt;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Print Styles */
        @media print {
            @page {
                margin: 10mm;
                size: A4;
            }
            
            body {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .print-button {
                display: none !important;
            }
            
            .container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }
            
            html, body {
                height: 100%;
                overflow: visible;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <div class="company-section">
                <div class="company-name">' . htmlspecialchars($company_info['company_name']) . '</div>
                <div class="company-details">
                    ' . nl2br(htmlspecialchars($company_info['address'])) . '<br>
                    <strong>Phone:</strong> ' . htmlspecialchars($company_info['phone']) . '
                </div>
            </div>
            
            <div class="invoice-section">
                <div class="invoice-title">SALES INVOICE</div>
                <div class="invoice-number">' . htmlspecialchars($order_data['bill_no']) . '</div>
            </div>
        </div>
        
        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Customer Info -->
            <div class="info-box customer">
                <div class="info-title">👤 Customer Information</div>
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value"><strong>' . htmlspecialchars($order_data['customer_name']) . '</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">' . htmlspecialchars($order_data['customer_phone']) . '</div>
                </div>';

            if (!empty($order_data['customer_address'])) {
                $html .= '<div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">' . htmlspecialchars($order_data['customer_address']) . '</div>
                </div>';
            }

            $html .= '</div>
            
            <!-- Order Details -->
            <div class="info-box">
                <div class="info-title">📋 Order Details</div>
                <div class="info-row">
                    <div class="info-label">Date:</div>
                    <div class="info-value"><strong>' . $order_date . ' ' . $order_time . '</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Payment Status:</div>
                    <div class="info-value">
                        <span class="status-badge ' . $status_class . '">' . $paid_status . '</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Section -->
        <div class="items-section">
            <div class="section-title">🛒 Order Items</div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Product</th>
                        <th style="width: 12%;" class="text-center">Quantity</th>
                        <th style="width: 19%;" class="text-right">Unit Price</th>
                        <th style="width: 19%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';

            $no = 1;
            foreach ($orders_items as $item) {
                $product_data = $this->model_products->getProductData($item['product_id']);
                $html .= '<tr>
                        <td class="text-center text-bold">' . $no++ . '</td>
                        <td class="text-bold">' . htmlspecialchars($product_data['name']) . '</td>
                        <td class="text-center"><strong>' . $item['qty'] . '</strong></td>
                        <td class="text-right">' . number_format($item['rate'], 2) . ' DZD</td>
                        <td class="text-right text-bold">' . number_format($item['amount'], 2) . ' DZD</td>
                    </tr>';
            }

            $html .= '</tbody>
            </table>
        </div>
        
        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="payment-row">
                <span>Gross Amount:</span>
                <span>' . number_format($order_data['gross_amount'], 2) . ' DZD</span>
            </div>';

            if ($order_data['discount'] > 0) {
                $html .= '<div class="payment-row">
                    <span>Discount:</span>
                    <span>- ' . number_format($order_data['discount'], 2) . ' DZD</span>
                </div>';
            }

            $html .= '<div class="payment-row total">
                <span>TOTAL AMOUNT</span>
                <span>' . number_format($order_data['net_amount'], 2) . ' DZD</span>
            </div>
            
            <div class="payment-row paid">
                <span>Amount Paid:</span>
                <span><strong>' . number_format($order_data['paid_amount'], 2) . ' DZD</strong></span>
            </div>';

            if ($order_data['due_amount'] > 0) {
                $html .= '<div class="payment-row due">
                    <span>Amount Due:</span>
                    <span><strong>' . number_format($order_data['due_amount'], 2) . ' DZD</strong></span>
                </div>';
            }

            $html .= '</div>';

            // Payment History
            if (!empty($payments) && count($payments) > 0) {
                $html .= '<div class="payment-history">
                <div class="history-title">💳 Payment History (' . count($payments) . ' installments)</div>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th style="width: 22%;">Date & Time</th>
                            <th style="width: 18%;" class="text-right">Amount</th>
                            <th style="width: 18%;" class="text-right">Balance</th>
                            <th style="width: 17%;">Method</th>
                            <th style="width: 17%;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($payments as $payment) {
                    $is_final = ($payment['remaining_balance'] == 0);

                    // Convert payment date
                    if (is_numeric($payment['payment_date'])) {
                        $payment_timestamp = $payment['payment_date'];
                    } else {
                        $datetime_obj = DateTime::createFromFormat('Y-m-d H:i:s', $payment['payment_date']);
                        if ($datetime_obj) {
                            $payment_timestamp = $datetime_obj->getTimestamp();
                        } else {
                            $payment_timestamp = strtotime($payment['payment_date']);
                            if ($payment_timestamp === false) {
                                $payment_timestamp = time();
                            }
                        }
                    }

                    $row_style = $is_final ? 'background: #d4edda;' : '';

                    $html .= '<tr style="' . $row_style . '">
                            <td class="text-center text-bold">#' . $payment['installment_number'] . '</td>
                            <td>' . date('d/m/Y H:i', $payment_timestamp) . '</td>
                            <td class="text-right text-bold" style="color: #27ae60;">' . number_format($payment['payment_amount'], 2) . ' DZD</td>
                            <td class="text-right">';

                    if ($is_final) {
                        $html .= '<strong style="color: #27ae60;">PAID ✓</strong>';
                    } else {
                        $html .= number_format($payment['remaining_balance'], 2) . ' DZD';
                    }

                    $method_display = ucfirst(str_replace('_', ' ', $payment['payment_method']));
                    $notes_display = !empty($payment['notes']) ? htmlspecialchars($payment['notes']) : '-';

                    $html .= '</td>
                            <td>' . $method_display . '</td>
                            <td style="font-size: 9pt;">' . $notes_display . '</td>
                        </tr>';
                }

                $html .= '</tbody>
                </table>
            </div>';
            }

            $html .= '
        <!-- Print Button -->
        <div class="print-button">
            <button class="btn-print" onclick="printInvoice()">
                🖨️ PRINT
            </button>
        </div>
        
    </div>

    <script>
    function printInvoice() {
        document.querySelector(\'.print-button\').style.display = \'none\';
        window.print();
        setTimeout(function() {
            document.querySelector(\'.print-button\').style.display = \'block\';
        }, 100);
    }
    </script>
</body>
</html>';

            echo $html;
        }
    }



    /**
     * Generate PDF Invoice with Payment Installments
     */
    public function printInvoice($order_id)
    {
        // 🔴 DÉSACTIVER LES ERREURS POUR LE PDF
        error_reporting(0);
        ini_set('display_errors', 0);
        ob_clean(); // Nettoyer le buffer de sortie

        if (!isset($this->permission['viewOrder'])) {
            redirect('dashboard', 'refresh');
        }
        if (!isset($this->permission['viewOrder'])) {
            redirect('dashboard', 'refresh');
        }

        // Load PDF library
        $this->load->library('Pdf');

        // Get order data
        $order = $this->model_orders->getOrdersData($order_id);
        $order_items = $this->model_orders->getOrdersItemData($order_id);
        $payments = $this->model_orders->getOrderPayments($order_id);
        $company = $this->model_company->getCompanyData(1);

        if (!$order) {
            show_404();
        }

        // Get product names for items
        $this->load->model('model_products');
        foreach ($order_items as &$item) {
            $product = $this->model_products->getProductData($item['product_id']);
            $item['name'] = $product['name'];
        }

        // Create PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Inventory System');
        $pdf->SetAuthor($company['company_name']);
        $pdf->SetTitle('Invoice - ' . $order['bill_no']);
        $pdf->SetSubject('Invoice');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Build HTML content
        $html = $this->generateInvoiceHTML($order, $order_items, $payments, $company);

        // Write HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $filename = 'Invoice_' . $order['bill_no'] . '_' . date('Ymd') . '.pdf';
        $pdf->Output($filename, 'D'); // D = Download
    }
    public function searchCustomer()
    {
        $customer_name = $this->input->post('customer_name');
        $customer_phone = $this->input->post('customer_phone');

        $response = array('found' => false);

        if ($customer_name || $customer_phone) {
            $customer = $this->model_customers->findCustomer($customer_name, $customer_phone);

            if ($customer) {
                $response['found'] = true;
                $response['customer'] = array(
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'address' => $customer['address'],
                    'customer_type' => $customer['customer_type'],
                    'customer_type_label' => $customer['customer_type'] == 'retail' ? 'Détail' : ($customer['customer_type'] == 'wholesale' ? 'Gros' : 'Super Gros')
                );
            }
        }

        echo json_encode($response);
    }

    /**
     * Generate Invoice HTML Content
     */
    private function generateInvoiceHTML($order, $order_items, $payments, $company)
    {
        $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            @media print {
                @page { margin: 0.75cm; size: A4; }
                .no-print { display: none !important; }
                body { background: white; }
            }
            
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-size: 11px;
                line-height: 1.5;
                color: #1a1a1a;
                background: white;
                padding: 20px;
            }
            
            .invoice-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
            }
            
            /* Top Bar */
            .top-bar {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 8px;
                margin-bottom: 30px;
            }
            
            /* Header */
            .invoice-header {
                display: table;
                width: 100%;
                margin-bottom: 35px;
            }
            
            .company-info {
                display: table-cell;
                width: 50%;
                vertical-align: top;
            }
            
            .company-info h1 {
                font-size: 24px;
                font-weight: 700;
                color: #1a1a1a;
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }
            
            .company-info p {
                color: #666;
                font-size: 10px;
                line-height: 1.6;
                margin: 1px 0;
            }
            
            .invoice-meta {
                display: table-cell;
                width: 50%;
                vertical-align: top;
                text-align: right;
            }
            
            .invoice-meta h2 {
                font-size: 28px;
                font-weight: 700;
                color: #667eea;
                margin-bottom: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .invoice-meta table {
                float: right;
                margin-top: 10px;
            }
            
            .invoice-meta td {
                padding: 4px 0;
                font-size: 10px;
            }
            
            .invoice-meta td:first-child {
                text-align: right;
                padding-right: 12px;
                color: #666;
                font-weight: 500;
            }
            
            .invoice-meta td:last-child {
                font-weight: 700;
                color: #1a1a1a;
            }
            
            /* Info Boxes */
            .info-grid {
                display: table;
                width: 100%;
                margin-bottom: 30px;
            }
            
            .info-box {
                display: table-cell;
                width: 48%;
                padding: 20px;
                background: #f8f9fc;
                border-left: 3px solid #667eea;
            }
            
            .info-box:last-child {
                padding-left: 25px;
            }
            
            .info-box h3 {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                color: #667eea;
                margin-bottom: 12px;
                letter-spacing: 1px;
            }
            
            .info-box p {
                font-size: 10px;
                line-height: 1.7;
                color: #333;
                margin: 3px 0;
            }
            
            .info-box strong {
                color: #1a1a1a;
            }
            
            /* Status Badges */
            .status-badge {
                display: inline-block;
                padding: 5px 12px;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-radius: 12px;
            }
            
            .status-paid { background: #e8f5e9; color: #27ae60; }
            .status-partial { background: #fff4e6; color: #e67e22; }
            .status-unpaid { background: #ffebee; color: #e74c3c; }
            
            /* Items Table */
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin: 25px 0;
            }
            
            .items-table thead {
                background: #f8f9fc;
                border-top: 2px solid #667eea;
                border-bottom: 2px solid #667eea;
            }
            
            .items-table th {
                padding: 12px 10px;
                text-align: left;
                font-weight: 700;
                font-size: 9px;
                text-transform: uppercase;
                color: #667eea;
                letter-spacing: 0.8px;
            }
            
            .items-table th.text-right { text-align: right; }
            .items-table th.text-center { text-align: center; }
            
            .items-table tbody tr {
                border-bottom: 1px solid #e8e8e8;
            }
            
            .items-table td {
                padding: 14px 10px;
                font-size: 10px;
                color: #333;
            }
            
            .items-table td.text-right { text-align: right; }
            .items-table td.text-center { text-align: center; }
            
            .items-table tbody tr:nth-child(even) {
                background: #fafbfc;
            }
            
            .items-table .product-name {
                font-weight: 600;
                color: #1a1a1a;
            }
            
            .items-table .total-row {
                border-top: 2px solid #667eea;
                background: #f8f9fc;
            }
            
            .items-table .total-row td {
                padding: 14px 10px;
                font-weight: 700;
                font-size: 12px;
                color: #1a1a1a;
            }
            
            .items-table .grand-total-row {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .items-table .grand-total-row td {
                padding: 16px 10px;
                font-size: 14px;
                font-weight: 700;
                color: white;
            }
            
            /* Summary */
            .summary-section {
                float: right;
                width: 360px;
                margin: 25px 0;
            }
            
            .summary-box {
                border: 2px solid #e8e8e8;
                border-radius: 8px;
                overflow: hidden;
            }
            
            .summary-box table {
                width: 100%;
            }
            
            .summary-box tr {
                border-bottom: 1px solid #f0f0f0;
            }
            
            .summary-box tr:last-child {
                border-bottom: none;
            }
            
            .summary-box td {
                padding: 14px 18px;
                font-size: 11px;
            }
            
            .summary-box td:first-child {
                font-weight: 600;
                color: #666;
            }
            
            .summary-box td:last-child {
                text-align: right;
                font-weight: 700;
                color: #1a1a1a;
            }
            
            .summary-box .total-row {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .summary-box .total-row td {
                color: white;
                font-size: 14px;
                padding: 16px 18px;
            }
            
            .summary-box .paid-row {
                background: #e8f5e9;
            }
            
            .summary-box .paid-row td {
                color: #27ae60;
                font-weight: 700;
            }
            
            .summary-box .due-row {
                background: #ffebee;
            }
            
            .summary-box .due-row td {
                color: #e74c3c;
                font-weight: 700;
                font-size: 12px;
            }
            
            /* Payment History */
            .payment-history {
                clear: both;
                margin-top: 35px;
                padding-top: 25px;
                border-top: 2px solid #e8e8e8;
            }
            
            .payment-history h3 {
                font-size: 13px;
                font-weight: 700;
                color: #1a1a1a;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            /* Footer */
            .invoice-footer {
                margin-top: 40px;
                padding-top: 25px;
                border-top: 2px solid #e8e8e8;
                text-align: center;
            }
            
            .invoice-footer p {
                font-size: 9px;
                color: #999;
                margin: 4px 0;
            }
            
            .invoice-footer .thank-you {
                font-size: 13px;
                font-weight: 600;
                color: #667eea;
                margin-top: 8px;
            }
            
            .clearfix::after {
                content: "";
                display: table;
                clear: both;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <div class="top-bar"></div>
            
            <!-- Header -->
            <div class="invoice-header">
                <div class="company-info">
                    <h1>' . $company['company_name'] . '</h1>
                    <p>
                        ' . (!empty($company['address']) ? $company['address'] . '<br>' : '') . '
                        ' . (!empty($company['phone']) ? 'Tel: ' . $company['phone'] . '<br>' : '') . '
                        ' . (!empty($company['email']) ? 'Email: ' . $company['email'] : '') . '
                    </p>
                </div>
                
                <div class="invoice-meta">
                    <h2>Invoice</h2>
                    <table>
                        <tr>
                            <td>Bill No:</td>
                            <td>' . $order['bill_no'] . '</td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td>' . date('d/m/Y', is_numeric($order['date_time']) ? $order['date_time'] : strtotime($order['date_time'])) . '</td>
                        </tr>
                        <tr>
                            <td>Time:</td>
                            <td>' . date('H:i', is_numeric($order['date_time']) ? $order['date_time'] : strtotime($order['date_time'])) . '</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-box">
                    <h3>Bill To</h3>
                    <p>
                        <strong>' . $order['customer_name'] . '</strong><br>
                        Phone: ' . $order['customer_phone'] . '<br>
                        ' . (!empty($order['customer_address']) ? 'Address: ' . $order['customer_address'] : '') . '
                    </p>
                </div>
                
                <div class="info-box">
                    <h3>Payment Status</h3>
                    <p>';

        if ($order['paid_status'] == 1) {
            $html .= '<span class="status-badge status-paid">✓ Fully Paid</span>';
        } elseif ($order['paid_status'] == 3) {
            $html .= '<span class="status-badge status-partial">⚠ Partially Paid</span>';
        } else {
            $html .= '<span class="status-badge status-unpaid">✗ Unpaid</span>';
        }

        $html .= '</p>
                </div>
            </div>
            
            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:5%;" class="text-center">#</th>
                        <th style="width:45%;">Description</th>
                        <th style="width:15%;" class="text-center">Quantity</th>
                        <th style="width:17%;" class="text-right">Unit Price</th>
                        <th style="width:18%;" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($order_items as $key => $item) {
            $html .= '<tr>
                        <td class="text-center">' . ($key + 1) . '</td>
                        <td class="product-name">' . $item['name'] . '</td>
                        <td class="text-center">' . $item['qty'] . '</td>
                        <td class="text-right">' . number_format($item['rate'], 2) . ' DZD</td>
                        <td class="text-right">' . number_format($item['amount'], 2) . ' DZD</td>
                    </tr>';
        }

        // Gross Amount
        $html .= '<tr class="total-row">
                        <td colspan="4" class="text-right">Gross Amount:</td>
                        <td class="text-right">' . number_format($order['gross_amount'], 2) . ' DZD</td>
                    </tr>';

        // Discount
        if ($order['discount'] > 0) {
            $html .= '<tr class="total-row">
                        <td colspan="4" class="text-right">Discount:</td>
                        <td class="text-right">- ' . number_format($order['discount'], 2) . ' DZD</td>
                    </tr>';
        }

        // Grand Total
        $html .= '<tr class="grand-total-row">
                        <td colspan="4" class="text-right">TOTAL AMOUNT</td>
                        <td class="text-right">' . number_format($order['net_amount'], 2) . ' DZD</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-box">
                    <table>
                        <tr class="total-row">
                            <td>Total Amount</td>
                            <td>' . number_format($order['net_amount'], 2) . ' DZD</td>
                        </tr>
                        <tr class="paid-row">
                            <td>Amount Paid</td>
                            <td>' . number_format($order['paid_amount'], 2) . ' DZD</td>
                        </tr>
                        <tr class="due-row">
                            <td>Balance Due</td>
                            <td>' . number_format($order['due_amount'], 2) . ' DZD</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="clearfix"></div>';

        // Payment History
        if (!empty($payments) && count($payments) > 0) {
            $html .= '<div class="payment-history">
                <h3>Payment History (' . count($payments) . ' payments)</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:8%;" class="text-center">#</th>
                            <th style="width:18%;" class="text-center">Date</th>
                            <th style="width:18%;" class="text-right">Amount Paid</th>
                            <th style="width:18%;" class="text-right">Balance</th>
                            <th style="width:20%;" class="text-center">Method</th>
                            <th style="width:18%;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($payments as $payment) {
                $html .= '<tr>
                            <td class="text-center">' . $payment['installment_number'] . '</td>
                            <td class="text-center">' . date('d/m/Y H:i', is_numeric($payment['payment_date']) ? $payment['payment_date'] : strtotime($payment['payment_date'])) . '</td>
                            <td class="text-right"><strong>' . number_format($payment['payment_amount'], 2) . ' DZD</strong></td>';

                if ($payment['remaining_balance'] == 0) {
                    $html .= '<td class="text-right"><span class="status-badge status-paid">PAID OFF ✓</span></td>';
                } else {
                    $html .= '<td class="text-right">' . number_format($payment['remaining_balance'], 2) . ' DZD</td>';
                }

                $method = ucfirst(str_replace('_', ' ', $payment['payment_method']));
                $notes = !empty($payment['payment_notes']) ? $payment['payment_notes'] : '-';

                $html .= '<td class="text-center">' . $method . '</td>
                            <td style="font-size:9px;">' . $notes . '</td>
                        </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Footer
        $html .= '<div class="invoice-footer">
                <p>This is a computer-generated document. No signature is required.</p>
                <p class="thank-you">Thank you for your business</p>
                <p style="margin-top:8px;">Generated on: ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
        </div>
    </body>
    </html>';

        return $html;
    }
}
