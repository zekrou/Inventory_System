<?php

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property Model_products $model_products
 * @property Model_orders $model_orders
 * @property Model_users $model_users
 * @property Model_company $model_company
 * @property Model_groups $model_groups
 * @property Model_categories $model_categories
 * @property Model_brands $model_brands
 * @property Model_stores $model_stores
 * @property Model_attributes $model_attributes
 */
class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->not_logged_in();

        $this->data['page_title'] = 'Users';

        $this->load->model('model_users');
        $this->load->model('model_groups');
    }

    public function index()
    {
        // ✅ FIX: Charger uniquement les users du TENANT actuel, pas de stock_master
        $user_data = $this->model_users->getUserData();

        $result = array();
        foreach ($user_data as $k => $v) {
            $result[$k]['user_info'] = $v;
            $group = $this->model_users->getUserGroup($v['id']);
            $result[$k]['user_group'] = $group ?? ['group_name' => 'No group'];
        }

        $this->data['user_data'] = $result;

        $this->render_template('users/index', $this->data);
    }

    public function create()
    {
        $this->form_validation->set_rules('groups', 'Group', 'required');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[30]|is_unique[users.username]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
        $this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]');
        $this->form_validation->set_rules('fname', 'First name', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $password = $this->password_hash($this->input->post('password'));
            $data = array(
                'username'  => $this->input->post('username'),
                'password'  => $password,
                'email'     => $this->input->post('email'),
                'firstname' => $this->input->post('fname'),
                'lastname'  => $this->input->post('lname'),
                'phone'     => $this->input->post('phone'),
                'gender'    => $this->input->post('gender'),
            );

            $create = $this->model_users->create($data, $this->input->post('groups'));
            if ($create == true) {
                $this->session->set_flashdata('success', 'Successfully created');
                redirect('users/', 'refresh');
            } else {
                $this->session->set_flashdata('errors', 'Error occurred!!');
                redirect('users/create', 'refresh');
            }
        } else {
            $group_data = $this->model_groups->getGroupData();
            $this->data['group_data'] = $group_data;

            $this->render_template('users/create', $this->data);
        }
    }

    public function password_hash($pass = '')
    {
        if ($pass) {
            return password_hash($pass, PASSWORD_DEFAULT);
        }
    }

    public function edit($id = null)
    {
        if ($id) {
            $this->form_validation->set_rules('groups', 'Group', 'required');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[30]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('fname', 'First name', 'trim|required');

            if ($this->form_validation->run() == TRUE) {

                if (empty($this->input->post('password')) && empty($this->input->post('cpassword'))) {

                    $data = array(
                        'username'  => $this->input->post('username'),
                        'email'     => $this->input->post('email'),
                        'firstname' => $this->input->post('fname'),
                        'lastname'  => $this->input->post('lname'),
                        'phone'     => $this->input->post('phone'),
                        'gender'    => $this->input->post('gender'),
                    );

                    $update = $this->model_users->edit($data, $id, $this->input->post('groups'));
                    if ($update == true) {
                        $this->session->set_flashdata('success', 'Successfully updated');
                        redirect('users/', 'refresh');
                    } else {
                        $this->session->set_flashdata('errors', 'Error occurred!!');
                        redirect('users/edit/' . $id, 'refresh');
                    }
                } else {
                    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
                    $this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]');

                    if ($this->form_validation->run() == TRUE) {
                        $password = $this->password_hash($this->input->post('password'));

                        $data = array(
                            'username'  => $this->input->post('username'),
                            'password'  => $password,
                            'email'     => $this->input->post('email'),
                            'firstname' => $this->input->post('fname'),
                            'lastname'  => $this->input->post('lname'),
                            'phone'     => $this->input->post('phone'),
                            'gender'    => $this->input->post('gender'),
                        );

                        $update = $this->model_users->edit($data, $id, $this->input->post('groups'));
                        if ($update == true) {
                            $this->session->set_flashdata('success', 'Successfully updated');
                            redirect('users/', 'refresh');
                        } else {
                            $this->session->set_flashdata('errors', 'Error occurred!!');
                            redirect('users/edit/' . $id, 'refresh');
                        }
                    } else {
                        $user_data = $this->model_users->getUserData($id);
                        $groups    = $this->model_users->getUserGroup($id);

                        $this->data['user_data']  = $user_data;
                        $this->data['user_group'] = $groups;

                        $group_data               = $this->model_groups->getGroupData();
                        $this->data['group_data'] = $group_data;

                        $this->render_template('users/edit', $this->data);
                    }
                }
            } else {
                $user_data = $this->model_users->getUserData($id);
                $groups    = $this->model_users->getUserGroup($id);

                $this->data['user_data']  = $user_data;
                $this->data['user_group'] = $groups;

                $group_data               = $this->model_groups->getGroupData();
                $this->data['group_data'] = $group_data;

                $this->render_template('users/edit', $this->data);
            }
        }
    }

    public function delete($id)
    {
        if ($id) {
            if ($this->input->post('confirm')) {
                $delete = $this->model_users->delete($id);
                if ($delete == true) {
                    $this->session->set_flashdata('success', 'Successfully removed');
                    redirect('users/', 'refresh');
                } else {
                    $this->session->set_flashdata('error', 'Error occurred!!');
                    redirect('users/delete/' . $id, 'refresh');
                }
            } else {
                $this->data['id'] = $id;
                $this->render_template('users/delete', $this->data);
            }
        }
    }

    public function profile()
    {
        $user_data = $this->session->userdata();

        // Get user info from master database
        $this->load->library('tenant');
        $master_db = $this->tenant->init_master_db();

        $query = $master_db->query("SELECT * FROM users WHERE id = ?", array($user_data['id']));

        if ($query->num_rows() > 0) {
            $user = $query->row_array();
            // Ensure all fields exist
            $this->data['user_info'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'firstname' => $user['firstname'] ?? '',
                'lastname' => $user['lastname'] ?? '',
                'gender' => $user['gender'] ?? '',
                'phone' => $user['phone'] ?? ''
            );
        } else {
            $this->data['user_info'] = array();
        }

        // Pass group info
        $this->data['user_group'] = array(
            'group_name' => $user_data['group_name'] ?? 'Administrator'
        );

        $this->render_template('users/profile', $this->data);
    }

    public function setting()
    {
            // ✅ Vérifier si system_admin
    if ($this->session->userdata('user_type') != 'system_admin') {
        redirect('dashboard', 'refresh');
    }
        $id = $this->session->userdata('id');

        if ($id) {
            $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[30]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('fname', 'First name', 'trim|required');

            if ($this->form_validation->run() == TRUE) {

                if (empty($this->input->post('password')) && empty($this->input->post('cpassword'))) {

                    $data = array(
                        'username'  => $this->input->post('username'),
                        'email'     => $this->input->post('email'),
                        'firstname' => $this->input->post('fname'),
                        'lastname'  => $this->input->post('lname'),
                        'phone'     => $this->input->post('phone'),
                        'gender'    => $this->input->post('gender'),
                    );

                    $update = $this->model_users->edit($data, $id);
                    if ($update == true) {
                        $this->session->set_flashdata('success', 'Successfully updated');
                        redirect('users/setting/', 'refresh');
                    } else {
                        $this->session->set_flashdata('errors', 'Error occurred!!');
                        redirect('users/setting/', 'refresh');
                    }
                } else {
                    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
                    $this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]');

                    if ($this->form_validation->run() == TRUE) {
                        $password = $this->password_hash($this->input->post('password'));

                        $data = array(
                            'username'  => $this->input->post('username'),
                            'password'  => $password,
                            'email'     => $this->input->post('email'),
                            'firstname' => $this->input->post('fname'),
                            'lastname'  => $this->input->post('lname'),
                            'phone'     => $this->input->post('phone'),
                            'gender'    => $this->input->post('gender'),
                        );

                        $update = $this->model_users->edit($data, $id, $this->input->post('groups'));
                        if ($update == true) {
                            $this->session->set_flashdata('success', 'Successfully updated');
                            redirect('users/setting/', 'refresh');
                        } else {
                            $this->session->set_flashdata('errors', 'Error occurred!!');
                            redirect('users/setting/', 'refresh');
                        }
                    } else {
                        $user_data = $this->model_users->getUserData($id);
                        $groups    = $this->model_users->getUserGroup($id);

                        $this->data['user_data']  = $user_data;
                        $this->data['user_group'] = $groups;

                        $group_data               = $this->model_groups->getGroupData();
                        $this->data['group_data'] = $group_data;

                        $this->render_template('users/setting', $this->data);
                    }
                }
            } else {
                $user_data = $this->model_users->getUserData($id);
                $groups    = $this->model_users->getUserGroup($id);

                $this->data['user_data']  = $user_data;
                $this->data['user_group'] = $groups;

                $group_data               = $this->model_groups->getGroupData();
                $this->data['group_data'] = $group_data;

                $this->render_template('users/setting', $this->data);
            }
        }
    }
}
