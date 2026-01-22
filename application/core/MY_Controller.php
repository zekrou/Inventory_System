<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->data['page_title'] = 'Inventory System';
        $this->data['errors'] = array();
    }

    public function logged_in()
    {
        $session_data = $this->session->userdata();
        if (!empty($session_data['logged_in']) && $session_data['logged_in'] == TRUE) {
            if (isset($session_data['user_type']) && $session_data['user_type'] == 'system_admin') {
                redirect('admin/dashboard', 'refresh');
            } else {
                redirect('dashboard', 'refresh');
            }
        }
    }

    public function not_logged_in()
    {
        $session_data = $this->session->userdata();
        if (empty($session_data['logged_in']) || $session_data['logged_in'] == FALSE) {
            redirect('auth/login', 'refresh');
        }
    }

    public function render_template($page = null, $data = array())
    {
        $data = array_merge($this->data, $data);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu', $data);
        $this->load->view('templates/side_menubar', $data);
        $this->load->view($page, $data);
        $this->load->view('templates/footer', $data);
    }
}

class Admin_Controller extends MY_Controller
{
    public $permission = array();
    public $user_permission = array();
    private $tenant_user_id = null; // ✅ Cache pour éviter les requêtes multiples

    public function __construct()
    {
        parent::__construct();

        $user_data = $this->session->userdata();

        if (empty($user_data['logged_in']) || $user_data['logged_in'] !== TRUE) {
            redirect('auth/login', 'refresh');
        }

        // Store user data
        $this->data['user_data'] = $user_data;

        // MERCHANT user
        if (isset($user_data['user_type']) && $user_data['user_type'] == 'merchant') {

            // ✅ Load tenant database
            if (!empty($user_data['tenant_id'])) {
                $this->load->library('tenant');

                // Switch to tenant database
                $tenant_db = $this->tenant->switch_tenant_db($user_data['tenant_id']);

                if ($tenant_db) {
                    // Replace $this->db with tenant connection
                    $this->db = $tenant_db;
                } else {
                    log_message('error', 'Failed to switch to tenant database for tenant_id: ' . $user_data['tenant_id']);
                    show_error('Unable to connect to tenant database');
                }
            }

            // ✅ Get and cache tenant user ID
            $this->tenant_user_id = $this->resolve_tenant_user_id($user_data['email']);

            // Get user permissions from tenant DB
            // Get user permissions from tenant DB
            $tenant_id = $this->get_tenant_user_id(); // ✅ CHANGÉ
            if ($tenant_id) { // ✅ CHANGÉ
                // Get user's group
                $query = $this->db->query("SELECT group_id FROM user_group WHERE user_id = ?", array($tenant_id)); // ✅ CHANGÉ

                if ($query->num_rows() > 0) {
                    $user_group_data = $query->row_array();
                    $group_id = $user_group_data['group_id'];

                    // Load group permissions
                    $query = $this->db->query("SELECT permission FROM `groups` WHERE id = ?", array($group_id));

                    if ($query->num_rows() > 0) {
                        $group_data = $query->row_array();

                        if (!empty($group_data['permission'])) {
                            $this->permission = @unserialize($group_data['permission']);

                            if ($this->permission === false || !is_array($this->permission)) {
                                $this->permission = array();
                            }
                        }
                    }
                }
            } else {
                $this->permission = array();
            }


            // Pass to template
            $this->data['user_permission'] = $this->permission;
            $this->user_permission = $this->permission;
        }
        // SYSTEM ADMIN
        elseif (isset($user_data['user_type']) && $user_data['user_type'] == 'system_admin') {
            $this->load->database();
            $this->permission = array();
            $this->data['user_permission'] = array();
        }
        // Charger les stats PreOrders pour sidebar
        if (isset($user_data['user_type']) && $user_data['user_type'] == 'merchant') {
            $this->load->model('model_preorders');
            $this->model_preorders->setTenantDb($this->db); // Utiliser la DB du tenant
            $stats = $this->model_preorders->getStatistics();
            $this->data['pending_preorders_count'] = $stats['pending_count'] ?? 0;
        } else {
            $this->data['pending_preorders_count'] = 0;
        }
    }

    /**
     * ✅ Resolve tenant user ID from master user email
     * Called once during __construct and cached
     * 
     * @param string $email Master user email
     * @return int Tenant user ID
     */
    private function resolve_tenant_user_id($email)
    {
        if (empty($email)) {
            log_message('error', 'Cannot resolve tenant user ID: email is empty');
            return 1; // Default to admin
        }

        // Try to find user by email in tenant DB
        $tenant_user_query = $this->db->query("SELECT id FROM users WHERE email = ? LIMIT 1", array($email));

        if ($tenant_user_query->num_rows() > 0) {
            $tenant_user = $tenant_user_query->row_array();
            return (int)$tenant_user['id'];
        }

        // If not found, log and return first user (admin)
        log_message('warning', 'User with email ' . $email . ' not found in tenant DB. Using default admin user.');

        $first_user_query = $this->db->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");

        if ($first_user_query->num_rows() > 0) {
            $first_user = $first_user_query->row_array();
            return (int)$first_user['id'];
        }

        // Last resort
        return 1;
    }

    /**
     * ✅ Get current tenant user ID
     * Returns cached value from __construct
     * 
     * @return int Tenant user ID
     */
    public function get_tenant_user_id()
    {
        // If cached, return it
        if ($this->tenant_user_id !== null) {
            return $this->tenant_user_id;
        }

        // If not cached (shouldn't happen), resolve it now
        $user_data = $this->session->userdata();

        if (isset($user_data['user_type']) && $user_data['user_type'] == 'merchant') {
            $email = isset($user_data['email']) ? $user_data['email'] : '';
            $this->tenant_user_id = $this->resolve_tenant_user_id($email);
            return $this->tenant_user_id;
        }

        // For system admin or fallback
        return 1;
    }

    /**
     * Get company currency from tenant database
     * 
     * @return string Currency code (default: DZD)
     */
    public function company_currency()
    {
        $user_data = $this->session->userdata();

        if (isset($user_data['user_type']) && $user_data['user_type'] == 'merchant') {
            $query = $this->db->query("SELECT currency FROM company WHERE id = 1 LIMIT 1");

            if ($query && $query->num_rows() > 0) {
                $result = $query->row_array();
                return isset($result['currency']) ? $result['currency'] : 'DZD';
            }
        }

        return 'DZD';
    }

    /**
     * Get list of available currencies
     * 
     * @return array Currency codes and names
     */
    public function currency()
    {
        return array(
            'DZD' => 'د.ج (Algerian Dinar)',
            'USD' => '$ (US Dollar)',
            'EUR' => '€ (Euro)',
            'GBP' => '£ (British Pound)',
            'JPY' => '¥ (Japanese Yen)',
            'CNY' => '¥ (Chinese Yuan)',
            'INR' => '₹ (Indian Rupee)',
            'CAD' => 'C$ (Canadian Dollar)',
            'AUD' => 'A$ (Australian Dollar)',
            'CHF' => 'Fr (Swiss Franc)',
            'SEK' => 'kr (Swedish Krona)',
            'NZD' => 'NZ$ (New Zealand Dollar)',
            'ZAR' => 'R (South African Rand)',
            'BRL' => 'R$ (Brazilian Real)',
            'MXN' => '$ (Mexican Peso)',
            'SGD' => 'S$ (Singapore Dollar)',
            'HKD' => 'HK$ (Hong Kong Dollar)',
            'NOK' => 'kr (Norwegian Krone)',
            'KRW' => '₩ (South Korean Won)',
            'TRY' => '₺ (Turkish Lira)',
            'RUB' => '₽ (Russian Ruble)',
            'PLN' => 'zł (Polish Zloty)',
            'THB' => '฿ (Thai Baht)',
            'IDR' => 'Rp (Indonesian Rupiah)',
            'MYR' => 'RM (Malaysian Ringgit)',
            'PHP' => '₱ (Philippine Peso)',
            'DKK' => 'kr (Danish Krone)',
            'CZK' => 'Kč (Czech Koruna)',
            'HUF' => 'Ft (Hungarian Forint)',
            'RON' => 'lei (Romanian Leu)',
            'AED' => 'د.إ (UAE Dirham)',
            'SAR' => 'ر.س (Saudi Riyal)',
            'EGP' => '£ (Egyptian Pound)',
            'MAD' => 'د.م. (Moroccan Dirham)',
            'TND' => 'د.ت (Tunisian Dinar)'
        );
    }
}
