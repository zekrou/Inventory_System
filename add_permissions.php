<?php
/**
 * Script final pour ajouter les permissions des nouveaux modules
 * VERSION ULTRA-ROBUSTE - Avec backticks pour les mots-clés réservés
 */

// Masquer les notices PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>🔐 Ajout des Permissions</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; max-width: 800px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        hr { margin: 20px 0; }
        ul { margin: 10px 0; padding-left: 20px; }
        h1 { color: #333; }
        h2 { color: #666; }
        h3 { color: #888; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
<?php

// ============================================
// CONFIGURATION BD
// ============================================
$db_config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'stock'
);

echo "<h1>🔐 Ajout des Permissions - Système d'Inventaire</h1>";
echo "<hr>";

// ============================================
// CONNEXION BD
// ============================================
echo "<h2>🔌 Connexion à la base de données...</h2>";

$mysqli = @new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if ($mysqli->connect_error) {
    echo "<p class='error'>❌ Connexion échouée: " . $mysqli->connect_error . "</p>";
    echo "<p>Vérifiez vos paramètres:</p>";
    echo "<ul>";
    echo "<li>hostname: " . $db_config['hostname'] . "</li>";
    echo "<li>username: " . $db_config['username'] . "</li>";
    echo "<li>database: " . $db_config['database'] . "</li>";
    echo "</ul>";
    die();
}

$mysqli->set_charset("utf8mb4");
echo "<p class='success'>✅ Connexion réussie!</p>";
echo "<hr>";

// ============================================
// ÉTAPE 1: Récupérer les permissions actuelles
// ============================================
echo "<h2>📋 ÉTAPE 1: Récupération des permissions existantes...</h2>";

// ⚠️ FIX: Utiliser des backticks pour le mot-clé réservé "groups"
$query = "SELECT `permission` FROM `groups` WHERE `id` = 1";
$result = $mysqli->query($query);

if (!$result) {
    echo "<p class='error'>❌ Erreur requête: " . $mysqli->error . "</p>";
    echo "<p>Essayez dans PhpMyAdmin: SELECT * FROM `groups`;</p>";
    die();
}

if ($result->num_rows == 0) {
    echo "<p class='error'>❌ Groupe Admin (id=1) non trouvé!</p>";
    echo "<p>Essayez: SELECT * FROM `groups`;</p>";
    die();
}

$row = $result->fetch_assoc();
$current_permissions = @unserialize($row['permission']);

if (!is_array($current_permissions)) {
    echo "<p class='error'>❌ Erreur: Les permissions ne sont pas un array valide!</p>";
    echo "<p>Valeur: " . substr($row['permission'], 0, 100) . "...</p>";
    die();
}

echo "<p class='success'>✅ Permissions existantes trouvées: " . count($current_permissions) . "</p>";
echo "<p class='info'>Exemple: " . implode(", ", array_slice($current_permissions, 0, 3)) . "</p>";
echo "<hr>";

// ============================================
// ÉTAPE 2: Définir les nouvelles permissions
// ============================================
echo "<h2>📋 ÉTAPE 2: Définition des nouvelles permissions...</h2>";

$new_permissions = [
    // Customers
    'viewCustomer', 'createCustomer', 'updateCustomer', 'deleteCustomer',
    // Suppliers
    'viewSupplier', 'createSupplier', 'updateSupplier', 'deleteSupplier',
    // Products
    'viewProduct', 'createProduct', 'updateProduct', 'deleteProduct',
    // Orders
    'viewOrder', 'createOrder', 'updateOrder', 'deleteOrder',
    // Purchases
    'viewPurchase', 'createPurchase', 'updatePurchase', 'deletePurchase',
    // Stock
    'viewStock', 'createStock', 'updateStock', 'deleteStock',
    // Payments
    'viewPayment', 'createPayment', 'updatePayment', 'deletePayment',
    // Brands
    'viewBrand', 'createBrand', 'updateBrand', 'deleteBrand',
    // Categories
    'viewCategory', 'createCategory', 'updateCategory', 'deleteCategory',
    // Company
    'viewCompany', 'updateCompany',
    // Reports
    'viewReport',
    // Attributes
    'viewAttribute', 'createAttribute', 'updateAttribute', 'deleteAttribute'
];

echo "<p class='success'>✅ Nouvelles permissions définies: " . count($new_permissions) . "</p>";
echo "<p class='info'>Exemple: " . implode(", ", array_slice($new_permissions, 0, 3)) . "</p>";
echo "<hr>";

// ============================================
// ÉTAPE 3: Fusionner et dédupliquer
// ============================================
echo "<h2>📋 ÉTAPE 3: Fusion des permissions...</h2>";

$all_permissions = array_unique(array_merge($current_permissions, $new_permissions));

echo "<p class='success'>✅ Fusion réussie!</p>";
echo "<ul>";
echo "<li>Permissions avant: " . count($current_permissions) . "</li>";
echo "<li>Permissions à ajouter: " . count($new_permissions) . "</li>";
echo "<li>Total final: " . count($all_permissions) . "</li>";
echo "</ul>";
echo "<hr>";

// ============================================
// ÉTAPE 4: Sérialiser
// ============================================
echo "<h2>📋 ÉTAPE 4: Sérialisation...</h2>";

$serialized = serialize($all_permissions);

echo "<p class='success'>✅ Sérialisation réussie!</p>";
echo "<p class='info'>Taille: " . strlen($serialized) . " caractères</p>";
echo "<hr>";

// ============================================
// ÉTAPE 5: Mettre à jour la BD
// ============================================
echo "<h2>📋 ÉTAPE 5: Mise à jour de la base de données...</h2>";

// ⚠️ FIX: Utiliser des backticks pour le mot-clé réservé "groups"
$query = "UPDATE `groups` SET `permission` = ? WHERE `id` = 1";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    echo "<p class='error'>❌ Erreur prepare: " . $mysqli->error . "</p>";
    die();
}

$stmt->bind_param('s', $serialized);

if (!$stmt->execute()) {
    echo "<p class='error'>❌ Erreur execute: " . $stmt->error . "</p>";
    die();
}

echo "<p class='success'>✅ UPDATE réussi!</p>";
echo "<hr>";

// ============================================
// RÉSULTAT FINAL
// ============================================
echo "<h1 class='success'>✅✅✅ SUCCÈS! Permissions ajoutées!</h1>";
echo "<hr>";

echo "<h3>📊 Résumé complet:</h3>";
echo "<ul>";
echo "<li><strong>Permissions initiales:</strong> " . count($current_permissions) . "</li>";
echo "<li><strong>Permissions ajoutées:</strong> " . count($new_permissions) . "</li>";
echo "<li><strong>Total final:</strong> " . count($all_permissions) . "</li>";
echo "</ul>";

echo "<h3>📝 Nouvelles permissions (48 au total):</h3>";
echo "<ul>";
foreach ($new_permissions as $perm) {
    echo "<li>" . $perm . "</li>";
}
echo "</ul>";

echo "<h3 style='color: red;'>🔄 PROCHAINES ÉTAPES:</h3>";
echo "<ol>";
echo "<li><strong>Déconnectez-vous</strong> de l'application</li>";
echo "<li><strong>Reconnectez-vous</strong></li>";
echo "<li><strong>Vérifiez le menu latéral</strong> - Tous les modules doivent apparaître! 🎉</li>";
echo "</ol>";

echo "<hr>";
echo "<p style='background: #fff3cd; padding: 10px; border-radius: 5px;'>";
echo "<strong>✅ Étape 1 COMPLÉTÉE!</strong><br>";
echo "Les permissions ont été ajoutées avec succès.<br>";
echo "Prochaine étape: <strong>Modifier side_menubar.php</strong> pour ajouter les liens";
echo "</p>";

$stmt->close();
$mysqli->close();

?>
</div>
</body>
</html>