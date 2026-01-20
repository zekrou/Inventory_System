<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Customers';

		$this->load->model('model_customers');
	}

	/**
	 * Index - List all customers
	 */
	public function index()
	{
		if(!isset($this->permission['viewCustomer'])) {
			redirect('dashboard', 'refresh');
		}

		$result = $this->model_customers->getCustomerData();

		$this->data['results'] = $result;

		$this->render_template('customers/index', $this->data);
	}

	/**
	 * Fetch customer data for DataTable via AJAX
	 */
	public function fetchCustomerData()
	{
		$result = array('data' => array());

		$data = $this->model_customers->getCustomerData();
		foreach ($data as $key => $value) {

			// Buttons
			$buttons = '';

			if(isset($this->permission['updateCustomer'])) {
				$buttons .= '<button type="button" class="btn btn-default" onclick="editCustomer('.$value['id'].')" data-toggle="modal" data-target="#editCustomerModal"><i class="fa fa-pencil"></i></button>';	
			}
			
			if(isset($this->permission['deleteCustomer'])) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeCustomer('.$value['id'].')" data-toggle="modal" data-target="#removeCustomerModal"><i class="fa fa-trash"></i></button>';
			}

			// Customer type badge
			$type_badge = '';
			switch($value['customer_type']) {
				case 'super_wholesale':
					$type_badge = '<span class="label label-danger">Super Gros</span>';
					break;
				case 'wholesale':
					$type_badge = '<span class="label label-warning">Gros</span>';
					break;
				case 'retail':
					$type_badge = '<span class="label label-info">Détail</span>';
					break;
			}

			// Status
			$status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';

			// Balance color
			$balance = number_format($value['current_balance'], 2);
			$balance_class = ($value['current_balance'] > 0) ? 'text-danger' : 'text-success';
			$balance_display = '<span class="'.$balance_class.'">'.$balance.' DZD</span>';

			$result['data'][$key] = array(
				$value['customer_code'],
				$value['customer_name'],
				$type_badge,
				$value['phone'],
				$balance_display,
				number_format($value['credit_limit'], 2) . ' DZD',
				$status,
				$buttons
			);
		}

		echo json_encode($result);
	}

	/**
	 * Fetch single customer data by ID
	 */
	public function fetchCustomerDataById($id)
	{
		if($id) {
			$data = $this->model_customers->getCustomerData($id);
			echo json_encode($data);
		}

		return false;
	}

	/**
	 * Create new customer
	 */
	public function create()
	{
		if(!isset($this->permission['createCustomer'])) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required');
		$this->form_validation->set_rules('customer_type', 'Customer Type', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	
        	// Generate customer code
        	$customer_code = $this->model_customers->generateCustomerCode();

        	$data = array(
        		'customer_code' => $customer_code,
        		'customer_name' => $this->input->post('customer_name'),
        		'customer_type' => $this->input->post('customer_type'),
        		'phone' => $this->input->post('phone'),
        		'address' => $this->input->post('address'),
        		'email' => $this->input->post('email'),
        		'credit_limit' => $this->input->post('credit_limit') ? $this->input->post('credit_limit') : 0,
        		'payment_terms' => $this->input->post('payment_terms'),
        		'tax_number' => $this->input->post('tax_number'),
        		'notes' => $this->input->post('notes'),
        		'active' => $this->input->post('active'),
        	);

        	$create = $this->model_customers->create($data);
        	if($create) {
        		$response['success'] = true;
        		$response['messages'] = 'Client créé avec succès - Code: ' . $customer_code;
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Erreur lors de la création du client';			
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);
	}

	/**
	 * Update customer
	 */
	public function update($id)
	{
		if(!isset($this->permission['updateCustomer'])) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_customer_name', 'Customer Name', 'trim|required');
			$this->form_validation->set_rules('edit_customer_type', 'Customer Type', 'trim|required');
			$this->form_validation->set_rules('edit_phone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('edit_active', 'Active', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'customer_name' => $this->input->post('edit_customer_name'),
	        		'customer_type' => $this->input->post('edit_customer_type'),
	        		'phone' => $this->input->post('edit_phone'),
	        		'address' => $this->input->post('edit_address'),
	        		'email' => $this->input->post('edit_email'),
	        		'credit_limit' => $this->input->post('edit_credit_limit') ? $this->input->post('edit_credit_limit') : 0,
	        		'current_balance' => $this->input->post('edit_current_balance') ? $this->input->post('edit_current_balance') : 0,
	        		'payment_terms' => $this->input->post('edit_payment_terms'),
	        		'tax_number' => $this->input->post('edit_tax_number'),
	        		'notes' => $this->input->post('edit_notes'),
	        		'active' => $this->input->post('edit_active'),
	        	);

	        	$update = $this->model_customers->update($data, $id);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Client mis à jour avec succès';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Erreur lors de la mise à jour du client';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Erreur, veuillez actualiser la page!!';
		}

		echo json_encode($response);
	}

	/**
	 * Delete/Deactivate customer
	 */
	public function remove()
	{
		if(!isset($this->permission['deleteCustomer'])) {
			redirect('dashboard', 'refresh');
		}
		
		$customer_id = $this->input->post('customer_id');
		$response = array();
		
		if($customer_id) {
			$delete = $this->model_customers->remove($customer_id);

			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Client supprimé/désactivé avec succès";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Erreur lors de la suppression du client";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Actualiser la page!!";
		}

		echo json_encode($response);
	}

	/**
	 * View customer details with statistics
	 */
	public function view($id = null)
	{
		if(!isset($this->permission['viewCustomer'])) {
			redirect('dashboard', 'refresh');
		}

		if($id) {
			$customer_data = $this->model_customers->getCustomerData($id);
			$customer_stats = $this->model_customers->getCustomerStats($id);

			$this->data['customer_data'] = $customer_data;
			$this->data['customer_stats'] = $customer_stats;

			$this->render_template('customers/view', $this->data);
		}
		else {
			redirect('customers', 'refresh');
		}
	}
}
