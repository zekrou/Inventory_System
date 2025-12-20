<?php 
class Model_purchases extends CI_Model
{
    // Gérer achats + MAJ automatique quantités
    public function getPurchaseData($id = null) { }
    public function create($data) { }  // Crée achat
    public function receivePurchase($purchase_id) { }  // Marque "received" + MAJ qty
    public function getPurchasesBySupplier($supplier_id) { }
    public function getPurchasesByProduct($product_id) { }
    public function getPurchaseStatistics() { }
}