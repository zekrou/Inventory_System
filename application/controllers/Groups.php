<?php 
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
class Groups extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();

        $this->not_logged_in();

        $this->data['page_title'] = 'Groups';

        $this->load->model('model_groups');
    }

    public function index()
    {
        // if(!isset($this->permission['viewGroup'])) {
        //     redirect('dashboard', 'refresh');
        // }

        $groups_data = $this->model_groups->getGroupData();
        $this->data['groups_data'] = $groups_data;

        $this->render_template('groups/index', $this->data);
    }

    public function create()
    {
        // if(!isset($this->permission['createGroup'])) {
        //     redirect('dashboard', 'refresh');
        // }

        $this->form_validation->set_rules('group_name', 'Group name', 'required');

        if ($this->form_validation->run() == TRUE) {
            $permission = serialize($this->input->post('permission'));

            $data = array(
                'group_name' => $this->input->post('group_name'),
                'permission' => $permission
            );

            $create = $this->model_groups->create($data);
            if ($create == true) {
                $this->session->set_flashdata('success', 'Successfully created');
                redirect('groups/', 'refresh');
            } else {
                $this->session->set_flashdata('errors', 'Error occurred!!');
                redirect('groups/create', 'refresh');
            }
        } else {
            $this->render_template('groups/create', $this->data);
        }
    }

    public function edit($id = null)
    {
        // if(!isset($this->permission['updateGroup'])) {
        //     redirect('dashboard', 'refresh');
        // }

        if ($id) {
            $this->form_validation->set_rules('group_name', 'Group name', 'required');

            if ($this->form_validation->run() == TRUE) {
                $permission = serialize($this->input->post('permission'));

                $data = array(
                    'group_name' => $this->input->post('group_name'),
                    'permission' => $permission
                );

                $update = $this->model_groups->edit($data, $id);
                if ($update == true) {
                    $this->session->set_flashdata('success', 'Successfully updated');
                    redirect('groups/', 'refresh');
                } else {
                    $this->session->set_flashdata('errors', 'Error occurred!!');
                    redirect('groups/edit/'.$id, 'refresh');
                }
            } else {
                $group_data = $this->model_groups->getGroupData($id);
                $this->data['group_data'] = $group_data;
                $this->render_template('groups/edit', $this->data);
            }
        }
    }

    public function delete($id)
    {
        // if(!isset($this->permission['deleteGroup'])) {
        //     redirect('dashboard', 'refresh');
        // }

        if ($id) {
            if ($this->input->post('confirm')) {

                $check = $this->model_groups->existInUserGroup($id);
                if ($check == true) {
                    $this->session->set_flashdata('error', 'Group exists in the users');
                    redirect('groups/', 'refresh');
                } else {
                    $delete = $this->model_groups->delete($id);
                    if ($delete == true) {
                        $this->session->set_flashdata('success', 'Successfully removed');
                        redirect('groups/', 'refresh');
                    } else {
                        $this->session->set_flashdata('error', 'Error occurred!!');
                        redirect('groups/delete/'.$id, 'refresh');
                    }
                }
            } else {
                $this->data['id'] = $id;
                $this->render_template('groups/delete', $this->data);
            }
        }
    }
}
