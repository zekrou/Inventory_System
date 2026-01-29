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

        // 2. Table groups
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

        // ✅ NOUVEAU: 4. Table pre_orders
        $tenant_db->query("
            CREATE TABLE IF NOT EXISTS `pre_orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_number` varchar(50) NOT NULL,
                `customer_name` varchar(255) NOT NULL,
                `customer_phone` varchar(50) NOT NULL,
                `customer_address` text DEFAULT NULL,
                `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
                `status` varchar(50) NOT NULL DEFAULT 'pending',
                `user_id` int(11) DEFAULT NULL,
                `notes` text DEFAULT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `order_number` (`order_number`),
                KEY `user_id` (`user_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // ✅ NOUVEAU: 5. Table pre_order_items
        $tenant_db->query("
            CREATE TABLE IF NOT EXISTS `pre_order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `pre_order_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `product_name` varchar(255) NOT NULL,
                `qty` int(11) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `subtotal` decimal(10,2) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `pre_order_id` (`pre_order_id`),
                KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // ✅ Si pas d'email fourni, utiliser l'ancien format auto-généré
        if (empty($admin_email)) {
            $admin_email = $admin_username . '@' . $database_name . '.com';
        }

        // Insert default admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $user_data = array(
            'username' => $admin_username,
            'email' => $admin_email,
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
                // Products
                'createProduct' => 1,
                'updateProduct' => 1,
                'viewProduct' => 1,
                'deleteProduct' => 1,

                // Brands
                'createBrand' => 1,
                'updateBrand' => 1,
                'viewBrand' => 1,
                'deleteBrand' => 1,

                // Categories
                'createCategory' => 1,
                'updateCategory' => 1,
                'viewCategory' => 1,
                'deleteCategory' => 1,

                // Stock
                'createStock' => 1,
                'updateStock' => 1,
                'viewStock' => 1,
                'deleteStock' => 1,

                // Purchase
                'createPurchase' => 1,
                'updatePurchase' => 1,
                'viewPurchase' => 1,
                'deletePurchase' => 1,

                // Orders
                'createOrder' => 1,
                'updateOrder' => 1,
                'viewOrder' => 1,
                'deleteOrder' => 1,

                // Customers
                'createCustomer' => 1,
                'updateCustomer' => 1,
                'viewCustomer' => 1,
                'deleteCustomer' => 1,

                // Suppliers
                'createSupplier' => 1,
                'updateSupplier' => 1,
                'viewSupplier' => 1,
                'deleteSupplier' => 1,

                // Users
                'createUser' => 1,
                'updateUser' => 1,
                'viewUser' => 1,
                'deleteUser' => 1,

                // Groups
                'createGroup' => 1,
                'updateGroup' => 1,
                'viewGroup' => 1,
                'deleteGroup' => 1,

                // Company & Reports
                'createCompany' => 1,
                'updateCompany' => 1,
                'viewCompany' => 1,
                'viewReports' => 1,

                // ✅ PRE-ORDERS MOBILE
                'viewPreorders'   => 1,
                'updatePreorders' => 1,
                'deletePreorders' => 1

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
        $this->create_pos_tables($tenant_db);
        $tenant_db->close();

        return TRUE;
    }
    /**
     * Run migrations on all active tenants + template database
     * 
     * @return array Results for each tenant
     */
    public function run_tenant_migrations()
    {
        if (!$this->master_db) {
            $this->init_master_db();
        }

        $results = array();

        // 1. Run migration on template DB 'stock' first
        log_message('info', '🔄 Running migrations on template database: stock');
        $results['stock_template'] = $this->run_migration_on_database('stock');

        // 2. Get all active tenants
        $query = $this->master_db->query("SELECT id, tenant_name, database_name FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        log_message('info', '📊 Found ' . count($tenants) . ' active tenants to migrate');

        // 3. Run migration on each tenant
        foreach ($tenants as $tenant) {
            $db_name = $tenant['database_name'];
            $tenant_id = $tenant['id'];

            log_message('info', "🔄 Running migrations on tenant: {$tenant['tenant_name']} (DB: {$db_name})");

            $results[$tenant_id] = array_merge(
                array('tenant_name' => $tenant['tenant_name']),
                $this->run_migration_on_database($db_name)
            );
        }

        return $results;
    }

    /**
     * Run migration on specific database
     * 
     * @param string $database_name Database name
     * @return array Result with success/error
     */
    private function run_migration_on_database($database_name)
    {
        try {
            // Connect to target database
            $tenant_config = $this->CI->config->item('tenant_db_template');
            $tenant_config['database'] = $database_name;
            $temp_db = $this->CI->load->database($tenant_config, TRUE);

            // Check connection
            if (!$temp_db || !$temp_db->conn_id) {
                return array(
                    'success' => FALSE,
                    'error' => 'Failed to connect to database'
                );
            }

            // Load migration library with temp DB
            $this->CI->load->library('migration');

            // Temporarily switch database
            $original_db = $this->CI->db;
            $this->CI->db = $temp_db;

            // Run migrations
            $current_version = $this->CI->migration->current();

            // Restore original database
            $this->CI->db = $original_db;

            if ($current_version === FALSE) {
                $error = $this->CI->migration->error_string();
                log_message('error', "❌ Migration failed for {$database_name}: {$error}");
                return array(
                    'success' => FALSE,
                    'error' => $error
                );
            }

            log_message('info', "✅ Migration successful for {$database_name}");
            return array(
                'success' => TRUE,
                'version' => $current_version
            );
        } catch (Exception $e) {
            log_message('error', "❌ Exception during migration for {$database_name}: " . $e->getMessage());
            return array(
                'success' => FALSE,
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Get migration status for all tenants
     * 
     * @return array Status for each tenant
     */
    public function get_migration_status()
    {
        if (!$this->master_db) {
            $this->init_master_db();
        }

        $status = array();

        // Check template DB
        $status['stock_template'] = $this->get_db_version('stock');

        // Check all tenants
        $query = $this->master_db->query("SELECT id, tenant_name, database_name FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        foreach ($tenants as $tenant) {
            $status[$tenant['id']] = array(
                'tenant_name' => $tenant['tenant_name'],
                'database_name' => $tenant['database_name'],
                'version' => $this->get_db_version($tenant['database_name'])
            );
        }

        return $status;
    }

    /**
     * Get current migration version for a database
     * 
     * @param string $database_name
     * @return int Version number
     */
    private function get_db_version($database_name)
    {
        try {
            $tenant_config = $this->CI->config->item('tenant_db_template');
            $tenant_config['database'] = $database_name;
            $temp_db = $this->CI->load->database($tenant_config, TRUE);

            if (!$temp_db || !$temp_db->conn_id) {
                return 0;
            }

            // Check if db_version table exists
            if (!$temp_db->table_exists('db_version')) {
                return 0;
            }

            $query = $temp_db->query("SELECT MAX(version) as max_version FROM db_version");
            if ($query && $query->num_rows() > 0) {
                $result = $query->row_array();
                return (int)$result['max_version'];
            }

            return 0;
        } catch (Exception $e) {
            log_message('error', "Error getting DB version for {$database_name}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Créer les tables POS pour un nouveau tenant  
     * @param object $db Connexion DB du tenant
     * @return bool
     */
    private function create_pos_tables($db)
    {
        // ✅ FIX: Créer une nouvelle instance de dbforge pour cette connexion DB
        $this->CI->load->dbforge($db, TRUE);
        $dbforge = $this->CI->dbforge;

        // 1. pos_sales
        if (!$db->table_exists('pos_sales')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'bill_no' => array('type' => 'VARCHAR', 'constraint' => 50, 'unique' => TRUE),
                'customer_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
                'customer_name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'customer_phone' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE),
                'customer_type' => array('type' => 'ENUM', 'constraint' => array('retail', 'wholesale', 'superwholesale'), 'default' => 'retail'),
                'gross_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'discount_type' => array('type' => 'ENUM', 'constraint' => array('percentage', 'fixed'), 'default' => 'fixed'),
                'discount_value' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'discount_amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'discount_reason' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'tax_rate' => array('type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0.00),
                'tax_amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'net_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'payment_method' => array('type' => 'ENUM', 'constraint' => array('cash', 'card', 'mobile_payment', 'bank_transfer', 'credit', 'split'), 'default' => 'cash'),
                'paid_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'change_amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'payment_reference' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'payment_notes' => array('type' => 'TEXT', 'null' => TRUE),
                'cashier_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'cash_register_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
                'status' => array('type' => 'ENUM', 'constraint' => array('completed', 'refunded', 'cancelled'), 'default' => 'completed'),
                'refund_reason' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'refunded_by' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
                'refunded_at' => array('type' => 'DATETIME', 'null' => TRUE),
                'total_items' => array('type' => 'INT', 'constraint' => 5, 'default' => 0),
                'total_quantity' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'receipt_printed' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 0),
                'receipt_printed_times' => array('type' => 'INT', 'constraint' => 3, 'default' => 0),
                'notes' => array('type' => 'TEXT', 'null' => TRUE),
                'created_at' => array('type' => 'DATETIME', 'null' => FALSE),
                'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_sales', TRUE);

            $db->query('ALTER TABLE `pos_sales` 
            ADD INDEX `idx_bill_no` (`bill_no`),
            ADD INDEX `idx_customer_id` (`customer_id`),
            ADD INDEX `idx_cashier_id` (`cashier_id`),
            ADD INDEX `idx_status` (`status`),
            ADD INDEX `idx_created_at` (`created_at`)
        ');
        }

        // 2. pos_sales_items
        if (!$db->table_exists('pos_sales_items')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'sale_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'product_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'product_name' => array('type' => 'VARCHAR', 'constraint' => 255),
                'product_sku' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
                'qty' => array('type' => 'INT', 'constraint' => 11, 'default' => 1),
                'unit_price' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'cost_price' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'line_discount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'subtotal' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'profit' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'loss_type' => array('type' => 'ENUM', 'constraint' => array('none', 'margin_loss', 'real_loss'), 'default' => 'none'),
                'loss_amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'loss_reason' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'created_at' => array('type' => 'DATETIME', 'null' => FALSE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_sales_items', TRUE);

            $db->query('ALTER TABLE `pos_sales_items` 
            ADD INDEX `idx_sale_id` (`sale_id`),
            ADD INDEX `idx_product_id` (`product_id`)
        ');
        }

        // 3. pos_holds
        if (!$db->table_exists('pos_holds')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'hold_reference' => array('type' => 'VARCHAR', 'constraint' => 50, 'unique' => TRUE),
                'customer_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
                'customer_name' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'cart_data' => array('type' => 'LONGTEXT'),
                'total_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'discount_data' => array('type' => 'TEXT', 'null' => TRUE),
                'cashier_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'notes' => array('type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE),
                'status' => array('type' => 'ENUM', 'constraint' => array('active', 'completed', 'cancelled'), 'default' => 'active'),
                'created_at' => array('type' => 'DATETIME', 'null' => FALSE),
                'expires_at' => array('type' => 'DATETIME', 'null' => TRUE),
                'completed_at' => array('type' => 'DATETIME', 'null' => TRUE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_holds', TRUE);
        }

        // 4. pos_cash_register
        if (!$db->table_exists('pos_cash_register')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'register_number' => array('type' => 'VARCHAR', 'constraint' => 50),
                'cashier_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'cashier_name' => array('type' => 'VARCHAR', 'constraint' => 255),
                'opening_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'opening_notes' => array('type' => 'TEXT', 'null' => TRUE),
                'opened_at' => array('type' => 'DATETIME', 'null' => FALSE),
                'closing_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'null' => TRUE),
                'expected_amount' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'null' => TRUE),
                'difference' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'total_sales_cash' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'total_sales_card' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'total_sales_mobile' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'total_sales_credit' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'total_sales' => array('type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00),
                'total_transactions' => array('type' => 'INT', 'constraint' => 11, 'default' => 0),
                'total_refunds' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'cash_withdrawals' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'cash_additions' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'closing_notes' => array('type' => 'TEXT', 'null' => TRUE),
                'closed_at' => array('type' => 'DATETIME', 'null' => TRUE),
                'closed_by' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE),
                'status' => array('type' => 'ENUM', 'constraint' => array('open', 'closed'), 'default' => 'open')
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_cash_register', TRUE);
        }

        // 5. pos_split_payments
        if (!$db->table_exists('pos_split_payments')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'sale_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'payment_method' => array('type' => 'ENUM', 'constraint' => array('cash', 'card', 'mobile_payment', 'bank_transfer')),
                'amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'reference' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
                'created_at' => array('type' => 'DATETIME', 'null' => FALSE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_split_payments', TRUE);
        }

        // 6. pos_cash_movements
        if (!$db->table_exists('pos_cash_movements')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'cash_register_id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'movement_type' => array('type' => 'ENUM', 'constraint' => array('addition', 'withdrawal')),
                'amount' => array('type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00),
                'reason' => array('type' => 'VARCHAR', 'constraint' => 255),
                'notes' => array('type' => 'TEXT', 'null' => TRUE),
                'created_by' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE),
                'created_at' => array('type' => 'DATETIME', 'null' => FALSE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_cash_movements', TRUE);
        }

        // 7. pos_settings
        if (!$db->table_exists('pos_settings')) {
            $dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE),
                'setting_key' => array('type' => 'VARCHAR', 'constraint' => 100, 'unique' => TRUE),
                'setting_value' => array('type' => 'TEXT', 'null' => TRUE),
                'updated_at' => array('type' => 'DATETIME', 'null' => TRUE)
            ));
            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_settings', TRUE);

            // Insert default settings
            $db->query("INSERT IGNORE INTO pos_settings (setting_key, setting_value) VALUES
            ('receipt_header', 'Merci pour votre visite!'),
            ('receipt_footer', 'À bientôt!'),
            ('auto_print_receipt', '1'),
            ('enable_barcode_scanner', '1'),
            ('hold_expiry_hours', '24'),
            ('default_customer_type', 'retail'),
            ('require_customer', '0'),
            ('enable_tax', '0'),
            ('tax_rate', '19'),
            ('currency_symbol', 'DZD'),
            ('products_per_page', '20'),
            ('enable_sound', '0')
        ");
        }

        log_message('info', '✅ POS tables created successfully');
        return TRUE;
    }
}
