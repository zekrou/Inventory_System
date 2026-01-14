<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_auth extends CI_Model
{
    public function login($email, $password, $is_admin_domain = false)
    {
        $this->load->library('tenant');
        $master_db = $this->tenant->init_master_db();
        
        // Get user from master database
        $query = $master_db->query("SELECT * FROM users WHERE email = ?", array($email));
        
        if($query->num_rows() == 1) {
            $result = $query->row_array();
            
            // Verify password
            if(password_verify($password, $result['password'])) {
                
                // Check if user has a tenant
                $tenant_query = $master_db->query(
                    "SELECT ut.*, t.tenant_name, t.database_name, t.status 
                     FROM user_tenant ut 
                     INNER JOIN tenants t ON ut.tenant_id = t.id 
                     WHERE ut.user_id = ? AND t.status = 'active' 
                     LIMIT 1", 
                    array($result['id'])
                );
                
                if($tenant_query->num_rows() > 0) {
                    // ====== MERCHANT USER (has tenant) ======
                    
                    // VALIDATION : Les merchants ne peuvent pas se connecter sur admin.taqseet.shop
                    if($is_admin_domain) {
                        return "Accès refusé. Utilisez inventory.taqseet.shop pour vous connecter.";
                    }
                    
                    $tenant_data = $tenant_query->row_array();
                    
                    // Set session data for MERCHANT
                    $session_data = array(
                        'id' => $result['id'],
                        'username' => $result['username'],
                        'email' => $result['email'],
                        'firstname' => $result['firstname'] ?? '',
                        'lastname' => $result['lastname'] ?? '',
                        'logged_in' => TRUE,
                        'user_type' => 'merchant',
                        'tenant_id' => $tenant_data['tenant_id'],
                        'tenant_name' => $tenant_data['tenant_name'],
                        'tenant_db' => $tenant_data['database_name'],
                        'tenant_role' => $tenant_data['role'],
                        'group_id' => 1,
                        'group_name' => 'Administrator'
                    );
                    
                    $this->session->set_userdata($session_data);
                    $this->tenant->switch_tenant_db($tenant_data['tenant_id']);
                    
                    return TRUE;
                    
                } else {
                    // ====== SYSTEM ADMIN (no tenant) ======
                    
                    // VALIDATION : Les admins système ne peuvent se connecter QUE sur admin.taqseet.shop
                    if(!$is_admin_domain) {
                        return "Accès refusé. Utilisez admin.taqseet.shop pour vous connecter.";
                    }
                    
                    $session_data = array(
                        'id' => $result['id'],
                        'username' => $result['username'],
                        'email' => $result['email'],
                        'firstname' => $result['firstname'],
                        'lastname' => $result['lastname'],
                        'logged_in' => TRUE,
                        'user_type' => 'system_admin',
                        'tenant_id' => NULL,
                        'tenant_name' => 'System Administrator',
                        'tenant_db' => NULL,
                        'tenant_role' => 'system_admin',
                        'group_id' => NULL
                    );
                    
                    $this->session->set_userdata($session_data);
                    return TRUE;
                }
            }
        }
        
        return "Email ou mot de passe incorrect";
    }
}
