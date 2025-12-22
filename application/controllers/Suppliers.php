<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suppliers extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->data['page_title'] = 'Suppliers';
        $this->load->model('model_suppliers');
    }

    public function index()
    {
        if(!in_array('viewSupplier', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        $this->render_template('suppliers/index', $this->data);
    }

    public function fetchSuppliersData()
    {
        $result = array('data' => array());
        $data = $this->model_suppliers->getSupplierData();

        foreach ($data as $key => $value) {
            $buttons = '';
            if(in_array('updateSupplier', $this->permission)) {
                $buttons .= '<button type="button" class="btn btn-default" onclick="editSupplier('.$value['id'].')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> ';
            }
            if(in_array('viewSupplier', $this->permission)) {
                $buttons .= '<a href="'.base_url('suppliers/view/'.$value['id']).'" class="btn btn-info"><i class="fa fa-eye"></i></a> ';
            }
            if(in_array('deleteSupplier', $this->permission)) {
                $buttons .= '<button type="button" class="btn btn-default" onclick="removeSupplier('.$value['id'].')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
            }

            $status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';

            $result['data'][$key] = array(
                $value['supplier_code'],
                $value['name'],
                $value['contact_person'],
                $value['phone'],
                $value['email'],
                $status,
                $buttons
            );
        }

        echo json_encode($result);
    }

    public function fetchSupplierDataById($id)
    {
        if($id) {
            $data = $this->model_suppliers->getSupplierData($id);
            echo json_encode($data);
        }
    }

    public function create()
    {
        if(!in_array('createSupplier', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $response = array();
        $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');
        $this->form_validation->set_rules('active', 'Active', 'trim|required');
        $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
            // Generate supplier code
            $last = $this->db->order_by('id', 'DESC')->limit(1)->get('suppliers')->row();
            $number = $last ? ((int)filter_var($last->supplier_code, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
            $supplier_code = 'SUP-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            $data = array(
                'supplier_code' => $supplier_code,
                'name' => $this->input->post('supplier_name'),
                'contact_person' => $this->input->post('contact_person'),
                'phone' => $this->input->post('phone'),
                'email' => $this->input->post('email'),
                'address' => $this->input->post('address'),
                'country' => $this->input->post('country'),
                'tax_number' => $this->input->post('tax_number'),
                'payment_terms' => $this->input->post('payment_terms'),
                'notes' => $this->input->post('notes'),
                'active' => $this->input->post('active'),
            );

            $create = $this->model_suppliers->create($data);
            if($create) {
                $response['success'] = true;
                $response['messages'] = 'Fournisseur créé avec succès - Code: ' . $supplier_code;
            } else {
                $response['success'] = false;
                $response['messages'] = 'Erreur lors de la création';
            }
        } else {
            $response['success'] = false;
            foreach ($_POST as $key => $value) {
                $response['messages'][$key] = form_error($key);
            }
        }

        echo json_encode($response);
    }

    public function update($id)
    {
        if(!in_array('updateSupplier', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        $response = array();
        if($id) {
            $this->form_validation->set_rules('edit_supplier_name', 'Supplier Name', 'trim|required');
            $this->form_validation->set_rules('edit_phone', 'Phone', 'trim|required');
            $this->form_validation->set_rules('edit_active', 'Active', 'trim|required');
            $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'name' => $this->input->post('edit_supplier_name'),
                    'contact_person' => $this->input->post('edit_contact_person'),
                    'phone' => $this->input->post('edit_phone'),
                    'email' => $this->input->post('edit_email'),
                    'address' => $this->input->post('edit_address'),
                    'country' => $this->input->post('edit_country'),
                    'tax_number' => $this->input->post('edit_tax_number'),
                    'payment_terms' => $this->input->post('edit_payment_terms'),
                    'notes' => $this->input->post('edit_notes'),
                    'active' => $this->input->post('edit_active'),
                );

                $update = $this->model_suppliers->update($data, $id);
                if($update == true) {
                    $response['success'] = true;
                    $response['messages'] = 'Mis à jour avec succès';
                } else {
                    $response['success'] = false;
                    $response['messages'] = 'Erreur lors de la mise à jour';
                }
            } else {
                $response['success'] = false;
                foreach ($_POST as $key => $value) {
                    $response['messages'][$key] = form_error($key);
                }
            }
        } else {
            $response['success'] = false;
            $response['messages'] = 'Erreur, veuillez actualiser la page';
        }

        echo json_encode($response);
    }

    public function remove()
    {
        if(!in_array('deleteSupplier', $this->permission)) {
            redirect('dashboard', 'refresh');
        }
        
        $supplier_id = $this->input->post('supplier_id');
        $response = array();
        
        if($supplier_id) {
            $delete = $this->model_suppliers->remove($supplier_id);
            if($delete == true) {
                $response['success'] = true;
                $response['messages'] = "Supprimé avec succès";
            } else {
                $response['success'] = false;
                $response['messages'] = "Erreur lors de la suppression";
            }
        } else {
            $response['success'] = false;
            $response['messages'] = "Actualiser la page";
        }

        echo json_encode($response);
    }

    public function view($id = null)
    {
        if(!in_array('viewSupplier', $this->permission)) {
            redirect('dashboard', 'refresh');
        }

        if($id) {
            $supplier_data = $this->model_suppliers->getSupplierData($id);
            $supplier_stats = $this->model_suppliers->getSupplierStats($id);
            $supplier_products = $this->model_suppliers->getSupplierProducts($id);

            $this->data['supplier_data'] = $supplier_data;
            $this->data['supplier_stats'] = $supplier_stats;
            $this->data['supplier_products'] = $supplier_products;

            $this->render_template('suppliers/view', $this->data);
        } else {
            redirect('suppliers', 'refresh');
        }
    }
}