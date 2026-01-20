<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Preorders extends Admin_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('model_preorders');
        
        // Check permissions
        if(!in_array('viewPreOrder', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
    }
    
    /**
     * List all pre-orders
     */
    public function index() {
        $this->data['page_title'] = 'Pre-Orders Mobile';
        
        // Get statistics
        $this->data['stats'] = $this->model_preorders->getStatistics();
        
        // Get all pre-orders
        $this->data['preorders'] = $this->model_preorders->getPreOrders();
        
        $this->render_template('preorders/index', $this->data);
    }
    
    /**
     * View pre-order details
     */
    public function view($id = null) {
        if(!$id) {
            redirect('preorders', 'refresh');
        }
        
        $preorder = $this->model_preorders->getPreOrderById($id);
        
        if(!$preorder) {
            $this->session->set_flashdata('error', 'Pre-order not found');
            redirect('preorders', 'refresh');
        }
        
        $this->data['page_title'] = 'Pre-Order Details - ' . $preorder['order_number'];
        $this->data['preorder'] = $preorder;
        $this->data['items'] = $this->model_preorders->getPreOrderItems($id);
        
        $this->render_template('preorders/view', $this->data);
    }
    
    /**
     * Update status (AJAX)
     */
    public function update_status($id = null) {
        if(!in_array('updatePreOrder', $this->permission)) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }
        
        if(!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            return;
        }
        
        $status = $this->input->post('status');
        
        if(!in_array($status, ['pending', 'approved', 'rejected', 'completed'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        
        if($this->model_preorders->updateStatus($id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }
    
    /**
     * Delete pre-order
     */
    public function delete($id = null) {
        if(!in_array('deletePreOrder', $this->permission)) {
            $this->session->set_flashdata('error', 'Permission denied');
            redirect('preorders', 'refresh');
        }
        
        if(!$id) {
            $this->session->set_flashdata('error', 'Invalid ID');
            redirect('preorders', 'refresh');
        }
        
        if($this->model_preorders->deletePreOrder($id)) {
            $this->session->set_flashdata('success', 'Pre-order deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete pre-order');
        }
        
        redirect('preorders', 'refresh');
    }
}
