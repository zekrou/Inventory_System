<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Company extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Company';

		$this->load->model('model_company');
	}

	/* 
    * It redirects to the company page and displays all the company information
    * It also updates the company information into the database if the 
    * validation for each input field is successfully valid
    */
	public function index()
	{
		if (!isset($this->permission['updateCompany'])) {
			redirect('dashboard', 'refresh');
		}

		$this->form_validation->set_rules('company_name', 'Company name', 'trim|required');
		$this->form_validation->set_rules('service_charge_value', 'Charge Amount', 'trim|integer');
		$this->form_validation->set_rules('vat_charge_value', 'Vat Charge', 'trim|integer');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('message', 'Message', 'trim|required');


		if ($this->form_validation->run() == TRUE) {
			// true case
			$data = array(
				'company_name' => $this->input->post('company_name'),
				'service_charge_value' => $this->input->post('service_charge_value'),
				'vat_charge_value' => $this->input->post('vat_charge_value'),
				'address' => $this->input->post('address'),
				'phone' => $this->input->post('phone'),
				'country' => $this->input->post('country'),
				'message' => $this->input->post('message'),
				'currency' => $this->input->post('currency')
			);

			$update = $this->model_company->update($data, 1);
			if ($update == true) {
				$this->session->set_flashdata('success', 'Successfully created');
				redirect('company/', 'refresh');
			} else {
				$this->session->set_flashdata('errors', 'Error occurred!!');
				redirect('company/index', 'refresh');
			}
		} else {
			// false case
			$this->data['currency_symbols'] = $this->currency();
			$company_data = $this->model_company->getCompanyData(1);

			// ✅ FIX: Ajouter des valeurs par défaut si la table company est vide
			if (empty($company_data)) {
				$company_data = array(
					'company_name' => '',
					'service_charge_value' => 0,
					'vat_charge_value' => 0,
					'address' => '',
					'phone' => '',
					'country' => '',
					'message' => '',
					'currency' => 'DZD'
				);
			}

			$this->data['company_data'] = $company_data;
			$this->render_template('company/index', $this->data);
		}
	}
}
