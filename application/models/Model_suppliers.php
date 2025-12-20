<?php 
class Model_suppliers extends CI_Model
{
    // Gérer fournisseurs
    public function getActiveSuppliers() { }
    public function getSupplierData($id = null) { }
    public function getSupplierProducts($supplier_id) { }
    public function linkProductToSupplier($supplier_id, $product_id, $data) { }
    public function create($data) { }
    public function update($data, $id) { }
    public function remove($id) { }
}