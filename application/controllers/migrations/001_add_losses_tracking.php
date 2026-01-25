<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_losses_tracking extends CI_Migration
{
    public function up()
    {
        // ========================================
        // 1. ALTER TABLE orders_item
        // ========================================
        
        $fields_to_add = array();
        
        if (!$this->db->field_exists('expected_price', 'orders_item')) {
            $fields_to_add['expected_price'] = array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => FALSE
            );
        }
        
        if (!$this->db->field_exists('actual_price', 'orders_item')) {
            $fields_to_add['actual_price'] = array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => FALSE
            );
        }
        
        if (!$this->db->field_exists('loss_amount', 'orders_item')) {
            $fields_to_add['loss_amount'] = array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => FALSE
            );
        }
        
        if (!$this->db->field_exists('loss_type', 'orders_item')) {
            $fields_to_add['loss_type'] = array(
                'type' => 'ENUM',
                'constraint' => array('none', 'margin_loss', 'real_loss'),
                'default' => 'none',
                'null' => FALSE
            );
        }
        
        if (!$this->db->field_exists('product_cost', 'orders_item')) {
            $fields_to_add['product_cost'] = array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'null' => FALSE
            );
        }
        
        if (!empty($fields_to_add)) {
            $this->dbforge->add_column('orders_item', $fields_to_add);
        }
        
        // ========================================
        // 2. CREATE TABLE sales_losses
        // ========================================
        
        if (!$this->db->table_exists('sales_losses')) {
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'order_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ),
                'order_item_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ),
                'product_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ),
                'customer_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ),
                'customer_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => FALSE
                ),
                'product_cost' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'expected_price' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'actual_price' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'quantity' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ),
                'loss_amount' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'total_loss' => array(
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => FALSE
                ),
                'loss_type' => array(
                    'type' => 'ENUM',
                    'constraint' => array('margin_loss', 'real_loss'),
                    'null' => FALSE
                ),
                'reason' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
                ),
                'approved_by' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ),
                'sale_date' => array(
                    'type' => 'DATETIME',
                    'null' => FALSE
                ),
                'created_at' => array(
                    'type' => 'TIMESTAMP',
                    'null' => FALSE
                )
            ));
            
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('sales_losses', TRUE);
            
            // Add indexes
            $this->db->query('ALTER TABLE `sales_losses` 
                ADD INDEX `idx_order_id` (`order_id`),
                ADD INDEX `idx_product_id` (`product_id`),
                ADD INDEX `idx_loss_type` (`loss_type`),
                ADD INDEX `idx_sale_date` (`sale_date`),
                MODIFY `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ');
        }
        
        // ========================================
        // 3. CREATE TABLE db_version
        // ========================================
        
        if (!$this->db->table_exists('db_version')) {
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'version' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ),
                'migration_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ),
                'executed_at' => array(
                    'type' => 'TIMESTAMP',
                    'null' => FALSE
                )
            ));
            
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('db_version', TRUE);
            
            $this->db->query('ALTER TABLE `db_version` MODIFY `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        }
        
        // Log this migration
        $this->db->insert('db_version', array(
            'version' => 1,
            'migration_name' => 'add_losses_tracking'
        ));
    }

    public function down()
    {
        // Remove columns from orders_item
        $columns = array('expected_price', 'actual_price', 'loss_amount', 'loss_type', 'product_cost');
        foreach ($columns as $col) {
            if ($this->db->field_exists($col, 'orders_item')) {
                $this->dbforge->drop_column('orders_item', $col);
            }
        }
        
        // Drop sales_losses table
        $this->dbforge->drop_table('sales_losses', TRUE);
        
        // Remove migration log
        $this->db->where('version', 1);
        $this->db->where('migration_name', 'add_losses_tracking');
        $this->db->delete('db_version');
    }
}
