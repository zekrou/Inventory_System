<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Preorders extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_preorders');
    }

    public function index()
    {
        if (!isset($this->permission['viewPreorders'])) {
            $this->session->set_flashdata('error', 'Permission refusée');
            redirect('dashboard');
        }

        $preorders = $this->Model_preorders->get_all_preorders();
        $this->data['preorders'] = $preorders;
        $this->render_template('preorders/index', $this->data);
    }

    public function view($id)
    {
        if (!isset($this->permission['viewPreorders'])) {
            $this->session->set_flashdata('error', 'Permission refusée');
            redirect('preorders');
        }

        $preorder = $this->Model_preorders->get_preorder($id);
        $items = $this->Model_preorders->get_preorder_items($id);
        $this->data['preorder'] = $preorder;
        $this->data['items'] = $items;
        $this->render_template('preorders/view', $this->data);
    }

    public function update($id)
    {
        if (!isset($this->permission['updatePreorders'])) {
            $this->session->set_flashdata('error', 'Permission refusée');
            redirect('preorders');
        }

        if ($this->input->post()) {
            $status = $this->input->post('status');
            $this->Model_preorders->update_status($id, $status);
            $this->session->set_flashdata('success', 'Pré-commande mise à jour');
            redirect('preorders');
        }

        $preorder = $this->Model_preorders->get_preorder($id);
        $this->data['preorder'] = $preorder;
        $this->render_template('preorders/update', $this->data);
    }

    public function delete($id)
    {
        if (!isset($this->permission['deletePreorders'])) {
            $this->session->set_flashdata('error', 'Permission refusée');
            redirect('preorders');
        }

        $this->Model_preorders->delete_preorder($id);
        $this->session->set_flashdata('success', 'Pré-commande supprimée');
        redirect('preorders');
    }
}
