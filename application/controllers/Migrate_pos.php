<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_pos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ
        if (ENVIRONMENT !== 'development') {
            if (!$this->session->userdata('user_id') || $this->session->userdata('user_id') != 1) {
                show_404();
            }
        }
        
        $this->load->model('Tenants'); // ✅ FIX: Avec un S
        $this->load->database(); // Load default DB
    }

    /**
     * Créer directement les tables POS (RECOMMANDÉ)
     */
    public function create_tables_direct()
    {
        echo "<h2>🚀 Direct POS Tables Creation - Multi-Tenant</h2>";
        echo "<hr>";

        // Get all active tenants from master database
        $query = $this->db->query("SELECT * FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        if (empty($tenants)) {
            echo "<p style='color:red'>❌ No active tenants found!</p>";
            return;
        }

        $success_count = 0;
        $error_count = 0;

        foreach ($tenants as $tenant) {
            $tenant_name = $tenant['tenant_name'];
            $tenant_id = $tenant['id'];
            $db_name = $tenant['database_name'];
            
            echo "<div style='margin:20px 0; padding:15px; background:#f5f5f5; border-left:4px solid #3498db;'>";
            echo "<h3>🏢 Tenant: {$tenant_name} (DB: {$db_name})</h3>";

            try {
                // Connect to tenant database directly
                $tenant_config = array(
                    'dsn'      => '',
                    'hostname' => 'localhost', // Adapter si différent
                    'username' => 'root',      // Adapter selon ta config
                    'password' => 'root',      // Adapter selon ta config
                    'database' => $db_name,
                    'dbdriver' => 'mysqli',
                    'dbprefix' => '',
                    'pconnect' => FALSE,
                    'db_debug' => FALSE, // Important pour éviter les erreurs fatales
                    'cache_on' => FALSE,
                    'cachedir' => '',
                    'char_set' => 'utf8mb4',
                    'dbcollat' => 'utf8mb4_unicode_ci',
                    'swap_pre' => '',
                    'encrypt'  => FALSE,
                    'compress' => FALSE,
                    'stricton' => FALSE,
                    'failover' => array(),
                    'save_queries' => TRUE
                );

                $tenant_db = $this->load->database($tenant_config, TRUE);
                
                if (!$tenant_db || !$tenant_db->conn_id) {
                    throw new Exception("Cannot connect to database: {$db_name}");
                }

                // Execute SQL directly on tenant DB
                $this->execute_pos_sql($tenant_db);

                echo "<p style='color:green'>✅ Tables created successfully!</p>";
                $success_count++;

                // Close connection
                $tenant_db->close();

            } catch (Exception $e) {
                echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
                $error_count++;
            }

            echo "</div>";
        }

        echo "<hr>";
        echo "<div style='padding:20px; background:#2ecc71; color:white; font-size:18px;'>";
        echo "✅ Successful: <strong>{$success_count}</strong> tenants<br>";
        echo "❌ Failed: <strong>{$error_count}</strong> tenants";
        echo "</div>";
    }

    /**
     * Exécuter SQL directement sur une DB tenant
     */
    private function execute_pos_sql($db)
    {
        // 1. pos_sales
        $sql = "CREATE TABLE IF NOT EXISTS `pos_sales` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `bill_no` VARCHAR(50) NOT NULL UNIQUE,
            `customer_id` INT(11) UNSIGNED NULL COMMENT 'NULL = Walk-in customer',
            `customer_name` VARCHAR(255) NULL,
            `customer_phone` VARCHAR(50) NULL,
            `customer_type` ENUM('retail','wholesale','superwholesale') DEFAULT 'retail',
            `gross_amount` DECIMAL(12,2) DEFAULT 0.00,
            `discount_type` ENUM('percentage','fixed') DEFAULT 'fixed',
            `discount_value` DECIMAL(10,2) DEFAULT 0.00,
            `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
            `discount_reason` VARCHAR(255) NULL,
            `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
            `tax_amount` DECIMAL(10,2) DEFAULT 0.00,
            `net_amount` DECIMAL(12,2) DEFAULT 0.00,
            `payment_method` ENUM('cash','card','mobile_payment','bank_transfer','credit','split') DEFAULT 'cash',
            `paid_amount` DECIMAL(12,2) DEFAULT 0.00,
            `change_amount` DECIMAL(10,2) DEFAULT 0.00,
            `payment_reference` VARCHAR(255) NULL,
            `payment_notes` TEXT NULL,
            `cashier_id` INT(11) UNSIGNED NOT NULL,
            `cash_register_id` INT(11) UNSIGNED NULL,
            `status` ENUM('completed','refunded','cancelled') DEFAULT 'completed',
            `refund_reason` VARCHAR(255) NULL,
            `refunded_by` INT(11) UNSIGNED NULL,
            `refunded_at` DATETIME NULL,
            `total_items` INT(5) DEFAULT 0,
            `total_quantity` INT(11) DEFAULT 0,
            `receipt_printed` TINYINT(1) DEFAULT 0,
            `receipt_printed_times` INT(3) DEFAULT 0,
            `notes` TEXT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `bill_no` (`bill_no`),
            KEY `customer_id` (`customer_id`),
            KEY `cashier_id` (`cashier_id`),
            KEY `status` (`status`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 2. pos_sales_items
        $sql = "CREATE TABLE IF NOT EXISTS `pos_sales_items` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `sale_id` INT(11) UNSIGNED NOT NULL,
            `product_id` INT(11) UNSIGNED NOT NULL,
            `product_name` VARCHAR(255) NOT NULL,
            `product_sku` VARCHAR(100) NULL,
            `qty` INT(11) DEFAULT 1,
            `unit_price` DECIMAL(10,2) DEFAULT 0.00,
            `cost_price` DECIMAL(10,2) DEFAULT 0.00,
            `line_discount` DECIMAL(10,2) DEFAULT 0.00,
            `subtotal` DECIMAL(10,2) DEFAULT 0.00,
            `profit` DECIMAL(10,2) DEFAULT 0.00,
            `loss_type` ENUM('none','margin_loss','real_loss') DEFAULT 'none',
            `loss_amount` DECIMAL(10,2) DEFAULT 0.00,
            `loss_reason` VARCHAR(255) NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `sale_id` (`sale_id`),
            KEY `product_id` (`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 3. pos_holds
        $sql = "CREATE TABLE IF NOT EXISTS `pos_holds` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `hold_reference` VARCHAR(50) NOT NULL UNIQUE,
            `customer_id` INT(11) UNSIGNED NULL,
            `customer_name` VARCHAR(255) NULL,
            `cart_data` LONGTEXT NOT NULL,
            `total_amount` DECIMAL(12,2) DEFAULT 0.00,
            `discount_data` TEXT NULL,
            `cashier_id` INT(11) UNSIGNED NOT NULL,
            `notes` VARCHAR(500) NULL,
            `status` ENUM('active','completed','cancelled') DEFAULT 'active',
            `created_at` DATETIME NOT NULL,
            `expires_at` DATETIME NULL,
            `completed_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `hold_reference` (`hold_reference`),
            KEY `cashier_id` (`cashier_id`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 4. pos_cash_register
        $sql = "CREATE TABLE IF NOT EXISTS `pos_cash_register` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `register_number` VARCHAR(50) NOT NULL,
            `cashier_id` INT(11) UNSIGNED NOT NULL,
            `cashier_name` VARCHAR(255) NOT NULL,
            `opening_amount` DECIMAL(12,2) DEFAULT 0.00,
            `opening_notes` TEXT NULL,
            `opened_at` DATETIME NOT NULL,
            `closing_amount` DECIMAL(12,2) NULL,
            `expected_amount` DECIMAL(12,2) NULL,
            `difference` DECIMAL(10,2) DEFAULT 0.00,
            `total_sales_cash` DECIMAL(12,2) DEFAULT 0.00,
            `total_sales_card` DECIMAL(12,2) DEFAULT 0.00,
            `total_sales_mobile` DECIMAL(12,2) DEFAULT 0.00,
            `total_sales_credit` DECIMAL(12,2) DEFAULT 0.00,
            `total_sales` DECIMAL(12,2) DEFAULT 0.00,
            `total_transactions` INT(11) DEFAULT 0,
            `total_refunds` DECIMAL(10,2) DEFAULT 0.00,
            `cash_withdrawals` DECIMAL(10,2) DEFAULT 0.00,
            `cash_additions` DECIMAL(10,2) DEFAULT 0.00,
            `closing_notes` TEXT NULL,
            `closed_at` DATETIME NULL,
            `closed_by` INT(11) UNSIGNED NULL,
            `status` ENUM('open','closed') DEFAULT 'open',
            PRIMARY KEY (`id`),
            KEY `cashier_id` (`cashier_id`),
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 5. pos_split_payments
        $sql = "CREATE TABLE IF NOT EXISTS `pos_split_payments` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `sale_id` INT(11) UNSIGNED NOT NULL,
            `payment_method` ENUM('cash','card','mobile_payment','bank_transfer') NOT NULL,
            `amount` DECIMAL(10,2) DEFAULT 0.00,
            `reference` VARCHAR(255) NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `sale_id` (`sale_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 6. pos_cash_movements
        $sql = "CREATE TABLE IF NOT EXISTS `pos_cash_movements` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `cash_register_id` INT(11) UNSIGNED NOT NULL,
            `movement_type` ENUM('addition','withdrawal') NOT NULL,
            `amount` DECIMAL(10,2) DEFAULT 0.00,
            `reason` VARCHAR(255) NOT NULL,
            `notes` TEXT NULL,
            `created_by` INT(11) UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `cash_register_id` (`cash_register_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

        // 7. pos_settings
        $sql = "CREATE TABLE IF NOT EXISTS `pos_settings` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `setting_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->query($sql);

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

        return true;
    }

    /**
     * Vérifier statut des tables POS
     */
    public function check_status()
    {
        $query = $this->db->query("SELECT * FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        echo "<h2>📊 POS Migration Status</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#34495e; color:white;'>";
        echo "<th>Tenant</th><th>Database</th><th>Tables Exist</th><th>Status</th>";
        echo "</tr>";

        foreach ($tenants as $tenant) {
            $db_name = $tenant['database_name'];
            
            // Connect to tenant DB
            $tenant_config = array(
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'database' => $db_name,
                'dbdriver' => 'mysqli',
                'db_debug' => FALSE
            );

            $tenant_db = $this->load->database($tenant_config, TRUE);
            
            if ($tenant_db && $tenant_db->conn_id) {
                $tables_exist = $tenant_db->table_exists('pos_sales') && 
                              $tenant_db->table_exists('pos_cash_register');

                $status = $tables_exist ? 
                    "<span style='color:green'>✅ Migrated</span>" : 
                    "<span style='color:red'>❌ Not Migrated</span>";
                
                $tenant_db->close();
            } else {
                $tables_exist = false;
                $status = "<span style='color:orange'>⚠️ Connection Failed</span>";
            }

            echo "<tr>";
            echo "<td><strong>{$tenant['tenant_name']}</strong></td>";
            echo "<td>{$db_name}</td>";
            echo "<td>" . ($tables_exist ? 'Yes' : 'No') . "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
}
