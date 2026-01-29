<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_pos_tables extends CI_Migration 
{
    public function up()
    {
        // =================================================================
        // 1. TABLE: pos_sales (Ventes POS principales)
        // =================================================================
        $this->dbforge->add_field(array(
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
                'null' => TRUE,
                'comment' => 'NULL = Walk-in customer'
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
                'constraint' => ['retail', 'wholesale', 'superwholesale'],
                'default' => 'retail'
            ),
            'gross_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'discount_type' => array(
                'type' => 'ENUM',
                'constraint' => ['percentage', 'fixed'],
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
                'default' => 0.00,
                'comment' => 'Calculated discount in DA'
            ),
            'discount_reason' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'tax_rate' => array(
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'comment' => 'TVA percentage if enabled'
            ),
            'tax_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ),
            'net_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Final total after discount and tax'
            ),
            'payment_method' => array(
                'type' => 'ENUM',
                'constraint' => ['cash', 'card', 'mobile_payment', 'bank_transfer', 'credit', 'split'],
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
                'default' => 0.00,
                'comment' => 'Money returned to customer'
            ),
            'payment_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,
                'comment' => 'Card/transfer reference number'
            ),
            'payment_notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'cashier_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'comment' => 'User ID who processed sale'
            ),
            'cash_register_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'Link to cash register session'
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => ['completed', 'refunded', 'cancelled'],
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
                'default' => 0,
                'comment' => 'Number of different products'
            ),
            'total_quantity' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Total qty of all items'
            ),
            'receipt_printed' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = Printed'
            ),
            'receipt_printed_times' => array(
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
                'comment' => 'Count reprints'
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

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('bill_no');
        $this->dbforge->add_key('customer_id');
        $this->dbforge->add_key('cashier_id');
        $this->dbforge->add_key('cash_register_id');
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('created_at');
        
        $this->dbforge->create_table('pos_sales', TRUE);

        // =================================================================
        // 2. TABLE: pos_sales_items (Lignes de vente)
        // =================================================================
        $this->dbforge->add_field(array(
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
                'constraint' => 255,
                'comment' => 'Snapshot for history'
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
                'default' => 0.00,
                'comment' => 'Price per unit at time of sale'
            ),
            'cost_price' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'For profit calculation'
            ),
            'line_discount' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Discount on this line'
            ),
            'subtotal' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'qty * unit_price - line_discount'
            ),
            'profit' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => '(unit_price - cost_price) * qty'
            ),
            'loss_type' => array(
                'type' => 'ENUM',
                'constraint' => ['none', 'margin_loss', 'real_loss'],
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

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('sale_id');
        $this->dbforge->add_key('product_id');
        
        $this->dbforge->create_table('pos_sales_items', TRUE);

        // =================================================================
        // 3. TABLE: pos_holds (Ventes en attente/suspendues)
        // =================================================================
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'hold_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => TRUE,
                'comment' => 'HOLD-XXXXX'
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
            'cart_data' => array(
                'type' => 'LONGTEXT',
                'comment' => 'JSON encoded cart items'
            ),
            'total_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'discount_data' => array(
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'JSON discount info'
            ),
            'cashier_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'notes' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => TRUE
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => ['active', 'completed', 'cancelled'],
                'default' => 'active'
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'expires_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'comment' => 'Auto-delete after X hours'
            ),
            'completed_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('hold_reference');
        $this->dbforge->add_key('cashier_id');
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('created_at');
        
        $this->dbforge->create_table('pos_holds', TRUE);

        // =================================================================
        // 4. TABLE: pos_cash_register (Sessions de caisse)
        // =================================================================
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'register_number' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'REG-001, REG-002 for multiple registers'
            ),
            'cashier_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'cashier_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
            'opening_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Cash in drawer at opening'
            ),
            'opening_notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'opened_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'closing_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => TRUE,
                'comment' => 'Actual cash counted at closing'
            ),
            'expected_amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => TRUE,
                'comment' => 'opening + sales_cash - withdrawals'
            ),
            'difference' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'closing - expected (+ = surplus, - = shortage)'
            ),
            'total_sales_cash' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'total_sales_card' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'total_sales_mobile' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'total_sales_credit' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00
            ),
            'total_sales' => array(
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
                'comment' => 'Sum of all payment methods'
            ),
            'total_transactions' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ),
            'total_refunds' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ),
            'cash_withdrawals' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Money taken out during shift'
            ),
            'cash_additions' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Money added during shift'
            ),
            'closing_notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'closed_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'closed_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'May differ from cashier (supervisor)'
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => ['open', 'closed'],
                'default' => 'open'
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('cashier_id');
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('opened_at');
        
        $this->dbforge->create_table('pos_cash_register', TRUE);

        // =================================================================
        // 5. TABLE: pos_split_payments (Paiements mixtes)
        // =================================================================
        $this->dbforge->add_field(array(
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
            'payment_method' => array(
                'type' => 'ENUM',
                'constraint' => ['cash', 'card', 'mobile_payment', 'bank_transfer'],
                'null' => FALSE
            ),
            'amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ),
            'reference' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('sale_id');
        
        $this->dbforge->create_table('pos_split_payments', TRUE);

        // =================================================================
        // 6. TABLE: pos_cash_movements (Mouvements caisse)
        // =================================================================
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'cash_register_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'movement_type' => array(
                'type' => 'ENUM',
                'constraint' => ['addition', 'withdrawal'],
                'null' => FALSE
            ),
            'amount' => array(
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ),
            'reason' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'comment' => 'Ex: Change needed, Bank deposit, etc.'
            ),
            'notes' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('cash_register_id');
        
        $this->dbforge->create_table('pos_cash_movements', TRUE);

        // =================================================================
        // 7. TABLE: pos_settings (Paramètres POS par tenant)
        // =================================================================
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'setting_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => TRUE
            ),
            'setting_value' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('setting_key');
        
        $this->dbforge->create_table('pos_settings', TRUE);

        // Insert default settings
        $default_settings = array(
            array('setting_key' => 'receipt_header', 'setting_value' => 'Merci pour votre visite!'),
            array('setting_key' => 'receipt_footer', 'setting_value' => 'À bientôt!'),
            array('setting_key' => 'auto_print_receipt', 'setting_value' => '1'),
            array('setting_key' => 'enable_barcode_scanner', 'setting_value' => '1'),
            array('setting_key' => 'hold_expiry_hours', 'setting_value' => '24'),
            array('setting_key' => 'default_customer_type', 'setting_value' => 'retail'),
            array('setting_key' => 'require_customer', 'setting_value' => '0'),
            array('setting_key' => 'enable_tax', 'setting_value' => '0'),
            array('setting_key' => 'tax_rate', 'setting_value' => '19'),
            array('setting_key' => 'currency_symbol', 'setting_value' => 'DZD'),
            array('setting_key' => 'products_per_page', 'setting_value' => '20'),
            array('setting_key' => 'enable_sound', 'setting_value' => '0')
        );

        $this->db->insert_batch('pos_settings', $default_settings);

        // =================================================================
        // FOREIGN KEYS (si InnoDB)
        // =================================================================
        // Note: Adapt based on your existing tables structure
        
        $this->db->query('
            ALTER TABLE pos_sales
            ADD CONSTRAINT fk_pos_sales_customer 
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
            
            ADD CONSTRAINT fk_pos_sales_cashier 
            FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE RESTRICT,
            
            ADD CONSTRAINT fk_pos_sales_register 
            FOREIGN KEY (cash_register_id) REFERENCES pos_cash_register(id) ON DELETE SET NULL
        ');

        $this->db->query('
            ALTER TABLE pos_sales_items
            ADD CONSTRAINT fk_pos_items_sale 
            FOREIGN KEY (sale_id) REFERENCES pos_sales(id) ON DELETE CASCADE,
            
            ADD CONSTRAINT fk_pos_items_product 
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
        ');

        $this->db->query('
            ALTER TABLE pos_holds
            ADD CONSTRAINT fk_pos_holds_cashier 
            FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE RESTRICT
        ');

        $this->db->query('
            ALTER TABLE pos_cash_register
            ADD CONSTRAINT fk_register_cashier 
            FOREIGN KEY (cashier_id) REFERENCES users(id) ON DELETE RESTRICT
        ');

        $this->db->query('
            ALTER TABLE pos_split_payments
            ADD CONSTRAINT fk_split_sale 
            FOREIGN KEY (sale_id) REFERENCES pos_sales(id) ON DELETE CASCADE
        ');

        $this->db->query('
            ALTER TABLE pos_cash_movements
            ADD CONSTRAINT fk_movement_register 
            FOREIGN KEY (cash_register_id) REFERENCES pos_cash_register(id) ON DELETE CASCADE,
            
            ADD CONSTRAINT fk_movement_user 
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
        ');

        echo "✅ POS tables created successfully!\n";
    }

    public function down()
    {
        // Drop in reverse order to avoid FK constraints
        $this->dbforge->drop_table('pos_cash_movements', TRUE);
        $this->dbforge->drop_table('pos_split_payments', TRUE);
        $this->dbforge->drop_table('pos_settings', TRUE);
        $this->dbforge->drop_table('pos_holds', TRUE);
        $this->dbforge->drop_table('pos_sales_items', TRUE);
        $this->dbforge->drop_table('pos_cash_register', TRUE);
        $this->dbforge->drop_table('pos_sales', TRUE);

        echo "✅ POS tables dropped successfully!\n";
    }
}
