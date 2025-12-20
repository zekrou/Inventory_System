<?php 
class Model_stock extends CI_Model
{
    // Gérer Stock (Parfum, Cosmétiques, Clothes)
    public function getActiveStock() { }
    public function getStockData($id = null) { }
    public function getStockWithCategories($stock_id) { }
    public function getStockStatistics($stock_id) { }
    public function create($data) { }
    public function update($data, $id) { }
    public function remove($id) { }
}