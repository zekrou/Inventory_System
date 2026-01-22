<?php
/**
 * SCRIPT DE DIAGNOSTIC - À placer temporairement dans application/controllers/
 * Accéder via: http://votresite.com/diagnostic_preorders
 * 
 * ⚠️ SUPPRIMER CE FICHIER APRÈS LE DEBUG !
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Diagnostic_preorders extends Admin_Controller 
{
    public function index()
    {
        // Forcer l'affichage des erreurs
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        echo "<h1>🔍 Diagnostic Preorders System</h1>";
        echo "<style>
            body { font-family: Arial; padding: 20px; }
            .success { color: green; font-weight: bold; }
            .error { color: red; font-weight: bold; }
            .info { color: blue; }
            pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background: #4CAF50; color: white; }
        </style>";
        
        // 1. Vérifier si l'utilisateur est connecté
        echo "<h2>1️⃣ Session Utilisateur</h2>";
        if($this->session->userdata('logged_in')) {
            echo "<p class='success'>✅ Utilisateur connecté</p>";
            echo "<pre>";
            print_r([
                'user_id' => $this->session->userdata('id'),
                'username' => $this->session->userdata('username'),
                'tenant_id' => $this->session->userdata('tenant_id')
            ]);
            echo "</pre>";
        } else {
            echo "<p class='error'>❌ Utilisateur NON connecté</p>";
            return;
        }
        
        // 2. Vérifier les permissions
        echo "<h2>2️⃣ Permissions</h2>";
        echo "<pre>";
        print_r($this->permission);
        echo "</pre>";
        
        $required_perms = ['viewPreorders', 'updatePreorders', 'deletePreorders'];
        foreach($required_perms as $perm) {
            if(in_array($perm, $this->permission)) {
                echo "<p class='success'>✅ {$perm}</p>";
            } else {
                echo "<p class='error'>❌ {$perm} - MANQUANTE!</p>";
            }
        }
        
        // 3. Vérifier la connexion tenant_db
        echo "<h2>3️⃣ Base de données Tenant</h2>";
        $tenant_db = $this->load_tenant_db();
        
        if($tenant_db) {
            echo "<p class='success'>✅ Connexion tenant_db OK</p>";
            echo "<p class='info'>Database: " . $tenant_db->database . "</p>";
        } else {
            echo "<p class='error'>❌ Échec connexion tenant_db</p>";
            return;
        }
        
        // 4. Vérifier si les tables existent
        echo "<h2>4️⃣ Vérification des tables</h2>";
        $tables = ['pre_orders', 'pre_order_items'];
        
        foreach($tables as $table) {
            if($tenant_db->table_exists($table)) {
                echo "<p class='success'>✅ Table '{$table}' existe</p>";
                
                // Compter les lignes
                $count = $tenant_db->count_all($table);
                echo "<p class='info'>&nbsp;&nbsp;&nbsp;→ {$count} enregistrements</p>";
            } else {
                echo "<p class='error'>❌ Table '{$table}' INTROUVABLE!</p>";
            }
        }
        
        // 5. Tester le model
        echo "<h2>5️⃣ Test du Model</h2>";
        $this->load->model('model_preorders');
        $this->model_preorders->setTenantDb($tenant_db);
        
        try {
            $stats = $this->model_preorders->getStatistics();
            echo "<p class='success'>✅ getStatistics() fonctionne</p>";
            echo "<pre>";
            print_r($stats);
            echo "</pre>";
        } catch(Exception $e) {
            echo "<p class='error'>❌ Erreur getStatistics(): " . $e->getMessage() . "</p>";
        }
        
        try {
            $preorders = $this->model_preorders->getPreOrders();
            echo "<p class='success'>✅ getPreOrders() fonctionne</p>";
            echo "<p class='info'>Nombre de commandes: " . count($preorders) . "</p>";
            
            if(!empty($preorders)) {
                echo "<h3>📋 Premières commandes:</h3>";
                echo "<table>";
                echo "<tr><th>ID</th><th>N° Commande</th><th>Client</th><th>Montant</th><th>Statut</th><th>Date</th></tr>";
                
                foreach(array_slice($preorders, 0, 5) as $order) {
                    echo "<tr>";
                    echo "<td>{$order['id']}</td>";
                    echo "<td>{$order['order_number']}</td>";
                    echo "<td>{$order['customer_name']}</td>";
                    echo "<td>" . number_format($order['total_amount'], 2) . " DZD</td>";
                    echo "<td>{$order['status']}</td>";
                    echo "<td>{$order['created_at']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='info'>ℹ️ Aucune commande dans la base</p>";
            }
        } catch(Exception $e) {
            echo "<p class='error'>❌ Erreur getPreOrders(): " . $e->getMessage() . "</p>";
        }
        
        // 6. Vérifier la structure de la table
        echo "<h2>6️⃣ Structure de la table pre_orders</h2>";
        $query = $tenant_db->query("DESCRIBE pre_orders");
        if($query) {
            echo "<table>";
            echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
            foreach($query->result_array() as $field) {
                echo "<tr>";
                echo "<td>{$field['Field']}</td>";
                echo "<td>{$field['Type']}</td>";
                echo "<td>{$field['Null']}</td>";
                echo "<td>{$field['Key']}</td>";
                echo "<td>{$field['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // 7. Test requête SQL directe
        echo "<h2>7️⃣ Test requête SQL directe</h2>";
        $sql = "SELECT po.*, u.username 
                FROM pre_orders po 
                LEFT JOIN users u ON po.user_id = u.id 
                ORDER BY po.created_at DESC 
                LIMIT 5";
        
        try {
            $result = $tenant_db->query($sql);
            echo "<p class='success'>✅ Requête SQL réussie</p>";
            echo "<p class='info'>Résultats: " . $result->num_rows() . " lignes</p>";
            
            if($result->num_rows() > 0) {
                echo "<pre>";
                print_r($result->result_array());
                echo "</pre>";
            }
        } catch(Exception $e) {
            echo "<p class='error'>❌ Erreur SQL: " . $e->getMessage() . "</p>";
        }
        
        // 8. Vérifier le routing
        echo "<h2>8️⃣ Routes configurées</h2>";
        echo "<p>URL attendues:</p>";
        echo "<ul>";
        echo "<li><a href='" . base_url('preorders') . "'>" . base_url('preorders') . "</a></li>";
        echo "<li><a href='" . base_url('preorders/index') . "'>" . base_url('preorders/index') . "</a></li>";
        echo "</ul>";
        
        echo "<h2>✅ Diagnostic terminé</h2>";
        echo "<p><strong>Si tout est vert ci-dessus mais la page preorders reste vide, le problème vient probablement de la vue (view).</strong></p>";
    }
}