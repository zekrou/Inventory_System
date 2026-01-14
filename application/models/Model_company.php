<?php 

class Model_company extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /* Get company data */
    public function getCompanyData($id = null)
    {
        if($id) {
            $sql = "SELECT * FROM `company` WHERE id = ?";
            $query = $this->db->query($sql, array($id));
            return $query->row_array();
        }
        
        // Get all companies
        $sql = "SELECT * FROM `company` ORDER BY id ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /* Create new company */
    public function create($data)
    {
        if($data) {
            $insert = $this->db->insert('company', $data);
            return ($insert == true) ? $this->db->insert_id() : false;
        }
        return false;
    }

    /* Update company */
    public function update($data, $id)
    {
        if($data && $id) {
            $this->db->where('id', $id);
            $update = $this->db->update('company', $data);
            return ($update == true) ? true : false;
        }
        return false;
    }

    /* Delete company */
    public function delete($id)
    {
        if($id) {
            $this->db->where('id', $id);
            $delete = $this->db->delete('company');
            return ($delete == true) ? true : false;
        }
        return false;
    }

    /* Check if company exists */
    public function companyExists($id)
    {
        $sql = "SELECT COUNT(*) as count FROM `company` WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        $result = $query->row_array();
        return $result['count'] > 0;
    }
}
