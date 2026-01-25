<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CodeIgniter Properties
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Email $email
 * @property CI_Upload $upload
 * @property CI_Security $security
 * 
 * Custom Models
 * @property Model_products $model_products
 * @property Model_orders $model_orders
 * @property Model_users $model_users
 * @property Model_company $model_company
 * @property Model_groups $model_groups
 * @property Model_categories $model_categories
 * @property Model_category $model_category
 * @property Model_brands $model_brands
 * @property Model_stores $model_stores
 * @property Model_attributes $model_attributes
 * @property Model_customers $model_customers
 * @property Model_suppliers $model_suppliers
 * @property Model_stock $model_stock
 * @property Model_reports $model_reports
 */
class Reports extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['page_title'] = 'Reports';
		$this->load->model('model_reports');
	}

	public function index()
	{
		if (!isset($this->permission['viewReports'])) {
			redirect('dashboard', 'refresh');
		}

		$today_year = date('Y');
		$today_month = null;

		if ($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		if ($this->input->post('select_month')) {
			$today_month = $this->input->post('select_month');
		}

		// Charger les 5 rapports
		$this->data['profit_report'] = $this->model_reports->getProfitReport($today_year, $today_month);
		$this->data['top_products'] = $this->model_reports->getTopProducts($today_year, 10);
		$this->data['sales_by_type'] = $this->model_reports->getSalesByPriceType($today_year, $today_month);
		$this->data['payment_status'] = $this->model_reports->getPaymentStatus($today_year, $today_month);
		$this->data['global_stats'] = $this->model_reports->getGlobalStats($today_year);

		// Ancien code pour le graphique (optionnel)
		if ($today_month) {
			$parking_data = $this->model_reports->getOrderDataByDay($today_year, $today_month);
		} else {
			$parking_data = $this->model_reports->getOrderData($today_year, null);
		}

		$final_parking_data = array();
		foreach ($parking_data as $k => $v) {
			if (is_array($v) && count($v) > 0) {
				$total_amount_earned = 0;
				foreach ($v as $v2) {
					if (is_array($v2) && isset($v2['gross_amount'])) {
						$total_amount_earned += floatval($v2['net_amount']);
					}
				}
				$final_parking_data[$k] = $total_amount_earned;
			} else {
				$final_parking_data[$k] = 0;
			}
		}

		$this->data['report_years'] = $this->model_reports->getOrderYear();	
		$this->data['selected_year'] = $today_year;
		$this->data['selected_month'] = $today_month;
		$this->data['company_currency'] = $this->company_currency();
		$this->data['results'] = $final_parking_data;
		// Losses stats
		$this->data['losses_summary'] = $this->model_reports->getLossesSummary($selected_year, $selected_month);
		$this->data['top_loss_products'] = $this->model_reports->getTopLossProducts($selected_year, $selected_month, 10);

		$this->render_template('reports/index', $this->data);
	}

	public function export_csv()
	{
		// ✅ CORRIGÉ ICI (ligne 101)
		if (!isset($this->permission['viewReports'])) {
			redirect('dashboard', 'refresh');
		}

		$selected_year = $this->input->get('year');
		$selected_month = $this->input->get('month');

		if (!$selected_year) {
			$selected_year = date('Y');
		}

		if ($selected_month) {
			$order_data = $this->model_reports->getOrderDataByDay($selected_year, $selected_month);
			$filename = 'sales_report_' . $selected_year . '_' . $selected_month . '.csv';
		} else {
			$order_data = $this->model_reports->getOrderData($selected_year, null);
			$filename = 'sales_report_' . $selected_year . '.csv';
		}

		$final_data = array();
		foreach ($order_data as $k => $v) {
			if (is_array($v) && count($v) > 0) {
				$total_amount_earned = 0;
				foreach ($v as $v2) {
					if (is_array($v2) && isset($v2['gross_amount'])) {
						$total_amount_earned += floatval($v2['net_amount']);
					}
				}
				$final_data[$k] = $total_amount_earned;
			} else {
				$final_data[$k] = 0;
			}
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);

		$output = fopen('php://output', 'w');
		fputcsv($output, array('Period', 'Revenue (' . $this->company_currency() . ')'));

		foreach ($final_data as $period => $revenue) {
			fputcsv($output, array($period, number_format($revenue, 2)));
		}

		fclose($output);
		exit();
	}

	/**
	 * Page de rapports détaillés
	 */
	public function detailed()
	{
		// ✅ CORRIGÉ ICI (ligne 161)
		if (!isset($this->permission['viewReports'])) {
			redirect('dashboard', 'refresh');
		}

		$today_year = date('Y');
		$today_month = null;

		if ($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		if ($this->input->post('select_month')) {
			$today_month = $this->input->post('select_month');
		}

		// 1. Rapport de Profit
		$this->data['profit_report'] = $this->model_reports->getProfitReport($today_year, $today_month);

		// 2. Top Produits
		$this->data['top_products'] = $this->model_reports->getTopProducts($today_year, 10);

		// 3. Ventes par Type de Client
		$this->data['sales_by_type'] = $this->model_reports->getSalesByPriceType($today_year, $today_month);

		// 4. État des Paiements
		$this->data['payment_status'] = $this->model_reports->getPaymentStatus($today_year, $today_month);

		// 5. Statistiques Globales
		$this->data['global_stats'] = $this->model_reports->getGlobalStats($today_year);

		$this->data['report_years'] = $this->model_reports->getOrderYear();
		$this->data['selected_year'] = $today_year;
		$this->data['selected_month'] = $today_month;
		$this->data['company_currency'] = $this->company_currency();

		$this->render_template('reports/index', $this->data);
	}

	/**
	 * Page Rapports Achats et Fournisseurs
	 */
	public function purchases()
	{
		// ✅ CORRIGÉ ICI (ligne 212)
		if (!isset($this->permission['viewReports'])) {
			redirect('dashboard', 'refresh');
		}

		$today_year = date('Y');
		$today_month = null;

		if ($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		if ($this->input->post('select_month')) {
			$today_month = $this->input->post('select_month');
		}

		// Charger les rapports achats
		$this->data['purchases_by_supplier'] = $this->model_reports->getPurchasesBySupplier($today_year, $today_month);
		$this->data['top_purchased_products'] = $this->model_reports->getTopPurchasedProducts($today_year, 10);
		$this->data['purchases_evolution'] = $this->model_reports->getPurchasesEvolution($today_year, $today_month);
		$this->data['supplier_payment_status'] = $this->model_reports->getSupplierPaymentStatus($today_year, $today_month);
		$this->data['purchase_global_stats'] = $this->model_reports->getPurchaseGlobalStats($today_year);
		$this->data['purchase_vs_sales'] = $this->model_reports->getPurchaseVsSales($today_year);

		$this->data['report_years'] = $this->model_reports->getOrderYear();
		$this->data['selected_year'] = $today_year;
		$this->data['selected_month'] = $today_month;
		$this->data['company_currency'] = $this->company_currency();

		$this->render_template('reports/purchases', $this->data);
	}

	/**
	 * Page Rapports Clients
	 */
	public function customers()
	{
		// ✅ CORRIGÉ ICI (ligne 253)
		if (!isset($this->permission['viewReports'])) {
			redirect('dashboard', 'refresh');
		}

		$today_year = date('Y');
		$today_month = null;

		if ($this->input->post('select_year')) {
			$today_year = $this->input->post('select_year');
		}

		if ($this->input->post('select_month')) {
			$today_month = $this->input->post('select_month');
		}

		// Charger les rapports clients
		$this->data['top_customers'] = $this->model_reports->getTopCustomers($today_year, 10);
		$this->data['customers_by_type'] = $this->model_reports->getCustomersByType($today_year);
		$this->data['customer_debt'] = $this->model_reports->getCustomerDebt($today_year);
		$this->data['customer_loyalty'] = $this->model_reports->getCustomerLoyalty($today_year);

		$this->data['report_years'] = $this->model_reports->getOrderYear();
		$this->data['selected_year'] = $today_year;
		$this->data['selected_month'] = $today_month;
		$this->data['company_currency'] = $this->company_currency();

		$this->render_template('reports/customers', $this->data);
	}
}
