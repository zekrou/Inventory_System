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
		$sql = "SELECT * FROM customers 
            WHERE active = 1 
            ORDER BY customer_name ASC";
		return $this->db->query($sql)->result_array();
	}

	// Ajoutez cette méthode APRÈS getActiveCustomers() - ligne 25
	public function getActiveCustomersForDropdown()
	{
		$sql = "SELECT 
            id,
            customer_code,
            customer_name as name,
            customer_type,
            phone,
            address,
            email
            FROM customers 
            WHERE active = 1 
            ORDER BY customer_name ASC";

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
	public function findCustomer($name, $phone)
	{
		$this->db->select('id, customer_name as name, phone, address, customer_type');
		$this->db->from('customers');

		if ($name) {
			$this->db->where('customer_name', $name);
		}
		if ($phone) {
			$this->db->or_where('phone', $phone);
		}

		$this->db->where('active', 1);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}

		return false;
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
		// Essayer de générer un code unique jusqu'à 10 fois
		$attempts = 0;
		$max_attempts = 10;

		do {
			// Obtenir le dernier code utilisé
			$row = $this->db
				->select('customer_code')
				->order_by('id', 'DESC')
				->limit(1)
				->get('customers')
				->row();

			if ($row) {
				// Extraire le numéro du dernier code
				$last_number = (int) filter_var($row->customer_code, FILTER_SANITIZE_NUMBER_INT);
				$number = $last_number + 1;
			} else {
				$number = 1;
			}

			$code = 'CUST-' . str_pad($number, 4, '0', STR_PAD_LEFT);

			// Vérifier si le code existe déjà
			$exists = $this->customerCodeExists($code);

			$attempts++;

			// Si le code n'existe pas, on sort de la boucle
			if (!$exists) {
				return $code;
			}

			// Si le code existe, on force l'incrémentation en cherchant le max ID
			if ($attempts >= $max_attempts) {
				// En dernier recours, utiliser un timestamp
				$number = time() % 10000;
				$code = 'CUST-' . str_pad($number, 4, '0', STR_PAD_LEFT);
			}
		} while ($attempts < $max_attempts);

		return $code;
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
	public function getTopCustomerThisMonth()
	{
		$month = date('Y-m');
		$sql = "SELECT 
            customer_name,
            SUM(net_amount) as total_spent
            FROM orders 
            WHERE DATE_FORMAT(date_time, '%Y-%m') = ?
            AND paid_status = 1
            GROUP BY customer_name
            ORDER BY total_spent DESC
            LIMIT 1";

		$query = $this->db->query($sql, array($month));

		if ($query->num_rows() > 0) {
			return $query->row_array();
		}

		return array('customer_name' => 'N/A', 'total_spent' => 0);
	}
}
