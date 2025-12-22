<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchases extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Purchases';
        $this->load->model('model_purchases');
        $this->load->model('model_suppliers');
        $this->load->model('model_products');
    }

    public function index()
    {
        if(!in_array('viewPurchase', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $this->data['purchase_stats'] = $this->model_purchases->getPurchaseStatistics();
        $this->render_template('purchases/index', $this->data);
    }

    public function fetchPurchasesData()
    {
        $result = array('data' => array());
        $data = $this->model_purchases->getPurchaseData();

        foreach ($data as $key => $value) {
            $buttons = '';
            if(in_array('viewPurchase', $this->permission)) {
                $buttons .= '<a href="'.base_url('purchases/view/'.$value['id']).'" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a> ';
            }
            if(in_array('updatePurchase', $this->permission) && $value['status'] == 'pending') {
                $buttons .= '<button type="button" class="btn btn-success btn-sm" onclick="receivePurchase('.$value['id'].')"><i class="fa fa-check"></i> Receive</button> ';
            }

            $status_badges = array(
                'pending' => '<span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>',
                'received' => '<span class="label label-success"><i class="fa fa-check"></i> Received</span>',
                'cancelled' => '<span class="label label-danger"><i class="fa fa-times"></i> Cancelled</span>'
            );
            $status = isset($status_badges[$value['status']]) ? $status_badges[$value['status']] : $value['status'];

            $date = date('d-m-Y', strtotime($value['purchase_date']));

            $result['data'][$key] = array(
                $value['purchase_no'],
                $value['supplier_name'],
                $date,
                number_format($value['total_amount'], 2) . ' DZD',
                $status,
                $buttons
            );
        }

        echo json_encode($result);
    }

    public function create()
    {
        if(!in_array('createPurchase', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $supplier_id = $this->input->post('supplier_id');
            $products = $this->input->post('product');
            $quantities = $this->input->post('qty');
            $prices = $this->input->post('price');

            $items = array();
            $total = 0;
            
            for($i = 0; $i < count($products); $i++) {
                if($products[$i] && $quantities[$i] && $prices[$i]) {
                    $subtotal = $quantities[$i] * $prices[$i];
                    $items[] = array(
                        'product_id' => $products[$i],
                        'quantity' => $quantities[$i],
                        'unit_price' => $prices[$i]
                    );
                    $total += $subtotal;
                }
            }

            $data = array(
                'supplier_id' => $supplier_id,
                'total_amount' => $total,
                'notes' => $this->input->post('notes')
            );

            $purchase_id = $this->model_purchases->create($data, $items);

            if($purchase_id) {
                $this->session->set_flashdata('success', 'Purchase created successfully!');
                redirect('purchases/view/'.$purchase_id, 'refresh');
            } else {
                $this->session->set_flashdata('error', 'Error creating purchase!');
                redirect('purchases/create', 'refresh');
            }
        } else {
            $this->data['suppliers'] = $this->model_suppliers->getActiveSuppliers();
            $this->data['products'] = $this->model_products->getActiveProductData();
            $this->render_template('purchases/create', $this->data);
        }
    }

    public function view($id = null)
    {
        if(!in_array('viewPurchase', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if($id) {
            $purchase = $this->model_purchases->getPurchaseData($id);
            $items = $this->model_purchases->getPurchaseItems($id);

            $this->data['purchase'] = $purchase;
            $this->data['items'] = $items;
            $this->render_template('purchases/view', $this->data);
        } else {
            redirect('purchases', 'refresh');
        }
    }

    public function receive($id)
    {
        if(!in_array('updatePurchase', $this->permission)) {
            echo json_encode(array('success' => false, 'message' => 'Permission denied'));
            return;
        }

        if($id) {
            $receive = $this->model_purchases->receivePurchase($id);
            
            if($receive) {
                echo json_encode(array('success' => true, 'message' => 'Purchase received! Stock updated.'));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Error receiving purchase'));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Invalid purchase ID'));
        }
    }

    public function getProductPrice()
    {
        $product_id = $this->input->post('product_id');
        $supplier_id = $this->input->post('supplier_id');
        
        if($product_id) {
            $product = $this->model_products->getProductData($product_id);
            
            // Try to get supplier-specific price
            if($supplier_id) {
                $sql = "SELECT supplier_price FROM supplier_product 
                        WHERE product_id = ? AND supplier_id = ?";
                $query = $this->db->query($sql, array($product_id, $supplier_id));
                $supplier_price = $query->row_array();
                
                if($supplier_price && $supplier_price['supplier_price'] > 0) {
                    $product['price_default'] = $supplier_price['supplier_price'];
                }
            }
            
            echo json_encode($product);
        }
    }
}