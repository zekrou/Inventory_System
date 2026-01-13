<?php
// test_supplier.php - Placez ce fichier dans C:/laragon/www/inventorysys/

// Activer les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simuler la création d'un supplier
$_POST = array(
    'supplier_name' => 'Test Supplier',
    'contact_person' => 'John Doe',
    'phone' => '0555123456',
    'email' => 'test@example.com',
    'address' => '123 Test Street',
    'country' => 'Algeria',
    'tax_number' => 'TAX123',
    'payment_terms' => 'Net 30',
    'notes' => 'Test notes',
    'active' => '1'
);

// Charger CodeIgniter
require_once('index.php');
