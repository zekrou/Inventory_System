<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Dashboard';
    }

    public function index()
    {
        // ✅ AJOUTER CETTE LIGNE
        $this->data['is_admin'] = true; // Toujours true pour merchant admin

        // Check if user has admin permissions
        $user_data = $this->session->userdata();

        // Sales Today
        $today = date('Y-m-d');
        $sales_today_query = $this->db->query("
            SELECT COALESCE(SUM(net_amount), 0) as total_sales, COUNT(*) as order_count
            FROM orders WHERE DATE(date_time) = ?
        ", array($today));
        $sales_today_data = $sales_today_query->row();
        $this->data['today_sales'] = $sales_today_data->total_sales;
        $this->data['today_orders_count'] = $sales_today_data->order_count;

        // Sales This Month
        $current_month = date('Y-m');
        $sales_month_query = $this->db->query("
            SELECT COALESCE(SUM(net_amount), 0) as total_sales, COUNT(*) as order_count
            FROM orders WHERE DATE_FORMAT(date_time, '%Y-%m') = ?
        ", array($current_month));
        $sales_month_data = $sales_month_query->row();
        $this->data['monthly_sales'] = $sales_month_data->total_sales;
        $this->data['monthly_orders_count'] = $sales_month_data->order_count;

        // Pending Orders
        $pending_query = $this->db->query("
            SELECT COUNT(*) as total_pending, COALESCE(SUM(net_amount), 0) as pending_amount
            FROM orders WHERE paid_status = 2
        ");
        $pending_data = $pending_query->row();
        $this->data['pending_orders_count'] = $pending_data->total_pending;
        $this->data['pending_orders_value'] = $pending_data->pending_amount;

        // Profit Margin (exemple fixe, à calculer selon vos besoins)
        $this->data['profit_margin'] = 25;

        // Inventory
        // ✅ APRÈS
        $this->data['total_stock_value'] = $this->db->query("
        SELECT COALESCE(SUM(price_default * qty), 0) as total FROM products WHERE availability = 1
        ")->row()->total;


        $this->data['total_products'] = $this->db->query("
            SELECT COUNT(*) as total FROM products WHERE availability = 1
        ")->row()->total;

        $this->data['stock_stats'] = array(
            'low_stock' => $this->db->query("
                SELECT COUNT(*) as total FROM products 
                WHERE qty <= 10 AND qty > 0 AND availability = 1
            ")->row()->total,
            'out_of_stock' => $this->db->query("
                SELECT COUNT(*) as total FROM products WHERE qty = 0 AND availability = 1
            ")->row()->total
        );

        // Operations
        $this->data['total_paid_orders'] = $this->db->query(
            "SELECT COUNT(*) as total FROM orders WHERE paid_status = 1"
        )->row()->total;

        $this->data['purchase_stats'] = array(
            'pending_purchases' => $this->db->query(
                "SELECT COUNT(*) as total FROM purchases WHERE status = 'pending'"
            )->row()->total,
            'completed_month' => $this->db->query(
                "SELECT COUNT(*) as total FROM purchases 
                WHERE status = 'received' AND DATE_FORMAT(purchase_date, '%Y-%m') = ?",
                array($current_month)
            )->row()->total
        );

        $this->data['stock_movements_today'] = $this->db->query(
            "SELECT COUNT(*) as total FROM stock_history WHERE DATE(created_at) = ?",
            array($today)
        )->row()->total;

        // Customers
        $this->data['total_customers'] = $this->db->query(
            "SELECT COUNT(*) as total FROM customers WHERE active = 1"
        )->row()->total;

        $this->data['customer_types'] = $this->db->query("
            SELECT customer_type, COUNT(*) as count 
            FROM customers 
            WHERE active = 1 
            GROUP BY customer_type
        ")->result_array();

        // Suppliers
        $this->data['total_suppliers'] = $this->db->query(
            "SELECT COUNT(*) as total FROM suppliers WHERE active = 1"
        )->row()->total;

        $this->data['active_suppliers_month'] = $this->db->query(
            "SELECT COUNT(DISTINCT supplier_id) as total FROM purchases 
            WHERE DATE_FORMAT(purchase_date, '%Y-%m') = ?",
            array($current_month)
        )->row()->total;

        // Top customer
        $top_customer_query = $this->db->query("
            SELECT customer_id, ANY_VALUE(customer_name) as customer_name, SUM(net_amount) as total_spent
            FROM orders
            WHERE DATE_FORMAT(date_time, '%Y-%m') = ?
            GROUP BY customer_id
            ORDER BY total_spent DESC
            LIMIT 1
        ", array($current_month));

        if ($top_customer_query->num_rows() > 0) {
            $top_customer = $top_customer_query->row();
            $this->data['top_customer_name'] = $top_customer->customer_name;
            $this->data['top_customer_amount'] = $top_customer->total_spent;
        } else {
            $this->data['top_customer_name'] = 'N/A';
            $this->data['top_customer_amount'] = 0;
        }

        $this->data['total_users'] = $this->db->query(
            "SELECT COUNT(*) as total FROM users"
        )->row()->total;

        // Recent Orders
        $this->data['recent_orders'] = $this->db->query(
            "SELECT * FROM orders ORDER BY date_time DESC LIMIT 5"
        )->result_array();

        // Stock Alerts
        $this->data['low_stock_products'] = $this->db->query("
            SELECT id, name, sku, qty, low_stock_threshold 
            FROM products 
            WHERE qty <= low_stock_threshold AND availability = 1 
            ORDER BY qty ASC 
            LIMIT 10
        ")->result_array();

        $this->render_template('dashboard', $this->data);
    }
}
