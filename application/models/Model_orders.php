<?php
class Model_orders extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getOrdersData($id = null)
	{
		if ($id) {
			$sql = "SELECT o.*, c.customer_name as cust_name, c.customer_code 
						FROM `orders` o
						LEFT JOIN `customers` c ON o.customer_id = c.id
						WHERE o.id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT o.*, c.customer_name as cust_name, c.customer_code 
					FROM `orders` o
					LEFT JOIN `customers` c ON o.customer_id = c.id
					ORDER BY o.id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getOrdersByStatus($status = null)
	{
		if ($status) {
			$sql = "SELECT o.*, c.customer_name as cust_name, c.customer_code 
						FROM `orders` o
						LEFT JOIN `customers` c ON o.customer_id = c.id
						WHERE o.paid_status = ?
						ORDER BY o.id DESC";
			$query = $this->db->query($sql, array($status));
			return $query->result_array();
		}
		return $this->getOrdersData();
	}

	public function getOrdersItemData($order_id = null)
	{
		if (!$order_id) {
			return false;
		}

		$sql = "SELECT * FROM `orders_item` WHERE order_id = ?";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}

	public function create()
	{
		// ✅ FIX 1: Vérifier si l'utilisateur existe dans la base tenant
		$user_id = $this->session->userdata('id');

		$user_check = $this->db->where('id', $user_id)->get('users');
		if ($user_check->num_rows() == 0) {
			// L'utilisateur n'existe pas dans ce tenant, utiliser l'admin
			$admin = $this->db->select('id')->order_by('id', 'ASC')->limit(1)->get('users')->row();
			$user_id = $admin ? $admin->id : 1;
		}

		// Validation - Noms de champs corrects
		$customer_name = $this->input->post('customername');
		$customer_phone = $this->input->post('customerphone');

		if (empty($customer_name) || empty($customer_phone)) {
			return false;
		}

		$bill_no = 'BILPR-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

		// Gestion du type client
		$customer_id = $this->input->post('customerid');

		// Get customer type from selected customer or input
		if ($customer_id && $customer_id != 'new' && !empty($customer_id)) {
			$this->load->model('model_customers');
			$customer_data = $this->model_customers->getCustomerData($customer_id);
			$customer_type = $customer_data['customer_type'];
		} else {
			$customer_type = $this->input->post('customertype') ? $this->input->post('customertype') : 'retail';
		}

		// Gestion du prix manuel (override)
		$price_type_override = $this->input->post('pricetypemanual');
		$override_reason = null;

		// If price type different from customer type, record reason
		if ($price_type_override && $price_type_override != $customer_type) {
			$type_labels = array(
				'super_wholesale' => 'Super Gros',
				'wholesale' => 'Gros',
				'retail' => 'Détail'
			);
			$override_label = isset($type_labels[$price_type_override]) ? $type_labels[$price_type_override] : $price_type_override;
			$customer_label = isset($type_labels[$customer_type]) ? $type_labels[$customer_type] : $customer_type;
			$override_reason = 'Manual price override: ' . $override_label . ' (Customer is ' . $customer_label . ')';
		}

		// Récupère les bonnes valeurs avec underscore
		$gross_amount = $this->input->post('gross_amount_value');
		$net_amount = $this->input->post('net_amount_value');
		$paid_amount = $this->input->post('paid_amount') ? $this->input->post('paid_amount') : 0;
		$discount = $this->input->post('discount') ? $this->input->post('discount') : 0;

		// VALIDATION: Vérifie que les montants ne sont pas NULL
		if ($gross_amount === null || $gross_amount === '' || $net_amount === null || $net_amount === '') {
			log_message('error', 'Order creation failed: gross_amount or net_amount is null');
			return false;
		}

		$due_amount = $net_amount - $paid_amount;

		if ($paid_amount == 0) {
			$paid_status = 2;
		} elseif ($paid_amount >= $net_amount) {
			$paid_status = 1;
		} else {
			$paid_status = 3;
		}

		// Create new customer if needed
		if ($customer_id === "new" || empty($customer_id)) {
			if (!empty($customer_name)) {
				$this->load->model('model_customers');
				$new_customer_data = array(
					'customer_code' => $this->model_customers->generateCustomerCode(),
					'customer_name' => $customer_name,
					'customer_type' => $customer_type,
					'phone' => $customer_phone,
					'address' => $this->input->post('customeraddress'),
					'active' => 1
				);
				$customer_id = $this->model_customers->create($new_customer_data);
			}
		}

		// DATA ARRAY CORRIGÉ
		$data = array(
			'bill_no' => $bill_no,
			'customer_id' => $customer_id,
			'customer_type' => $customer_type,
			'price_type_override' => $price_type_override ? $price_type_override : '',
			'override_reason' => $override_reason,
			'customer_name' => $customer_name,
			'customer_address' => $this->input->post('customeraddress'),
			'customer_phone' => $customer_phone,
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
			'user_id' => $user_id  // ✅ Utilise l'ID validé
		);

		$insert = $this->db->insert('orders', $data);
		$order_id = $this->db->insert_id();

		$this->load->model('model_products');

		$count_product = count($this->input->post('product'));
		for ($x = 0; $x < $count_product; $x++) {
			$product_id = $this->input->post('product')[$x];
			$qty_ordered = $this->input->post('qty')[$x];

			$product_data = $this->model_products->getProductData($product_id);
			if ($product_data['qty'] < $qty_ordered) {
				$this->db->where('id', $order_id);
				$this->db->delete('orders');
				return false;
			}

			// rate_value et amount_value (avec underscore)
			$items = array(
				'order_id' => $order_id,
				'product_id' => $product_id,
				'qty' => $qty_ordered,
				'rate' => $this->input->post('rate_value')[$x],
				'amount' => $this->input->post('amount_value')[$x],
			);

			$this->db->insert('orders_item', $items);

			$qty_before = $product_data['qty'];
			$qty_after = $qty_before - $qty_ordered;

			$update_product = array('qty' => $qty_after);
			$this->model_products->update($update_product, $product_id);

			$this->recordStockHistory($product_id, $order_id, 'sale', $qty_ordered, $qty_before, $qty_after, $user_id);
		}

		// Record first payment if paid
		if ($paid_amount > 0) {
			$this->recordPaymentInstallment($order_id, $paid_amount, $this->input->post('payment_method'), $this->input->post('payment_notes'), $due_amount, $user_id);
		}

		if ($customer_id) {
			$this->load->model('model_customers');
			$this->model_customers->updateBalance($customer_id, $due_amount);
		}

		return ($order_id) ? $order_id : false;
	}



	public function countOrderItem($order_id)
	{
		if ($order_id) {
			$sql = "SELECT * FROM `orders_item` WHERE order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}

	public function update($id)
	{
		if ($id) {
			$user_id = $this->session->userdata('id');
			$user_check = $this->db->where('id', $user_id)->get('users');
			if ($user_check->num_rows() == 0) {
				$admin = $this->db->select('id')->order_by('id', 'ASC')->limit(1)->get('users')->row();
				$user_id = $admin ? $admin->id : 1;
			}
			$old_order = $this->getOrdersData($id);
			$old_net_amount = $old_order['net_amount'];
			$old_paid_amount = $old_order['paid_amount'];
			$old_due_amount = $old_order['due_amount'];
			$old_customer_id = $old_order['customer_id'];

			// 🔴 CORRECTION: Avec underscore
			$gross_amount = $this->input->post('gross_amount_value');
			$net_amount = $this->input->post('net_amount_value');
			$paid_amount = $this->input->post('paid_amount') ? $this->input->post('paid_amount') : 0;
			$discount = $this->input->post('discount') ? $this->input->post('discount') : 0;
			$due_amount = $net_amount - $paid_amount;

			if ($paid_amount == 0) {
				$paid_status = 2;
			} elseif ($paid_amount >= $net_amount) {
				$paid_status = 1;
			} else {
				$paid_status = 3;
			}

			$customer_id = $this->input->post('customerid') ? $this->input->post('customerid') : NULL;
			$customer_type = $this->input->post('customertype') ? $this->input->post('customertype') : 'retail';

			// Gestion du prix manuel dans UPDATE
			$price_type_override = $this->input->post('pricetypemanual');
			$override_reason = null;

			if ($price_type_override && $price_type_override != $customer_type) {
				$type_labels = array(
					'super_wholesale' => 'Super Gros',
					'wholesale' => 'Gros',
					'retail' => 'Détail'
				);
				$override_label = isset($type_labels[$price_type_override]) ? $type_labels[$price_type_override] : $price_type_override;
				$customer_label = isset($type_labels[$customer_type]) ? $type_labels[$customer_type] : $customer_type;
				$override_reason = 'Manual price override: ' . $override_label . ' (Customer is ' . $customer_label . ')';
			}

			$data = array(
				'customer_id' => $customer_id,
				'customer_type' => $customer_type,
				'price_type_override' => $price_type_override ? $price_type_override : '',
				'override_reason' => $override_reason,
				'customer_name' => $this->input->post('customername'),
				'customer_address' => $this->input->post('customeraddress'),
				'customer_phone' => $this->input->post('customerphone'),
				'gross_amount' => $gross_amount,  // 🔴 CORRIGÉ
				'service_charge_rate' => 0,
				'service_charge' => 0,
				'vat_charge_rate' => 0,
				'vat_charge' => 0,
				'net_amount' => $net_amount,
				'paid_amount' => $paid_amount,
				'due_amount' => $due_amount,
				'discount' => $discount,
				'paid_status' => $paid_status,
				'payment_method' => $this->input->post('payment_method'),  // 🔴 CORRIGÉ
				'payment_notes' => $this->input->post('payment_notes'),    // 🔴 CORRIGÉ
				'user_id' => $user_id
			);

			$this->db->where('id', $id);
			$update = $this->db->update('orders', $data);

			$this->load->model('model_customers');

			if ($old_customer_id) {
				$this->model_customers->updateBalance($old_customer_id, -$old_due_amount);
			}

			if ($customer_id) {
				$this->model_customers->updateBalance($customer_id, $due_amount);
			}

			$this->load->model('model_products');
			$get_order_item = $this->getOrdersItemData($id);
			foreach ($get_order_item as $k => $v) {
				$product_id = $v['product_id'];
				$qty = $v['qty'];

				$product_data = $this->model_products->getProductData($product_id);
				$qty_before = $product_data['qty'];
				$qty_after = $qty_before + $qty;

				$update_product_data = array('qty' => $qty_after);
				$this->model_products->update($update_product_data, $product_id);

				$this->recordStockHistory($product_id, $id, 'return', $qty, $qty_before, $qty_after, $user_id);
			}

			$this->db->where('order_id', $id);
			$this->db->delete('orders_item');

			$count_product = count($this->input->post('product'));
			for ($x = 0; $x < $count_product; $x++) {
				$product_id = $this->input->post('product')[$x];
				$qty_ordered = $this->input->post('qty')[$x];

				// 🔴 CORRECTION: rate_value et amount_value (avec underscore)
				$items = array(
					'order_id' => $id,
					'product_id' => $product_id,
					'qty' => $qty_ordered,
					'rate' => $this->input->post('rate_value')[$x],
					'amount' => $this->input->post('amount_value')[$x],
				);
				$this->db->insert('orders_item', $items);

				$product_data = $this->model_products->getProductData($product_id);
				$qty_before = $product_data['qty'];
				$qty_after = $qty_before - $qty_ordered;

				$update_product = array('qty' => $qty_after);
				$this->model_products->update($update_product, $product_id);

				$this->recordStockHistory($product_id, $id, 'sale', $qty_ordered, $qty_before, $qty_after, $user_id);
			}

			// Only record new payment if amount increased
			if ($paid_amount > $old_paid_amount) {
				$new_payment = $paid_amount - $old_paid_amount;
				$this->recordPaymentInstallment($id, $new_payment, $this->input->post('payment_method'), $this->input->post('payment_notes'), $due_amount, $user_id);
			}

			return true;
		}
	}


	public function remove($id)
	{
		if ($id) {
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

			$order_data = $this->getOrdersData($id);

			$this->load->model('model_products');
			$order_items = $this->getOrdersItemData($id);

			foreach ($order_items as $item) {
				$product_id = $item['product_id'];
				$qty        = $item['qty'];

				$product_data = $this->model_products->getProductData($product_id);
				$qty_before   = $product_data['qty'];
				$qty_after    = $qty_before + $qty;

				$update_product = array('qty' => $qty_after);
				$this->model_products->update($update_product, $product_id);

				$this->recordStockHistory($product_id, $id, 'return', $qty, $qty_before, $qty_after, $user_id);
			}

			if ($order_data['customer_id']) {
				$this->load->model('model_customers');
				$this->model_customers->updateBalance($order_data['customer_id'], -$order_data['due_amount']);
			}

			// Delete payment history
			$this->db->where('order_id', $id);
			$this->db->delete('order_payments');

			$this->db->where('order_id', $id);
			$this->db->delete('orders_item');

			$this->db->where('id', $id);
			$delete = $this->db->delete('orders');

			return ($delete == true) ? true : false;
		}
	}

	/**
	 * Add payment installment
	 */
	public function addPaymentInstallment($order_id, $payment_amount, $payment_method, $payment_notes)
	{
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

		$order = $this->getOrdersData($order_id);
		if (!$order) {
			return array('success' => false, 'message' => 'Order not found');
		}

		// Validate payment amount
		if ($payment_amount > $order['due_amount']) {
			return array('success' => false, 'message' => 'Payment amount exceeds due amount');
		}

		// Calculate new balances
		$new_paid_amount = $order['paid_amount'] + $payment_amount;
		$new_due_amount  = $order['net_amount'] - $new_paid_amount;

		// Determine new payment status
		if ($new_due_amount == 0) {
			$new_paid_status = 1; // Fully Paid
		} elseif ($new_paid_amount > 0 && $new_due_amount > 0) {
			$new_paid_status = 3; // Partially Paid
		} else {
			$new_paid_status = 2; // Unpaid
		}

		// Get next installment number
		$installment_number = $this->getNextInstallmentNumber($order_id);

		// Record payment installment
		$this->recordPaymentInstallment(
			$order_id,
			$payment_amount,
			$payment_method,
			$payment_notes,
			$new_due_amount,
			$user_id,            // ✅ user valide du tenant
			$installment_number
		);

		// Update order totals
		$update_data = array(
			'paid_amount'     => $new_paid_amount,
			'due_amount'      => $new_due_amount,
			'paid_status'     => $new_paid_status,
			'payment_method'  => $payment_method,
			'payment_notes'   => $payment_notes,
		);
		$this->db->where('id', $order_id);
		$this->db->update('orders', $update_data);

		// Update customer balance
		if ($order['customer_id']) {
			$this->load->model('model_customers');
			$this->model_customers->updateBalance($order['customer_id'], -$payment_amount);
		}

		return array(
			'success'            => true,
			'message'            => 'Payment installment recorded successfully',
			'new_due_amount'     => $new_due_amount,
			'paid_status'        => $new_paid_status,
			'installment_number' => $installment_number,
		);
	}


	/**
	 * Get next installment number
	 */
	private function getNextInstallmentNumber($order_id)
	{
		$sql = "SELECT MAX(installment_number) as max_installment FROM `order_payments` WHERE order_id = ?";
		$query = $this->db->query($sql, array($order_id));
		$result = $query->row_array();

		return ($result['max_installment'] ? $result['max_installment'] + 1 : 1);
	}

	/**
	 * Record payment installment in history - Compatible with your existing table
	 */
	private function recordPaymentInstallment($order_id, $amount, $method, $notes, $remaining_balance, $user_id, $installment_number = null)
	{
		if ($installment_number === null) {
			$installment_number = $this->getNextInstallmentNumber($order_id);
		}

		$payment_data = array(
			'order_id' => $order_id,
			'installment_number' => $installment_number,
			'payment_date' => date('Y-m-d H:i:s'), // Using datetime format for your table
			'payment_amount' => $amount,
			'payment_method' => $method,
			'reference_number' => NULL, // Your table has this field
			'notes' => $notes,
			'remaining_balance' => $remaining_balance,
			'received_by' => $user_id
		);

		$this->db->insert('order_payments', $payment_data);
	}

	/**
	 * Record stock movement
	 */
	private function recordStockHistory($product_id, $order_id, $type, $qty, $qty_before, $qty_after, $user_id)
	{
		// Check if stock_history table exists
		if ($this->db->table_exists('stock_history')) {
			$stock_data = array(
				'product_id' => $product_id,
				'order_id' => $order_id,
				'movement_type' => $type,
				'quantity' => $qty,
				'quantity_before' => $qty_before,
				'quantity_after' => $qty_after,
				'user_id' => $user_id
			);

			$this->db->insert('stock_history', $stock_data);
		}
	}

	/**
	 * Get payment history for an order - Compatible with your table
	 */
	public function getOrderPayments($order_id)
	{
		if ($order_id) {
			$sql = "SELECT 
							id,
							order_id,
							installment_number,
							payment_date,
							payment_amount,
							payment_method,
							reference_number,
							notes as payment_notes,
							remaining_balance,
							received_by
						FROM `order_payments` 
						WHERE order_id = ? 
						ORDER BY installment_number ASC";
			$query = $this->db->query($sql, array($order_id));
			$results = $query->result_array();

			// Convert payment_date to timestamp for consistency
			foreach ($results as &$result) {
				if (isset($result['payment_date'])) {
					$result['payment_date'] = strtotime($result['payment_date']);
				}
			}

			return $results;
		}
		return array();
	}

	/**
	 * Get order statistics
	 */
	public function getOrderStats()
	{
		$sql = "SELECT 
						COUNT(*) as total_orders,
						SUM(CASE WHEN paid_status = 1 THEN 1 ELSE 0 END) as paid_orders,
						SUM(CASE WHEN paid_status = 2 THEN 1 ELSE 0 END) as unpaid_orders,
						SUM(CASE WHEN paid_status = 3 THEN 1 ELSE 0 END) as partial_orders,
						SUM(net_amount) as total_sales,
						SUM(paid_amount) as total_paid,
						SUM(due_amount) as total_due
					FROM `orders`";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function countTotalPaidOrders()
	{
		$sql = "SELECT * FROM `orders` WHERE paid_status = ?";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}

	public function countTotalUnpaidOrders()
	{
		$sql = "SELECT * FROM `orders` WHERE paid_status = ?";
		$query = $this->db->query($sql, array(2));
		return $query->num_rows();
	}

	public function countPartiallyPaidOrders()
	{
		$sql = "SELECT * FROM `orders` WHERE paid_status = ?";
		$query = $this->db->query($sql, array(3));
		return $query->num_rows();
	}

	public function getOrdersByCustomer($customer_id)
	{
		if ($customer_id) {
			$sql = "SELECT * FROM `orders` WHERE customer_id = ? ORDER BY id DESC";
			$query = $this->db->query($sql, array($customer_id));
			return $query->result_array();
		}
		return false;
	}
	public function getTodaySales()
	{
		$today = date('Y-m-d');
		$sql = "SELECT 
            COUNT(*) as orders_count,
            COALESCE(SUM(net_amount), 0) as total_sales
            FROM orders 
            WHERE DATE(date_time) = ? 
            AND paid_status = 1";

		$query = $this->db->query($sql, array($today));
		return $query->row_array();
	}

	public function getMonthlySales()
	{
		$month = date('Y-m');
		$sql = "SELECT 
            COUNT(*) as orders_count,
            COALESCE(SUM(net_amount), 0) as total_sales
            FROM orders 
            WHERE DATE_FORMAT(date_time, '%Y-%m') = ? 
            AND paid_status = 1";

		$query = $this->db->query($sql, array($month));
		return $query->row_array();
	}

	public function getPendingOrders()
	{
		$sql = "SELECT 
            COUNT(*) as count,
            COALESCE(SUM(net_amount), 0) as total_value
            FROM orders 
            WHERE paid_status = 2"; // 2 = pending

		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function calculateProfitMargin()
	{
		$sql = "SELECT 
            COALESCE(SUM(oi.amount), 0) as total_sales,
            COALESCE(SUM(p.price_default * oi.qty), 0) as total_cost
            FROM orders o
            JOIN orders_item oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.paid_status = 1
            AND MONTH(o.date_time) = MONTH(CURRENT_DATE())
            AND YEAR(o.date_time) = YEAR(CURRENT_DATE())";

		$query = $this->db->query($sql);
		$result = $query->row_array();

		if ($result['total_sales'] > 0) {
			$profit = $result['total_sales'] - $result['total_cost'];
			return round(($profit / $result['total_sales']) * 100, 1);
		}

		return 0;
	}

	public function getRecentOrders($limit = 5)
	{
		$sql = "SELECT * FROM orders 
            ORDER BY date_time DESC 
            LIMIT ?";

		$query = $this->db->query($sql, array($limit));
		return $query->result_array();
	}
	public function getOrdersDataWithSearch($status = null, $searchTerm = null)
	{
		// 🔴 CORRECTION: Utilise les bons noms de colonnes
		$this->db->select('o.*, c.customer_name as cust_name, c.customer_code');
		$this->db->from('orders o');
		$this->db->join('customers c', 'o.customer_id = c.id', 'LEFT');

		// Filter by status if provided
		if ($status && $status != 'all') {
			$this->db->where('o.paid_status', $status);
		}

		// Search filter - 🔴 Utilise customer_name, customer_phone, bill_no (avec underscores)
		if (!empty($searchTerm)) {
			$this->db->group_start();
			$this->db->like('o.customer_name', $searchTerm);
			$this->db->or_like('o.customer_phone', $searchTerm);
			$this->db->or_like('o.bill_no', $searchTerm);
			$this->db->group_end();
		}

		$this->db->order_by('o.id', 'DESC');
		$query = $this->db->get();

		// 🔴 Vérifie si la requête a réussi
		if (!$query) {
			log_message('error', 'Database error: ' . $this->db->error()['message']);
			return array();
		}

		return $query->result_array();
	}
}
