<?php

class Model_customers extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* ===================== BASIC GETTERS ===================== */

	public function getActiveCustomers()
	{
		$sql = "SELECT * FROM customers WHERE active = 1 ORDER BY customer_name ASC";
		return $this->db->query($sql)->result_array();
	}

	public function getCustomersByType($type = null)
	{
		if ($type) {
			$sql = "SELECT * FROM customers 
					WHERE customer_type = ? AND active = 1 
					ORDER BY customer_name ASC";
			return $this->db->query($sql, [$type])->result_array();
		}
		return $this->getActiveCustomers();
	}

	public function getCustomerData($id = null)
	{
		if ($id) {
			return $this->db
				->get_where('customers', ['id' => $id])
				->row_array();
		}

		return $this->db
			->order_by('id', 'DESC')
			->get('customers')
			->result_array();
	}

	public function getCustomerByCode($code)
	{
		return $this->db
			->get_where('customers', ['customer_code' => $code])
			->row_array();
	}

	/* ===================== DUPLICATE CHECK ===================== */

	public function checkDuplicates($customer_name, $customer_phone)
	{
		$conditions = [];
		$params = [];

		if (!empty($customer_name)) {
			$conditions[] = "(LOWER(customer_name) = LOWER(?) OR customer_name LIKE ?)";
			$params[] = $customer_name;
			$params[] = '%' . $customer_name . '%';
		}

		if (!empty($customer_phone)) {
			$clean_phone = preg_replace('/[^0-9]/', '', $customer_phone);
			if ($clean_phone) {
				$conditions[] =
					"REPLACE(REPLACE(REPLACE(phone,' ',''),'-',''),'+','') LIKE ?";
				$params[] = '%' . $clean_phone . '%';
			}
		}

		if (empty($conditions)) {
			return [];
		}

		$sql = "SELECT id, customer_name, customer_code, phone, address, customer_type
				FROM customers
				WHERE active = 1 AND (" . implode(' OR ', $conditions) . ")
				LIMIT 5";

		$results = $this->db->query($sql, $params)->result_array();
		$suggestions = [];

		foreach ($results as $c) {
			$reason = [];

			if ($customer_name && stripos($c['customer_name'], $customer_name) !== false) {
				$reason[] = 'Name matches';
			}

			if ($customer_phone) {
				$p1 = preg_replace('/[^0-9]/', '', $customer_phone);
				$p2 = preg_replace('/[^0-9]/', '', $c['phone']);
				if ($p1 && stripos($p2, $p1) !== false) {
					$reason[] = 'Phone matches';
				}
			}

			$suggestions[] = [
				'id' => $c['id'],
				'name' => $c['customer_name'],
				'code' => $c['customer_code'],
				'phone' => $c['phone'],
				'address' => $c['address'],
				'type' => $c['customer_type'],
				'match_reason' => implode(', ', $reason)
			];
		}

		return $suggestions;
	}

	/* ===================== CUSTOMER CODE ===================== */

	public function generateCustomerCode()
	{
		$row = $this->db
			->select('customer_code')
			->order_by('id', 'DESC')
			->limit(1)
			->get('customers')
			->row();

		$number = $row
			? (int) filter_var($row->customer_code, FILTER_SANITIZE_NUMBER_INT) + 1
			: 1;

		return 'CUST-' . str_pad($number, 4, '0', STR_PAD_LEFT);
	}

	public function customerCodeExists($code, $exclude_id = null)
	{
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}

		return $this->db
			->where('customer_code', $code)
			->count_all_results('customers') > 0;
	}

	/* ===================== CRUD ===================== */

	public function create($data)
	{
		if (empty($data['customer_code'])) {
			$data['customer_code'] = $this->generateCustomerCode();
		}

		$insert = $this->db->insert('customers', $data);
		return $insert ? $this->db->insert_id() : false;
	}

	public function update($data, $id)
	{
		return $this->db
			->where('id', $id)
			->update('customers', $data);
	}

	public function remove($id)
	{
		$order_count = $this->db
			->where('customer_id', $id)
			->count_all_results('orders');

		if ($order_count > 0) {
			return $this->db
				->where('id', $id)
				->update('customers', ['active' => 0]);
		}

		return $this->db
			->where('id', $id)
			->delete('customers');
	}

	/* ===================== BALANCE ===================== */

	public function updateBalance($customer_id, $amount)
	{
		if ($customer_id && $amount != 0) {
			$sql = "UPDATE customers 
					SET current_balance = current_balance + ? 
					WHERE id = ?";
			return $this->db->query($sql, [$amount, $customer_id]);
		}
		return false;
	}

	/* ===================== STATS ===================== */

	public function getCustomerStats($customer_id)
	{
		$sql = "SELECT 
					COUNT(*) total_orders,
					SUM(net_amount) total_sales,
					MAX(date_time) last_order_date
				FROM orders
				WHERE customer_id = ?";

		return $this->db->query($sql, [$customer_id])->row_array();
	}

	public function countTotalCustomers()
	{
		return $this->db
			->where('active', 1)
			->count_all_results('customers');
	}

	public function countCustomersByType()
	{
		$sql = "SELECT customer_type, COUNT(*) count
				FROM customers
				WHERE active = 1
				GROUP BY customer_type";

		return $this->db->query($sql)->result_array();
	}
}
