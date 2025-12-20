<?php 

class Model_category extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get active brand information */
	public function getActiveCategroy()
	{
		$sql = "SELECT * FROM `categories` WHERE active = ?";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	/* get the category data */
	public function getCategoryData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM `categories` WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM `categories`";
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
			$this->db->where('id', $id);
			$delete = $this->db->delete('categories');
			return ($delete == true) ? true : false;
		}
	}

	/**
	 * ========================================
	 * BRAND RELATIONSHIP METHODS
	 * ========================================
	 */

	/**
	 * Get brands assigned to a category
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

	/**
	 * Get brand IDs for a category (for dropdowns)
	 */
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

	/**
	 * Assign brands to a category
	 */
	public function assignBrands($category_id, $brand_ids)
	{
		if($category_id) {
			// First, remove all existing brand assignments
			$this->db->where('category_id', $category_id);
			$this->db->delete('category_brands');
			
			// Then add new assignments
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

	/**
	 * ========================================
	 * ATTRIBUTE RELATIONSHIP METHODS
	 * ========================================
	 */

	/**
	 * Get attributes assigned to a category
	 */
	public function getCategoryAttributes($category_id)
	{
		if($category_id) {
			$sql = "SELECT a.* FROM attributes a
					INNER JOIN category_attributes ca ON a.id = ca.attribute_id
					WHERE ca.category_id = ? AND a.active = 1
					ORDER BY a.name ASC";
			$query = $this->db->query($sql, array($category_id));
			return $query->result_array();
		}
		return array();
	}

	/**
	 * Get attribute IDs for a category
	 */
	public function getCategoryAttributeIds($category_id)
	{
		if($category_id) {
			$sql = "SELECT attribute_id FROM category_attributes WHERE category_id = ?";
			$query = $this->db->query($sql, array($category_id));
			$results = $query->result_array();
			
			$attribute_ids = array();
			foreach($results as $row) {
				$attribute_ids[] = $row['attribute_id'];
			}
			return $attribute_ids;
		}
		return array();
	}

	/**
	 * Assign attributes to a category
	 */
	public function assignAttributes($category_id, $attribute_ids)
	{
		if($category_id) {
			// Remove existing attribute assignments
			$this->db->where('category_id', $category_id);
			$this->db->delete('category_attributes');
			
			// Add new assignments
			if(!empty($attribute_ids) && is_array($attribute_ids)) {
				foreach($attribute_ids as $attribute_id) {
					$data = array(
						'category_id' => $category_id,
						'attribute_id' => $attribute_id
					);
					$this->db->insert('category_attributes', $data);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Get attributes with values for a category (for product creation)
	 */
	public function getCategoryAttributesWithValues($category_id)
	{
		if($category_id) {
			$attributes = $this->getCategoryAttributes($category_id);
			
			$this->load->model('model_attributes');
			$attributes_with_values = array();
			
			foreach($attributes as $attribute) {
				$attribute_values = $this->model_attributes->getAttributeValueData($attribute['id']);
				
				$attributes_with_values[] = array(
					'attribute_data' => $attribute,
					'attribute_value' => $attribute_values
				);
			}
			
			return $attributes_with_values;
		}
		return array();
	}

	/**
	 * Check if category has any attributes
	 */
	public function categoryHasAttributes($category_id)
	{
		if($category_id) {
			$sql = "SELECT COUNT(*) as count FROM category_attributes WHERE category_id = ?";
			$query = $this->db->query($sql, array($category_id));
			$result = $query->row_array();
			return ($result['count'] > 0);
		}
		return false;
	}

	/**
	 * Get category with full details (brands + attributes)
	 */
	public function getCategoryWithDetails($category_id)
	{
		if($category_id) {
			$category = $this->getCategoryData($category_id);
			
			if($category) {
				$category['brands'] = $this->getCategoryBrands($category_id);
				$category['brand_ids'] = $this->getCategoryBrandIds($category_id);
				$category['attributes'] = $this->getCategoryAttributes($category_id);
				$category['attribute_ids'] = $this->getCategoryAttributeIds($category_id);
				$category['has_attributes'] = $this->categoryHasAttributes($category_id);
				
				return $category;
			}
		}
		return null;
	}

	/**
	 * Get categories with their brand and attribute counts
	 */
	public function getCategoriesWithCounts()
	{
		$sql = "SELECT 
					c.*,
					COUNT(DISTINCT cb.brand_id) as brand_count,
					COUNT(DISTINCT ca.attribute_id) as attribute_count
				FROM categories c
				LEFT JOIN category_brands cb ON c.id = cb.category_id
				LEFT JOIN category_attributes ca ON c.id = ca.category_id
				GROUP BY c.id
				ORDER BY c.name ASC";
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}

}