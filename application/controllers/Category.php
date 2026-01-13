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
		$this->load->model('model_stock'); // AJOUTÉ
	}

	public function index()
	{
		if(!isset($this->permission['viewCategory'])) {
			redirect('dashboard', 'refresh');
		}

		$this->data['stocks'] = $this->model_stock->getActiveStock(); // AJOUTÉ

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

			$status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			$buttons = '';
			if(isset($this->permission['updateCategory'])) {
				$buttons .= '<button type="button" class="btn btn-sm btn-info" onclick="editFunc('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> ';
			}
			if(isset($this->permission['deleteCategory'])) {
				$buttons .= '<button type="button" class="btn btn-sm btn-danger" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			$result['data'][$key] = array(
				$value['name'],
				$status,
				$value['stock_name'], // affichage du stock
				$buttons
			);
		}

		echo json_encode($result);
	}

	public function create()
	{
		if(!isset($this->permission['createCategory'])) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('category_name', 'Category name', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');
		$this->form_validation->set_rules('stock_id', 'Stock', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'name' => $this->input->post('category_name'),
        		'stock_id' => $this->input->post('stock_id'),  // NOUVEAU
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
		if(!isset($this->permission['updateCategory'])) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_category_name', 'Category name', 'trim|required');
			$this->form_validation->set_rules('edit_active', 'Active', 'trim|required');
			$this->form_validation->set_rules('edit_stock_id', 'Stock', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'name' => $this->input->post('edit_category_name'),
	        		'stock_id' => $this->input->post('edit_stock_id'),  // NOUVEAU
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
		if(!isset($this->permission['deleteCategory'])) {
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
}
