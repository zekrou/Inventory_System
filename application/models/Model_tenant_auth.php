<?php 

class Model_tenant_auth extends CI_Model
{
    private $master_db;
    
    public function __construct()
    {
        parent::__construct();
        // Load tenant library and connect to master DB
        $this->load->library('tenant');
        $this->master_db = $this->tenant->init_master_db();
    }
    
    /**
     * Check if email exists in master database
     */
    public function check_email($email) 
    {
        if($email) {
            $sql = "SELECT * FROM `users` WHERE email = ?";
            $query = $this->master_db->query($sql, array($email));
            return ($query->num_rows() == 1) ? true : false;
        }
        return false;
    }
    
    /**
     * Login with master database and return user + tenants
     */
    public function login($email, $password) 
    {
        if($email && $password) {
            $sql = "SELECT * FROM `users` WHERE email = ?";
            $query = $this->master_db->query($sql, array($email));
            
            if($query->num_rows() == 1) {
                $result = $query->row_array();
                $hash_password = password_verify($password, $result['password']);
                
                if($hash_password === true) {
                    // Get user's tenants
                    $tenants = $this->tenant->get_user_tenants($result['id']);
                    $result['tenants'] = $tenants;
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
    
    /**
     * Get specific tenant info
     */
    public function get_tenant($tenant_id)
    {
        $sql = "SELECT * FROM `tenants` WHERE id = ? AND status = 'active'";
        $query = $this->master_db->query($sql, array($tenant_id));
        return $query->row_array();
    }
}
