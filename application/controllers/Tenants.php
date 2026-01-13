<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tenants extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tenant');
        $this->load->model('model_tenant_auth');
    }

    /**
     * List all tenants
     */
    public function index()
    {
        if(!isset($user_permission['viewTenant'])) {
            redirect('dashboard', 'refresh');
        }

        $this->data['page_title'] = 'Manage Merchants';
        
        // Get all tenants from master database
        $master_db = $this->tenant->init_master_db();
        $query = $master_db->query("SELECT * FROM tenants ORDER BY created_at DESC");
        $this->data['tenants'] = $query->result_array();
        
        $this->render_template('tenants/index', $this->data);
    }

    /**
     * Create new tenant form
     */
    public function create()
    {
        if(!isset($user_permission['createTenant'])) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('tenant_name', 'Merchant Name', 'trim|required');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('plan', 'Plan', 'required');
        $this->form_validation->set_rules('admin_email', 'Admin Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('admin_username', 'Admin Username', 'trim|required');
        $this->form_validation->set_rules('admin_password', 'Admin Password', 'trim|required|min_length[6]');

        if ($this->form_validation->run() == TRUE) {
            // Create tenant
            $result = $this->tenant->create_tenant(
                $this->input->post('tenant_name'),
                $this->input->post('company_name'),
                $this->input->post('plan')
            );
            
            if($result['success']) {
                // Create user in master database
                $master_db = $this->tenant->init_master_db();
                
                $admin_email = $this->input->post('admin_email');
                
                // Check if user already exists
                $check_user = $master_db->query("SELECT id FROM users WHERE email = ?", array($admin_email));
                
                if($check_user->num_rows() > 0) {
                    // User exists, link to tenant
                    $user_id = $check_user->row()->id;
                } else {
                    // Create new user
                    $user_data = array(
                        'username' => $this->input->post('admin_username'),
                        'email' => $admin_email,
                        'password' => password_hash($this->input->post('admin_password'), PASSWORD_DEFAULT)
                    );
                    $master_db->insert('users', $user_data);
                    $user_id = $master_db->insert_id();
                }
                
                // Link user to tenant
                $user_tenant_data = array(
                    'user_id' => $user_id,
                    'tenant_id' => $result['tenant_id'],
                    'role' => 'admin'
                );
                $master_db->insert('user_tenant', $user_tenant_data);
                
                $this->session->set_flashdata('success', 'Merchant account created successfully!');
                redirect('tenants', 'refresh');
            } else {
                $this->data['errors'] = 'Failed to create merchant account';
            }
        }

        $this->data['page_title'] = 'Create New Merchant';
        $this->render_template('tenants/create', $this->data);
    }

    /**
     * Edit tenant
     */
    public function edit($id = null)
    {
        if(!isset($user_permission['updateTenant'])) {
            redirect('dashboard', 'refresh');
        }

        if(!$id) {
            redirect('tenants', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();
        $query = $master_db->query("SELECT * FROM tenants WHERE id = ?", array($id));
        
        if($query->num_rows() == 0) {
            $this->session->set_flashdata('error', 'Merchant not found');
            redirect('tenants', 'refresh');
        }

        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('plan', 'Plan', 'required');

        if ($this->form_validation->run() == TRUE) {
            $update_data = array(
                'company_name' => $this->input->post('company_name'),
                'status' => $this->input->post('status'),
                'plan' => $this->input->post('plan')
            );
            
            $master_db->where('id', $id);
            $master_db->update('tenants', $update_data);
            
            $this->session->set_flashdata('success', 'Merchant updated successfully!');
            redirect('tenants', 'refresh');
        }

        $this->data['tenant'] = $query->row_array();
        $this->data['page_title'] = 'Edit Merchant';
        $this->render_template('tenants/edit', $this->data);
    }

    /**
     * Delete tenant (WARNING: This will delete the entire database!)
     */
    public function delete($id = null)
    {
        if(!isset($user_permission['deleteTenant'])) {
            redirect('dashboard', 'refresh');
        }

        if(!$id) {
            redirect('tenants', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();
        $query = $master_db->query("SELECT * FROM tenants WHERE id = ?", array($id));
        
        if($query->num_rows() > 0) {
            $tenant = $query->row_array();
            $database_name = $tenant['database_name'];
            
            // Drop the tenant database
            $master_db->query("DROP DATABASE IF EXISTS `{$database_name}`");
            
            // Delete tenant record
            $master_db->query("DELETE FROM tenants WHERE id = ?", array($id));
            
            // Delete user-tenant links
            $master_db->query("DELETE FROM user_tenant WHERE tenant_id = ?", array($id));
            
            $this->session->set_flashdata('success', 'Merchant deleted successfully!');
        }
        
        redirect('tenants', 'refresh');
    }

    /**
     * View tenant users
     */
    public function users($tenant_id = null)
    {
        if(!$tenant_id) {
            redirect('tenants', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();
        
        // Get tenant info
        $tenant_query = $master_db->query("SELECT * FROM tenants WHERE id = ?", array($tenant_id));
        if($tenant_query->num_rows() == 0) {
            redirect('tenants', 'refresh');
        }
        
        // Get users for this tenant
        $users_query = $master_db->query(
            "SELECT u.*, ut.role 
             FROM users u 
             INNER JOIN user_tenant ut ON u.id = ut.user_id 
             WHERE ut.tenant_id = ?", 
            array($tenant_id)
        );
        
        $this->data['tenant'] = $tenant_query->row_array();
        $this->data['users'] = $users_query->result_array();
        $this->data['page_title'] = 'Merchant Users';
        $this->render_template('tenants/users', $this->data);
    }
}
