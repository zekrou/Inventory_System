<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CodeIgniter Properties
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Email $email
 * @property CI_Upload $upload
 * @property CI_Security $security
 * 
 * Custom Models
 * @property Model_products $model_products
 * @property Model_orders $model_orders
 * @property Model_users $model_users
 * @property Model_company $model_company
 * @property Model_groups $model_groups
 * @property Model_categories $model_categories
 * @property Model_category $model_category 
 * @property Model_brands $model_brands
 * @property Model_stores $model_stores
 * @property Model_attributes $model_attributes
 * @property Model_customers $model_customers
 * @property Model_suppliers $model_suppliers
 * @property Model_stock $model_stock
 * @property Model_purchases $model_purchases
 */
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
		$this->load->model('model_stock');
	}

	public function index()
	{
		if (!isset($this->permission['viewProduct'])) {
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

			// View button
			if (isset($this->permission['viewProduct'])) {
				$buttons .= '<a href="' . base_url('products/view/' . $value['id']) . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a> ';
			}

			// Edit button
			if (isset($this->permission['updateProduct'])) {
				$buttons .= '<a href="' . base_url('products/update/' . $value['id']) . '" class="btn btn-sm btn-default" title="Edit"><i class="fa fa-pencil"></i></a> ';
			}

			// Delete button
			if (isset($this->permission['deleteProduct'])) {
				$buttons .= '<button type="button" class="btn btn-danger" onclick="removeFunc(' . $value['id'] . ')"><i class="fa fa-trash"></i></button>';
			}

			// Image
			$img_path = !empty($value['image']) && file_exists($value['image'])
				? base_url($value['image'])
				: base_url('assets/images/product_image/default.png');
			$img = '<img src="' . $img_path . '" alt="' . htmlspecialchars($value['name']) . '" class="img-circle" width="50" height="50" />';

			// Availability
			$availability = ($value['availability'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			// Quantity
			$qty_status = '';
			if (isset($value['qty'])) {
				if ($value['qty'] <= 0) {
					$qty_status = ' <span class="label label-danger">Out of stock!</span>';
				} else if ($value['qty'] <= 10) {
					$qty_status = ' <span class="label label-warning">Low!</span>';
				}
			} else {
				$value['qty'] = 0;
			}

			// Price display
			$price_default = isset($value['price_default']) ? $value['price_default'] : '0.00';
			$price_retail = isset($value['price_retail']) ? $value['price_retail'] : '0.00';
			$price_wholesale = isset($value['price_wholesale']) ? $value['price_wholesale'] : '0.00';
			$price_super = isset($value['price_super_wholesale']) ? $value['price_super_wholesale'] : '0.00';

			$price_tooltip = 'Cost: ' . number_format($price_default, 2) . ' DZD\n' .
				'Retail: ' . number_format($price_retail, 2) . ' DZD\n' .
				'Wholesale: ' . number_format($price_wholesale, 2) . ' DZD\n' .
				'Super: ' . number_format($price_super, 2) . ' DZD';

			$price_display = '<span title="' . htmlspecialchars($price_tooltip) . '" data-toggle="tooltip" style="cursor:pointer;">' .
				number_format($price_default, 2) . ' DZD ' .
				'<i class="fa fa-info-circle text-muted"></i></span>';

			$result['data'][$key] = array(
				$img,
				isset($value['sku']) ? $value['sku'] : '',
				isset($value['name']) ? $value['name'] : '',
				$price_display,
				$value['qty'] . $qty_status,
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
		if (!isset($this->permission['createProduct'])) {
			redirect('dashboard', 'refresh');
		}

		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
		$this->form_validation->set_rules('price_default', 'Cost Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_super_wholesale', 'Super Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_wholesale', 'Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_retail', 'Détail Price', 'trim|required|numeric');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required');
		$this->form_validation->set_rules('stock', 'Stock', 'trim|required');
		$this->form_validation->set_rules('availability', 'Availability', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$upload_image = $this->upload_image();

			$data = array(
				'name'                   => $this->input->post('product_name'),
				'sku'                    => $this->input->post('sku'),
				'price_default'          => $this->input->post('price_default'),
				'price_super_wholesale'  => $this->input->post('price_super_wholesale'),
				'price_wholesale'        => $this->input->post('price_wholesale'),
				'price_retail'           => $this->input->post('price_retail'),
				'qty' => 0, // ✅ TOUJOURS 0 pour les nouveaux produits
				'image'                  => (is_string($upload_image) && strpos($upload_image, 'assets/') === 0) ? $upload_image : '',
				'description'            => $this->input->post('description'),
				'stock_id'               => $this->input->post('stock'),
				'availability'           => $this->input->post('availability'),
				'alert_threshold'        => 10,
				'average_cost'           => $this->input->post('price_default'), // Initialize
				'last_purchase_price'    => $this->input->post('price_default'), // Initialize
			);

			$product_id = $this->model_products->create($data);

			if ($product_id) {
				$category_ids = $this->input->post('category');
				$this->model_products->linkProductToCategories($product_id, $category_ids);

				$this->session->set_flashdata('success', 'Successfully created');
				redirect('products/', 'refresh');
			} else {
				$this->session->set_flashdata('errors', 'Error occurred!!');
				redirect('products/create', 'refresh');
			}
		} else {
			$this->data['brands'] = $this->model_brands->getActiveBrands();
			$this->data['category'] = $this->model_category->getActiveCategroy();
			$this->data['stocks'] = $this->model_stock->getActiveStock();

			$this->render_template('products/create', $this->data);
		}
	}

	public function update($product_id = null)
	{
		if (!isset($this->permission['updateProduct'])) {
			redirect('dashboard', 'refresh');
		}

		// ✅ Convertir en integer
		$product_id = intval($product_id);

		if ($product_id <= 0) {
			$this->session->set_flashdata('error', 'Product ID is required');
			redirect('products', 'refresh');
		}

		$this->form_validation->set_rules('product_name', 'Product name', 'trim|required');
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
		$this->form_validation->set_rules('price_default', 'Cost Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_super_wholesale', 'Super Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_wholesale', 'Gros Price', 'trim|required|numeric');
		$this->form_validation->set_rules('price_retail', 'Détail Price', 'trim|required|numeric');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required|numeric');
		$this->form_validation->set_rules('stock', 'Stock', 'trim|required');
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
				'stock_id' => $this->input->post('stock'),
				'availability' => $this->input->post('availability'),
			);

			if (isset($_FILES['product_image']) && $_FILES['product_image']['size'] > 0) {
				$upload_image = $this->upload_image();
				if (is_string($upload_image) && strpos($upload_image, 'assets/') === 0) {
					$data['image'] = $upload_image;
				}
			}

			$update = $this->model_products->update($data, $product_id);

			if ($update == true) {
				$category_ids = $this->input->post('category');
				if (!empty($category_ids)) {
					$this->model_products->linkProductToCategories($product_id, $category_ids);
				}

				$this->session->set_flashdata('success', 'Product updated successfully');
				redirect('products/', 'refresh');
			} else {
				$this->session->set_flashdata('errors', 'Error occurred while updating product');
				redirect('products/update/' . $product_id, 'refresh');
			}
		} else {
			$this->data['brands'] = $this->model_brands->getActiveBrands();
			$this->data['categories'] = $this->model_category->getActiveCategroy();
			$this->data['stocks'] = $this->model_stock->getActiveStock();

			$product_data = $this->model_products->getProductData($product_id);

			if (!$product_data) {
				$this->session->set_flashdata('error', 'Product not found');
				redirect('products', 'refresh');
			}

			$this->data['product_data'] = $product_data;

			$this->render_template('products/edit', $this->data);
		}
	}

	public function remove()
	{
		if (!isset($this->permission['deleteProduct'])) {
			echo json_encode(array('success' => false, 'messages' => 'Permission denied'));
			return;
		}

		$product_id = $this->input->post('product_id');
		$force_delete = $this->input->post('force_delete');
		$deactivate_only = $this->input->post('deactivate_only');

		$response = array();

		if ($product_id) {
			if ($deactivate_only == 'yes') {
				$deactivate = $this->model_products->deactivate($product_id);
				$response['success'] = $deactivate ? true : false;
				$response['messages'] = $deactivate ? "Produit desactive" : "Erreur";
			} else {
				$result = $this->model_products->remove($product_id, ($force_delete == 'yes'));

				$response['success'] = $result['success'];
				$response['messages'] = $result['message'];

				if (!$result['success'] && isset($result['type'])) {
					$response['type'] = $result['type'];
				}
			}
		} else {
			$response['success'] = false;
			$response['messages'] = "ID manquant";
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}



	public function upload_image()
	{
		$config['upload_path'] = 'assets/images/product_image';
		$config['file_name'] =  uniqid();
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '1000';

		$this->load->library('upload', $config);
		if (! $this->upload->do_upload('product_image')) {
			$error = $this->upload->display_errors();
			return $error;
		} else {
			$data = array('upload_data' => $this->upload->data());
			$type = explode('.', $_FILES['product_image']['name']);
			$type = $type[count($type) - 1];

			$path = $config['upload_path'] . '/' . $config['file_name'] . '.' . $type;
			return ($data == true) ? $path : false;
		}
	}

	/**
	 * View product details
	 */
	public function view($product_id = null)
	{
		if (!isset($this->permission['viewProduct'])) {
			redirect('dashboard', 'refresh');
		}

		$product_id = intval($product_id);
		if ($product_id <= 0) {
			$this->session->set_flashdata('error', 'Product ID is required');
			redirect('products', 'refresh');
		}

		// Get product data
		$product = $this->model_products->getProductData($product_id);
		if (!$product) {
			$this->session->set_flashdata('error', 'Product not found');
			redirect('products', 'refresh');
		}

		// Get price history
		$price_history = $this->model_products->getProductPriceHistory($product_id);

		// Get product statistics
		$product_stats = $this->model_products->getProductStatistics($product_id);

		// ✅ AJOUTEZ CETTE LIGNE - Get purchase history
		$purchase_history = $this->model_products->getProductPurchaseHistory($product_id, 10);

		$this->data['product'] = $product;
		$this->data['price_history'] = $price_history;
		$this->data['product_stats'] = $product_stats;
		$this->data['purchase_history'] = $purchase_history; // ✅ NOUVEAU

		$this->render_template('products/view', $this->data);
	}
}
