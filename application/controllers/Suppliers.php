<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * CodeIgniter Properties
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Email $email
 * @property CI_Upload $upload
 * @property CI_Security $security
 * 
 * Custom Models
 * @property Model_products $model_products
 * @property Model_orders $model_orders
 * @property Model_users $model_users
 * @property Model_company $model_company
 * @property Model_groups $model_groups
 * @property Model_categories $model_categories
 * @property Model_brands $model_brands
 * @property Model_stores $model_stores
 * @property Model_attributes $model_attributes
 * @property Model_customers $model_customers
 * @property Model_suppliers $model_suppliers
 */
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
        if (!isset($this->permission['viewSupplier'])) {
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
            if (isset($this->permission['updateSupplier'])) {
                $buttons .= '<button type="button" class="btn btn-default" onclick="editSupplier(' . $value['id'] . ')" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil"></i></button> ';
            }
            if (isset($this->permission['viewSupplier'])) {
                $buttons .= '<a href="' . base_url('suppliers/view/' . $value['id']) . '" class="btn btn-info"><i class="fa fa-eye"></i></a> ';
            }
            if (isset($this->permission['deleteSupplier'])) {
                $buttons .= '<button type="button" class="btn btn-danger" onclick="removeSupplier(' . $value['id'] . ')"><i class="fa fa-trash"></i></button>';
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
        if ($id) {
            $data = $this->model_suppliers->getSupplierData($id);
            echo json_encode($data);
        }
    }

    public function create()
    {
        if (!isset($this->permission['createSupplier'])) {
            echo json_encode(array('success' => false, 'messages' => 'Permission denied'));
            return;
        }

        $response = array();

        $this->form_validation->set_rules('supplier_name', 'Supplier Name', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required');

        if ($this->form_validation->run() == TRUE) {

            // ✅ NOUVELLE GÉNÉRATION DU CODE SUPPLIER
            // Obtenir le dernier ID (pas le code)
            $query = $this->db->select('id')->order_by('id', 'DESC')->limit(1)->get('suppliers');

            if ($query->num_rows() > 0) {
                $last = $query->row();
                $number = $last->id + 1;
            } else {
                $number = 1;
            }

            $supplier_code = 'SUP-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // ✅ VÉRIFIER SI LE CODE EXISTE DÉJÀ (sécurité supplémentaire)
            $check = $this->db->where('supplier_code', $supplier_code)->get('suppliers');

            // Si le code existe, incrémenter jusqu'à trouver un code libre
            while ($check->num_rows() > 0) {
                $number++;
                $supplier_code = 'SUP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
                $check = $this->db->where('supplier_code', $supplier_code)->get('suppliers');
            }

            // Préparer les données
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
                'active' => $this->input->post('active') ? (int)$this->input->post('active') : 1,
            );

            // Insérer
            $insert = $this->db->insert('suppliers', $data);

            if ($insert) {
                $response['success'] = true;
                $response['messages'] = 'Fournisseur créé avec succès - Code: ' . $supplier_code;
            } else {
                $error = $this->db->error();
                $response['success'] = false;
                $response['messages'] = 'Erreur SQL: ' . $error['message'];
            }
        } else {
            $response['success'] = false;
            $response['messages'] = strip_tags(validation_errors());
        }

        echo json_encode($response);
    }






    public function update($id)
    {
        if (!isset($this->permission['updateSupplier'])) {
            redirect('dashboard', 'refresh');
        }

        $response = array();
        if ($id) {
            $this->form_validation->set_rules('edit_supplier_name', 'Supplier Name', 'trim|required');
            $this->form_validation->set_rules('edit_phone', 'Phone', 'trim|required');
            $this->form_validation->set_rules('edit_active', 'Active', 'trim|required');
            $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

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
                if ($update == true) {
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
        if (!isset($this->permission['deleteSupplier'])) {
            redirect('dashboard', 'refresh');
        }

        $supplier_id = $this->input->post('supplier_id');
        $force_delete = $this->input->post('force_delete'); // NEW
        $deactivate_only = $this->input->post('deactivate_only'); // NEW

        $response = array();

        if ($supplier_id) {
            // If user wants to deactivate only
            if ($deactivate_only == 'yes') {
                $deactivate = $this->model_suppliers->deactivate($supplier_id);
                if ($deactivate) {
                    $response['success'] = true;
                    $response['messages'] = "Fournisseur désactivé avec succès";
                } else {
                    $response['success'] = false;
                    $response['messages'] = "Erreur lors de la désactivation";
                }
            } else {
                // Try to delete (with force option)
                $result = $this->model_suppliers->remove($supplier_id, $force_delete == 'yes');

                if ($result['success']) {
                    $response['success'] = true;
                    $response['messages'] = $result['message'];
                } else {
                    $response['success'] = false;
                    $response['type'] = $result['type'];
                    $response['messages'] = $result['message'];
                    $response['purchases_count'] = $result['purchases_count'];
                }
            }
        } else {
            $response['success'] = false;
            $response['messages'] = "Actualiser la page";
        }

        echo json_encode($response);
    }


    public function view($id = null)
    {
        if (!isset($this->permission['viewSupplier'])) {
            redirect('dashboard', 'refresh');
        }

        if ($id) {
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
