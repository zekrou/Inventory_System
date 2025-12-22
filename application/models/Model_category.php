<?php 

class Model_category extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get active categories */
	public function getActiveCategroy()
	{
		$sql = "SELECT c.*, s.name as stock_name FROM `categories` c
				LEFT JOIN `stock` s ON c.stock_id = s.id
				WHERE c.active = ?
				ORDER BY c.name ASC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	/* get categories by stock */
	public function getCategoriesByStock($stock_id)
	{
		$sql = "SELECT * FROM `categories` WHERE stock_id = ? AND active = ? ORDER BY name ASC";
		$query = $this->db->query($sql, array($stock_id, 1));
		return $query->result_array();
	}

	/* get the category data */
	public function getCategoryData($id = null)
	{
		if($id) {
			$sql = "SELECT c.*, s.name as stock_name FROM `categories` c
					LEFT JOIN `stock` s ON c.stock_id = s.id
					WHERE c.id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT c.*, s.name as stock_name FROM `categories` c
				LEFT JOIN `stock` s ON c.stock_id = s.id
				ORDER BY c.name ASC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('categories', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('categories', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			// Check if category has products
			$sql = "SELECT COUNT(*) as count FROM `category_product` WHERE category_id = ?";
			$query = $this->db->query($sql, array($id));
			$result = $query->row_array();
			
			if($result['count'] > 0) {
				// Has products, deactivate
				$this->db->where('id', $id);
				return $this->db->update('categories', array('active' => 0));
			} else {
				// No products, safe to delete
				// Remove brand assignments first
				$this->db->where('category_id', $id);
				$this->db->delete('category_brands');
				
				// Then delete category
				$this->db->where('id', $id);
				return $this->db->delete('categories');
			}
		}
	}

	/**
	 * BRAND RELATIONSHIP METHODS
	 */
	public function getCategoryBrands($category_id)
	{
		if($category_id) {
			$sql = "SELECT b.* FROM brands b
					INNER JOIN category_brands cb ON b.id = cb.brand_id
					WHERE cb.category_id = ? AND b.active = 1
					ORDER BY b.name ASC";
			$query = $this->db->query($sql, array($category_id));
			return $query->result_array();
		}
		return array();
	}

	public function getCategoryBrandIds($category_id)
	{
		if($category_id) {
			$sql = "SELECT brand_id FROM category_brands WHERE category_id = ?";
			$query = $this->db->query($sql, array($category_id));
			$results = $query->result_array();
			
			$brand_ids = array();
			foreach($results as $row) {
				$brand_ids[] = $row['brand_id'];
			}
			return $brand_ids;
		}
		return array();
	}

	public function assignBrands($category_id, $brand_ids)
	{
		if($category_id) {
			// Remove existing
			$this->db->where('category_id', $category_id);
			$this->db->delete('category_brands');
			
			// Add new
			if(!empty($brand_ids) && is_array($brand_ids)) {
				foreach($brand_ids as $brand_id) {
					$data = array(
						'category_id' => $category_id,
						'brand_id' => $brand_id
					);
					$this->db->insert('category_brands', $data);
				}
			}
			return true;
		}
		return false;
	}

	public function getCategoryWithDetails($category_id)
	{
		if($category_id) {
			$category = $this->getCategoryData($category_id);
			
			if($category) {
				$category['brands'] = $this->getCategoryBrands($category_id);
				$category['brand_ids'] = $this->getCategoryBrandIds($category_id);
				return $category;
			}
		}
		return null;
	}

	public function getCategoriesWithCounts()
	{
		$sql = "SELECT 
					c.*,
					s.name as stock_name,
					COUNT(DISTINCT cb.brand_id) as brand_count,
					COUNT(DISTINCT cp.product_id) as product_count
				FROM categories c
				LEFT JOIN stock s ON c.stock_id = s.id
				LEFT JOIN category_brands cb ON c.id = cb.category_id
				LEFT JOIN category_product cp ON c.id = cp.category_id
				GROUP BY c.id
				ORDER BY c.name ASC";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}