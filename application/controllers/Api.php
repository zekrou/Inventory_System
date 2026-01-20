<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('model_products');
        $this->load->model('model_users');
        $this->load->database();
        
        // Enable CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    // ==================== AUTHENTICATION ====================
    
    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        if(empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password required']);
            return;
        }
        
        $user = $this->model_users->getUserDataByUsername($username);
        
        if($user && password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(32));
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'email' => $user['email']
                ],
                'token' => $token
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }
    
    // ==================== PRODUCTS ====================
    
    public function products() {
        $products = $this->model_products->getActiveProductData();
        
        $formatted = array_map(function($p) {
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'sku' => $p['sku'],
                'price' => floatval($p['price']),
                'qty' => intval($p['qty']),
                'image' => $p['image'] ? base_url('uploads/products/' . $p['image']) : null,
                'description' => $p['description'] ?? '',
                'category_id' => $p['category_id'] ?? null,
                'brand_id' => $p['brand_id'] ?? null
            ];
        }, $products);
        
        echo json_encode(['success' => true, 'products' => $formatted]);
    }
    
    // ==================== PRE-ORDERS ====================
    
    public function create_preorder() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if(!$data || !isset($data['customer_name']) || !isset($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }
        
        $this->db->trans_start();
        
        // Generate order number
        $order_number = 'PRE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        // Calculate total
        $total = 0;
        foreach($data['items'] as $item) {
            $total += floatval($item['price']) * intval($item['qty']);
        }
        
        // Insert pre_order
        $pre_order_data = [
            'order_number' => $order_number,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? '',
            'customer_address' => $data['customer_address'] ?? '',
            'total_amount' => $total,
            'status' => 'pending',
            'user_id' => $data['user_id'] ?? null,
            'notes' => $data['notes'] ?? ''
        ];
        
        $this->db->insert('pre_orders', $pre_order_data);
        $pre_order_id = $this->db->insert_id();
        
        // Insert items
        foreach($data['items'] as $item) {
            $item_data = [
                'pre_order_id' => $pre_order_id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => floatval($item['price']) * intval($item['qty'])
            ];
            $this->db->insert('pre_order_items', $item_data);
        }
        
        $this->db->trans_complete();
        
        if($this->db->trans_status()) {
            echo json_encode([
                'success' => true,
                'message' => 'Pre-order created successfully',
                'order_number' => $order_number,
                'pre_order_id' => $pre_order_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }
    
    public function preorders() {
        $user_id = $this->input->get('user_id');
        
        $this->db->select('*');
        $this->db->from('pre_orders');
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->order_by('created_at', 'DESC');
        
        $preorders = $this->db->get()->result_array();
        
        // Get items for each order
        foreach($preorders as &$order) {
            $this->db->select('*');
            $this->db->from('pre_order_items');
            $this->db->where('pre_order_id', $order['id']);
            $order['items'] = $this->db->get()->result_array();
        }
        
        echo json_encode(['success' => true, 'preorders' => $preorders]);
    }
}
