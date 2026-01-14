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

    /**
     * Display company form (create or update)
     */
    public function index()
    {
        if (!isset($this->permission['updateCompany'])) {
            redirect('dashboard', 'refresh');
        }

        // Validation rules
        $this->form_validation->set_rules('company_name', 'Company name', 'trim|required');
        $this->form_validation->set_rules('service_charge_value', 'Service Charge', 'trim|numeric');
        $this->form_validation->set_rules('vat_charge_value', 'VAT Charge', 'trim|numeric');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('message', 'Message', 'trim');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'company_name' => $this->input->post('company_name'),
                'service_charge_value' => $this->input->post('service_charge_value') ?: 0,
                'vat_charge_value' => $this->input->post('vat_charge_value') ?: 0,
                'address' => $this->input->post('address'),
                'phone' => $this->input->post('phone'),
                'country' => $this->input->post('country'),
                'message' => $this->input->post('message'),
                'currency' => $this->input->post('currency') ?: 'DZD'
            );

            // Check if company exists
            if ($this->model_company->companyExists(1)) {
                // UPDATE existing company
                $update = $this->model_company->update($data, 1);
                if ($update) {
                    $this->session->set_flashdata('success', 'Company information updated successfully!');
                    redirect('company/', 'refresh');
                } else {
                    $this->session->set_flashdata('errors', 'Error updating company information!');
                    redirect('company/', 'refresh');
                }
            } else {
                // CREATE new company with id = 1
                $data['id'] = 1;
                $create = $this->model_company->create($data);
                if ($create) {
                    $this->session->set_flashdata('success', 'Company information created successfully!');
                    redirect('company/', 'refresh');
                } else {
                    $this->session->set_flashdata('errors', 'Error creating company information!');
                    redirect('company/', 'refresh');
                }
            }
        } else {
            // Load form
            $this->data['currency_symbols'] = $this->currency();
            
            // Get existing company data or use defaults
            $company_data = $this->model_company->getCompanyData(1);
            
            if (empty($company_data)) {
                $company_data = array(
                    'id' => 1,
                    'company_name' => '',
                    'service_charge_value' => 0,
                    'vat_charge_value' => 0,
                    'address' => '',
                    'phone' => '',
                    'country' => 'Algeria',
                    'message' => 'Thank you for your business!',
                    'currency' => 'DZD'
                );
            }

            $this->data['company_data'] = $company_data;
            $this->render_template('company/index', $this->data);
        }
    }
}
