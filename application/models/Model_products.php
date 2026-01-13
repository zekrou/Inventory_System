<?php

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get product data with complete details
	 * Includes: stock_name, categories, brand_name, status, suppliers
	 */
	/**
	 * Get product data with all related information
	 */
	public function getProductData($id = null)
	{
		if ($id) {
			// Get single product with full details
			$sql = "SELECT p.*, s.name as stock_name, s.id as stock_id 
                FROM products p 
                LEFT JOIN stock s ON p.stock_id = s.id 
                WHERE p.id = ?";

			$query = $this->db->query($sql, array($id));
			$product = $query->row_array();

			if ($product) {
				// ✅ IMPORTANT: S'assurer que les prix existent
				$product['price_superwholesale'] = isset($product['price_superwholesale']) ? $product['price_superwholesale'] : 0;
				$product['price_wholesale'] = isset($product['price_wholesale']) ? $product['price_wholesale'] : 0;
				$product['price_retail'] = isset($product['price_retail']) ? $product['price_retail'] : 0;
				$product['price_default'] = isset($product['price_default']) ? $product['price_default'] : 0;

				// ✅ Recalculate average_cost from actual purchases
				$this->recalculateAverageCost($id);

				// Re-fetch to get updated average_cost
				$query = $this->db->query($sql, array($id));
				$product = $query->row_array();

				// Add categories
				$product['categories'] = $this->getProductCategories($id);
				$product['category_names'] = $this->getProductCategoryNames($id);
				$product['category_ids'] = $this->getProductCategoryIds($id);
				$product['brand_ids'] = $this->getProductBrandIds($id);

				// Add brand names
				$product['brand_name'] = $this->getProductBrandName($product);

				// Add suppliers
				$product['suppliers'] = $this->getProductSuppliers($id);

				// Ensure status is up to date
				$this->updateProductStatus($id);

				return $product;
			}

			return null;
		}

		// Get all products with stock name
		$sql = "SELECT p.*, s.name as stock_name
            FROM `products` p
            LEFT JOIN `stock` s ON p.stock_id = s.id
            ORDER BY p.id DESC";
		$query = $this->db->query($sql);
		$products = $query->result_array();

		// Add categories and brands for each
		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);

			// ✅ AJOUTE CES 2 LIGNES:
			$product['category_ids'] = $this->getProductCategoryIds($product['id']);
			$product['brand_ids'] = $this->getProductBrandIds($product['id']);
		}

		return $products;
	}
	/**
	 * Recalculate average cost from purchase history
	 * Uses actual database column names with underscores
	 */
	public function recalculateAverageCost($product_id)
	{
		if (!$product_id) {
			return false;
		}

		// Calculate average cost from purchase items
		$sql = "SELECT 
                AVG(unit_price) as avg_cost, 
                MAX(unit_price) as last_price,
                COUNT(*) as purchase_count
            FROM purchase_items 
            WHERE product_id = ?";

		$query = $this->db->query($sql, array($product_id));
		$result = $query->row_array();

		if ($result && $result['purchase_count'] > 0) {
			$avg_cost = $result['avg_cost'] ? $result['avg_cost'] : 0;
			$last_price = $result['last_price'] ? $result['last_price'] : 0;

			// Update product with calculated average
			$update_sql = "UPDATE products 
                       SET average_cost = ?, 
                           last_purchase_price = ?,
                           purchase_price_updated_at = NOW()
                       WHERE id = ?";

			$this->db->query($update_sql, array($avg_cost, $last_price, $product_id));

			return array(
				'success' => true,
				'average_cost' => $avg_cost,
				'last_purchase_price' => $last_price
			);
		}

		return array('success' => false, 'message' => 'No purchase history found');
	}

	/**
	 * ✅ CORRIGÉ: Récupérer les IDs des brands d'un produit
	 */
	public function getProductBrandIds($product_id)
	{
		// Vérifier si la table product_brands existe
		if ($this->db->table_exists('product_brands')) {
			$sql = "SELECT brand_id FROM product_brands WHERE product_id = ?";
			$query = $this->db->query($sql, array($product_id));
			if ($query->num_rows() > 0) {
				$results = $query->result_array();
				$brand_ids = array();
				foreach ($results as $row) {
					$brand_ids[] = $row['brand_id'];
				}
				return $brand_ids;
			}
		}
		return array(); // Retourner un tableau vide
	}




	/**
	 * ✅ NOUVELLE FONCTION: Récupérer les IDs des catégories d'un produit
	 */
	public function getProductCategoryIds($product_id)
	{
		$sql = "SELECT category_id FROM category_product WHERE product_id = ?";  // ✅ CORRIGÉ
		$query = $this->db->query($sql, array($product_id));
		if ($query->num_rows() > 0) {
			$results = $query->result_array();
			$category_ids = array();
			foreach ($results as $row) {
				$category_ids[] = $row['category_id'];
			}
			return $category_ids;
		}
		return array();
	}



	/**
	 * Get active products only
	 */
	public function getActiveProductData()
	{
		$sql = "SELECT p.*, s.name as stock_name
				FROM `products` p
				LEFT JOIN `stock` s ON p.stock_id = s.id
				WHERE p.availability = ? 
				ORDER BY p.id DESC";
		$query = $this->db->query($sql, array(1));
		$products = $query->result_array();

		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}

		return $products;
	}

	/**
	 * Get product categories (from category_product table)
	 */
	public function getProductCategories($product_id)
	{
		$sql = "SELECT c.* FROM categories c
				INNER JOIN category_product cp ON c.id = cp.category_id
				WHERE cp.product_id = ?
				ORDER BY c.name ASC";
		$query = $this->db->query($sql, array($product_id));
		return $query->result_array();
	}

	/**
	 * Get product category names as string
	 */
	public function getProductCategoryNames($product_id)
	{
		$categories = $this->getProductCategories($product_id);
		$names = array();
		foreach ($categories as $cat) {
			$names[] = $cat['name'];
		}
		return !empty($names) ? implode(', ', $names) : '-';
	}

	/**
	 * Get product brand name (from JSON brand_id or direct)
	 */
	public function getProductBrandName($product)
	{
		if (!empty($product['brand_id'])) {
			// Try to decode JSON first
			$brand_ids = json_decode($product['brand_id']);
			if (is_array($brand_ids) && !empty($brand_ids)) {
				$this->load->model('model_brands');
				$brand = $this->model_brands->getBrandData($brand_ids[0]);
				if ($brand) {
					return $brand['name'];
				}
			}
		}
		return '-';
	}

	/**
	 * Get product suppliers
	 */
	public function getProductSuppliers($product_id)
	{
		$sql = "SELECT s.*, sp.supplier_price, sp.lead_time_days
				FROM suppliers s
				INNER JOIN supplier_product sp ON s.id = sp.supplier_id
				WHERE sp.product_id = ?
				ORDER BY s.name ASC";
		$query = $this->db->query($sql, array($product_id));
		return $query->result_array();
	}

	/**
	 * Get products by stock
	 */
	public function getProductsByStock($stock_id)
	{
		$sql = "SELECT p.*, s.name as stock_name
				FROM `products` p
				LEFT JOIN `stock` s ON p.stock_id = s.id
				WHERE p.stock_id = ?
				ORDER BY p.name ASC";
		$query = $this->db->query($sql, array($stock_id));
		$products = $query->result_array();

		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}

		return $products;
	}

	/**
	 * Get products by category
	 */
	public function getProductsByCategory($category_id)
	{
		$sql = "SELECT p.*, s.name as stock_name
				FROM `products` p
				INNER JOIN category_product cp ON p.id = cp.product_id
				LEFT JOIN `stock` s ON p.stock_id = s.id
				WHERE cp.category_id = ?
				ORDER BY p.name ASC";
		$query = $this->db->query($sql, array($category_id));
		$products = $query->result_array();

		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}

		return $products;
	}

	/**
	 * Update product status based on quantity
	 * Status: available, low_stock, not_available
	 */
	public function updateProductStatus($product_id)
	{
		if ($product_id) {
			$sql = "SELECT qty, alert_threshold FROM products WHERE id = ?";
			$query = $this->db->query($sql, array($product_id));
			$product = $query->row_array();

			if ($product) {
				$qty = $product['qty'];
				$threshold = $product['alert_threshold'] ? $product['alert_threshold'] : 10;

				$status = 'available';
				if ($qty == 0) {
					$status = 'not_available';
				} elseif ($qty <= $threshold) {
					$status = 'low_stock';
				}

				// Update status
				$update_sql = "UPDATE products SET status = ? WHERE id = ?";
				$this->db->query($update_sql, array($status, $product_id));

				return $status;
			}
		}
		return false;
	}

	/**
	 * Get products with low stock
	 */
	public function getLowStockProducts($threshold = 10)
	{
		$sql = "SELECT p.*, s.name as stock_name
				FROM products p
				LEFT JOIN stock s ON p.stock_id = s.id
				WHERE p.qty > 0 AND p.qty <= ? AND p.availability = 1
				ORDER BY p.qty ASC";
		$query = $this->db->query($sql, array($threshold));
		$products = $query->result_array();

		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}

		return $products;
	}

	/**
	 * Get out of stock products
	 */
	public function getOutOfStockProducts()
	{
		$sql = "SELECT p.*, s.name as stock_name
				FROM products p
				LEFT JOIN stock s ON p.stock_id = s.id
				WHERE p.qty = 0 AND p.availability = 1
				ORDER BY p.name ASC";
		$query = $this->db->query($sql);
		$products = $query->result_array();

		foreach ($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}

		return $products;
	}

	/**
	 * Create product
	 */
	public function create($data)
	{
		if ($data) {
			// Set default values
			if (!isset($data['alert_threshold'])) {
				$data['alert_threshold'] = 10;
			}

			$insert = $this->db->insert('products', $data);
			$product_id = $this->db->insert_id();

			if ($product_id) {
				// Update status after creation
				$this->updateProductStatus($product_id);
				return $product_id;
			}
			return false;
		}
		return false;
	}

	/**
	 * Update product
	 */
	public function update($data, $id)
	{
		if ($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);

			if ($update) {
				// Update status after update
				$this->updateProductStatus($id);
			}

			return ($update == true) ? true : false;
		}
		return false;
	}

	public function remove($id, $force_delete = false)
	{
		if (!$id) {
			return array('success' => false, 'type' => 'error', 'message' => 'ID invalide');
		}

		// Check orders
		$sql1 = "SELECT COUNT(*) as count FROM orders_item WHERE product_id = ?";
		$query1 = $this->db->query($sql1, array($id));
		$order_result = $query1->row_array();
		$order_count = $order_result['count'];

		// Check purchases
		$sql2 = "SELECT COUNT(*) as count FROM purchase_items WHERE product_id = ?";
		$query2 = $this->db->query($sql2, array($id));
		$purchase_result = $query2->row_array();
		$purchase_count = $purchase_result['count'];

		// Check stock_history
		$sql3 = "SELECT COUNT(*) as count FROM stock_history WHERE product_id = ?";
		$query3 = $this->db->query($sql3, array($id));
		$stock_result = $query3->row_array();
		$stock_count = $stock_result['count'];

		$total_usage = $order_count + $purchase_count + $stock_count;

		if ($total_usage > 0) {
			if ($force_delete === true) {
				// Force delete with CASCADE
				$this->db->trans_start();

				// 1. Delete from orders_item
				$this->db->where('product_id', $id);
				$this->db->delete('orders_item');

				// 2. Delete from purchase_items
				$this->db->where('product_id', $id);
				$this->db->delete('purchase_items');

				// 3. Delete from stock_history
				$this->db->where('product_id', $id);
				$this->db->delete('stock_history');

				// 4. Delete from category_product
				$this->db->where('product_id', $id);
				$this->db->delete('category_product');

				// 5. Finally delete the product
				$this->db->where('id', $id);
				$this->db->delete('products');

				$this->db->trans_complete();

				if ($this->db->trans_status() === FALSE) {
					return array('success' => false, 'type' => 'error', 'message' => 'Erreur de suppression');
				}

				return array('success' => true, 'type' => 'force_deleted', 'message' => 'Produit supprime definitivement');
			} else {
				$message = 'Ce produit est utilise dans ';
				$details = array();
				if ($order_count > 0) $details[] = $order_count . ' commande(s)';
				if ($purchase_count > 0) $details[] = $purchase_count . ' achat(s)';
				if ($stock_count > 0) $details[] = $stock_count . ' mouvement(s) stock';

				return array(
					'success' => false,
					'type' => 'has_relations',
					'message' => $message . implode(', ', $details)
				);
			}
		} else {
			// Safe delete (no relations)
			$this->db->trans_start();

			// Delete category links
			$this->db->where('product_id', $id);
			$this->db->delete('category_product');

			// Delete product
			$this->db->where('id', $id);
			$this->db->delete('products');

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				return array('success' => false, 'type' => 'error', 'message' => 'Erreur de suppression');
			}

			return array('success' => true, 'type' => 'deleted', 'message' => 'Produit supprime avec succes');
		}
	}










	// Deactivate product instead of deleting
	public function deactivate($id)
	{
		if ($id) {
			$data = array('availability' => 0);
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
		return false;
	}


	/**
	 * Link product to categories
	 */
	public function linkProductToCategories($product_id, $category_ids)
	{
		if ($product_id) {
			// First, remove existing links
			$this->db->where('product_id', $product_id);
			$this->db->delete('category_product');

			// Then add new links
			if (!empty($category_ids) && is_array($category_ids)) {
				foreach ($category_ids as $category_id) {
					$data = array(
						'product_id' => $product_id,
						'category_id' => $category_id
					);
					$this->db->insert('category_product', $data);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Count total products
	 */
	public function countTotalProducts()
	{
		$sql = "SELECT COUNT(*) as total FROM `products`";
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return $result['total'];
	}

	/**
	 * Get product price based on customer type
	 */
	public function getProductDataWithPrice($product_id, $customer_type = 'retail')
	{
		if ($product_id) {
			$sql = "SELECT * FROM `products` WHERE id = ?";
			$query = $this->db->query($sql, array($product_id));
			$product = $query->row_array();

			if ($product) {
				// Set the correct price based on customer type
				switch ($customer_type) {
					case 'super_wholesale':
						$product['price'] = $product['price_super_wholesale'] ? $product['price_super_wholesale'] : $product['price_default'];
						break;
					case 'wholesale':
						$product['price'] = $product['price_wholesale'] ? $product['price_wholesale'] : $product['price_default'];
						break;
					case 'retail':
						$product['price'] = $product['price_retail'] ? $product['price_retail'] : $product['price_default'];
						break;
					default:
						$product['price'] = $product['price_default'];
				}

				return $product;
			}
		}

		return false;
	}


	/**
	 * Get all products with pricing for a specific customer type
	 */
	public function getProductsWithPricing($customer_type = 'retail')
	{
		$sql = "SELECT * FROM `products` WHERE availability = ?";
		$query = $this->db->query($sql, array(1));
		$products = $query->result_array();

		foreach ($products as &$product) {
			// Set the correct price based on customer type
			switch ($customer_type) {
				case 'super_wholesale':
					$product['price'] = $product['price_super_wholesale'] ? $product['price_super_wholesale'] : $product['price_default'];
					break;
				case 'wholesale':
					$product['price'] = $product['price_wholesale'] ? $product['price_wholesale'] : $product['price_default'];
					break;
				case 'retail':
					$product['price'] = $product['price_retail'] ? $product['price_retail'] : $product['price_default'];
					break;
				default:
					$product['price'] = $product['price_default'];
			}
		}

		return $products;
	}

	/**
	 * Get product price history
	 */
	public function getProductPriceHistory($product_id, $limit = 10)
	{
		if ($product_id) {
			// Check if table exists
			if ($this->db->table_exists('product_price_history')) {
				$sql = "SELECT pph.*, u.username as changed_by_name
                    FROM product_price_history pph
                    LEFT JOIN users u ON pph.changed_by = u.id
                    WHERE pph.product_id = ?
                    ORDER BY pph.changed_at DESC
                    LIMIT ?";

				$query = $this->db->query($sql, array($product_id, $limit));
				return $query->result_array();
			}
		}

		return array();
	}

	/**
	 * Record price change in history
	 */
	public function recordPriceChange($product_id, $old_price, $new_price, $purchase_id = null, $reason = null)
	{
		if ($this->db->table_exists('product_price_history')) {
			$user_id = $this->session->userdata('id');

			$data = array(
				'product_id' => $product_id,
				'old_price' => $old_price,
				'new_price' => $new_price,
				'purchase_id' => $purchase_id,
				'changed_by' => $user_id,
				'changed_at' => date('Y-m-d H:i:s'),
				'reason' => $reason
			);

			$this->db->insert('product_price_history', $data);
			return true;
		}

		return false;
	}

	/**
	 * Get product sales statistics
	 */
	public function getProductStatistics($product_id)
	{
		if ($product_id) {
			$sql = "SELECT 
                COUNT(DISTINCT oi.order_id) as total_orders,
                SUM(oi.qty) as total_sold,
                SUM(oi.amount) as total_revenue,
                AVG(oi.rate) as average_price,
                MAX(o.date_time) as last_sold_date
                FROM orders_item oi
                INNER JOIN orders o ON oi.order_id = o.id
                WHERE oi.product_id = ?
                AND o.paid_status IN (1, 3)";

			$query = $this->db->query($sql, array($product_id));
			$result = $query->row_array();

			// Set defaults if no data
			if (!$result || $result['total_orders'] == 0) {
				return array(
					'total_orders' => 0,
					'total_sold' => 0,
					'total_revenue' => 0,
					'average_price' => 0,
					'last_sold_date' => null
				);
			}

			return $result;
		}

		return array();
	}

	/**
	 * Get purchase history for a product
	 */
	public function getProductPurchaseHistory($product_id, $limit = 10)
	{
		if ($product_id) {
			$sql = "SELECT 
                p.purchase_no,
                p.purchase_date,
                pi.quantity,
                pi.unit_price,
                pi.total_price,
                s.name as supplier_name
                FROM purchase_items pi
                INNER JOIN purchases p ON pi.purchase_id = p.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE pi.product_id = ?
                ORDER BY p.purchase_date DESC
                LIMIT ?";

			$query = $this->db->query($sql, array($product_id, $limit));
			return $query->result_array();
		}

		return array();
	}
}
