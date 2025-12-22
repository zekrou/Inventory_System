<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Products';

		$this->load->model('model_products');
        $this->load->model('model_category');
		$this->load->model('model_brands');
		$this->load->model('model_stock'); // remplacé model_stores → model_stock
	}

	public function index()
	{
        if(!in_array('viewProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

		$this->render_template('products/index', $this->data);	
	}

	public function fetchProductData()
	{
		$result = array('data' => array());

		$data = $this->model_products->getProductData();

		if (!$data) {
			$data = array();
		}

		foreach ($data as $key => $value) {

			// Stock name
			$stock_name = '';
			if (!empty($value['stock_id'])) {
				$stock_data = $this->model_stock->getStockData($value['stock_id']);
				$stock_name = isset($stock_data['name']) ? $stock_data['name'] : '';
			}

			// Buttons
			$buttons = '';
			if(in_array('updateProduct', $this->permission)) {
				$buttons .= '<a href="'.base_url('products/update/'.$value['id']).'" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			}

			if(in_array('deleteProduct', $this->permission)) { 
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			}

			// Image
			$img_path = !empty($value['image']) ? base_url($value['image']) : base_url('assets/images/no_image.png');
			$img = '<img src="'.$img_path.'" alt="'.htmlspecialchars($value['name']).'" class="img-circle" width="50" height="50" />';

			// Availability
			$availability = ($value['availability'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			// Quantity
			$qty_status = '';
			if(isset($value['qty'])) {
				if($value['qty'] <= 0) {
					$qty_status = '<span class="label label-danger">Out of stock !</span>';
				} else if($value['qty'] <= 10) {
					$qty_status = '<span class="label label-warning">Low !</span>';
				}
			} else {
				$value['qty'] = 0;
			}

			// Price display
			$price_display = isset($value['price_default']) ? $value['price_default'] : (isset($value['price']) ? $value['price'] : '0.00');

			$result['data'][$key] = array(
				$img,
				isset($value['sku']) ? $value['sku'] : '',
				isset($value['name']) ? $value['name'] : '',
				$price_display . ' DZD',
				$value['qty'] . ' ' . $qty_status,
				$stock_name,
				$availability,
				$buttons
			);
		}

		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function create()
	{
		if(!in_array('createProduct', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
		$this->form_validation->set_rules('price_default', 'Cost Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_super_wholesale', 'Super Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_wholesale', 'Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_retail', 'Détail Price', 'trim|required|numeric');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required');
		$this->form_validation->set_rules('stock', 'Stock', 'trim|required'); // remplacé store → stock
		$this->form_validation->set_rules('availability', 'Availability', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$upload_image = $this->upload_image();

			$data = array(
				'name' => $this->input->post('product_name'),
				'sku' => $this->input->post('sku'),
				'price_default' => $this->input->post('price_default'),
				'price_super_wholesale' => $this->input->post('price_super_wholesale'),
				'price_wholesale' => $this->input->post('price_wholesale'),
				'price_retail' => $this->input->post('price_retail'),
				'qty' => $this->input->post('qty'),
				'image' => $upload_image,
				'description' => $this->input->post('description'),
				'brand_id' => json_encode($this->input->post('brands')),
				'category_id' => json_encode($this->input->post('category')),
				'stock_id' => $this->input->post('stock'), // remplacé store_id → stock_id
				'availability' => $this->input->post('availability'),
			);

			$create = $this->model_products->create($data);
			if($create == true) {
				$this->session->set_flashdata('success', 'Successfully created');
				redirect('products/', 'refresh');
			}
			else {
				$this->session->set_flashdata('errors', 'Error occurred!!');
				redirect('products/create', 'refresh');
			}
		}
		else {
			$this->data['brands'] = $this->model_brands->getActiveBrands();
			$this->data['category'] = $this->model_category->getActiveCategroy();
			$this->data['stocks'] = $this->model_stock->getActiveStock(); // remplacé stores → stocks

			$this->render_template('products/create', $this->data);
		}
	}

	public function update($product_id)
	{      
        if(!in_array('updateProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if(!$product_id) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
        $this->form_validation->set_rules('sku', 'SKU', 'trim|required');
        $this->form_validation->set_rules('price_default', 'Cost Price', 'trim|required|numeric');
        $this->form_validation->set_rules('price_super_wholesale', 'Super Gros Price', 'trim|required|numeric');
        $this->form_validation->set_rules('price_wholesale', 'Gros Price', 'trim|required|numeric');
        $this->form_validation->set_rules('price_retail', 'Détail Price', 'trim|required|numeric');
        $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('stock', 'Stock', 'trim|required'); // remplacé store → stock
        $this->form_validation->set_rules('availability', 'Availability', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'name' => $this->input->post('product_name'),
                'sku' => $this->input->post('sku'),
                'price_default' => $this->input->post('price_default'),
                'price_super_wholesale' => $this->input->post('price_super_wholesale'),
                'price_wholesale' => $this->input->post('price_wholesale'),
                'price_retail' => $this->input->post('price_retail'),
                'qty' => $this->input->post('qty'),
                'description' => $this->input->post('description'),
                'brand_id' => json_encode($this->input->post('brands')),
                'category_id' => json_encode($this->input->post('category')),
                'stock_id' => $this->input->post('stock'), // remplacé store_id → stock_id
                'availability' => $this->input->post('availability'),
            );

            if($_FILES['product_image']['size'] > 0) {
                $upload_image = $this->upload_image();
                $upload_image = array('image' => $upload_image);
                $this->model_products->update($upload_image, $product_id);
            }

            $update = $this->model_products->update($data, $product_id);
            if($update == true) {
                $this->session->set_flashdata('success', 'Successfully updated');
                redirect('products/', 'refresh');
            }
            else {
                $this->session->set_flashdata('errors', 'Error occurred!!');
                redirect('products/update/'.$product_id, 'refresh');
            }
        }
        else {
            $this->data['brands'] = $this->model_brands->getActiveBrands();         
            $this->data['category'] = $this->model_category->getActiveCategroy();           
            $this->data['stocks'] = $this->model_stock->getActiveStock(); // remplacé stores → stocks

            $product_data = $this->model_products->getProductData($product_id);
            $this->data['product_data'] = $product_data;
            $this->render_template('products/edit', $this->data); 
        }   
	}

	public function remove()
	{
        if(!in_array('deleteProduct', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $product_id = $this->input->post('product_id');

        $response = array();
        if($product_id) {
            $delete = $this->model_products->remove($product_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Successfully removed"; 
            }
            else {
                $response['success'] = false;
                $response['messages'] = "Error in the database while removing the product information";
            }
        }
        else {
            $response['success'] = false;
            $response['messages'] = "Refresh the page again!!";
        }

        echo json_encode($response);
	}

	public function upload_image()
    {
        $config['upload_path'] = 'assets/images/product_image';
        $config['file_name'] =  uniqid();
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1000';

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('product_image'))
        {
            $error = $this->upload->display_errors();
            return $error;
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());
            $type = explode('.', $_FILES['product_image']['name']);
            $type = $type[count($type) - 1];
            
            $path = $config['upload_path'].'/'.$config['file_name'].'.'.$type;
            return ($data == true) ? $path : false;            
        }
    }
}
