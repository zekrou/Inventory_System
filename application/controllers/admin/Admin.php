<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->data['page_title'] = 'System Administration';

        $user_data = $this->session->userdata();

        if (empty($user_data['logged_in']) || $user_data['logged_in'] !== TRUE) {
            redirect('auth/login', 'refresh');
        }

        if (!isset($user_data['user_type']) || $user_data['user_type'] != 'system_admin') {
            redirect('dashboard', 'refresh');
        }

        $this->data['user_data'] = $user_data;
        $this->load->library('tenant');
    }

    public function index()
    {
        redirect('admin/dashboard', 'refresh');
    }

    public function dashboard()
    {
        $this->data['page_title'] = 'System Administration Dashboard';
        $master_db = $this->tenant->init_master_db();

        $tenants = $master_db->query("SELECT * FROM tenants ORDER BY created_at DESC")->result_array();
        $this->data['tenants'] = $tenants;
        $this->data['total_tenants'] = count($tenants);
        $this->data['active_tenants'] = $master_db->query("SELECT COUNT(*) as total FROM tenants WHERE status = 'active'")->row()->total;
        $this->data['inactive_tenants'] = $this->data['total_tenants'] - $this->data['active_tenants'];
        $this->data['total_users'] = $master_db->query("SELECT COUNT(*) as total FROM users")->row()->total;
        $this->data['recent_tenants'] = array_slice($tenants, 0, 5);

        $this->render_template_admin('admin/dashboard', $this->data);
    }

    public function tenants()
    {
        $this->data['page_title'] = 'Manage Merchants';
        $master_db = $this->tenant->init_master_db();

        $query = "SELECT t.*, COUNT(ut.user_id) as user_count
                  FROM tenants t
                  LEFT JOIN user_tenant ut ON t.id = ut.tenant_id
                  GROUP BY t.id
                  ORDER BY t.created_at DESC";

        $this->data['tenants'] = $master_db->query($query)->result_array();
        $this->data['total_tenants'] = count($this->data['tenants']);
        $this->data['total_users'] = $master_db->query("SELECT COUNT(*) as total FROM users")->row()->total;

        $this->render_template_admin('admin/tenants', $this->data);
    }

    public function users()
    {
        $this->data['page_title'] = 'Manage Users';
        $master_db = $this->tenant->init_master_db();

        $query = "SELECT u.*, 
                     COALESCE(GROUP_CONCAT(DISTINCT t.company_name SEPARATOR ', '), 'N/A') as tenant_names,
                     COUNT(DISTINCT ut.tenant_id) as tenant_count,
                     COALESCE(GROUP_CONCAT(DISTINCT ut.role SEPARATOR ', '), 'N/A') as roles
              FROM users u
              LEFT JOIN user_tenant ut ON u.id = ut.user_id
              LEFT JOIN tenants t ON ut.tenant_id = t.id
              GROUP BY u.id
              ORDER BY u.id DESC";

        $this->data['users'] = $master_db->query($query)->result_array();
        $this->data['total_users'] = count($this->data['users']);
        $this->data['total_tenants'] = $master_db->query("SELECT COUNT(*) as total FROM tenants")->row()->total;

        $this->render_template_admin('admin/users', $this->data);
    }

    private function render_template_admin($view, $data = array())
    {
        $data = array_merge($this->data, $data);
        $this->load->view('templates/admin/header', $data);
        $this->load->view('templates/admin/side_menubar', $data);
        $this->load->view($view, $data);
        $this->load->view('templates/admin/footer', $data);
    }

    public function create_tenant()
    {
        $this->data['page_title'] = 'Create New Merchant';

        $this->form_validation->set_rules('tenant_name', 'Merchant Name', 'trim|required|alpha_dash');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('plan', 'Plan', 'required|in_list[free,basic,premium,enterprise]');
        $this->form_validation->set_rules('admin_email', 'Admin Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('admin_username', 'Admin Username', 'trim|required|alpha_dash|min_length[4]');
        $this->form_validation->set_rules('admin_password', 'Admin Password', 'trim|required|min_length[6]');

        if ($this->form_validation->run() == TRUE) {
            $tenant_name = $this->input->post('tenant_name');
            $company_name = $this->input->post('company_name');
            $plan = $this->input->post('plan');
            $admin_email = $this->input->post('admin_email');
            $admin_username = $this->input->post('admin_username');
            $admin_password = $this->input->post('admin_password');

            $result = $this->tenant->create_tenant($tenant_name, $company_name, $plan, $admin_email);

            if ($result['success']) {
                $tenant_id = $result['tenant_id'];
                $master_db = $this->tenant->init_master_db();

                $check_user = $master_db->query("SELECT id FROM users WHERE email = ?", [$admin_email]);

                if ($check_user->num_rows() > 0) {
                    $user_id = $check_user->row()->id;
                } else {
                    $user_data = [
                        'username' => $admin_username,
                        'email' => $admin_email,
                        'password' => password_hash($admin_password, PASSWORD_DEFAULT),
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $master_db->insert('users', $user_data);
                    $user_id = $master_db->insert_id();
                }

                $user_tenant_data = [
                    'user_id' => $user_id,
                    'tenant_id' => $tenant_id,
                    'role' => 'admin'
                ];
                $master_db->insert('user_tenant', $user_tenant_data);

                $this->session->set_flashdata('success', 'Merchant created successfully! Database: ' . $result['database_name'] . ' | Login: ' . $admin_email);
                redirect('admin/tenants', 'refresh');
            } else {
                $this->data['errors'] = $result['message'];
            }
        }

        $this->render_template_admin('admin/tenants/create', $this->data);
    }

    private function get_all_permissions()
    {
        return [
            'createProduct', 'updateProduct', 'viewProduct', 'deleteProduct',
            'createBrand', 'updateBrand', 'viewBrand', 'deleteBrand',
            'createCategory', 'updateCategory', 'viewCategory', 'deleteCategory',
            'createStock', 'updateStock', 'viewStock', 'deleteStock',
            'createPurchase', 'updatePurchase', 'viewPurchase', 'deletePurchase',
            'createOrder', 'updateOrder', 'viewOrder', 'deleteOrder',
            'createCustomer', 'updateCustomer', 'viewCustomer', 'deleteCustomer',
            'createSupplier', 'updateSupplier', 'viewSupplier', 'deleteSupplier',
            'createUser', 'updateUser', 'viewUser', 'deleteUser',
            'createGroup', 'updateGroup', 'viewGroup', 'deleteGroup',
            'createCompany', 'updateCompany', 'viewCompany', 'viewReports'
        ];
    }

    public function edit_tenant($id = null)
    {
        if (!$id) {
            redirect('admin/tenants', 'refresh');
        }

        $this->data['page_title'] = 'Edit Merchant';
        $master_db = $this->tenant->init_master_db();
        $query = $master_db->query("SELECT * FROM tenants WHERE id = ?", [$id]);

        if ($query->num_rows() == 0) {
            $this->session->set_flashdata('error', 'Merchant not found');
            redirect('admin/tenants', 'refresh');
        }

        $this->data['tenant'] = $query->row_array();

        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,suspended]');
        $this->form_validation->set_rules('plan', 'Plan', 'required|in_list[free,basic,premium,enterprise]');

        if ($this->form_validation->run() == TRUE) {
            $update_data = [
                'company_name' => $this->input->post('company_name'),
                'status' => $this->input->post('status'),
                'plan' => $this->input->post('plan')
            ];

            $master_db->where('id', $id);
            $master_db->update('tenants', $update_data);

            $this->session->set_flashdata('success', 'Merchant updated successfully!');
            redirect('admin/tenants', 'refresh');
        }

        $this->render_template_admin('admin/tenants/edit', $this->data);
    }

    public function delete_tenant($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'No merchant ID provided');
            redirect('admin/tenants', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();
        $query = $master_db->query("SELECT * FROM tenants WHERE id = ?", array($id));

        if ($query->num_rows() > 0) {
            $tenant = $query->row_array();
            $database_name = $tenant['database_name'];
            $tenant_name = $tenant['tenant_name'];

            try {
                $users_query = $master_db->query("SELECT user_id FROM user_tenant WHERE tenant_id = ?", array($id));

                $user_ids = array();
                foreach ($users_query->result_array() as $row) {
                    $user_ids[] = $row['user_id'];
                }

                $master_db->query("DROP DATABASE IF EXISTS `{$database_name}`");
                $master_db->query("DELETE FROM user_tenant WHERE tenant_id = ?", array($id));
                $master_db->query("DELETE FROM tenants WHERE id = ?", array($id));

                if (!empty($user_ids)) {
                    foreach ($user_ids as $user_id) {
                        $check = $master_db->query("SELECT COUNT(*) as count FROM user_tenant WHERE user_id = ?", array($user_id))->row_array();
                        $user_info = $master_db->query("SELECT user_type FROM users WHERE id = ?", array($user_id))->row_array();

                        if ($check['count'] == 0 && $user_info && $user_info['user_type'] != 'admin') {
                            $master_db->query("DELETE FROM users WHERE id = ?", array($user_id));
                        }
                    }
                }

                $this->session->set_flashdata('success', '✅ Merchant "' . $tenant_name . '", database "' . $database_name . '" and associated users deleted successfully!');
            } catch (Exception $e) {
                log_message('error', 'Failed to delete merchant: ' . $e->getMessage());
                $this->session->set_flashdata('error', '❌ Error deleting merchant: ' . $e->getMessage());
            }
        } else {
            $this->session->set_flashdata('error', '❌ Merchant not found');
        }

        redirect('admin/tenants', 'refresh');
    }

    public function tenant_users($tenant_id = null)
    {
        if (!$tenant_id) {
            redirect('admin/tenants', 'refresh');
        }

        $this->data['page_title'] = 'Merchant Users';
        $master_db = $this->tenant->init_master_db();

        $tenant_query = $master_db->query("SELECT * FROM tenants WHERE id = ?", [$tenant_id]);
        if ($tenant_query->num_rows() == 0) {
            redirect('admin/tenants', 'refresh');
        }

        $this->data['tenant'] = $tenant_query->row_array();

        $users_query = $master_db->query(
            "SELECT u.*, ut.role FROM users u 
             INNER JOIN user_tenant ut ON u.id = ut.user_id 
             WHERE ut.tenant_id = ?",
            [$tenant_id]
        );

        $this->data['users'] = $users_query->result_array();
        $this->data['total_tenants'] = $master_db->query("SELECT COUNT(*) as total FROM tenants")->row()->total;
        $this->data['total_users'] = $master_db->query("SELECT COUNT(*) as total FROM users")->row()->total;

        $this->render_template_admin('admin/tenants/users', $this->data);
    }

    public function settings()
    {
        if ($this->session->userdata('user_type') != 'system_admin') {
            redirect('dashboard', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();

        if ($this->input->post()) {
            $settings = array(
                'app_name' => $this->input->post('app_name'),
                'app_email' => $this->input->post('app_email'),
                'timezone' => $this->input->post('timezone'),
                'date_format' => $this->input->post('date_format'),
                'currency' => $this->input->post('currency'),
                'backup_enabled' => $this->input->post('backup_enabled') ? 1 : 0,
                'backup_frequency' => $this->input->post('backup_frequency'),
                'max_tenants' => $this->input->post('max_tenants'),
                'max_users_per_tenant' => $this->input->post('max_users_per_tenant'),
                'maintenance_mode' => $this->input->post('maintenance_mode') ? 1 : 0,
                'allow_registration' => $this->input->post('allow_registration') ? 1 : 0,
                'session_timeout' => $this->input->post('session_timeout'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            foreach ($settings as $key => $value) {
                $check = $master_db->query("SELECT * FROM system_settings WHERE setting_key = ?", array($key));

                if ($check->num_rows() > 0) {
                    $master_db->query("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?", array($value, $key));
                } else {
                    $master_db->query("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)", array($key, $value));
                }
            }

            $this->session->set_flashdata('success', '✅ System settings updated successfully!');
            redirect('admin/settings', 'refresh');
        }

        $result = $master_db->query("SELECT * FROM system_settings")->result_array();
        $this->data['settings'] = array();
        foreach ($result as $row) {
            $this->data['settings'][$row['setting_key']] = $row['setting_value'];
        }

        $this->data['stats'] = array(
            'total_tenants' => $master_db->query("SELECT COUNT(*) as count FROM tenants")->row()->count,
            'total_users' => $master_db->query("SELECT COUNT(*) as count FROM users")->row()->count,
            'active_tenants' => $master_db->query("SELECT COUNT(*) as count FROM tenants WHERE status = 'active'")->row()->count,
            'database_size' => $this->get_database_size($master_db)
        );

        $this->data['page_title'] = 'System Settings';
        $this->render_template_admin('admin/settings', $this->data);
    }

    private function get_database_size($db)
    {
        $result = $db->query("
            SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
            FROM information_schema.TABLES 
            WHERE table_schema = 'stock_master'
        ")->row();

        return round($result->size_mb, 2) . ' MB';
    }

    public function backup()
    {
        if ($this->session->userdata('user_type') != 'system_admin') {
            redirect('dashboard', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();

        if ($this->input->post('create_backup')) {
            $backup_type = $this->input->post('backup_type');
            $this->load->dbutil();
            $backup_path = FCPATH . 'backups/';

            if (!is_dir($backup_path)) {
                mkdir($backup_path, 0777, true);
            }

            try {
                if ($backup_type == 'master') {
                    $prefs = array('format' => 'zip', 'filename' => 'stock_master_' . date('Y-m-d_H-i-s') . '.sql');
                    $backup = $this->dbutil->backup($prefs);
                    $filename = 'master_backup_' . date('Y-m-d_H-i-s') . '.zip';
                    file_put_contents($backup_path . $filename, $backup);

                    $master_db->query("INSERT INTO backup_history (backup_name, backup_type, file_size, created_by, created_at) VALUES (?, ?, ?, ?, ?)",
                        array($filename, 'master', filesize($backup_path . $filename), $this->session->userdata('id'), date('Y-m-d H:i:s')));
                } else {
                    $tenants = $master_db->query("SELECT * FROM tenants")->result_array();

                    foreach ($tenants as $tenant) {
                        $tenant_db = $this->load->database($tenant['database_name'], TRUE);
                        $prefs = array('format' => 'zip', 'filename' => $tenant['database_name'] . '_' . date('Y-m-d_H-i-s') . '.sql');
                        $backup = $this->dbutil->backup($prefs);
                        $filename = 'tenant_' . $tenant['id'] . '_' . date('Y-m-d_H-i-s') . '.zip';
                        file_put_contents($backup_path . $filename, $backup);

                        $master_db->query("INSERT INTO backup_history (backup_name, backup_type, tenant_id, file_size, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?)",
                            array($filename, 'tenant', $tenant['id'], filesize($backup_path . $filename), $this->session->userdata('id'), date('Y-m-d H:i:s')));
                    }
                }

                $this->session->set_flashdata('success', '✅ Backup created successfully!');
            } catch (Exception $e) {
                $this->session->set_flashdata('error', '❌ Backup failed: ' . $e->getMessage());
            }

            redirect('admin/backup', 'refresh');
        }

        if ($this->input->get('delete')) {
            $backup_id = $this->input->get('delete');
            $backup = $master_db->query("SELECT * FROM backup_history WHERE id = ?", array($backup_id))->row_array();

            if ($backup) {
                $filepath = FCPATH . 'backups/' . $backup['backup_name'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                $master_db->query("DELETE FROM backup_history WHERE id = ?", array($backup_id));
                $this->session->set_flashdata('success', '✅ Backup deleted!');
            }

            redirect('admin/backup', 'refresh');
        }

        if ($this->input->get('download')) {
            $backup_id = $this->input->get('download');
            $backup = $master_db->query("SELECT * FROM backup_history WHERE id = ?", array($backup_id))->row_array();

            if ($backup) {
                $filepath = FCPATH . 'backups/' . $backup['backup_name'];
                if (file_exists($filepath)) {
                    $this->load->helper('download');
                    force_download($filepath, NULL);
                }
            }
        }

        $this->data['backups'] = $master_db->query(
            "SELECT bh.*, u.username, t.tenant_name 
             FROM backup_history bh
             LEFT JOIN users u ON bh.created_by = u.id
             LEFT JOIN tenants t ON bh.tenant_id = t.id
             ORDER BY bh.created_at DESC"
        )->result_array();

        $this->data['page_title'] = 'Database Backup';
        $this->render_template_admin('admin/backup', $this->data);
    }

    public function logs()
    {
        if ($this->session->userdata('user_type') != 'system_admin') {
            redirect('dashboard', 'refresh');
        }

        $master_db = $this->tenant->init_master_db();

        $filter_user = $this->input->get('user_id');
        $filter_tenant = $this->input->get('tenant_id');
        $filter_action = $this->input->get('action_type');
        $filter_date = $this->input->get('date');

        $where = array();
        $params = array();

        if ($filter_user) {
            $where[] = "al.user_id = ?";
            $params[] = $filter_user;
        }

        if ($filter_tenant) {
            $where[] = "al.tenant_id = ?";
            $params[] = $filter_tenant;
        }

        if ($filter_action) {
            $where[] = "al.action_type = ?";
            $params[] = $filter_action;
        }

        if ($filter_date) {
            $where[] = "DATE(al.created_at) = ?";
            $params[] = $filter_date;
        }

        $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $this->data['logs'] = $master_db->query(
            "SELECT al.*, u.username, u.email, t.tenant_name 
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             LEFT JOIN tenants t ON al.tenant_id = t.id
             $where_clause
             ORDER BY al.created_at DESC
             LIMIT 500",
            $params
        )->result_array();

        $this->data['users'] = $master_db->query("SELECT id, username, email FROM users ORDER BY username")->result_array();
        $this->data['tenants'] = $master_db->query("SELECT id, tenant_name FROM tenants ORDER BY tenant_name")->result_array();
        $this->data['action_types'] = array('login', 'logout', 'create', 'update', 'delete', 'view', 'export');

        $this->data['stats'] = array(
            'today_logins' => $master_db->query("SELECT COUNT(*) as count FROM activity_logs WHERE action_type = 'login' AND DATE(created_at) = CURDATE()")->row()->count,
            'today_actions' => $master_db->query("SELECT COUNT(*) as count FROM activity_logs WHERE DATE(created_at) = CURDATE()")->row()->count,
            'total_logs' => $master_db->query("SELECT COUNT(*) as count FROM activity_logs")->row()->count,
            'failed_logins' => $master_db->query("SELECT COUNT(*) as count FROM activity_logs WHERE action_type = 'failed_login' AND DATE(created_at) = CURDATE()")->row()->count
        );

        $this->data['page_title'] = 'Activity Logs';
        $this->render_template_admin('admin/logs', $this->data);
    }

    public function log_activity($user_id, $tenant_id, $action_type, $description, $ip_address = null)
    {
        $master_db = $this->tenant->init_master_db();

        if (!$ip_address) {
            $ip_address = $this->input->ip_address();
        }

        $master_db->query(
            "INSERT INTO activity_logs (user_id, tenant_id, action_type, description, ip_address, user_agent, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            array($user_id, $tenant_id, $action_type, $description, $ip_address, $this->input->user_agent(), date('Y-m-d H:i:s'))
        );
    }
}
