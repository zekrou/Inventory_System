<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    private $tenant_db;
    private $user_id;
    private $tenant_id;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // Enable CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $this->_authenticate();
    }

    private function _authenticate()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        if (strpos($request_uri, '/login') !== false) {
            return;
        }

        $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Token required']);
            exit;
        }

        $parts = explode('|', $token);
        if (count($parts) !== 3) {
            echo json_encode(['success' => false, 'message' => 'Invalid token format']);
            exit;
        }

        $this->tenant_id = (int)$parts[0];
        $this->user_id = (int)$parts[1];

        $tenant = $this->db->where('id', $this->tenant_id)->get('tenants')->row_array();

        if (!$tenant) {
            echo json_encode(['success' => false, 'message' => 'Tenant not found']);
            exit;
        }

        $dbConfig = [
            'hostname' => 'inventorysystem-mysqlinventory-ydsxph',
            'username' => 'mysql',
            'password' => 'Zakaria1304@',
            'database' => $tenant['database_name'],
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => FALSE,
            'cache_on' => FALSE,
            'charset' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
        ];

        $this->tenant_db = $this->load->database($dbConfig, TRUE);
    }

    public function login()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? null;
        $password = $input['password'] ?? null;

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Username and password required']);
            return;
        }

        $result = $this->db
            ->select('u.*, ut.tenant_id, ut.role, t.tenant_name, t.database_name, t.status')
            ->from('users u')
            ->join('user_tenant ut', 'u.id = ut.user_id', 'inner')
            ->join('tenants t', 'ut.tenant_id = t.id', 'inner')
            ->group_start()
            ->where('u.username', $username)
            ->or_where('u.email', $username)
            ->group_end()
            ->where('t.status', 'active')
            ->get()
            ->row_array();

        if (!$result || !password_verify($password, $result['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            return;
        }

        $token = $result['tenant_id'] . '|' . $result['id'] . '|' . bin2hex(random_bytes(16));

        echo json_encode([
            'success' => true,
            'token' => $token,
            'tenant_id' => (int)$result['tenant_id'],
            'tenant_name' => $result['tenant_name'],
            'database_name' => $result['database_name'],
            'user' => [
                'id' => (int)$result['id'],
                'email' => $result['email'],
                'username' => $result['username'],
                'firstname' => $result['firstname'],
                'lastname' => $result['lastname'],
                'phone' => $result['phone'],
                'role' => $result['role']
            ]
        ]);
    }

    // ================================
    // 📦 PRODUCTS
    // ================================

    public function products()
    {
        try {
            $products = $this->tenant_db
                ->order_by('id', 'DESC')
                ->get('products')->result_array();

            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function product($id = null)
    {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            return;
        }

        $product = $this->tenant_db->get_where('products', ['id' => $id])->row_array();

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        echo json_encode(['success' => true, 'product' => $product]);
    }

    // ================================
    // 🛒 PRE-ORDERS - CREATE
    // ================================
    
    // ✅ MÉTHODE MANQUANTE - AJOUTE ICI
    public function create_pre_order()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?: $_POST;

        error_log("create_pre_order called");
        error_log("Data received: " . json_encode($data));

        if (empty($data['customer_name']) || empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Customer name and items required']);
            return;
        }

        $this->tenant_db->trans_start();

        try {
            $order_number = 'PRE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $total = 0;

            foreach ($data['items'] as $item) {
                $total += floatval($item['price']) * intval($item['qty']);
            }

            $preorder_data = [
                'order_number' => $order_number,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? '',
                'customer_address' => $data['customer_address'] ?? '',
                'total_amount' => $total,
                'status' => 'pending',
                'user_id' => $this->user_id,
                'notes' => $data['notes'] ?? ''
            ];

            error_log("Inserting into tenant DB: " . $this->tenant_db->database);
            
            $this->tenant_db->insert('pre_orders', $preorder_data);
            $preorder_id = $this->tenant_db->insert_id();

            error_log("Created preorder ID: $preorder_id");

            foreach ($data['items'] as $item) {
                $item_data = [
                    'pre_order_id' => $preorder_id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => floatval($item['price']) * intval($item['qty'])
                ];
                
                $this->tenant_db->insert('pre_order_items', $item_data);
            }

            $this->tenant_db->trans_complete();

            if ($this->tenant_db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            echo json_encode([
                'success' => true,
                'order_number' => $order_number,
                'pre_order_id' => $preorder_id,
                'total_amount' => $total,
                'debug' => [
                    'database' => $this->tenant_db->database,
                    'tenant_id' => $this->tenant_id,
                    'user_id' => $this->user_id
                ]
            ]);
        } catch (Exception $e) {
            $this->tenant_db->trans_rollback();
            error_log("create_pre_order error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ✅ ALIAS sans underscore
    public function create_preorder()
    {
        return $this->create_pre_order();
    }

    // ================================
    // 🛒 PRE-ORDERS - READ
    // ================================

    public function preorders()
    {
        $user_id = $this->input->get('user_id');
        $status = $this->input->get('status');

        $this->tenant_db->select('*')->from('pre_orders');

        if ($user_id) {
            $this->tenant_db->where('user_id', $user_id);
        }

        if ($status) {
            $this->tenant_db->where('status', $status);
        }

        $this->tenant_db->order_by('created_at', 'DESC');
        $preorders = $this->tenant_db->get()->result_array();

        foreach ($preorders as &$order) {
            $order['id'] = (int)$order['id'];
            $order['total_amount'] = (float)$order['total_amount'];
            $order['user_id'] = (int)$order['user_id'];

            $items = $this->tenant_db
                ->get_where('pre_order_items', ['pre_order_id' => $order['id']])
                ->result_array();

            foreach ($items as &$item) {
                $item['id'] = (int)$item['id'];
                $item['pre_order_id'] = (int)$item['pre_order_id'];
                $item['product_id'] = (int)$item['product_id'];
                $item['qty'] = (int)$item['qty'];
                $item['price'] = (float)$item['price'];
                $item['subtotal'] = (float)$item['subtotal'];
            }

            $order['items'] = $items;
        }

        echo json_encode(['success' => true, 'preorders' => $preorders]);
    }

    public function preorder($id = null)
    {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Pre-order ID required']);
            return;
        }

        error_log("Getting preorder ID: $id");

        $preorder = $this->tenant_db->get_where('pre_orders', ['id' => $id])->row_array();

        if (!$preorder) {
            error_log("Preorder not found: $id");
            echo json_encode(['success' => false, 'message' => 'Pre-order not found']);
            return;
        }

        error_log("Preorder found: " . json_encode($preorder));

        $preorder['id'] = (int)$preorder['id'];
        $preorder['total_amount'] = (float)$preorder['total_amount'];
        $preorder['user_id'] = isset($preorder['user_id']) ? (int)$preorder['user_id'] : null;

        $items = $this->tenant_db
            ->get_where('pre_order_items', ['pre_order_id' => $id])
            ->result_array();

        error_log("Items count: " . count($items));
        error_log("Items: " . json_encode($items));

        foreach ($items as &$item) {
            $item['id'] = (int)$item['id'];
            $item['pre_order_id'] = (int)$item['pre_order_id'];
            $item['product_id'] = (int)$item['product_id'];
            $item['qty'] = (int)$item['qty'];
            $item['price'] = (float)$item['price'];
            $item['subtotal'] = (float)$item['subtotal'];
        }

        $preorder['items'] = $items;

        error_log("Final preorder: " . json_encode($preorder));

        echo json_encode([
            'success' => true,
            'preorder' => $preorder,
            'debug' => [
                'items_count' => count($items),
                'preorder_id' => $id
            ]
        ]);
    }
}
