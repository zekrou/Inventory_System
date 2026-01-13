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
 */
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
        if (!isset($this->permission['viewStock'])) {
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
        foreach ($products as $product) {
            $include = true;

            // ✅ FILTRE CATÉGORIE (seulement si un filtre est sélectionné)
            if ($category_filter && !empty($category_filter)) {
                if (isset($product['category_ids']) && is_array($product['category_ids'])) {
                    if (!in_array($category_filter, $product['category_ids'])) {
                        $include = false;
                    }
                } else {
                    // Pas de catégories pour ce produit, on l'exclut du filtre
                    $include = false;
                }
            }

            // ✅ FILTRE STOCK (seulement si un filtre est sélectionné)
            if ($stock_filter && !empty($stock_filter)) {
                if ($product['stock_id'] != $stock_filter) {
                    $include = false;
                }
            }

            // ✅ FILTRE STATUT (seulement si un filtre est sélectionné)
            if ($status_filter && !empty($status_filter)) {
                $qty = $product['qty'];
                $low_threshold = isset($product['low_stock_threshold']) ? $product['low_stock_threshold'] : 20;
                $critical_threshold = isset($product['critical_stock_threshold']) ? $product['critical_stock_threshold'] : 5;

                if ($status_filter == 'good') {
                    // Good stock: quantité > low_stock_threshold
                    if ($qty <= $low_threshold || $qty == 0) {
                        $include = false;
                    }
                }

                if ($status_filter == 'low') {
                    // Low stock: entre critical et low threshold
                    if ($qty == 0 || $qty > $low_threshold || $qty <= $critical_threshold) {
                        $include = false;
                    }
                }

                if ($status_filter == 'critical') {
                    // Critical stock: quantité <= critical_threshold mais > 0
                    if ($qty == 0 || $qty > $critical_threshold) {
                        $include = false;
                    }
                }

                if ($status_filter == 'out_of_stock') {
                    // Out of stock: quantité = 0
                    if ($qty != 0) {
                        $include = false;
                    }
                }
            }

            // ✅ SI LE PRODUIT PASSE TOUS LES FILTRES
            if ($include) {
                // Add status labels based on thresholds
                $low_threshold = isset($product['low_stock_threshold']) ? $product['low_stock_threshold'] : 20;
                $critical_threshold = isset($product['critical_stock_threshold']) ? $product['critical_stock_threshold'] : 5;

                if ($product['qty'] == 0) {
                    $product['stock_status_label'] = 'Out of Stock';
                    $product['stock_status_class'] = 'danger';
                } else if ($product['qty'] <= $critical_threshold) {
                    $product['stock_status_label'] = 'Critical';
                    $product['stock_status_class'] = 'danger';
                } else if ($product['qty'] <= $low_threshold) {
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
        if (!isset($this->permission['createStock'])) {
            redirect('dashboard', 'refresh');
        }

        $response = array();

        $this->form_validation->set_rules('stock_name', 'Stock name', 'trim|required');
        $this->form_validation->set_rules('active', 'Active', 'trim|required');

        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'name' => $this->input->post('stock_name'),
                'description' => $this->input->post('description'),
                'active' => $this->input->post('active'),
            );

            $create = $this->model_stock->create($data);
            if ($create) {
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
        if ($id) {
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

            if (isset($this->permission['updateStock'])) {
                $buttons .= '<button type="button" class="btn btn-default" onclick="editFunc(' . $value['id'] . ')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button>';
            }

            if (isset($this->permission['deleteStock'])) {
                $buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc(' . $value['id'] . ')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
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
        if (!isset($this->permission['updateStock'])) {
            redirect('dashboard', 'refresh');
        }

        $response = array();

        if ($id) {
            $this->form_validation->set_rules('edit_stock_name', 'Stock name', 'trim|required');
            $this->form_validation->set_rules('edit_active', 'Active', 'trim|required');

            $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('edit_stock_name'),
                    'description' => $this->input->post('edit_description'),
                    'active' => $this->input->post('edit_active'),
                );

                $update = $this->model_stock->update($data, $id);
                if ($update == true) {
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
        if (!isset($this->permission['deleteStock'])) {
            redirect('dashboard', 'refresh');
        }

        $stock_id = $this->input->post('stock_id');

        $response = array();
        if ($stock_id) {
            $delete = $this->model_stock->remove($stock_id);
            if ($delete == true) {
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
        if (!isset($this->permission['viewStock'])) {
            redirect('dashboard', 'refresh');
        }

        if (!$stock_id) {
            redirect('stock', 'refresh');
        }

        $this->data['stock_data'] = $this->model_stock->getStockWithCategories($stock_id);
        $this->data['categories'] = $this->model_category->getCategoriesByStock($stock_id);
        $this->data['statistics'] = $this->model_stock->getStockStatistics($stock_id);

        // Get products for this stock
        $this->data['products'] = $this->model_products->getProductsByStock($stock_id);

        $this->render_template('stock/details', $this->data);
    }

    /**
     * ✅ NOUVELLE FONCTION: Get full product details (AJAX)
     */
    public function getProductDetails()
    {
        $product_id = $this->input->post('product_id');

        if (!$product_id) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Product ID is required'
            ));
            return;
        }

        // Récupérer toutes les données du produit
        $product = $this->model_products->getProductData($product_id);

        if (!$product) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Product not found'
            ));
            return;
        }

        // Générer le HTML des détails
        $html = $this->generateProductDetailsHTML($product);

        echo json_encode(array(
            'success' => true,
            'html' => $html
        ));
    }

    /**
     * ✅ NOUVELLE FONCTION: Generate HTML for product details
     */
    private function generateProductDetailsHTML($product)
    {
        $html = '<div class="product-details-container">';

        // ========== IMAGE & BASIC INFO ==========
        $html .= '<div class="row">';

        // LEFT: Image
        $html .= '<div class="col-md-4 text-center">';
        $html .= '<img src="' . base_url($product['image']) . '" class="img-responsive img-thumbnail" style="max-height: 300px;">';
        $html .= '</div>';

        // RIGHT: Basic Info
        $html .= '<div class="col-md-8">';
        $html .= '<h3 style="margin-top: 0;">' . $product['name'] . '</h3>';
        $html .= '<p class="text-muted"><strong>SKU:</strong> ' . $product['sku'] . '</p>';

        // Status Badge
        if ($product['status'] == 'available') {
            $html .= '<span class="label label-success" style="font-size: 14px;"><i class="fa fa-check-circle"></i> Available</span>';
        } elseif ($product['status'] == 'low_stock') {
            $html .= '<span class="label label-warning" style="font-size: 14px;"><i class="fa fa-exclamation-triangle"></i> Low Stock</span>';
        } else {
            $html .= '<span class="label label-danger" style="font-size: 14px;"><i class="fa fa-times-circle"></i> Not Available</span>';
        }

        $html .= '<hr>';

        // Description
        if (!empty($product['description'])) {
            $html .= '<p><strong><i class="fa fa-file-text"></i> Description:</strong></p>';
            $html .= '<p class="text-muted">' . $product['description'] . '</p>';
        }

        $html .= '</div>';
        $html .= '</div>'; // End row

        $html .= '<hr>';

        // ========== PRICING INFO ==========
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><i class="fa fa-money"></i> Pricing Information</h4>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row">';

        // Cost Price
        $html .= '<div class="col-md-3 col-sm-6">';
        $html .= '<div class="info-box bg-gray">';
        $html .= '<span class="info-box-icon"><i class="fa fa-dollar"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Cost Price</span>';
        $html .= '<span class="info-box-number">' . number_format($product['price_default'], 2) . ' DZD</span>';
        $html .= '</div></div></div>';

        // Super Wholesale
        $html .= '<div class="col-md-3 col-sm-6">';
        $html .= '<div class="info-box bg-aqua">';
        $html .= '<span class="info-box-icon"><i class="fa fa-truck"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Super Gros</span>';
        $html .= '<span class="info-box-number">' . number_format($product['price_super_wholesale'], 2) . ' DZD</span>';
        $html .= '</div></div></div>';

        // Wholesale
        $html .= '<div class="col-md-3 col-sm-6">';
        $html .= '<div class="info-box bg-green">';
        $html .= '<span class="info-box-icon"><i class="fa fa-building"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Gros</span>';
        $html .= '<span class="info-box-number">' . number_format($product['price_wholesale'], 2) . ' DZD</span>';
        $html .= '</div></div></div>';

        // Retail
        $html .= '<div class="col-md-3 col-sm-6">';
        $html .= '<div class="info-box bg-yellow">';
        $html .= '<span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Détail</span>';
        $html .= '<span class="info-box-number">' . number_format($product['price_retail'], 2) . ' DZD</span>';
        $html .= '</div></div></div>';

        $html .= '</div>'; // End pricing row

        $html .= '<hr>';

        // ========== STOCK INFO ==========
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><i class="fa fa-cubes"></i> Stock Information</h4>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<table class="table table-bordered">';
        $html .= '<tr><th style="width: 50%;">Stock Location</th><td>' . $product['stock_name'] . '</td></tr>';
        $html .= '<tr><th>Current Quantity</th><td><strong style="font-size: 18px; color: #00a65a;">' . $product['qty'] . '</strong></td></tr>';
        $html .= '<tr><th>Low Stock Threshold</th><td>' . $product['low_stock_threshold'] . '</td></tr>';
        $html .= '<tr><th>Critical Threshold</th><td>' . $product['critical_stock_threshold'] . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<table class="table table-bordered">';
        $html .= '<tr><th style="width: 50%;">Availability</th><td>';
        if ($product['availability'] == 1) {
            $html .= '<span class="label label-success">Yes</span>';
        } else {
            $html .= '<span class="label label-danger">No</span>';
        }
        $html .= '</td></tr>';

        // Categories
        if (!empty($product['category_names'])) {
            $html .= '<tr><th>Categories</th><td>' . $product['category_names'] . '</td></tr>';
        }

        // Brand
        if (!empty($product['brand_name'])) {
            $html .= '<tr><th>Brand</th><td>' . $product['brand_name'] . '</td></tr>';
        }

        // Dates
        if (!empty($product['created_at'])) {
            $html .= '<tr><th>Created</th><td>' . date('d/m/Y H:i', strtotime($product['created_at'])) . '</td></tr>';
        }

        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';

        // ========== SUPPLIERS (if any) ==========
        if (!empty($product['suppliers']) && count($product['suppliers']) > 0) {
            $html .= '<hr>';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<h4><i class="fa fa-users"></i> Suppliers</h4>';
            $html .= '<ul>';
            foreach ($product['suppliers'] as $supplier) {
                $html .= '<li>' . $supplier['name'] . ' - ' . $supplier['phone'] . '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>'; // End container

        return $html;
    }
}
