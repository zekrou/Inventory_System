<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Stock Management';
        $this->load->model('model_stock');
        $this->load->model('model_products');
        $this->load->model('model_category');
    }

    public function index()
    {
        if(!in_array('viewStock', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        // Get filter parameters
        $category_filter = $this->input->get('category');
        $stock_filter = $this->input->get('stock');
        $status_filter = $this->input->get('status');

        // Load data for filters
        $this->data['categories'] = $this->model_category->getActiveCategroy();
        $this->data['stocks'] = $this->model_stock->getActiveStock();
        
        // Apply filters
        $this->data['current_category'] = $category_filter;
        $this->data['current_stock'] = $stock_filter;
        $this->data['current_status'] = $status_filter;

        // Get products based on filters
        $products = $this->model_products->getProductData();
        
        // Filter products
        $filtered_products = array();
        foreach($products as $product) {
            $include = true;
            
            // Category filter
            if($category_filter) {
                $product_categories = json_decode($product['category_id']);
                if(!in_array($category_filter, $product_categories)) {
                    $include = false;
                }
            }
            
            // Stock filter
            if($stock_filter && $product['stock_id'] != $stock_filter) {
                $include = false;
            }
            
            // Status filter
            if($status_filter) {
                $qty = $product['qty'];
                if($status_filter == 'good' && $qty <= 10) $include = false;
                if($status_filter == 'low' && ($qty == 0 || $qty > 10)) $include = false;
                if($status_filter == 'critical' && ($qty == 0 || $qty > 10)) $include = false;
                if($status_filter == 'out_of_stock' && $qty != 0) $include = false;
            }
            
            if($include) {
                // Add status labels
                if($product['qty'] == 0) {
                    $product['stock_status_label'] = 'Out of Stock';
                    $product['stock_status_class'] = 'danger';
                } else if($product['qty'] <= 10) {
                    $product['stock_status_label'] = 'Low Stock';
                    $product['stock_status_class'] = 'warning';
                } else {
                    $product['stock_status_label'] = 'Good Stock';
                    $product['stock_status_class'] = 'success';
                }
                
                $filtered_products[] = $product;
            }
        }
        
        $this->data['products'] = $filtered_products;
        
        // Get statistics
        $this->data['statistics'] = $this->model_stock->getStockStatistics();

        $this->render_template('stock/index', $this->data);
    }

    public function create()
    {
        if(!in_array('createStock', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $response = array();
        $this->form_validation->set_rules('stock_name', 'Stock name', 'trim|required');
        $this->form_validation->set_rules('active', 'Active', 'trim|required');
        $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'name' => $this->input->post('stock_name'),
                'description' => $this->input->post('description'),
                'active' => $this->input->post('active'),
            );

            $create = $this->model_stock->create($data);
            if($create) {
                $response['success'] = true;
                $response['messages'] = 'Successfully created';
            } else {
                $response['success'] = false;
                $response['messages'] = 'Error in the database while creating stock';
            }
        } else {
            $response['success'] = false;
            foreach ($_POST as $key => $value) {
                $response['messages'][$key] = form_error($key);
            }
        }

        echo json_encode($response);
    }

    public function fetchStockDataById($id)
    {
        if($id) {
            $data = $this->model_stock->getStockData($id);
            echo json_encode($data);
        }
    }

    public function fetchStockData()
    {
        $result = array('data' => array());
        $data = $this->model_stock->getStockData();

        foreach ($data as $key => $value) {
            $buttons = '';
            
            if(in_array('updateStock', $this->permission)) {
                $buttons .= '<button type="button" class="btn btn-default" onclick="editFunc('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button>';
            }

            if(in_array('deleteStock', $this->permission)) {
                $buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }

            $status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

            $result['data'][$key] = array(
                $value['name'],
                $value['description'],
                $status,
                $buttons
            );
        }

        echo json_encode($result);
    }

    public function update($id)
    {
        if(!in_array('updateStock', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $response = array();

        if($id) {
            $this->form_validation->set_rules('edit_stock_name', 'Stock name', 'trim|required');
            $this->form_validation->set_rules('edit_active', 'Active', 'trim|required');
            $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('edit_stock_name'),
                    'description' => $this->input->post('edit_description'),
                    'active' => $this->input->post('edit_active'),
                );

                $update = $this->model_stock->update($data, $id);
                if($update == true) {
                    $response['success'] = true;
                    $response['messages'] = 'Successfully updated';
                } else {
                    $response['success'] = false;
                    $response['messages'] = 'Error in the database while updating';
                }
            } else {
                $response['success'] = false;
                foreach ($_POST as $key => $value) {
                    $response['messages'][$key] = form_error($key);
                }
            }
        } else {
            $response['success'] = false;
            $response['messages'] = 'Error please refresh the page again!!';
        }

        echo json_encode($response);
    }

    public function remove()
    {
        if(!in_array('deleteStock', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $stock_id = $this->input->post('stock_id');
        $response = array();
        
        if($stock_id) {
            $delete = $this->model_stock->remove($stock_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed";
            } else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing stock";
            }
        } else {
            $response['success'] = false;
            $response['messages'] = "Refresh the page again!!";
        }

        echo json_encode($response);
    }

    public function viewDetails($stock_id)
    {
        if(!in_array('viewStock', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$stock_id) {
            redirect('stock', 'refresh');
        }

        $this->data['stock_data'] = $this->model_stock->getStockWithCategories($stock_id);
        $this->data['categories'] = $this->model_category->getCategoriesByStock($stock_id);
        $this->data['statistics'] = $this->model_stock->getStockStatistics($stock_id);
        
        // Get products for this stock
        $this->data['products'] = $this->model_products->getProductsByStock($stock_id);

        $this->render_template('stock/details', $this->data);
    }
}