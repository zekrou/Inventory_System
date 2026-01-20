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
    
    public function index() {
        $this->data['page_title'] = 'Pre-Orders Mobile';
        $preorders = $this->model_preorders->getPreOrders();
        $this->data['preorders'] = $preorders;
        $this->render_template('preorders/index', $this->data);
    }
    
    public function view($id) {
        $preorder = $this->model_preorders->getPreOrderById($id);
        if(!$preorder) {
            show_404();
        }
        
        $this->data['page_title'] = 'Pre-Order Details';
        $this->data['preorder'] = $preorder;
        $this->data['items'] = $this->model_preorders->getPreOrderItems($id);
        $this->render_template('preorders/view', $this->data);
    }
    
    public function update_status($id) {
        if(!in_array('updatePreOrder', $this->permission)) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }
        
        $status = $this->input->post('status');
        if($this->model_preorders->updateStatus($id, $status)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    public function delete($id) {
        if(!in_array('deletePreOrder', $this->permission)) {
            redirect('preorders', 'refresh');
        }
        
        if($this->model_preorders->deletePreOrder($id)) {
            $this->session->set_flashdata('success', 'Pre-order deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Error deleting pre-order');
        }
        redirect('preorders', 'refresh');
    }
}
