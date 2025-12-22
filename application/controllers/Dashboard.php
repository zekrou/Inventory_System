<?php 

class Dashboard extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->not_logged_in();
		$this->data['page_title'] = 'Dashboard';
		
		$this->load->model('model_products');
		$this->load->model('model_orders');
		$this->load->model('model_users');
		$this->load->model('model_stock');
		$this->load->model('model_customers');
		$this->load->model('model_suppliers');
		$this->load->model('model_purchases');
	}

	public function index()
	{
		// Basic counts
		$this->data['total_products'] = $this->model_products->countTotalProducts();
		$this->data['total_paid_orders'] = $this->model_orders->countTotalPaidOrders();
		$this->data['total_users'] = $this->model_users->countTotalUsers();
		$this->data['total_stocks'] = $this->model_stock->countTotalStock();
		
		// Customer data
		$this->data['total_customers'] = $this->model_customers->countTotalCustomers();
		$this->data['customer_types'] = $this->model_customers->countCustomersByType();
		
		// Supplier & Purchase stats
		$this->data['total_suppliers'] = $this->model_suppliers->countTotalSuppliers();
		$this->data['purchase_stats'] = $this->model_purchases->getPurchaseStatistics();
		
		// Stock statistics
		$this->data['stock_stats'] = $this->model_stock->getStockStatistics();
		
		// Low stock alerts
		$this->data['low_stock_products'] = $this->model_products->getLowStockProducts(10);
		$this->data['out_of_stock_products'] = $this->model_products->getOutOfStockProducts();

		// Check admin
		$user_id = $this->session->userdata('id');
		$is_admin = ($user_id == 1) ? true : false;
		$this->data['is_admin'] = $is_admin;
		
		$this->render_template('dashboard', $this->data);
	}
}
