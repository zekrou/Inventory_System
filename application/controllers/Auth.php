<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_auth');
    }

    public function login()
    {
        // Redirect if already logged in
        $session_data = $this->session->userdata();
        if (!empty($session_data['logged_in']) && $session_data['logged_in'] == TRUE) {
            // Redirect based on user type
            if($session_data['user_type'] == 'system_admin') {
                redirect('admin/dashboard', 'refresh');
            } else {
                redirect('dashboard', 'refresh');
            }
        }
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            
            $login = $this->model_auth->login($email, $password);
            
            if($login === TRUE) {
                $user_data = $this->session->userdata();
                
                // Redirect based on user type
                if($user_data['user_type'] == 'system_admin') {
                    redirect('admin/dashboard', 'refresh');
                } else {
                    redirect('dashboard', 'refresh');
                }
            } else {
                $this->data['errors'] = 'Incorrect email or password';
                $this->load->view('login', $this->data);
            }
        } else {
            $this->load->view('login', $this->data);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login', 'refresh');
    }
}
