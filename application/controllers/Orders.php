<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $this->data['order_stats'] = $this->model_orders->getOrderStats();
		$this->render_template('orders/index', $this->data);		
	}

	public function fetchOrdersData()
	{
		$result = array('data' => array());
		$status = $this->input->get('status');
		
		if($status && $status != 'all') {
			$status_map = array('paid' => 1, 'unpaid' => 2, 'partial' => 3);
			$data = $this->model_orders->getOrdersByStatus($status_map[$status]);
		} else {
			$data = $this->model_orders->getOrdersData();
		}

		foreach ($data as $key => $value) {
			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);
			$date_time = $date . ' ' . $time;

			$buttons = '';
			if(in_array('viewOrder', $this->permission)) {
				$buttons .= '<button class="btn btn-info btn-sm" onclick="viewOrderDetails('.$value['id'].')"><i class="fa fa-eye"></i></button> ';
				$buttons .= '<a target="__blank" href="'.base_url('orders/invoice/'.$value['id']).'" class="btn btn-default btn-sm"><i class="fa fa-print"></i></a> ';
			}
			if(in_array('updateOrder', $this->permission)) {
				$buttons .= '<a href="'.base_url('orders/update/'.$value['id']).'" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></a> ';
			}
			if(in_array('deleteOrder', $this->permission)) {
				$buttons .= '<button type="button" class="btn btn-danger btn-sm" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			if($value['paid_status'] == 1) {
				$paid_status = '<span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span>';	
			} elseif($value['paid_status'] == 3) {
				$due = number_format($value['due_amount'], 2);
				$paid_status = '<span class="label label-warning"><i class="fa fa-clock-o"></i> Partial</span><br><small class="text-danger">Due: '.$due.' DZD</small>';
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
				'<span class="'.($value['due_amount'] > 0 ? 'text-danger' : 'text-success').'">'.number_format($value['due_amount'], 2).' DZD</span>',
				$paid_status,
				$buttons
			);
		}

		echo json_encode($result);
	}

	/**
	 * Check for duplicate customers (AJAX)
	 */
	public function checkDuplicateCustomer()
	{
		$customer_name = $this->input->post('customer_name');
		$customer_phone = $this->input->post('customer_phone');
		
		$response = array('exists' => false, 'suggestions' => array());
		
		if(empty($customer_name) && empty($customer_phone)) {
			echo json_encode($response);
			return;
		}
		
		// Check for exact or similar matches
		$duplicates = $this->model_customers->checkDuplicates($customer_name, $customer_phone);
		
		if(!empty($duplicates)) {
			$response['exists'] = true;
			$response['suggestions'] = $duplicates;
		}
		
		echo json_encode($response);
	}

	public function create()
	{
		if(!in_array('createOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required');
    	$this->form_validation->set_rules('customer_phone', 'Customer Phone', 'trim|required');
		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		$price_type = $this->input->post('price_type_manual');  // super_wholesale/wholesale/retail

		if ($this->form_validation->run() == TRUE) {
			$order_id = $this->model_orders->create();
			if($order_id) {
				$this->session->set_flashdata('success', 'Order created successfully!');
				redirect('orders/update/'.$order_id, 'refresh');
			} else {
				$this->session->set_flashdata('error', 'Error: Insufficient stock or invalid data!');
				redirect('orders/create', 'refresh');
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
			$this->render_template('orders/create', $this->data);
		}	
	}

	public function update($id)
	{
		if(!in_array('updateOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		if(!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$update = $this->model_orders->update($id);
			if($update == true) {
				$this->session->set_flashdata('success', 'Successfully updated');
				redirect('orders/update/'.$id, 'refresh');
			} else {
				$this->session->set_flashdata('error', 'Error occurred!!');
				redirect('orders/update/'.$id, 'refresh');
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
			foreach($orders_item as $k => $v) {
				$result['order_item'][] = $v;
			}
			$this->data['order_data'] = $result;
			$this->render_template('orders/edit', $this->data);
		}
	}

	public function remove()
	{
		if(!in_array('deleteOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		$order_id = $this->input->post('order_id');
		$response = array();
		if($order_id) {
			$delete = $this->model_orders->remove($order_id);
			if($delete == true) {
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

	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		$customer_type = $this->input->post('customer_type');
		if($product_id) {
			if($customer_type) {
				$product_data = $this->model_products->getProductDataWithPrice($product_id, $customer_type);
			} else {
				$product_data = $this->model_products->getProductDataWithPrice($product_id, 'retail');
			}
			echo json_encode($product_data);
		}
	}

	public function getTableProductRow()
	{
		$customer_type = $this->input->post('customer_type');
		if($customer_type) {
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
		echo '<tr><th width="40%">Bill No:</th><td><strong>'.$order['bill_no'].'</strong></td></tr>';
		echo '<tr><th>Customer:</th><td>'.$order['customer_name'].'</td></tr>';
		echo '<tr><th>Phone:</th><td>'.$order['customer_phone'].'</td></tr>';
		echo '<tr><th>Address:</th><td>'.$order['customer_address'].'</td></tr>';
		echo '<tr><th>Date:</th><td>'.date('d-m-Y h:i a', $order['date_time']).'</td></tr>';
		echo '</table>';
		echo '</div>';
		
		echo '<div class="col-md-6">';
		echo '<h4><i class="fa fa-money"></i> Payment Summary</h4>';
		echo '<table class="table table-condensed">';
		echo '<tr><th width="40%">Total Amount:</th><td><strong>'.number_format($order['net_amount'], 2).' DZD</strong></td></tr>';
		echo '<tr><th>Paid Amount:</th><td class="text-success"><strong>'.number_format($order['paid_amount'], 2).' DZD</strong></td></tr>';
		echo '<tr><th>Due Amount:</th><td class="text-danger"><strong>'.number_format($order['due_amount'], 2).' DZD</strong></td></tr>';
		
		if($order['paid_status'] == 1) {
			echo '<tr><th>Status:</th><td><span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span></td></tr>';
		} elseif($order['paid_status'] == 3) {
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
		foreach($items as $item) {
			$product = $this->model_products->getProductData($item['product_id']);
			echo '<tr>';
			echo '<td>'.$product['name'].'</td>';
			echo '<td class="text-center">'.$item['qty'].'</td>';
			echo '<td class="text-right">'.number_format($item['rate'], 2).' DZD</td>';
			echo '<td class="text-right"><strong>'.number_format($item['amount'], 2).' DZD</strong></td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
		
		echo '<hr>';
		
		echo '<h4><i class="fa fa-history"></i> Payment Installments History</h4>';
		
		if(!empty($payments)) {
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
			
			foreach($payments as $payment) {
				echo '<tr>';
				echo '<td class="text-center">';
				echo '<span class="label label-primary" style="font-size: 14px;">#'.$payment['installment_number'].'</span>';
				if($payment['remaining_balance'] == 0) {
					echo '<br><small class="text-success"><i class="fa fa-check-circle"></i> Final</small>';
				}
				echo '</td>';
				echo '<td><i class="fa fa-calendar"></i> '.date('d-m-Y', $payment['payment_date']).'<br><i class="fa fa-clock-o"></i> '.date('h:i A', $payment['payment_date']).'</td>';
				echo '<td class="text-right"><strong style="color: #00a65a; font-size: 16px;">'.number_format($payment['payment_amount'], 2).' DZD</strong></td>';
				
				if($payment['remaining_balance'] > 0) {
					echo '<td class="text-right"><span class="text-danger"><strong>'.number_format($payment['remaining_balance'], 2).' DZD</strong></span></td>';
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
				echo '<td><span class="label label-'.$badge_color.'">'.ucfirst(str_replace('_', ' ', $payment['payment_method'])).'</span></td>';
				
				echo '<td>'.(!empty($payment['payment_notes']) ? $payment['payment_notes'] : '<em class="text-muted">No notes</em>').'</td>';
				echo '</tr>';
			}
			
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			
			$progress_percentage = ($order['paid_amount'] / $order['net_amount']) * 100;
			echo '<div style="margin-top: 20px;">';
			echo '<h5><i class="fa fa-bar-chart"></i> Payment Progress</h5>';
			echo '<div class="progress" style="height: 30px; margin-bottom: 10px;">';
			echo '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: '.$progress_percentage.'%; line-height: 30px; font-size: 14px;">';
			echo number_format($progress_percentage, 1).'% Paid';
			echo '</div>';
			echo '</div>';
			echo '<div class="row">';
			echo '<div class="col-sm-4 text-center">';
			echo '<div style="padding: 10px; background: #d4edda; border-radius: 5px;">';
			echo '<small>Total Paid</small><br><strong style="color: #155724; font-size: 18px;">'.number_format($order['paid_amount'], 2).' DZD</strong>';
			echo '</div>';
			echo '</div>';
			echo '<div class="col-sm-4 text-center">';
			echo '<div style="padding: 10px; background: #f8d7da; border-radius: 5px;">';
			echo '<small>Remaining</small><br><strong style="color: #721c24; font-size: 18px;">'.number_format($order['due_amount'], 2).' DZD</strong>';
			echo '</div>';
			echo '</div>';
			echo '<div class="col-sm-4 text-center">';
			echo '<div style="padding: 10px; background: #d1ecf1; border-radius: 5px;">';
			echo '<small>Installments</small><br><strong style="color: #0c5460; font-size: 18px;">'.count($payments).'</strong>';
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
		
		if($order['due_amount'] > 0 && in_array('updateOrder', $this->permission)) {
			echo '<div class="text-center" style="margin-top: 20px;">';
			echo '<button type="button" class="btn btn-success btn-lg" onclick="openAddPaymentModal('.$order['id'].', '.$order['due_amount'].')">';
			echo '<i class="fa fa-plus-circle"></i> Add Payment Installment';
			echo '</button>';
			echo '</div>';
		}
	}

	public function addPayment()
	{
		if(!in_array('updateOrder', $this->permission)) {
			echo json_encode(array('success' => false, 'message' => 'Permission denied'));
			return;
		}
		
		$order_id = $this->input->post('order_id');
		$payment_amount = $this->input->post('payment_amount');
		$payment_method = $this->input->post('payment_method');
		$payment_notes = $this->input->post('payment_notes');
		
		$response = array();
		
		if(!$order_id || !$payment_amount || $payment_amount <= 0) {
			$response['success'] = false;
			$response['message'] = 'Invalid payment amount';
			echo json_encode($response);
			return;
		}
		
		$result = $this->model_orders->addPaymentInstallment($order_id, $payment_amount, $payment_method, $payment_notes);
		
		if($result['success']) {
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

	public function invoice($id)
	{
		$payments = $this->model_orders->getOrderPayments($id);

		if(!in_array('viewOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
		if($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y', $order_data['date_time']);
			$order_time = date('h:i A', $order_data['date_time']);
			
			if($order_data['paid_status'] == 1) {
				$paid_status = "Fully Paid";
				$status_color = "#28a745";
			} elseif($order_data['paid_status'] == 3) {
				$paid_status = "Partially Paid";
				$status_color = "#ffc107";
			} else {
				$paid_status = "Unpaid";
				$status_color = "#dc3545";
			}

			$html = '<!DOCTYPE html>
			<html>
			<head>
				<meta charset="UTF-8">
				<title>Invoice - '.$order_data['bill_no'].'</title>
				<style>
					body { font-family: Arial, sans-serif; margin: 20px; }
					.invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
					.header { text-align: center; margin-bottom: 30px; }
					.company-name { font-size: 28px; font-weight: bold; color: #333; }
					.invoice-title { font-size: 24px; color: #666; margin-top: 10px; }
					.info-section { margin: 20px 0; }
					.info-row { display: table; width: 100%; margin-bottom: 10px; }
					.info-col { display: table-cell; width: 50%; vertical-align: top; }
					.label { font-weight: bold; color: #333; }
					.value { color: #666; }
					table { width: 100%; border-collapse: collapse; margin: 20px 0; }
					th { background: #f8f9fa; padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold; }
					td { padding: 10px; border: 1px solid #ddd; }
					.text-right { text-align: right; }
					.totals { margin-top: 20px; float: right; width: 300px; }
					.totals table { margin: 0; }
					.total-row { font-weight: bold; font-size: 16px; background: #f8f9fa; }
					.status-badge { display: inline-block; padding: 5px 15px; border-radius: 3px; color: white; font-weight: bold; }
					.footer { margin-top: 50px; text-align: center; color: #999; font-size: 12px; }
					@media print {
						.no-print { display: none; }
						body { margin: 0; }
					}
				</style>
			</head>
			<body>
				<div class="invoice-box">
					<div class="header">
						<div class="company-name">'.$company_info['company_name'].'</div>
						<div style="color: #666;">'.$company_info['address'].'</div>
						<div style="color: #666;">Tel: '.$company_info['phone'].'</div>
						<div class="invoice-title">FACTURE / INVOICE</div>
					</div>

					<div class="info-section">
						<div class="info-row">
							<div class="info-col">
								<div><span class="label">N° Facture:</span> <span class="value">'.$order_data['bill_no'].'</span></div>
								<div><span class="label">Date:</span> <span class="value">'.$order_date.' '.$order_time.'</span></div>
								<div><span class="label">Statut:</span> <span class="status-badge" style="background:'.$status_color.'">'.$paid_status.'</span></div>
							</div>
							<div class="info-col" style="text-align: right;">
								<div style="font-weight: bold; margin-bottom: 5px;">FACTURÉ À:</div>
								<div><strong>'.$order_data['customer_name'].'</strong></div>
								<div>'.$order_data['customer_address'].'</div>
								<div>Tél: '.$order_data['customer_phone'].'</div>
							</div>
						</div>
					</div>

					<table>
						<thead>
							<tr>
								<th style="width: 50%">Produit / Product</th>
								<th style="width: 15%; text-align: center;">Qté</th>
								<th style="width: 17%; text-align: right;">Prix Unit.</th>
								<th style="width: 18%; text-align: right;">Montant</th>
							</tr>
						</thead>
						<tbody>';

			foreach ($orders_items as $key => $val) {
				$product_data = $this->model_products->getProductData($val['product_id']); 
				
				$html .= '<tr>
					<td>'.$product_data['name'].'</td>
					<td style="text-align: center;">'.$val['qty'].'</td>
					<td style="text-align: right;">'.number_format($val['rate'], 2).' DZD</td>
					<td style="text-align: right;">'.number_format($val['amount'], 2).' DZD</td>
				</tr>';
			}
					
			$html .= '</tbody>
					</table>

					<div class="totals">
						<table>
							<tr>
								<td><strong>Montant Brut:</strong></td>
								<td class="text-right">'.number_format($order_data['gross_amount'], 2).' DZD</td>
							</tr>';
			
			if($order_data['discount'] > 0) {
				$html .= '<tr>
					<td><strong>Remise:</strong></td>
					<td class="text-right">- '.number_format($order_data['discount'], 2).' DZD</td>
				</tr>';
			}
			
			$html .= '<tr class="total-row">
								<td><strong>TOTAL:</strong></td>
								<td class="text-right">'.number_format($order_data['net_amount'], 2).' DZD</td>
							</tr>
							<tr style="background: #d4edda;">
								<td><strong>Montant Payé:</strong></td>
								<td class="text-right" style="color: #155724;">'.number_format($order_data['paid_amount'], 2).' DZD</td>
							</tr>';
			
			if($order_data['due_amount'] > 0) {
				$html .= '<tr style="background: #f8d7da;">
					<td><strong>Reste à Payer:</strong></td>
					<td class="text-right" style="color: #721c24; font-weight: bold;">'.number_format($order_data['due_amount'], 2).' DZD</td>
				</tr>';
			}
			
			$html .= '</table>
					</div>
					<div style="clear: both;"></div>

					<div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">';
			
			if($order_data['payment_method']) {
				$html .= '<div><strong>Mode de Paiement:</strong> '.ucfirst(str_replace('_', ' ', $order_data['payment_method'])).'</div>';
			}
			
			if($order_data['payment_notes']) {
				$html .= '<div><strong>Notes:</strong> '.$order_data['payment_notes'].'</div>';
			}
			
			$html .= '</div>

					<div class="footer">
						<p>Merci pour votre confiance!</p>
						<p>'.$company_info['company_name'].' - '.$company_info['country'].'</p>
						<button class="no-print" onclick="window.print()" style="margin-top: 20px; padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
							<i class="fa fa-print"></i> Imprimer / Print
						</button>
					</div>
				</div>
			</body>
			</html>';

			echo $html;
		}
	}
}