<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
    private $tenant_db;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_products');
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
    public function login()
    {
        header('Content-Type: application/json');

        // 📥 Lire username (ou email) + password
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (!$username || !$password) {
            echo json_encode(['success' => false, 'message' => 'Username and password required']);
            return;
        }

        // 1️⃣ Vérifier l'utilisateur dans stock_master
        $user = $this->db
            ->group_start()
            ->where('username', $username)
            ->or_where('email', $username)
            ->group_end()
            ->get('users')->row_array();

        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            return;
        }

        // 2️⃣ Récupérer le tenant associé
        $userTenant = $this->db
            ->where('user_id', $user['id'])
            ->get('user_tenant')->row_array();

        if (!$userTenant) {
            echo json_encode(['success' => false, 'message' => 'User not linked to any tenant']);
            return;
        }

        $tenant = $this->db
            ->where('id', $userTenant['tenant_id'])
            ->get('tenants')->row_array();

        if (!$tenant) {
            echo json_encode(['success' => false, 'message' => 'Tenant not found']);
            return;
        }

        // 3️⃣ Connecter à la base du tenant
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

        // 4️⃣ Générer token
        $token = bin2hex(random_bytes(32));

        echo json_encode([
            'success' => true,
            'token' => $token,
            'tenant_id' => (int)$userTenant['tenant_id'],
            'user' => [
                'id' => (int)$user['id'],
                'email' => $user['email'],
                'username' => $user['username'] ?? '',
                'firstname' => $user['firstname'] ?? '',
                'lastname' => $user['lastname'] ?? '',
            ]
        ]);
    }


    // ==================== PRODUCTS ====================
    public function products()
    {
        try {
            // Vérifier que tenant_db existe
            if (!isset($this->tenant_db) || !$this->tenant_db) {
                echo json_encode(['success' => false, 'message' => 'Tenant DB not loaded. Login first.']);
                return;
            }

            $products = $this->tenant_db
                ->order_by('id', 'DESC')
                ->get('products')->result_array();

            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products),
                'debug' => 'DB: ' . $this->tenant_db->database
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

    // ==================== PRE-ORDERS ====================
    public function create_preorder()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?: $_POST;

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

            $this->tenant_db->insert('pre_orders', $pre_order_data);
            $pre_order_id = $this->tenant_db->insert_id();

            foreach ($data['items'] as $item) {
                $item_data = [
                    'pre_order_id' => $pre_order_id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => floatval($item['price']) * intval($item['qty'])
                ];
                $this->tenant_db->insert('pre_order_items', $item_data);
            }

            $this->tenant_db->trans_complete();

            echo json_encode([
                'success' => true,
                'order_number' => $order_number,
                'pre_order_id' => $pre_order_id,
                'total_amount' => $total
            ]);
        } catch (Exception $e) {
            $this->tenant_db->trans_rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function preorders()
    {
        $user_id = $this->input->get('user_id');
        $status = $this->input->get('status');

        $this->tenant_db->select('*')->from('pre_orders');
        if ($user_id) $this->tenant_db->where('user_id', $user_id);
        if ($status) $this->tenant_db->where('status', $status);
        $this->tenant_db->order_by('created_at', 'DESC');
        $preorders = $this->tenant_db->get()->result_array();

        foreach ($preorders as &$order) {
            $order['items'] = $this->tenant_db->get_where('pre_order_items', ['pre_order_id' => $order['id']])->result_array();
        }

        echo json_encode(['success' => true, 'preorders' => $preorders]);
    }

    public function preorder($id = null)
    {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Pre-order ID required']);
            return;
        }
        $preorder = $this->tenant_db->get_where('pre_orders', ['id' => $id])->row_array();
        if (!$preorder) {
            echo json_encode(['success' => false, 'message' => 'Pre-order not found']);
            return;
        }
        $preorder['items'] = $this->tenant_db->get_where('pre_order_items', ['pre_order_id' => $id])->result_array();
        echo json_encode(['success' => true, 'preorder' => $preorder]);
    }
}
