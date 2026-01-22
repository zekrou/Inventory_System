<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
 * @property Model_preorders $model_preorders
 * @property Model_users $model_users
 */
class Preorders extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();

        $this->not_logged_in();

        $this->load->model('model_preorders');

        // ✅ MULTI-TENANT: Charger tenant_db
        $tenant_db = $this->load_tenant_db();
        if ($tenant_db) {
            $this->model_preorders->setTenantDb($tenant_db);
        }

        // ✅ CORRECTION: Nom de permission cohérent (minuscules)
        if (!in_array('viewPreorders', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
    }

    /**
     * List all pre-orders
     */
    public function index()
    {
        $this->data['page_title'] = 'Commandes Mobile';
        
        // Get filter status
        $status_filter = $this->input->get('status');
        
        // Get statistics
        $this->data['stats'] = $this->model_preorders->getStatistics();
        
        // Get all pre-orders
        $this->data['preorders'] = $this->model_preorders->getPreOrders($status_filter);
        $this->data['status_filter'] = $status_filter;
        
        // ✅ IMPORTANT: Passer aussi les permissions à la vue
        $this->data['user_permission'] = $this->permission;
        
        $this->render_template('preorders/index', $this->data);
    }

    /**
     * View pre-order details
     */
    public function view($id = null)
    {
        if (!$id) {
            redirect('preorders', 'refresh');
        }

        $preorder = $this->model_preorders->getPreOrderById($id);
        
        if (!$preorder) {
            $this->session->set_flashdata('error', 'Commande introuvable');
            redirect('preorders', 'refresh');
        }

        $this->data['page_title'] = 'Commande - ' . $preorder['order_number'];
        $this->data['preorder'] = $preorder;
        $this->data['items'] = $this->model_preorders->getPreOrderItems($id);
        
        // Load user info
        $this->load->model('model_users');
        if (!empty($preorder['user_id'])) {
            $this->data['created_by'] = $this->model_users->getUserData($preorder['user_id']);
        }
        
        // ✅ IMPORTANT: Passer aussi les permissions à la vue
        $this->data['user_permission'] = $this->permission;
        
        $this->render_template('preorders/view', $this->data);
    }

    /**
     * Update status (AJAX)
     */
    public function update_status($id = null)
    {
        header('Content-Type: application/json');

        if (!in_array('updatePreorders', $this->permission)) {
            echo json_encode(['success' => false, 'message' => 'Permission refusée']);
            return;
        }

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $status = $this->input->post('status');
        
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'])) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            return;
        }

        if ($this->model_preorders->updateStatus($id, $status)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Statut mis à jour avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Échec de la mise à jour'
            ]);
        }
    }

    /**
     * Delete pre-order
     */
    public function delete($id = null)
    {
        if (!in_array('deletePreorders', $this->permission)) {
            $this->session->set_flashdata('error', 'Permission refusée');
            redirect('preorders', 'refresh');
        }

        if (!$id) {
            $this->session->set_flashdata('error', 'ID invalide');
            redirect('preorders', 'refresh');
        }

        if ($this->model_preorders->deletePreOrder($id)) {
            $this->session->set_flashdata('success', 'Commande supprimée avec succès');
        } else {
            $this->session->set_flashdata('error', 'Échec de la suppression');
        }

        redirect('preorders', 'refresh');
    }
}