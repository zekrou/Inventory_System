<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Form_validation $form_validation
 * @property Model_orders $model_orders
 * @property Model_products $model_products
 * @property Model_customers $model_customers
 * @property Model_orders $model_orders
 * @property Model_company $model_company
 * @property CI_Output $output
 * 
 * Custom Models
 * @property Model_purchases $model_purchases
 * @property Model_suppliers $model_suppliers
 * @property Model_tenant_auth $model_tenant_auth
 */
class Api extends CI_Controller
{

    public function __construct()
    {
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
    /**
     * Retourne la connexion DB du bon tenant
     * @param int $tenant_id - ID du tenant (donomagic = 1, merchant2 = 2, etc.)
     * @return CI_DB_mysql_driver
     */
    private function get_tenant_db($tenant_id)
    {
        // 1. Vérifier que le tenant existe et est actif
        $this->load->model('model_tenant_auth');
        $tenant = $this->model_tenant_auth->get_tenant($tenant_id);

        if (!$tenant) {
            throw new Exception('Tenant not found or inactive');
        }

        // 2. Construire les credentials de connexion
        $db_config = [
            'hostname' => $tenant['db_hostname'] ?? 'localhost',
            'username' => $tenant['db_username'],
            'password' => $tenant['db_password'],
            'database' => $tenant['db_name'], // stock_donomagic, stock_merchant2, etc.
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => FALSE,
            'cache_on' => FALSE,
            'cachedir' => '',
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci',
            'swap_pre' => '',
            'encrypt' => FALSE,
            'compress' => FALSE,
            'stricton' => FALSE,
            'failover' => array(),
            'save_queries' => TRUE
        ];

        // 3. Créer une connexion temporaire
        $this->tenant_db = $this->load->database($db_config, TRUE, 'tenant_db');

        return $this->tenant_db;
    }
    // ==================== AUTHENTICATION ====================

    /**
     * Login employé avec switch automatique tenant
     */
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username/email et password requis']);
            return;
        }

        try {
            // ✅ DB donomagic
            $this->load->database('stock_donomagic_1768902664', TRUE);
            $this->load->model('model_users');

            // ✅ Ta fonction cherche déjà username OU email
            $user = $this->model_users->getUserDataByUsername($username);

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
                return;
            }

            if (!password_verify($password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
                return;
            }

            $token = bin2hex(random_bytes(32));

            echo json_encode([
                'success' => true,
                'message' => 'Login réussi',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'firstname' => $user['firstname'] ?? '',
                    'lastname' => $user['lastname'] ?? '',
                    'phone' => $user['phone'] ?? '',
                    'email' => $user['email'] ?? '',
                    'tenant_id' => 1
                ],
                'token' => $token
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }




    // ==================== PRODUCTS ====================

    /**
     * Liste produits du tenant
     */
    public function products()
    {
        // 1) Charger tous les produits disponibles
        $this->db->select('id, name, sku, description, qty, price_retail, price_wholesale, price_super_wholesale, image, availability');
        $this->db->from('products');
        $this->db->where('availability', 1); // produits actifs uniquement
        // Si tu veux vraiment filtrer stock > 0, garde cette ligne, sinon commente là
        // $this->db->where('qty >', 0);

        $products = $this->db->get()->result_array();

        // 2) Formatage pour l’app mobile
        $formatted = array_map(function ($p) {
            return [
                'id' => (int)$p['id'],
                'name' => $p['name'],
                'sku' => $p['sku'] ?? '',
                'description' => $p['description'] ?? '',
                'qty' => isset($p['qty']) ? (int)$p['qty'] : 0,
                'price_retail' => isset($p['price_retail']) ? (float)$p['price_retail'] : 0,
                'price_wholesale' => isset($p['price_wholesale']) ? (float)$p['price_wholesale'] : 0,
                'price_super_wholesale' => isset($p['price_super_wholesale']) ? (float)$p['price_super_wholesale'] : 0,
                'image' => !empty($p['image']) ? base_url('uploads/products/' . $p['image']) : null,
                'availability' => isset($p['availability']) ? (int)$p['availability'] : 0,
            ];
        }, $products);

        // 3) Réponse JSON
        echo json_encode([
            'success' => true,
            'products' => $formatted,
            'count' => count($formatted),
        ]);
    }




    public function product($id = null)
    {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            return;
        }

        $product = $this->model_products->getProductData($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }

        $formatted = [
            'id' => $product['id'],
            'name' => $product['name'],
            'sku' => $product['sku'] ?? '',
            'price' => floatval($product['price']),
            'qty' => intval($product['qty']),
            'image' => !empty($product['image']) ? base_url('uploads/products/' . $product['image']) : null,
            'description' => $product['description'] ?? '',
            'category_id' => $product['category_id'] ?? null,
            'brand_id' => $product['brand_id'] ?? null
        ];

        echo json_encode(['success' => true, 'product' => $formatted]);
    }

    // ==================== PRE-ORDERS ====================

    public function create_preorder()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            $data = $_POST;
        }

        // Validation
        if (!isset($data['customer_name']) || empty($data['customer_name'])) {
            echo json_encode(['success' => false, 'message' => 'Customer name is required']);
            return;
        }

        if (!isset($data['items']) || empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Order items are required']);
            return;
        }

        $this->db->trans_start();

        try {
            // Generate order number
            $order_number = 'PRE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Calculate total
            $total = 0;
            foreach ($data['items'] as $item) {
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
            foreach ($data['items'] as $item) {
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

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Pre-order created successfully',
                'order_number' => $order_number,
                'pre_order_id' => $pre_order_id,
                'total_amount' => $total
            ]);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Pre-order creation failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to create pre-order: ' . $e->getMessage()]);
        }
    }

    public function preorders()
    {
        $user_id = $this->input->get('user_id');
        $status = $this->input->get('status');

        $this->db->select('*');
        $this->db->from('pre_orders');

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }

        if ($status) {
            $this->db->where('status', $status);
        }

        $this->db->order_by('created_at', 'DESC');
        $preorders = $this->db->get()->result_array();

        // Get items for each order
        foreach ($preorders as &$order) {
            $this->db->select('*');
            $this->db->from('pre_order_items');
            $this->db->where('pre_order_id', $order['id']);
            $order['items'] = $this->db->get()->result_array();
        }

        echo json_encode(['success' => true, 'preorders' => $preorders]);
    }

    public function preorder($id = null)
    {
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Pre-order ID required']);
            return;
        }

        $preorder = $this->db->get_where('pre_orders', ['id' => $id])->row_array();

        if (!$preorder) {
            echo json_encode(['success' => false, 'message' => 'Pre-order not found']);
            return;
        }

        // Get items
        $items = $this->db->get_where('pre_order_items', ['pre_order_id' => $id])->result_array();
        $preorder['items'] = $items;

        echo json_encode(['success' => true, 'preorder' => $preorder]);
    }
}
