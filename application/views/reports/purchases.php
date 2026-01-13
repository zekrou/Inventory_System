<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <h1>
      <i class="fa fa-shopping-cart"></i> Rapports Achats & Fournisseurs
      <small>Analyse Complète des Achats</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="<?php echo base_url('reports') ?>">Rapports</a></li>
      <li class="active">Achats</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Filtres -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filtres</h3>
      </div>
      <div class="box-body">
        <?php echo form_open('reports/purchases', array('class' => 'form-horizontal')) ?>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="col-sm-4 control-label">Année</label>
              <div class="col-sm-8">
                <select class="form-control" name="select_year" id="select_year">
                  <?php foreach ($report_years as $year): ?>
                    <option value="<?php echo $year ?>" <?php if ($selected_year == $year) {
                                                          echo "selected";
                                                        } ?>><?php echo $year ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="col-sm-4 control-label">Mois</label>
              <div class="col-sm-8">
                <select class="form-control" name="select_month" id="select_month">
                  <option value="">Toute l'année</option>
                  <option value="01" <?php if ($selected_month == '01') {
                                        echo "selected";
                                      } ?>>Janvier</option>
                  <option value="02" <?php if ($selected_month == '02') {
                                        echo "selected";
                                      } ?>>Février</option>
                  <option value="03" <?php if ($selected_month == '03') {
                                        echo "selected";
                                      } ?>>Mars</option>
                  <option value="04" <?php if ($selected_month == '04') {
                                        echo "selected";
                                      } ?>>Avril</option>
                  <option value="05" <?php if ($selected_month == '05') {
                                        echo "selected";
                                      } ?>>Mai</option>
                  <option value="06" <?php if ($selected_month == '06') {
                                        echo "selected";
                                      } ?>>Juin</option>
                  <option value="07" <?php if ($selected_month == '07') {
                                        echo "selected";
                                      } ?>>Juillet</option>
                  <option value="08" <?php if ($selected_month == '08') {
                                        echo "selected";
                                      } ?>>Août</option>
                  <option value="09" <?php if ($selected_month == '09') {
                                        echo "selected";
                                      } ?>>Septembre</option>
                  <option value="10" <?php if ($selected_month == '10') {
                                        echo "selected";
                                      } ?>>Octobre</option>
                  <option value="11" <?php if ($selected_month == '11') {
                                        echo "selected";
                                      } ?>>Novembre</option>
                  <option value="12" <?php if ($selected_month == '12') {
                                        echo "selected";
                                      } ?>>Décembre</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fa fa-search"></i> Filtrer
            </button>
          </div>
        </div>
        <?php echo form_close() ?>
      </div>
    </div>

    <!-- STATISTIQUES GLOBALES ACHATS -->
    <?php if ($purchase_global_stats): ?>
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Dépenses</span>
              <span class="info-box-number"><?php echo number_format($purchase_global_stats['total_depenses'], 2) ?> <?php echo $company_currency ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-truck"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Fournisseurs</span>
              <span class="info-box-number"><?php echo $purchase_global_stats['nb_fournisseurs'] ?></span>
              <span class="progress-description"><?php echo $purchase_global_stats['total_achats'] ?> achats</span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-check"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Payé</span>
              <span class="info-box-number"><?php echo number_format($purchase_global_stats['total_paye'], 2) ?> <?php echo $company_currency ?></span>
              <span class="progress-description"><?php echo $purchase_global_stats['taux_paiement'] ?>%</span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-orange">
            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Dû aux Fournisseurs</span>
              <span class="info-box-number"><?php echo number_format($purchase_global_stats['total_du'], 2) ?> <?php echo $company_currency ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- RAPPORT 1: ACHATS PAR FOURNISSEUR -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-truck"></i> Rapport 1: Achats par Fournisseur</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Fournisseur</th>
                <th>Nb Achats</th>
                <th>Quantité</th>
                <th>Total Dépensé</th>
                <th>Prix Moyen</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($purchases_by_supplier): ?>
                <?php foreach ($purchases_by_supplier as $supplier): ?>
                  <tr>
                    <td><strong><?php echo $supplier['fournisseur'] ?></strong></td>
                    <td><?php echo $supplier['nb_achats'] ?></td>
                    <td><?php echo $supplier['quantite_totale'] ?></td>
                    <td><?php echo number_format($supplier['total_achats'], 2) ?> <?php echo $company_currency ?></td>
                    <td><?php echo number_format($supplier['prix_moyen'], 2) ?> <?php echo $company_currency ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 2: TOP PRODUITS ACHETÉS -->
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-star"></i> Rapport 2: Top 10 Produits Achetés</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Quantité Achetée</th>
                <th>Nb Commandes</th>
                <th>Total Dépensé</th>
                <th>Prix Achat Moyen</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($top_purchased_products): ?>
                <?php $rank = 1;
                foreach ($top_purchased_products as $product): ?>
                  <tr>
                    <td><strong><?php echo $rank++ ?></strong></td>
                    <td><?php echo $product['produit'] ?></td>
                    <td><?php echo $product['quantite_achetee'] ?></td>
                    <td><?php echo $product['nb_commandes_achat'] ?></td>
                    <td><?php echo number_format($product['total_depense'], 2) ?> <?php echo $company_currency ?></td>
                    <td><?php echo number_format($product['prix_achat_moyen'], 2) ?> <?php echo $company_currency ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 3: ÉVOLUTION DES ACHATS -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-line-chart"></i> Rapport 3: Évolution des Achats</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Période</th>
                <th>Nb Achats</th>
                <th>Quantité</th>
                <th>Total Dépensé</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($purchases_evolution): ?>
                <?php
                $total_achats = 0;
                $total_qty = 0;
                $total_depense = 0;
                foreach ($purchases_evolution as $row):
                  $periode = isset($row['date']) ? $row['date'] : $selected_year . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
                  $total_achats += $row['nb_achats'];
                  $total_qty += $row['quantite'];
                  $total_depense += $row['total_depense'];
                ?>
                  <tr>
                    <td><?php echo $periode ?></td>
                    <td><?php echo $row['nb_achats'] ?></td>
                    <td><?php echo $row['quantite'] ?></td>
                    <td><?php echo number_format($row['total_depense'], 2) ?> <?php echo $company_currency ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="bg-gray">
                  <td><strong>TOTAL</strong></td>
                  <td><strong><?php echo $total_achats ?></strong></td>
                  <td><strong><?php echo $total_qty ?></strong></td>
                  <td><strong><?php echo number_format($total_depense, 2) ?> <?php echo $company_currency ?></strong></td>
                </tr>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 4: PAIEMENTS FOURNISSEURS -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-money"></i> Rapport 4: État Paiements Fournisseurs</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Période</th>
                <th>Nb Achats</th>
                <th>Montant Total</th>
                <th>Payé</th>
                <th>Dû</th>
                <th>Taux Paiement</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($supplier_payment_status): ?>
                <?php
                $total_montant = 0;
                $total_paye = 0;
                $total_du = 0;
                foreach ($supplier_payment_status as $row):
                  $periode = isset($row['date']) ? $row['date'] : $selected_year . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
                  $total_montant += $row['montant_total'];
                  $total_paye += $row['montant_paye'];
                  $total_du += $row['montant_du'];
                  $taux = floatval($row['taux_paiement']);
                ?>
                  <tr>
                    <td><?php echo $periode ?></td>
                    <td><?php echo $row['nb_achats'] ?></td>
                    <td><?php echo number_format($row['montant_total'], 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-success"><?php echo number_format($row['montant_paye'], 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-danger"><?php echo number_format($row['montant_du'], 2) ?> <?php echo $company_currency ?></td>
                    <td>
                      <span class="badge <?php echo $taux >= 80 ? 'bg-green' : ($taux >= 50 ? 'bg-yellow' : 'bg-red') ?>">
                        <?php echo $taux ?>%
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <tr class="bg-gray">
                  <td colspan="2"><strong>TOTAL</strong></td>
                  <td><strong><?php echo number_format($total_montant, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td class="text-success"><strong><?php echo number_format($total_paye, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td class="text-danger"><strong><?php echo number_format($total_du, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td><strong><?php echo $total_montant > 0 ? round(($total_paye / $total_montant) * 100, 2) : 0 ?>%</strong></td>
                </tr>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Rapport 5: Achats vs Ventes (Simplifié) -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-exchange"></i> Achats vs Ventes (Top 10)
        </h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead style="background: #00a65a; color: white;">
              <tr>
                <th>Produit</th>
                <th class="text-center">Qté Achetée</th>
                <th class="text-center">Qté Vendue</th>
                <th class="text-center">Stock Actuel</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($purchase_vs_sales)): ?>
                <?php foreach ($purchase_vs_sales as $row): ?>
                  <tr>
                    <td><strong><?php echo $row['produit']; ?></strong></td>
                    <td class="text-center">
                      <span class="label label-primary">
                        <?php echo $row['qty_achetee']; ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="label label-warning">
                        <?php echo $row['qty_vendue']; ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="label label-success">
                        <?php echo $row['stock_actuel']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>



  </section>
</div>