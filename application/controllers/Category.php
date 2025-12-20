<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Category';

		$this->load->model('model_category');
		$this->load->model('model_brands');
		$this->load->model('model_attributes');
	}

	public function index()
	{
		if(!in_array('viewCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		// Load brands and attributes for the management interface
		$this->data['all_brands'] = $this->model_brands->getActiveBrands();
		$this->data['all_attributes'] = $this->model_attributes->getActiveAttributeData();

		$this->render_template('category/index', $this->data);	
	}	

	public function fetchCategoryDataById($id) 
	{
		if($id) {
			$data = $this->model_category->getCategoryWithDetails($id);
			echo json_encode($data);
		}

		return false;
	}

	public function fetchCategoryData()
	{
		$result = array('data' => array());

		$data = $this->model_category->getCategoriesWithCounts();

		foreach ($data as $key => $value) {

			// Show brand and attribute counts
			$brand_count = '<span class="badge bg-blue">' . $value['brand_count'] . ' brands</span>';
			$attribute_count = '<span class="badge bg-green">' . $value['attribute_count'] . ' attributes</span>';

			// button
			$buttons = '';

			if(in_array('updateCategory', $this->permission)) {
				$buttons .= '<button type="button" class="btn btn-sm btn-info" onclick="manageBrandsAttributes('.$value['id'].')" title="Manage Brands & Attributes"><i class="fa fa-link"></i></button> ';
				$buttons .= '<button type="button" class="btn btn-sm btn-default" onclick="editFunc('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> ';
			}

			if(in_array('deleteCategory', $this->permission)) {
				$buttons .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}
				

			$status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			$result['data'][$key] = array(
				$value['name'],
				$brand_count,
				$attribute_count,
				$status,
				$buttons
			);
		}

		echo json_encode($result);
	}

	public function create()
	{
		if(!in_array('createCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('category_name', 'Category name', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'name' => $this->input->post('category_name'),
        		'active' => $this->input->post('active'),	
        	);

        	$create = $this->model_category->create($data);
        	if($create == true) {
        		$response['success'] = true;
        		$response['messages'] = 'Successfully created';
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Error in the database while creating the category information';			
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);
	}

	public function update($id)
	{
		if(!in_array('updateCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_category_name', 'Category name', 'trim|required');
			$this->form_validation->set_rules('edit_active', 'Active', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'name' => $this->input->post('edit_category_name'),
	        		'active' => $this->input->post('edit_active'),	
	        	);

	        	$update = $this->model_category->update($data, $id);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Successfully updated';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error in the database while updated the category information';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Error please refresh the page again!!';
		}

		echo json_encode($response);
	}

	public function remove()
	{
		if(!in_array('deleteCategory', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		
		$category_id = $this->input->post('category_id');

		$response = array();
		if($category_id) {
			$delete = $this->model_category->remove($category_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the category information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refresh the page again!!";
		}

		echo json_encode($response);
	}

	/**
	 * ========================================
	 * BRAND & ATTRIBUTE ASSIGNMENT METHODS
	 * ========================================
	 */

	/**
	 * Assign brands to category
	 */
	public function assignBrands($category_id)
	{
		if(!in_array('updateCategory', $this->permission)) {
			echo json_encode(array('success' => false, 'messages' => 'Permission denied'));
			return;
		}

		$response = array();

		if($category_id) {
			$brand_ids = $this->input->post('brand_ids');
			
			// brand_ids can be empty array if user unchecks all
			if(!is_array($brand_ids)) {
				$brand_ids = array();
			}

			$assign = $this->model_category->assignBrands($category_id, $brand_ids);
			
			if($assign) {
				$response['success'] = true;
				$response['messages'] = 'Brands assigned successfully';
			} else {
				$response['success'] = false;
				$response['messages'] = 'Error assigning brands';
			}
		} else {
			$response['success'] = false;
			$response['messages'] = 'Category ID is required';
		}

		echo json_encode($response);
	}

	/**
	 * Assign attributes to category
	 */
	public function assignAttributes($category_id)
	{
		if(!in_array('updateCategory', $this->permission)) {
			echo json_encode(array('success' => false, 'messages' => 'Permission denied'));
			return;
		}

		$response = array();

		if($category_id) {
			$attribute_ids = $this->input->post('attribute_ids');
			
			// attribute_ids can be empty array if user unchecks all
			if(!is_array($attribute_ids)) {
				$attribute_ids = array();
			}

			$assign = $this->model_category->assignAttributes($category_id, $attribute_ids);
			
			if($assign) {
				$response['success'] = true;
				$response['messages'] = 'Attributes assigned successfully';
			} else {
				$response['success'] = false;
				$response['messages'] = 'Error assigning attributes';
			}
		} else {
			$response['success'] = false;
			$response['messages'] = 'Category ID is required';
		}

		echo json_encode($response);
	}

	/**
	 * AJAX: Get brands for selected categories (for product creation)
	 */
	public function getBrandsForCategories()
	{
		$category_ids = $this->input->post('category_ids');
		$brands = array();

		if(!empty($category_ids) && is_array($category_ids)) {
			// Get brands for all selected categories
			foreach($category_ids as $category_id) {
				$category_brands = $this->model_category->getCategoryBrands($category_id);
				foreach($category_brands as $brand) {
					// Avoid duplicates
					if(!isset($brands[$brand['id']])) {
						$brands[$brand['id']] = $brand;
					}
				}
			}
		} else {
			// No category selected - return all brands
			$brands = $this->model_brands->getActiveBrands();
			$brands = array_column($brands, null, 'id');
		}

		echo json_encode(array_values($brands));
	}

	/**
	 * AJAX: Get attributes for selected categories (for product creation)
	 */
	public function getAttributesForCategories()
	{
		$category_ids = $this->input->post('category_ids');
		$attributes = array();

		if(!empty($category_ids) && is_array($category_ids)) {
			// Get attributes for all selected categories
			foreach($category_ids as $category_id) {
				$category_attrs = $this->model_category->getCategoryAttributesWithValues($category_id);
				foreach($category_attrs as $attr) {
					// Avoid duplicates
					$attr_id = $attr['attribute_data']['id'];
					if(!isset($attributes[$attr_id])) {
						$attributes[$attr_id] = $attr;
					}
				}
			}
		}

		echo json_encode(array_values($attributes));
	}

}