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
	public function getProductData($id = null)
	{
		if($id) {
			// Get single product with full details
			$sql = "SELECT p.*, s.name as stock_name, s.id as stock_id
					FROM `products` p
					LEFT JOIN `stock` s ON p.stock_id = s.id
					WHERE p.id = ?";
			$query = $this->db->query($sql, array($id));
			$product = $query->row_array();
			
			if($product) {
				// Add categories
				$product['categories'] = $this->getProductCategories($id);
				$product['category_names'] = $this->getProductCategoryNames($id);
				
				// Add brand
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
		foreach($products as &$product) {
			$product['category_names'] = $this->getProductCategoryNames($product['id']);
			$product['brand_name'] = $this->getProductBrandName($product);
		}
		
		return $products;
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
		
		foreach($products as &$product) {
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
		foreach($categories as $cat) {
			$names[] = $cat['name'];
		}
		return !empty($names) ? implode(', ', $names) : '-';
	}

	/**
	 * Get product brand name (from JSON brand_id or direct)
	 */
	public function getProductBrandName($product)
	{
		if(!empty($product['brand_id'])) {
			// Try to decode JSON first
			$brand_ids = json_decode($product['brand_id']);
			if(is_array($brand_ids) && !empty($brand_ids)) {
				$this->load->model('model_brands');
				$brand = $this->model_brands->getBrandData($brand_ids[0]);
				if($brand) {
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
		
		foreach($products as &$product) {
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
		
		foreach($products as &$product) {
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
		if($product_id) {
			$sql = "SELECT qty, alert_threshold FROM products WHERE id = ?";
			$query = $this->db->query($sql, array($product_id));
			$product = $query->row_array();
			
			if($product) {
				$qty = $product['qty'];
				$threshold = $product['alert_threshold'] ? $product['alert_threshold'] : 10;
				
				$status = 'available';
				if($qty == 0) {
					$status = 'not_available';
				} elseif($qty <= $threshold) {
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
		
		foreach($products as &$product) {
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
		
		foreach($products as &$product) {
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
		if($data) {
			// Set default values
			if(!isset($data['alert_threshold'])) {
				$data['alert_threshold'] = 10;
			}
			
			$insert = $this->db->insert('products', $data);
			$product_id = $this->db->insert_id();
			
			if($product_id) {
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
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			
			if($update) {
				// Update status after update
				$this->updateProductStatus($id);
			}
			
			return ($update == true) ? true : false;
		}
		return false;
	}

	/**
	 * Remove product
	 */
	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
		return false;
	}

	/**
	 * Link product to categories
	 */
	public function linkProductToCategories($product_id, $category_ids)
	{
		if($product_id) {
			// First, remove existing links
			$this->db->where('product_id', $product_id);
			$this->db->delete('category_product');
			
			// Then add new links
			if(!empty($category_ids) && is_array($category_ids)) {
				foreach($category_ids as $category_id) {
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
		if($product_id) {
			$sql = "SELECT * FROM `products` WHERE id = ?";
			$query = $this->db->query($sql, array($product_id));
			$product = $query->row_array();
			
			if($product) {
				// Set the correct price based on customer type
				switch($customer_type) {
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
			switch($customer_type) {
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

}