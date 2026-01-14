<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tenant
{

    protected $CI;
    protected $master_db;
    protected $tenant_db;
    protected $current_tenant;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('tenant');
    }

    /**
     * Initialize master database connection
     */
    public function init_master_db()
    {
        $master_config = $this->CI->config->item('master_db');
        $this->master_db = $this->CI->load->database($master_config, TRUE);
        return $this->master_db;
    }

    /**
     * Switch to tenant database
     * @param int $tenant_id ID du tenant
     * @return object|bool Retourne l'objet de connexion DB ou FALSE en cas d'erreur
     */
  public function switch_tenant_db($tenant_id)
{
    // Get tenant info from master DB
    if (!$this->master_db) {
        $this->init_master_db();
    }

    $query = $this->master_db->query("SELECT * FROM tenants WHERE id = ? AND status = 'active'", array($tenant_id));

    if ($query->num_rows() == 0) {
        log_message('error', 'Tenant not found or inactive: ' . $tenant_id);
        
        // ✅ NOUVEAU : Déconnecter l'utilisateur si le tenant n'existe plus
        $this->CI->session->sess_destroy();
        show_error('Unable to connect to tenant database. Your account may have been deleted or suspended. Please contact support.', 403, 'Tenant Not Available');
        return FALSE;
    }

    $tenant = $query->row_array();
    $this->current_tenant = $tenant;

    // Load tenant database configuration
    $tenant_config = $this->CI->config->item('tenant_db_template');
    $tenant_config['database'] = $tenant['database_name'];

    try {
        // Create new connection to tenant database
        $this->tenant_db = $this->CI->load->database($tenant_config, TRUE);

        // Verify connection
        if (!$this->tenant_db) {
            log_message('error', 'Failed to connect to tenant database: ' . $tenant['database_name']);
            $this->CI->session->sess_destroy();
            show_error('Unable to connect to tenant database', 500, 'Database Connection Error');
            return FALSE;
        }

        // Test the connection
        if (!$this->tenant_db->conn_id) {
            log_message('error', 'Tenant database connection failed: ' . $tenant['database_name']);
            $this->CI->session->sess_destroy();
            show_error('Unable to connect to tenant database', 500, 'Database Connection Error');
            return FALSE;
        }

        // Return the database connection object
        return $this->tenant_db;
    } catch (Exception $e) {
        log_message('error', 'Exception while connecting to tenant DB: ' . $e->getMessage());
        $this->CI->session->sess_destroy();
        show_error('Unable to connect to tenant database', 500, 'Database Connection Error');
        return FALSE;
    }
}





    /**
     * Get current tenant info
     */
    public function get_current_tenant()
    {
        return $this->current_tenant;
    }

    /**
     * Get tenants for a specific user
     */
    public function get_user_tenants($user_id)
    {
        if (!$this->master_db) {
            $this->init_master_db();
        }

        $sql = "SELECT t.*, ut.role 
                FROM tenants t 
                INNER JOIN user_tenant ut ON t.id = ut.tenant_id 
                WHERE ut.user_id = ? AND t.status = 'active'";

        $query = $this->master_db->query($sql, array($user_id));
        return $query->result_array();
    }

    /**
     * Create new tenant database
     */
    public function create_tenant($tenant_name, $company_name, $plan = 'basic', $creator_email = null)
    {
        if (!$this->master_db) {
            $this->init_master_db();
        }

        // Generate unique database name
        $database_name = 'stock_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tenant_name)) . '_' . time();

        // Insert tenant record
        $tenant_data = array(
            'tenant_name' => $tenant_name,
            'company_name' => $company_name,
            'database_name' => $database_name,
            'status' => 'active',
            'plan' => $plan
        );

        $this->master_db->insert('tenants', $tenant_data);
        $tenant_id = $this->master_db->insert_id();

        // Create tenant database
        $this->master_db->query("CREATE DATABASE `{$database_name}` CHARACTER SET utf8 COLLATE utf8_general_ci");

        // Clone schema from template database 'stock'
        $this->clone_database_schema('stock', $database_name);

        // ✅ Seed initial data avec l'email du créateur (si fourni)
        $this->seed_tenant_data($database_name, $tenant_name, $creator_email);

        return array(
            'success' => TRUE,
            'tenant_id' => $tenant_id,
            'database_name' => $database_name
        );
    }


    /**
     * Clone database schema from template (tables and views)
     */
    private function clone_database_schema($source_db, $target_db)
    {
        // Disable foreign key checks
        $this->master_db->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->master_db->query("USE `{$target_db}`");

        // Step 1: Get all TABLES (exclude views)
        $tables_query = "SELECT TABLE_NAME 
                 FROM information_schema.TABLES 
                 WHERE TABLE_SCHEMA = '{$source_db}' 
                 AND TABLE_TYPE = 'BASE TABLE'";
        $tables = $this->master_db->query($tables_query)->result_array();

        // Create tables first
        foreach ($tables as $table) {
            $table_name = $table['TABLE_NAME'];

            // ✅ CORRECTION: Skip master-only tables ET tables qui seront créées par seed_tenant_data
            if (in_array($table_name, array('tenants', 'user_tenant', 'subscriptions', 'users', 'groups', 'user_group'))) {
                continue;
            }

            // Get CREATE TABLE statement
            $query = $this->master_db->query("SHOW CREATE TABLE `{$source_db}`.`{$table_name}`");

            if ($query && $query->num_rows() > 0) {
                $row = $query->row_array();
                $values = array_values($row);
                $create_sql = isset($values[1]) ? $values[1] : '';

                if (!empty($create_sql)) {
                    try {
                        $this->master_db->query($create_sql);
                    } catch (Exception $e) {
                        log_message('error', "Failed to create table {$table_name}: " . $e->getMessage());
                    }
                }
            }
        }

        // Step 2: Get all VIEWS
        $views_query = "SELECT TABLE_NAME 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = '{$source_db}' 
                AND TABLE_TYPE = 'VIEW'";
        $views = $this->master_db->query($views_query)->result_array();

        // Create views after all tables are created
        foreach ($views as $view) {
            $view_name = $view['TABLE_NAME'];

            // Get CREATE VIEW statement
            $query = $this->master_db->query("SHOW CREATE VIEW `{$source_db}`.`{$view_name}`");

            if ($query && $query->num_rows() > 0) {
                $row = $query->row_array();
                $values = array_values($row);
                $create_sql = isset($values[1]) ? $values[1] : '';

                if (!empty($create_sql)) {
                    // Replace source database name with target database name in view definition
                    $create_sql = str_replace("`{$source_db}`.", "`{$target_db}`.", $create_sql);
                    $create_sql = str_replace("DEFINER=`root`@`localhost`", "", $create_sql);

                    try {
                        $this->master_db->query($create_sql);
                    } catch (Exception $e) {
                        log_message('error', "Failed to create view {$view_name}: " . $e->getMessage());
                    }
                }
            }
        }

        // Re-enable foreign key checks
        $this->master_db->query("SET FOREIGN_KEY_CHECKS = 1");
        $this->master_db->query("USE `stock_master`");

        return TRUE;
    }



    /**
     * Seed initial data for new tenant
     */
    public function seed_tenant_data($database_name, $admin_username = 'admin', $admin_email = null)
    {
        // Connect to tenant database
        $tenant_config = $this->CI->config->item('tenant_db_template');
        $tenant_config['database'] = $database_name;
        $tenant_db = $this->CI->load->database($tenant_config, TRUE);

        // Créer les tables avec backticks

        // 1. Table users
        $tenant_db->query("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `firstname` varchar(255) NOT NULL,
            `lastname` varchar(255) NOT NULL,
            `phone` varchar(25) DEFAULT NULL,
            `gender` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

        // 2. Table groups (avec backticks!)
        $tenant_db->query("
        CREATE TABLE IF NOT EXISTS `groups` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `group_name` varchar(255) NOT NULL,
            `permission` text NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

        // 3. Table user_group
        $tenant_db->query("
        CREATE TABLE IF NOT EXISTS `user_group` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `group_id` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

        // ✅ Si pas d'email fourni, utiliser l'ancien format auto-généré
        if (empty($admin_email)) {
            $admin_email = $admin_username . '@' . $database_name . '.com';
        }

        // Insert default admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $user_data = array(
            'username' => $admin_username,
            'email' => $admin_email, // ✅ Utiliser l'email fourni ou généré
            'password' => $password,
            'firstname' => 'Admin',
            'lastname' => 'User',
            'phone' => '',
            'gender' => 1
        );

        $tenant_db->insert('users', $user_data);
        $admin_user_id = $tenant_db->insert_id();

        // Insert default admin group
        $group_data = array(
            'group_name' => 'Administrator',
            'permission' => serialize(array(
                'createUser' => 1,
                'updateUser' => 1,
                'viewUser' => 1,
                'deleteUser' => 1,
                'createGroup' => 1,
                'updateGroup' => 1,
                'viewGroup' => 1,
                'deleteGroup' => 1,
                'createBrand' => 1,
                'updateBrand' => 1,
                'viewBrand' => 1,
                'deleteBrand' => 1,
                'createCategory' => 1,
                'updateCategory' => 1,
                'viewCategory' => 1,
                'deleteCategory' => 1,
                'createProduct' => 1,
                'updateProduct' => 1,
                'viewProduct' => 1,
                'deleteProduct' => 1,
                'createOrder' => 1,
                'updateOrder' => 1,
                'viewOrder' => 1,
                'deleteOrder' => 1,
                'createReport' => 1,
                'viewReport' => 1,
                'updateCompany' => 1,
                'viewCompany' => 1,
                'viewCustomer' => 1,
                'createCustomer' => 1,
                'updateCustomer' => 1,
                'deleteCustomer' => 1,
                'viewSupplier' => 1,
                'createSupplier' => 1,
                'updateSupplier' => 1,
                'deleteSupplier' => 1,
                'viewPurchase' => 1,
                'createPurchase' => 1,
                'updatePurchase' => 1,
                'deletePurchase' => 1
            ))
        );

        $tenant_db->insert('groups', $group_data);
        $admin_group_id = $tenant_db->insert_id();

        // Link user to admin group
        $user_group_data = array(
            'user_id' => $admin_user_id,
            'group_id' => $admin_group_id
        );
        $tenant_db->insert('user_group', $user_group_data);

        // Insert default company data
        $company_data = array(
            'company_name' => 'New Company',
            'service_charge_value' => 0,
            'vat_charge_value' => 19,
            'address' => '',
            'phone' => '',
            'country' => '',
            'message' => 'Thank you for your business',
            'currency' => 'DZD'
        );
        $tenant_db->insert('company', $company_data);

        $tenant_db->close();

        return TRUE;
    }
}
