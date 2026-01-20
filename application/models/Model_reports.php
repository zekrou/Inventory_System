<?php

/**
 * CodeIgniter Properties
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Email $email
 * @property CI_Upload $upload
 * @property CI_Security $security
 * 
 * Custom Models
 * @property Model_products $model_products
 * @property Model_orders $model_orders
 * @property Model_users $model_users
 * @property Model_company $model_company
 * @property Model_groups $model_groups
 * @property Model_categories $model_categories
 * @property Model_brands $model_brands
 * @property Model_stores $model_stores
 * @property Model_attributes $model_attributes
 * @property Model_customers $model_customers
 * @property Model_suppliers $model_suppliers
 * @property Model_stock $model_stock
 * @property Model_purchases $model_purchases
 */
class Model_reports extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/*getting the total months*/
	private function months()
	{
		return array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
	}

	/* getting the year of the orders */
	public function getOrderYear()
	{
		// Fixed year range from 2023 to 2030
		$start_year = 2023;
		$end_year = 2030;

		$return_data = array();

		// Generate years in descending order (2030, 2029, ..., 2023)
		for ($year = $end_year; $year >= $start_year; $year--) {
			$return_data[] = $year;
		}

		return $return_data;
	}


	/* getting the months of the orders for a specific year */
	public function getOrderMonths($year)
	{
		$sql = "SELECT DISTINCT MONTH(date_time) as order_month FROM `orders` WHERE paid_status = ? AND YEAR(date_time) = ? AND date_time IS NOT NULL ORDER BY order_month ASC";
		$query = $this->db->query($sql, array(1, $year));
		$result = $query->result_array();

		$return_data = array();
		foreach ($result as $row) {
			if (!empty($row['order_month']) && $row['order_month'] > 0) {
				$return_data[] = str_pad($row['order_month'], 2, '0', STR_PAD_LEFT);
			}
		}

		return $return_data;
	}

	// getting the order reports based on the year and months
	public function getOrderData($year, $month = null)
	{
		if ($year) {
			// Si un mois spécifique est sélectionné
			if ($month) {
				$sql = "SELECT * FROM `orders` WHERE paid_status = ? AND YEAR(date_time) = ? AND MONTH(date_time) = ? AND date_time IS NOT NULL";
				$query = $this->db->query($sql, array(1, $year, $month));
				$result = $query->result_array();

				$get_mon_year = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
				$final_data[$get_mon_year] = $result;

				return $final_data;
			}
			// Sinon, obtenir toutes les données de l'année
			else {
				$months = $this->months();

				$sql = "SELECT * FROM `orders` WHERE paid_status = ? AND date_time IS NOT NULL";
				$query = $this->db->query($sql, array(1));
				$result = $query->result_array();

				$final_data = array();
				foreach ($months as $month_k => $month_y) {
					$get_mon_year = $year . '-' . $month_y;

					$final_data[$get_mon_year] = array();
					foreach ($result as $k => $v) {
						if (isset($v['date_time']) && !empty($v['date_time'])) {
							$timestamp = strtotime($v['date_time']);

							if ($timestamp !== false && $timestamp > 0) {
								$month_year = date('Y-m', $timestamp);

								if ($get_mon_year == $month_year) {
									$final_data[$get_mon_year][] = $v;
								}
							}
						}
					}
				}

				return $final_data;
			}
		}
	}

	// Get daily data for a specific month
	public function getOrderDataByDay($year, $month)
	{
		$sql = "SELECT * FROM `orders` WHERE paid_status = ? AND YEAR(date_time) = ? AND MONTH(date_time) = ? AND date_time IS NOT NULL ORDER BY date_time ASC";
		$query = $this->db->query($sql, array(1, $year, $month));
		$result = $query->result_array();

		$final_data = array();
		foreach ($result as $order) {
			$day = date('Y-m-d', strtotime($order['date_time']));
			if (!isset($final_data[$day])) {
				$final_data[$day] = array();
			}
			$final_data[$day][] = $order;
		}

		return $final_data;
	}

    // ========================================
    // RAPPORTS VENTES
    // ========================================

	/**
	 * Rapport de Profit Réel (Ventes - Coûts)
	 */
	public function getProfitReport($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
                    DATE(o.date_time) as date,
					SUM(oi.amount * (o.net_amount / o.gross_amount)) as total_ventes,
                    SUM(p.price_default * oi.qty) as total_couts,
                    SUM((oi.rate - p.price_default) * oi.qty) as profit_net
                FROM orders o
                JOIN orders_item oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE YEAR(o.date_time) = ? 
                AND MONTH(o.date_time) = ?
                GROUP BY DATE(o.date_time)
                ORDER BY date ASC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
                    MONTH(o.date_time) as mois,
					SUM(oi.amount * (o.net_amount / o.gross_amount)) as total_ventes,
                    SUM(p.price_default * oi.qty) as total_couts,
                    SUM((oi.rate - p.price_default) * oi.qty) as profit_net
                FROM orders o
                JOIN orders_item oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE YEAR(o.date_time) = ?
                GROUP BY MONTH(o.date_time)
                ORDER BY mois ASC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * Top Produits par Profit et Quantité
	 */
	public function getTopProducts($year, $limit = 10)
	{
		$sql = "SELECT 
                p.id,
                p.name,
                p.price_default as prix_achat,
                COUNT(DISTINCT o.id) as nb_commandes,
                SUM(oi.qty) as quantite_vendue,
                SUM(oi.amount * (o.net_amount / NULLIF(o.gross_amount, 0))) as total_ventes,
                SUM(p.price_default * oi.qty) as total_couts,
                SUM((oi.rate - p.price_default) * oi.qty) as profit_total,
                ROUND(AVG((oi.rate - p.price_default) / p.price_default * 100), 2) as marge_moyenne_pct
            FROM orders o
            JOIN orders_item oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE YEAR(o.date_time) = ?
            GROUP BY p.id
            ORDER BY profit_total DESC
            LIMIT ?";

		$query = $this->db->query($sql, array($year, $limit));
		return $query->result_array();
	}

	/**
	 * Analyse par Type de Prix (Client)
	 */
	public function getSalesByPriceType($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
                    CASE 
                        WHEN oi.rate = p.price_retail THEN 'Détail'
                        WHEN oi.rate = p.price_wholesale THEN 'Gros'
                        WHEN oi.rate = p.price_super_wholesale THEN 'Super Gros'
                        ELSE 'Autre'
                    END as type_client,
                    COUNT(DISTINCT o.id) as nb_commandes,
                    SUM(oi.qty) as quantite_totale,
                    SUM(oi.amount * (o.net_amount / NULLIF(o.gross_amount, 0))) as total_ventes,
                    SUM((oi.rate - p.price_default) * oi.qty) as profit_total
                FROM orders o
                JOIN orders_item oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE YEAR(o.date_time) = ? 
                AND MONTH(o.date_time) = ?
                GROUP BY type_client
                ORDER BY total_ventes DESC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
                    CASE 
                        WHEN oi.rate = p.price_retail THEN 'Détail'
                        WHEN oi.rate = p.price_wholesale THEN 'Gros'
                        WHEN oi.rate = p.price_super_wholesale THEN 'Super Gros'
                        ELSE 'Autre'
                    END as type_client,
                    COUNT(DISTINCT o.id) as nb_commandes,
                    SUM(oi.qty) as quantite_totale,
                    SUM(oi.amount * (o.net_amount / NULLIF(o.gross_amount, 0))) as total_ventes,
                    SUM((oi.rate - p.price_default) * oi.qty) as profit_total
                FROM orders o
                JOIN orders_item oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE YEAR(o.date_time) = ?
                GROUP BY type_client
                ORDER BY total_ventes DESC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * État des Paiements (Payé vs Crédit)
	 */
	public function getPaymentStatus($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
                    DATE(date_time) as date,
                    COUNT(*) as nb_commandes,
                    SUM(net_amount) as montant_total,
                    SUM(paid_amount) as montant_paye,
                    SUM(due_amount) as montant_credit,
                    ROUND((SUM(paid_amount) / NULLIF(SUM(net_amount), 0)) * 100, 2) as taux_paiement
                FROM orders
                WHERE YEAR(date_time) = ? 
                AND MONTH(date_time) = ?
                GROUP BY DATE(date_time)
                ORDER BY date ASC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
                    MONTH(date_time) as mois,
                    COUNT(*) as nb_commandes,
                    SUM(net_amount) as montant_total,
                    SUM(paid_amount) as montant_paye,
                    SUM(due_amount) as montant_credit,
                    ROUND((SUM(paid_amount) / NULLIF(SUM(net_amount), 0)) * 100, 2) as taux_paiement
                FROM orders
                WHERE YEAR(date_time) = ?
                GROUP BY MONTH(date_time)
                ORDER BY mois ASC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * Statistiques Globales pour une année
	 */
	public function getGlobalStats($year)
	{
		$sql = "SELECT 
                COUNT(DISTINCT o.id) as total_commandes,
                COUNT(DISTINCT oi.product_id) as produits_vendus,
                SUM(oi.qty) as quantite_totale,
                SUM(o.net_amount) as chiffre_affaires,
                SUM(o.paid_amount) as encaisse,
                SUM(o.due_amount) as credit_total,
                SUM(p.price_default * oi.qty) as couts_totaux,
                SUM((oi.rate - p.price_default) * oi.qty) as profit_net
            FROM orders o
            JOIN orders_item oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE YEAR(o.date_time) = ?";

		$query = $this->db->query($sql, array($year));
		$result = $query->row_array();

		if ($result && $result['chiffre_affaires'] > 0) {
			$result['marge_beneficiaire'] = round(($result['profit_net'] / $result['chiffre_affaires']) * 100, 2);
		} else {
			$result['marge_beneficiaire'] = 0;
		}

		return $result;
	}

    // ========================================
// RAPPORTS ACHATS / FOURNISSEURS
// ========================================

	/**
	 * Rapport Achats par Fournisseur
	 */
	public function getPurchasesBySupplier($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
                s.name as fournisseur,
                COUNT(DISTINCT p.id) as nb_achats,
                SUM(pi.quantity) as quantite_totale,
                SUM(pi.total_price) as total_achats,
                AVG(pi.unit_price) as prix_moyen
            FROM purchases p
            JOIN purchase_items pi ON p.id = pi.purchase_id
            JOIN suppliers s ON p.supplier_id = s.id
            WHERE YEAR(p.purchase_date) = ? 
            AND MONTH(p.purchase_date) = ?
            GROUP BY s.id
            ORDER BY total_achats DESC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
                s.name as fournisseur,
                COUNT(DISTINCT p.id) as nb_achats,
                SUM(pi.quantity) as quantite_totale,
                SUM(pi.total_price) as total_achats,
                AVG(pi.unit_price) as prix_moyen
            FROM purchases p
            JOIN purchase_items pi ON p.id = pi.purchase_id
            JOIN suppliers s ON p.supplier_id = s.id
            WHERE YEAR(p.purchase_date) = ?
            GROUP BY s.id
            ORDER BY total_achats DESC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * Top Produits Achetés
	 */
	public function getTopPurchasedProducts($year, $limit = 10)
	{
		$sql = "SELECT 
            pr.name as produit,
            COUNT(DISTINCT p.id) as nb_commandes_achat,
            SUM(pi.quantity) as quantite_achetee,
            SUM(pi.total_price) as total_depense,
            AVG(pi.unit_price) as prix_achat_moyen
        FROM purchases p
        JOIN purchase_items pi ON p.id = pi.purchase_id
        JOIN products pr ON pi.product_id = pr.id
        WHERE YEAR(p.purchase_date) = ?
        GROUP BY pr.id
        ORDER BY quantite_achetee DESC
        LIMIT ?";

		$query = $this->db->query($sql, array($year, $limit));
		return $query->result_array();
	}

	/**
	 * Évolution des Achats par Période
	 */
	public function getPurchasesEvolution($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
                DATE(p.purchase_date) as date,
                COUNT(DISTINCT p.id) as nb_achats,
                SUM(pi.quantity) as quantite,
                SUM(pi.total_price) as total_depense
            FROM purchases p
            JOIN purchase_items pi ON p.id = pi.purchase_id
            WHERE YEAR(p.purchase_date) = ? 
            AND MONTH(p.purchase_date) = ?
            GROUP BY DATE(p.purchase_date)
            ORDER BY date ASC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
                MONTH(p.purchase_date) as mois,
                COUNT(DISTINCT p.id) as nb_achats,
                SUM(pi.quantity) as quantite,
                SUM(pi.total_price) as total_depense
            FROM purchases p
            JOIN purchase_items pi ON p.id = pi.purchase_id
            WHERE YEAR(p.purchase_date) = ?
            GROUP BY MONTH(p.purchase_date)
            ORDER BY mois ASC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * État Paiements Fournisseurs
	 * CORRIGÉ: Utilise paid_amount et due_amount réels de la table purchases
	 */
	public function getSupplierPaymentStatus($year, $month = null)
	{
		if ($month) {
			$sql = "SELECT 
            DATE(purchase_date) as date,
            COUNT(*) as nb_achats,
            SUM(total_amount) as montant_total,
            SUM(COALESCE(paid_amount, 0)) as montant_paye,
            SUM(COALESCE(due_amount, total_amount)) as montant_du,
            ROUND((SUM(COALESCE(paid_amount, 0)) / NULLIF(SUM(total_amount), 0)) * 100, 2) as taux_paiement
        FROM purchases
        WHERE YEAR(purchase_date) = ?
        AND MONTH(purchase_date) = ?
        GROUP BY DATE(purchase_date)
        ORDER BY date ASC";
			$query = $this->db->query($sql, array($year, $month));
		} else {
			$sql = "SELECT 
            MONTH(purchase_date) as mois,
            COUNT(*) as nb_achats,
            SUM(total_amount) as montant_total,
            SUM(COALESCE(paid_amount, 0)) as montant_paye,
            SUM(COALESCE(due_amount, total_amount)) as montant_du,
            ROUND((SUM(COALESCE(paid_amount, 0)) / NULLIF(SUM(total_amount), 0)) * 100, 2) as taux_paiement
        FROM purchases
        WHERE YEAR(purchase_date) = ?
        GROUP BY MONTH(purchase_date)
        ORDER BY mois ASC";
			$query = $this->db->query($sql, array($year));
		}

		return $query->result_array();
	}

	/**
	 * Statistiques Globales Achats
	 * CORRIGÉ: Utilise paid_amount et due_amount réels de la table purchases
	 */
	public function getPurchaseGlobalStats($year)
	{
		$sql = "SELECT 
        COUNT(DISTINCT p.id) as total_achats,
        COUNT(DISTINCT s.id) as nb_fournisseurs,
        SUM(pi.quantity) as quantite_totale,
        SUM(p.total_amount) as total_depenses,
        SUM(COALESCE(p.paid_amount, 0)) as total_paye,
        SUM(COALESCE(p.due_amount, p.total_amount)) as total_du
    FROM purchases p
    JOIN purchase_items pi ON p.id = pi.purchase_id
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    WHERE YEAR(p.purchase_date) = ?";

		$query = $this->db->query($sql, array($year));
		$result = $query->row_array();

		// Calculer le taux de paiement
		if ($result && $result['total_depenses'] > 0) {
			$result['taux_paiement'] = round(($result['total_paye'] / $result['total_depenses']) * 100, 2);
		} else {
			if (!$result) {
				$result = array(
					'total_achats' => 0,
					'nb_fournisseurs' => 0,
					'quantite_totale' => 0,
					'total_depenses' => 0,
					'total_paye' => 0,
					'total_du' => 0
				);
			}
			$result['taux_paiement'] = 0;
		}

		return $result;
	}

	/**
	 * Comparaison Achats vs Ventes
	 */
	/**
	 * Comparaison Achats vs Ventes (Corrigé avec stock_history)
	 */
	/**
	 * Rapport Simplifié: Achats vs Ventes
	 * Affiche uniquement: Produit, Qté Achetée, Qté Vendue, Stock Actuel
	 */
	public function getPurchaseVsSales($year)
	{
		$sql = "SELECT
            pr.name as produit,
            
            -- Quantité achetée
            COALESCE((SELECT SUM(pi.quantity) 
                      FROM purchase_items pi 
                      JOIN purchases p ON pi.purchase_id = p.id
                      WHERE pi.product_id = pr.id 
                      AND YEAR(p.purchase_date) = ?), 0) as qty_achetee,
            
            -- Quantité vendue
            COALESCE((SELECT SUM(oi.qty) 
                      FROM orders_item oi 
                      JOIN orders o ON oi.order_id = o.id
                      WHERE oi.product_id = pr.id 
                      AND YEAR(o.date_time) = ?), 0) as qty_vendue,
            
            -- Stock actuel dans la base
            pr.qty as stock_actuel
            
        FROM products pr
        
        HAVING qty_achetee > 0 OR qty_vendue > 0
        
        ORDER BY qty_vendue DESC
        
        LIMIT 10";

		$query = $this->db->query($sql, array($year, $year));
		return $query->result_array();
	}



    // ========================================
    // RAPPORTS CLIENTS
    // ========================================

	/**
	 * Top Clients
	 */
	public function getTopCustomers($year, $limit = 10)
	{
		$sql = "SELECT 
                o.customer_name,
                o.customer_phone,
                COUNT(o.id) as nb_commandes,
                SUM(o.net_amount) as total_achats,
                SUM(o.paid_amount) as total_paye,
                SUM(o.due_amount) as total_du,
                AVG(o.net_amount) as panier_moyen
            FROM orders o
            WHERE YEAR(o.date_time) = ?
            GROUP BY o.customer_name, o.customer_phone
            ORDER BY total_achats DESC
            LIMIT ?";

		$query = $this->db->query($sql, array($year, $limit));
		return $query->result_array();
	}

	/**
	 * Clients par Type de Prix
	 */
	public function getCustomersByType($year)
	{
		$sql = "SELECT 
                CASE 
                    WHEN oi.rate = p.price_retail THEN 'Détail'
                    WHEN oi.rate = p.price_wholesale THEN 'Gros'
                    WHEN oi.rate = p.price_super_wholesale THEN 'Super Gros'
                    ELSE 'Autre'
                END as type_client,
                COUNT(DISTINCT o.customer_name) as nb_clients,
                COUNT(DISTINCT o.id) as nb_commandes,
                SUM(o.net_amount) as total_ventes
            FROM orders o
            JOIN orders_item oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE YEAR(o.date_time) = ?
            GROUP BY type_client
            ORDER BY total_ventes DESC";

		$query = $this->db->query($sql, array($year));
		return $query->result_array();
	}

	/**
	 * Clients avec Dettes
	 */
	public function getCustomerDebt($year)
	{
		$sql = "SELECT 
                customer_name,
                customer_phone,
                COUNT(*) as nb_factures_impayees,
                SUM(net_amount) as total_facture,
                SUM(paid_amount) as total_paye,
                SUM(due_amount) as total_du
            FROM orders
            WHERE YEAR(date_time) = ?
            AND due_amount > 0
            GROUP BY customer_name, customer_phone
            ORDER BY total_du DESC";

		$query = $this->db->query($sql, array($year));
		return $query->result_array();
	}

	/**
	 * Fidélité Clients
	 */
	public function getCustomerLoyalty($year)
	{
		$sql = "SELECT 
                customer_name,
                customer_phone,
                COUNT(*) as nb_commandes,
                MIN(date_time) as premiere_commande,
                MAX(date_time) as derniere_commande,
                SUM(net_amount) as total_depense,
                DATEDIFF(MAX(date_time), MIN(date_time)) as anciennete_jours
            FROM orders
            WHERE YEAR(date_time) = ?
            GROUP BY customer_name, customer_phone
            HAVING nb_commandes > 1
            ORDER BY nb_commandes DESC
            LIMIT 20";

		$query = $this->db->query($sql, array($year));
		return $query->result_array();
	}
}
