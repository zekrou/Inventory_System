<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_pos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ: Accessible seulement en dev ou par super admin
        if (ENVIRONMENT !== 'development') {
            // Vérifier si super admin
            if (!$this->session->userdata('user_id') || $this->session->userdata('user_id') != 1) {
                show_404();
            }
        }
        
        $this->load->library('migration');
        $this->load->database();
    }

    /**
     * Migrer TOUS les tenants
     */
    public function run_all_tenants()
    {
        echo "<h2>🚀 Migration POS Multi-Tenant</h2>";
        echo "<hr>";

        // Get all tenants from central/main database
        $central_db = $this->load->database('default', TRUE);
        $tenants = $central_db->get('tenants')->result_array();

        if (empty($tenants)) {
            echo "<p style='color:red'>❌ No tenants found!</p>";
            return;
        }

        $success_count = 0;
        $error_count = 0;

        foreach ($tenants as $tenant) {
            echo "<div style='margin:20px 0; padding:15px; background:#f5f5f5; border-left:4px solid #3498db;'>";
            echo "<h3>🏢 Tenant: {$tenant['tenant_name']} (DB: {$tenant['db_name']})</h3>";

            try {
                // Switch to tenant database
                $tenant_db_config = array(
                    'dsn'      => '',
                    'hostname' => $tenant['db_host'],
                    'username' => $tenant['db_user'],
                    'password' => $tenant['db_password'],
                    'database' => $tenant['db_name'],
                    'dbdriver' => 'mysqli',
                    'dbprefix' => '',
                    'pconnect' => FALSE,
                    'db_debug' => TRUE,
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

                $this->db->close();
                $this->db->initialize($tenant_db_config, FALSE, TRUE);

                // Run migration
                if ($this->migration->current() === FALSE) {
                    throw new Exception($this->migration->error_string());
                }

                echo "<p style='color:green'>✅ Migration successful!</p>";
                $success_count++;

            } catch (Exception $e) {
                echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
                $error_count++;
            }

            echo "</div>";
        }

        // Summary
        echo "<hr>";
        echo "<div style='padding:20px; background:#2ecc71; color:white; font-size:18px;'>";
        echo "✅ Successful: <strong>{$success_count}</strong> tenants<br>";
        echo "❌ Failed: <strong>{$error_count}</strong> tenants";
        echo "</div>";

        // Reconnect to default database
        $this->db->close();
        $this->db->initialize($this->load->database('default', TRUE, TRUE), FALSE, TRUE);
    }

    /**
     * Migrer UN seul tenant
     */
    public function run_single($tenant_id)
    {
        if (!$tenant_id) {
            echo "❌ Tenant ID required!";
            return;
        }

        $central_db = $this->load->database('default', TRUE);
        $tenant = $central_db->get_where('tenants', array('id' => $tenant_id))->row_array();

        if (!$tenant) {
            echo "❌ Tenant not found!";
            return;
        }

        echo "<h2>🚀 Migration POS - {$tenant['tenant_name']}</h2>";
        echo "<hr>";

        try {
            $tenant_db_config = array(
                'hostname' => $tenant['db_host'],
                'username' => $tenant['db_user'],
                'password' => $tenant['db_password'],
                'database' => $tenant['db_name'],
                'dbdriver' => 'mysqli',
                'char_set' => 'utf8mb4',
                'dbcollat' => 'utf8mb4_unicode_ci'
            );

            $this->db->close();
            $this->db->initialize($tenant_db_config, FALSE, TRUE);

            if ($this->migration->current() === FALSE) {
                throw new Exception($this->migration->error_string());
            }

            echo "<p style='color:green; font-size:20px'>✅ Migration successful!</p>";

        } catch (Exception $e) {
            echo "<p style='color:red; font-size:20px'>❌ Error: " . $e->getMessage() . "</p>";
        }
    }

    /**
     * Vérifier état migration par tenant
     */
    public function check_status()
    {
        $central_db = $this->load->database('default', TRUE);
        $tenants = $central_db->get('tenants')->result_array();

        echo "<h2>📊 POS Migration Status</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#34495e; color:white;'>";
        echo "<th>Tenant</th><th>Database</th><th>Tables Exist</th><th>Status</th>";
        echo "</tr>";

        foreach ($tenants as $tenant) {
            $tenant_db_config = array(
                'hostname' => $tenant['db_host'],
                'username' => $tenant['db_user'],
                'password' => $tenant['db_password'],
                'database' => $tenant['db_name'],
                'dbdriver' => 'mysqli'
            );

            $this->db->close();
            $tenant_db = $this->db->initialize($tenant_db_config, FALSE, TRUE);

            $tables_exist = $tenant_db->table_exists('pos_sales') && 
                          $tenant_db->table_exists('pos_cash_register');

            $status = $tables_exist ? 
                "<span style='color:green'>✅ Migrated</span>" : 
                "<span style='color:red'>❌ Not Migrated</span>";

            echo "<tr>";
            echo "<td><strong>{$tenant['tenant_name']}</strong></td>";
            echo "<td>{$tenant['db_name']}</td>";
            echo "<td>" . ($tables_exist ? 'Yes' : 'No') . "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
}
