<?php

class Model_payments extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Record a new payment for an order (uses existing order_payments table)
	 */
	public function recordPayment($order_id, $amount_paid, $payment_method = 'Cash', $notes = '', $reference_number = '')
	{
		if(!$order_id || !$amount_paid || $amount_paid <= 0) {
			return false;
		}

		$user_id = $this->session->userdata('id');

		$data = array(
			'order_id' => $order_id,
			'amount_paid' => $amount_paid,
			'payment_date' => date('Y-m-d H:i:s'),
			'payment_method' => $payment_method,
			'notes' => $notes,
			'reference_number' => $reference_number ? $reference_number : 'RCP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)),
			'received_by' => $user_id
		);

		$insert = $this->db->insert('order_payments', $data);
		$payment_id = $this->db->insert_id();

		if($insert) {
			// Update order paid_amount and status
			$this->updateOrderPaymentStatus($order_id);
			return $payment_id;
		}

		return false;
	}

	/**
	 * Update order paid_amount and auto-detect paid_status
	 */
	public function updateOrderPaymentStatus($order_id)
	{
		// Get order details
		$order = $this->db->query("SELECT * FROM `orders` WHERE id = ?", array($order_id))->row_array();
		if(!$order) {
			return false;
		}

		// Sum all payments for this order
		$payments = $this->db->query("SELECT SUM(amount_paid) as total_paid FROM `order_payments` WHERE order_id = ?", array($order_id))->row_array();
		$total_paid = $payments['total_paid'] ? floatval($payments['total_paid']) : 0;

		// Get net_amount
		$net = floatval(str_replace(',', '', $order['net_amount']));

		// Calculate remaining
		$due = $net - $total_paid;
		if($due < 0) $due = 0;

		// Determine status: 1=Paid, 2=Unpaid, 3=Partial
		if($total_paid >= $net) {
			$status = 1; // Paid
		} elseif($total_paid <= 0) {
			$status = 2; // Unpaid
		} else {
			$status = 3; // Partially Paid
		}

		// Update order
		$update = array(
			'paid_amount' => $total_paid,
			'due_amount' => $due,
			'paid_status' => $status
		);

		$this->db->where('id', $order_id);
		return $this->db->update('orders', $update);
	}

	/**
	 * Get all payments for an order
	 */
	public function getOrderPayments($order_id)
	{
		$sql = "SELECT p.*, u.firstname, u.lastname FROM `order_payments` p 
				LEFT JOIN `users` u ON p.received_by = u.id 
				WHERE p.order_id = ? 
				ORDER BY p.payment_date DESC";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}

	/**
	 * Get single payment
	 */
	public function getPayment($payment_id)
	{
		$sql = "SELECT p.*, u.firstname, u.lastname FROM `order_payments` p 
				LEFT JOIN `users` u ON p.received_by = u.id 
				WHERE p.id = ?";
		$query = $this->db->query($sql, array($payment_id));
		return $query->row_array();
	}

	/**
	 * Delete a payment and recalculate order status
	 */
	public function deletePayment($payment_id)
	{
		$payment = $this->getPayment($payment_id);
		if(!$payment) {
			return false;
		}

		$order_id = $payment['order_id'];

		$this->db->where('id', $payment_id);
		$delete = $this->db->delete('order_payments');

		if($delete) {
			// Recalculate paid amount and status
			$this->updateOrderPaymentStatus($order_id);
			return true;
		}

		return false;
	}

	/**
	 * Update a payment
	 */
	public function updatePayment($payment_id, $amount_paid = '', $payment_method = '', $notes = '')
	{
		$payment = $this->getPayment($payment_id);
		if(!$payment) {
			return false;
		}

		$data = array();
		if($amount_paid && $amount_paid > 0) {
			$data['amount_paid'] = $amount_paid;
		}
		if($payment_method) {
			$data['payment_method'] = $payment_method;
		}
		if($notes !== '') {
			$data['notes'] = $notes;
		}

		if(empty($data)) {
			return false;
		}

		$this->db->where('id', $payment_id);
		$update = $this->db->update('order_payments', $data);

		if($update) {
			// Recalculate order status
			$this->updateOrderPaymentStatus($payment['order_id']);
			return true;
		}

		return false;
	}

	/**
	 * Get payment receipt details for printing
	 */
	public function getPaymentReceipt($payment_id)
	{
		$sql = "SELECT p.*, o.*, u.firstname, u.lastname FROM `order_payments` p
				LEFT JOIN `orders` o ON p.order_id = o.id
				LEFT JOIN `users` u ON p.received_by = u.id
				WHERE p.id = ?";
		$query = $this->db->query($sql, array($payment_id));
		return $query->row_array();
	}

	/**
	 * Get payment summary for an order
	 */
	public function getPaymentSummary($order_id)
	{
		$sql = "SELECT 
				o.net_amount,
				o.paid_amount,
				o.due_amount,
				o.paid_status,
				COUNT(p.id) as payment_count
				FROM `orders` o
				LEFT JOIN `order_payments` p ON o.id = p.order_id
				WHERE o.id = ?
				GROUP BY o.id";
		$query = $this->db->query($sql, array($order_id));
		return $query->row_array();
	}
}

