<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_auth');
    }

    public function login()
    {
        // Déterminer le type d'authentification selon le domaine
        $host = $_SERVER['HTTP_HOST'];
        $is_admin_domain = (strpos($host, 'admin.') === 0);

        // Redirect if already logged in
        $session_data = $this->session->userdata();
        if (!empty($session_data['logged_in']) && $session_data['logged_in'] == TRUE) {
            // Redirect based on user type
            if ($session_data['user_type'] == 'system_admin') {
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

            // Passer le type de domaine au modèle
            $login = $this->model_auth->login($email, $password, $is_admin_domain);

            if ($login === TRUE) {
                $user_data = $this->session->userdata();

                // Redirect based on user type
                if ($user_data['user_type'] == 'system_admin') {
                    redirect('admin/dashboard', 'refresh');
                } else {
                    redirect('dashboard', 'refresh');
                }
            } else {
                $this->data['errors'] = $login; // Afficher le message d'erreur spécifique
                $this->load->view('login', $this->data);
            }
        } else {
            // Passer une variable pour personnaliser la vue
            $this->data['is_admin_login'] = $is_admin_domain;
            $this->load->view('login', $this->data);
        }
    }


    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login', 'refresh');
    }
}
