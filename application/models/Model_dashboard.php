<?php

class Model_dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ========== BUSINESS METRICS ==========

    public function getTodaySales()
    {
        $today = date('Y-m-d');
        $this->db->select_sum('net_amount');
        $this->db->where('DATE(date_time)', $today);
        $this->db->where('paid_status', 1);
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['net_amount'] ? $result['net_amount'] : 0;
    }

    public function getTodayOrdersCount()
    {
        $today = date('Y-m-d');
        $this->db->where('DATE(date_time)', $today);
        return $this->db->count_all_results('orders');
    }

    public function getMonthlySales()
    {
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $this->db->select_sum('net_amount');
        $this->db->where('date_time >=', $first_day);
        $this->db->where('date_time <=', $last_day . ' 23:59:59');
        $this->db->where('paid_status', 1);
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['net_amount'] ? $result['net_amount'] : 0;
    }

    public function getMonthlyOrdersCount()
    {
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $this->db->where('date_time >=', $first_day);
        $this->db->where('date_time <=', $last_day . ' 23:59:59');
        return $this->db->count_all_results('orders');
    }

    public function getPendingOrdersCount()
    {
        $this->db->where('paid_status', 2); // 2 = pending
        return $this->db->count_all_results('orders');
    }

    public function getPendingOrdersValue()
    {
        $this->db->select_sum('net_amount');
        $this->db->where('paid_status', 2);
        $query = $this->db->get('orders');
        $result = $query->row_array();
        return $result['net_amount'] ? $result['net_amount'] : 0;
    }

    public function getProfitMargin()
    {
        // Calculate average profit margin from orders
        // ✅ Utiliser average_cost (coût d'achat) ou price_default comme fallback
        $this->db->select('
            SUM(oi.qty * (oi.rate - COALESCE(p.average_cost, p.price_default))) as total_profit,
            SUM(oi.qty * oi.rate) as total_revenue
        ');
        $this->db->from('orders_item oi');
        $this->db->join('products p', 'oi.product_id = p.id');
        $this->db->join('orders o', 'oi.order_id = o.id');
        $this->db->where('o.paid_status', 1);
        $this->db->where('MONTH(o.date_time)', date('m'));
        $this->db->where('YEAR(o.date_time)', date('Y'));

        $query = $this->db->get();
        $result = $query->row_array();

        if ($result && $result['total_revenue'] > 0 && $result['total_profit'] !== null) {
            return round(($result['total_profit'] / $result['total_revenue']) * 100, 1);
        }
        return 0;
    }

    // ========== INVENTORY HEALTH ========== ✅ CORRIGÉ

    public function getTotalStockValue()
    {
        // ✅ Utiliser average_cost si disponible, sinon price_default
        $this->db->select('SUM(p.qty * COALESCE(p.average_cost, p.price_default)) as total_value');
        $this->db->from('products p');
        $this->db->where('p.availability', 1);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['total_value'] ? $result['total_value'] : 0;
    }

    public function getStockStats()
    {
        // Low stock (qty <= 10 or custom alert_qty)
        $this->db->where('qty <=', 10);
        $this->db->where('qty >', 0);
        $this->db->where('availability', 1);
        $low_stock = $this->db->count_all_results('products');

        // Out of stock
        $this->db->where('qty', 0);
        $this->db->where('availability', 1);
        $out_of_stock = $this->db->count_all_results('products');

        return array(
            'low_stock' => $low_stock,
            'out_of_stock' => $out_of_stock
        );
    }

    // ========== OPERATIONS ==========

    public function getPurchaseStats()
    {
        // Pending purchases
        $this->db->where('status', 'pending');
        $pending = $this->db->count_all_results('purchases');

        // Completed this month
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $this->db->where('status', 'received');
        $this->db->where('purchase_date >=', $first_day);
        $this->db->where('purchase_date <=', $last_day);
        $completed_month = $this->db->count_all_results('purchases');

        return array(
            'pending_purchases' => $pending,
            'completed_month' => $completed_month
        );
    }

    public function getTodayStockMovements()
    {
        $today = date('Y-m-d');
        
        // ✅ Vérifier si la table existe
        if (!$this->db->table_exists('stock_history')) {
            return 0;
        }
        
        // ✅ Utiliser created_at au lieu de movement_date
        $this->db->where('DATE(created_at)', $today);
        return $this->db->count_all_results('stock_history');
    }

    // ========== CONTACTS ==========

    public function getActiveSuppliersThisMonth()
    {
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $this->db->select('COUNT(DISTINCT supplier_id) as count');
        $this->db->from('purchases');
        $this->db->where('purchase_date >=', $first_day);
        $this->db->where('purchase_date <=', $last_day);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['count'] ? $result['count'] : 0;
    }

    public function getTopCustomerThisMonth()
    {
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        // ✅ Utiliser customer_name au lieu de c.name
        $sql = "SELECT customer_name, SUM(net_amount) as total
                FROM orders
                WHERE date_time >= ? 
                AND date_time <= ?
                AND paid_status = 1
                AND customer_name IS NOT NULL
                GROUP BY customer_name
                ORDER BY total DESC
                LIMIT 1";
        
        $query = $this->db->query($sql, array($first_day, $last_day . ' 23:59:59'));
        $result = $query->row_array();
        
        return $result ? $result['customer_name'] : 'N/A';
    }

    // ========== RECENT ACTIVITY ==========

    public function getRecentOrders($limit = 5)
    {
        // ✅ Utiliser customer_name qui existe déjà dans orders
        $this->db->select('o.id, o.bill_no, o.customer_name, o.net_amount, o.paid_status, o.date_time');
        $this->db->from('orders o');
        $this->db->order_by('o.date_time', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getLowStockProducts($limit = 10)
    {
        $this->db->select('id, name, sku, qty');
        $this->db->from('products');
        $this->db->where('qty <=', 10);
        $this->db->where('qty >', 0);
        $this->db->where('availability', 1);
        $this->db->order_by('qty', 'ASC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }
}
