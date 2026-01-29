<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_pos extends CI_Controller
{
    private $db_hostname = 'inventorysystem-mysqlinventory-ydsxph';
    private $db_username = 'mysql';
    private $db_password = 'Zakaria1304@';

    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ
        if (ENVIRONMENT !== 'development') {
            if (!$this->session->userdata('user_id') || $this->session->userdata('user_id') != 1) {
                show_404();
            }
        }
        
        $this->load->database(); // Load master DB
        $this->load->dbforge();
    }

    /**
     * Créer les tables POS sur tous les tenants
     */
    public function create_tables()
    {
        echo "<h2>🚀 POS Tables Creation - Multi-Tenant</h2>";
        echo "<p><strong>Database:</strong> {$this->db_hostname}</p>";
        echo "<hr>";

        // Get all active tenants
        $query = $this->db->query("SELECT * FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        if (empty($tenants)) {
            echo "<p style='color:red'>❌ No active tenants found!</p>";
            return;
        }

        echo "<p>Found <strong>" . count($tenants) . "</strong> active tenant(s)</p>";
        echo "<hr>";

        $success_count = 0;
        $error_count = 0;

        foreach ($tenants as $tenant) {
            $tenant_name = $tenant['tenant_name'];
            $tenant_id = $tenant['id'];
            $db_name = $tenant['database_name'];
            
            echo "<div style='margin:20px 0; padding:15px; background:#f5f5f5; border-left:4px solid #3498db;'>";
            echo "<h3>🏢 Tenant: <strong>{$tenant_name}</strong></h3>";
            echo "<p>Database: <code>{$db_name}</code></p>";

            try {
                // Connect to tenant database
                $tenant_config = array(
                    'dsn'      => '',
                    'hostname' => $this->db_hostname,
                    'username' => $this->db_username,
                    'password' => $this->db_password,
                    'database' => $db_name,
                    'dbdriver' => 'mysqli',
                    'dbprefix' => '',
                    'pconnect' => FALSE,
                    'db_debug' => FALSE,
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

                // Create tables
                $this->create_pos_tables($tenant_db);

                echo "<p style='color:green;font-weight:bold'>✅ Tables created successfully!</p>";
                $success_count++;

                // Close connection
                $tenant_db->close();

            } catch (Exception $e) {
                echo "<p style='color:red;font-weight:bold'>❌ Error: " . $e->getMessage() . "</p>";
                $error_count++;
            }

            echo "</div>";
        }

        // Summary
        echo "<hr>";
        echo "<div style='padding:20px; background:" . ($error_count > 0 ? '#f39c12' : '#2ecc71') . "; color:white; font-size:18px; border-radius:5px;'>";
        echo "<strong>📊 Migration Summary:</strong><br><br>";
        echo "✅ Successful: <strong>{$success_count}</strong> tenant(s)<br>";
        echo "❌ Failed: <strong>{$error_count}</strong> tenant(s)";
        echo "</div>";
    }

    /**
     * Créer les tables POS (style ancien migration)
     */
    private function create_pos_tables($db)
    {
        $dbforge = $this->load->dbforge($db, TRUE);

        // 1. pos_sales
        if (!$db->table_exists('pos_sales')) {
            $dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'bill_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'unique' => TRUE
                ),
                'customer_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ),
                'customer_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ),
                'customer_phone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => TRUE
                ),
                'customer_type' => array(
                    'type' => 'ENUM',
                    'constraint' => array('retail', 'wholesale', 'superwholesale'),
                    'default' => 'retail'
                ),
                'gross_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => 0.00
                ),
                'discount_type' => array(
                    'type' => 'ENUM',
                    'constraint' => array('percentage', 'fixed'),
                    'default' => 'fixed'
                ),
                'discount_value' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'discount_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'discount_reason' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ),
                'tax_rate' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => 0.00
                ),
                'tax_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'net_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => 0.00
                ),
                'payment_method' => array(
                    'type' => 'ENUM',
                    'constraint' => array('cash', 'card', 'mobile_payment', 'bank_transfer', 'credit', 'split'),
                    'default' => 'cash'
                ),
                'paid_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => 0.00
                ),
                'change_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'payment_reference' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ),
                'payment_notes' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
                ),
                'cashier_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ),
                'cash_register_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ),
                'status' => array(
                    'type' => 'ENUM',
                    'constraint' => array('completed', 'refunded', 'cancelled'),
                    'default' => 'completed'
                ),
                'refund_reason' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ),
                'refunded_by' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE
                ),
                'refunded_at' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE
                ),
                'total_items' => array(
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0
                ),
                'total_quantity' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0
                ),
                'receipt_printed' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ),
                'receipt_printed_times' => array(
                    'type' => 'INT',
                    'constraint' => 3,
                    'default' => 0
                ),
                'notes' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => FALSE
                ),
                'updated_at' => array(
                    'type' => 'DATETIME',
                    'null' => TRUE
                )
            ));

            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_sales', TRUE);

            // Add indexes
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
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'sale_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ),
                'product_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE
                ),
                'product_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ),
                'product_sku' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ),
                'qty' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1
                ),
                'unit_price' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'cost_price' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'line_discount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'subtotal' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'profit' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'loss_type' => array(
                    'type' => 'ENUM',
                    'constraint' => array('none', 'margin_loss', 'real_loss'),
                    'default' => 'none'
                ),
                'loss_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ),
                'loss_reason' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => FALSE
                )
            ));

            $dbforge->add_key('id', TRUE);
            $dbforge->create_table('pos_sales_items', TRUE);

            $db->query('ALTER TABLE `pos_sales_items` 
                ADD INDEX `idx_sale_id` (`sale_id`),
                ADD INDEX `idx_product_id` (`product_id`)
            ');
        }

        // 3-7: Autres tables (continué dans le prochain message car trop long)
        $this->create_remaining_tables($db, $dbforge);

        return true;
    }

    /**
     * Créer les tables restantes
     */
    private function create_remaining_tables($db, $dbforge)
    {
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
    }

    /**
     * Vérifier statut
     */
    public function check_status()
    {
        $query = $this->db->query("SELECT * FROM tenants WHERE status = 'active'");
        $tenants = $query->result_array();

        echo "<h2>📊 POS Tables Status</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#34495e; color:white;'>";
        echo "<th>Tenant</th><th>Database</th><th>pos_sales</th><th>pos_cash_register</th><th>pos_settings</th><th>Status</th>";
        echo "</tr>";

        foreach ($tenants as $tenant) {
            $db_name = $tenant['database_name'];
            
            $tenant_config = array(
                'hostname' => $this->db_hostname,
                'username' => $this->db_username,
                'password' => $this->db_password,
                'database' => $db_name,
                'dbdriver' => 'mysqli',
                'db_debug' => FALSE
            );

            $tenant_db = $this->load->database($tenant_config, TRUE);
            
            if ($tenant_db && $tenant_db->conn_id) {
                $t1 = $tenant_db->table_exists('pos_sales') ? '✅' : '❌';
                $t2 = $tenant_db->table_exists('pos_cash_register') ? '✅' : '❌';
                $t3 = $tenant_db->table_exists('pos_settings') ? '✅' : '❌';
                
                $all_exist = ($t1 == '✅' && $t2 == '✅' && $t3 == '✅');
                $status = $all_exist ? 
                    "<span style='color:green;font-weight:bold'>✅ Complete</span>" : 
                    "<span style='color:orange;font-weight:bold'>⚠️ Partial</span>";
                
                $tenant_db->close();
            } else {
                $t1 = $t2 = $t3 = '❓';
                $status = "<span style='color:red;font-weight:bold'>❌ Connection Failed</span>";
            }

            echo "<tr>";
            echo "<td><strong>{$tenant['tenant_name']}</strong></td>";
            echo "<td><code>{$db_name}</code></td>";
            echo "<td style='text-align:center'>{$t1}</td>";
            echo "<td style='text-align:center'>{$t2}</td>";
            echo "<td style='text-align:center'>{$t3}</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
}
