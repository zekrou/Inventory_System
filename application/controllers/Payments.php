<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->not_logged_in();
		$this->load->model('model_payments');
		$this->load->model('model_orders');
		$this->load->model('model_company');
	}

	/**
	 * Record a new payment for an order
	 */
	public function recordPayment()
	{
		if(!isset($this->permission['updateOrder'])) {
			echo json_encode(array('success' => false, 'message' => 'Permission denied'));
			return;
		}

		$order_id = $this->input->post('order_id');
		$amount_paid = $this->input->post('amount_paid');
		$payment_method = $this->input->post('payment_method', true) ?: 'Cash';
		$notes = $this->input->post('notes', true);

		if(!$order_id || !$amount_paid || $amount_paid <= 0) {
			echo json_encode(array('success' => false, 'message' => 'Invalid order ID or amount'));
			return;
		}

		// Verify order exists
		$order = $this->model_orders->getOrdersData($order_id);
		if(!$order) {
			echo json_encode(array('success' => false, 'message' => 'Order not found'));
			return;
		}

		// Record payment
		$payment_id = $this->model_payments->recordPayment($order_id, $amount_paid, $payment_method, $notes);

		if($payment_id) {
			echo json_encode(array(
				'success' => true, 
				'message' => 'Payment recorded successfully',
				'payment_id' => $payment_id
			));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Error recording payment'));
		}
	}

	/**
	 * Get payment history for an order
	 */
	public function getOrderPayments()
	{
		if(!isset($this->permission['viewOrder'])) {
			echo json_encode(array('success' => false, 'message' => 'Permission denied'));
			return;
		}

		$order_id = $this->input->get('order_id');

		if(!$order_id) {
			echo json_encode(array('success' => false, 'message' => 'Order ID required'));
			return;
		}

		$payments = $this->model_payments->getOrderPayments($order_id);
		$summary = $this->model_payments->getPaymentSummary($order_id);

		echo json_encode(array(
			'success' => true,
			'payments' => $payments,
			'summary' => $summary
		));
	}

	/**
	 * Delete a payment
	 */
	public function deletePayment()
	{
		if(!isset($this->permission['updateOrder'])) {
			echo json_encode(array('success' => false, 'message' => 'Permission denied'));
			return;
		}

		$payment_id = $this->input->post('payment_id');

		if(!$payment_id) {
			echo json_encode(array('success' => false, 'message' => 'Payment ID required'));
			return;
		}

		if($this->model_payments->deletePayment($payment_id)) {
			echo json_encode(array('success' => true, 'message' => 'Payment deleted successfully'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Error deleting payment'));
		}
	}

	/**
	 * Update payment
	 */
	public function updatePayment()
	{
		if(!isset($this->permission['updateOrder'])) {
			echo json_encode(array('success' => false, 'message' => 'Permission denied'));
			return;
		}

		$payment_id = $this->input->post('payment_id');
		$amount = $this->input->post('amount');
		$payment_method = $this->input->post('payment_method', true);
		$notes = $this->input->post('notes', true);

		if(!$payment_id) {
			echo json_encode(array('success' => false, 'message' => 'Payment ID required'));
			return;
		}

		if($this->model_payments->updatePayment($payment_id, $amount, $payment_method, $notes)) {
			echo json_encode(array('success' => true, 'message' => 'Payment updated successfully'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Error updating payment'));
		}
	}

	/**
	 * Print payment receipt
	 */
	public function printReceipt($payment_id)
	{
		if(!isset($this->permission['viewOrder'])) {
			redirect('dashboard', 'refresh');
		}

		if(!$payment_id) {
			redirect('dashboard', 'refresh');
		}

		$receipt = $this->model_payments->getPaymentReceipt($payment_id);
		$company = $this->model_company->getCompanyData(1);

		if(!$receipt) {
			redirect('dashboard', 'refresh');
		}

		$payment_date = date('d/m/Y H:i', strtotime($receipt['payment_date']));
		$created_by = ($receipt['firstname'] && $receipt['lastname']) ? $receipt['firstname'] . ' ' . $receipt['lastname'] : 'System';

		$html = '<!DOCTYPE html>
<html>
<head>
	<title>Payment Receipt</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 20px; }
		.receipt-container { max-width: 800px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; }
		.header { text-align: center; margin-bottom: 20px; }
		.header h1 { margin: 0; }
		.header p { margin: 5px 0; color: #666; }
		.section { margin-bottom: 20px; }
		.section-title { font-weight: bold; border-bottom: 2px solid #333; padding-bottom: 5px; }
		table { width: 100%; border-collapse: collapse; }
		table td { padding: 8px; border-bottom: 1px solid #eee; }
		.label { font-weight: bold; width: 30%; }
		.total-row { font-size: 18px; font-weight: bold; }
		.footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
	</style>
</head>
<body>

<div class="receipt-container">
	<div class="header">
		<h1>' . $company['company_name'] . '</h1>
		<p>' . $company['address'] . '</p>
		<p>' . $company['phone'] . '</p>
	</div>

	<div class="section">
		<div class="section-title">PAYMENT RECEIPT</div>
		<table>
			<tr>
				<td class="label">Receipt Number:</td>
				<td>' . $receipt['receipt_number'] . '</td>
			</tr>
			<tr>
				<td class="label">Payment Date:</td>
				<td>' . $payment_date . '</td>
			</tr>
			<tr>
				<td class="label">Recorded By:</td>
				<td>' . $created_by . '</td>
			</tr>
		</table>
	</div>

	<div class="section">
		<div class="section-title">ORDER INFORMATION</div>
		<table>
			<tr>
				<td class="label">Bill Number:</td>
				<td>' . $receipt['bill_no'] . '</td>
			</tr>
			<tr>
				<td class="label">Customer Name:</td>
				<td>' . $receipt['customer_name'] . '</td>
			</tr>
			<tr>
				<td class="label">Customer Phone:</td>
				<td>' . $receipt['customer_phone'] . '</td>
			</tr>
		</table>
	</div>

	<div class="section">
		<div class="section-title">PAYMENT DETAILS</div>
		<table>
			<tr>
				<td class="label">Order Total:</td>
				<td>' . $receipt['net_amount'] . '</td>
			</tr>
			<tr>
				<td class="label">Previously Paid:</td>
				<td>' . number_format(floatval($receipt['paid_amount']) - floatval($receipt['amount']), 2) . '</td>
			</tr>
			<tr class="total-row">
				<td class="label">Payment Amount:</td>
				<td>' . number_format(floatval($receipt['amount']), 2) . '</td>
			</tr>
			<tr>
				<td class="label">Payment Method:</td>
				<td>' . $receipt['payment_method'] . '</td>
			</tr>
			<tr>
				<td class="label">Total Paid:</td>
				<td>' . number_format(floatval($receipt['paid_amount']), 2) . '</td>
			</tr>
		</table>
	</div>

	<div class="section">
		<div class="section-title">BALANCE</div>
		<table>
			<tr class="total-row">
				<td class="label">Remaining Balance:</td>
				<td>' . number_format(floatval(str_replace(',', '', $receipt['net_amount'])) - floatval($receipt['paid_amount']), 2) . '</td>
			</tr>
		</table>
	</div>

	<div class="footer">
		<p>Thank you for your payment!</p>
		<p>This is a system-generated receipt. Please print or save for your records.</p>
		<p>Generated on ' . date('d/m/Y H:i:s') . '</p>
	</div>
</div>

</body>
</html>';

		echo $html;
	}
}
