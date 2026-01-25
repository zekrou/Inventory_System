<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <h1>
      <i class="fa fa-line-chart"></i> Rapports Détaillés
      <small>Analyse Complète des Ventes et Profits</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="<?php echo base_url('reports') ?>">Rapports</a></li>
      <li class="active">Détaillés</li>
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
        <?php echo form_open('reports/detailed', array('class' => 'form-horizontal')) ?>
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
              <label class="col-sm-4 control-label">Mois (optionnel)</label>
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

    <!-- STATISTIQUES GLOBALES -->
    <?php if ($global_stats): ?>
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Chiffre d'Affaires</span>
              <span class="info-box-number"><?php echo number_format($global_stats['chiffre_affaires'], 2) ?> <?php echo $company_currency ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Profit Net</span>
              <span class="info-box-number"><?php echo number_format($global_stats['profit_net'], 2) ?> <?php echo $company_currency ?></span>
              <span class="progress-description">Marge: <?php echo $global_stats['marge_beneficiaire'] ?>%</span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Crédit/Dû</span>
              <span class="info-box-number"><?php echo number_format($global_stats['credit_total'], 2) ?> <?php echo $company_currency ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Commandes</span>
              <span class="info-box-number"><?php echo $global_stats['total_commandes'] ?></span>
              <span class="progress-description"><?php echo $global_stats['quantite_totale'] ?> unités vendues</span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- Losses Stats -->
    <div class="row">
      <div class="col-md-12">
        <h3 style="margin-top: 30px; border-bottom: 2px solid #e74c3c; padding-bottom: 10px;">
          <i class="fa fa-exclamation-triangle text-danger"></i> Pertes sur Ventes
        </h3>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-minus-circle"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Pertes</span>
            <span class="info-box-number">
              <?= number_format($losses_summary['total_loss'] ?? 0, 2) ?> DZD
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="info-box bg-orange">
          <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Commandes avec Perte</span>
            <span class="info-box-number">
              <?= $losses_summary['nb_orders_with_loss'] ?? 0 ?>
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pertes Réelles</span>
            <span class="info-box-number">
              <?= number_format($losses_summary['real_losses'] ?? 0, 2) ?> DZD
            </span>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="info-box bg-purple">
          <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pertes de Marge</span>
            <span class="info-box-number">
              <?= number_format($losses_summary['margin_losses'] ?? 0, 2) ?> DZD
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Products with Losses -->
    <?php if (!empty($top_loss_products)): ?>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="fa fa-warning"></i> Top 10 Produits Vendus à Perte
              </h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th class="text-center">Commandes</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Perte Totale</th>
                    <th class="text-right">Perte/Unité</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $rank = 1;
                  foreach ($top_loss_products as $prod): ?>
                    <tr>
                      <td><?= $rank++ ?></td>
                      <td>
                        <strong><?= $prod['name'] ?></strong>
                        <br><small class="text-muted"><?= $prod['sku'] ?></small>
                      </td>
                      <td class="text-center"><?= $prod['nb_orders'] ?></td>
                      <td class="text-center"><?= $prod['qty_sold_at_loss'] ?></td>
                      <td class="text-right text-danger">
                        <strong>-<?= number_format($prod['total_loss'], 2) ?> DZD</strong>
                      </td>
                      <td class="text-right">
                        -<?= number_format($prod['avg_loss_per_unit'], 2) ?> DZD
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- RAPPORT 1: ANALYSE DE PROFIT -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Rapport 1: Analyse de Profit (Ventes vs Coûts)</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Période</th>
                <th>Total Ventes</th>
                <th>Coûts d'Achat</th>
                <th>Profit Net</th>
                <th>Marge %</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($profit_report): ?>
                <?php
                $total_ventes = 0;
                $total_couts = 0;
                $total_profit = 0;
                foreach ($profit_report as $row):
                  $periode = isset($row['date']) ? $row['date'] : $selected_year . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
                  $ventes = floatval($row['total_ventes']);
                  $couts = floatval($row['total_couts']);
                  $profit = floatval($row['profit_net']);
                  $marge = $ventes > 0 ? round(($profit / $ventes) * 100, 2) : 0;

                  $total_ventes += $ventes;
                  $total_couts += $couts;
                  $total_profit += $profit;
                ?>
                  <tr>
                    <td><strong><?php echo $periode ?></strong></td>
                    <td><?php echo number_format($ventes, 2) ?> <?php echo $company_currency ?></td>
                    <td><?php echo number_format($couts, 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-success"><strong><?php echo number_format($profit, 2) ?> <?php echo $company_currency ?></strong></td>
                    <td><span class="badge bg-green"><?php echo $marge ?>%</span></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="bg-gray">
                  <td><strong>TOTAL</strong></td>
                  <td><strong><?php echo number_format($total_ventes, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td><strong><?php echo number_format($total_couts, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td class="text-success"><strong><?php echo number_format($total_profit, 2) ?> <?php echo $company_currency ?></strong></td>
                  <td><strong><?php echo $total_ventes > 0 ? round(($total_profit / $total_ventes) * 100, 2) : 0 ?>%</strong></td>
                </tr>
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

    <!-- RAPPORT 2: TOP PRODUITS -->
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-trophy"></i> Rapport 2: Top 10 Produits les Plus Rentables</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Qté Vendue</th>
                <th>Ventes</th>
                <th>Coûts</th>
                <th>Profit</th>
                <th>Marge %</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($top_products): ?>
                <?php $rank = 1;
                foreach ($top_products as $product): ?>
                  <tr>
                    <td><strong><?php echo $rank++ ?></strong></td>
                    <td><?php echo $product['name'] ?></td>
                    <td><?php echo $product['quantite_vendue'] ?></td>
                    <td><?php echo number_format($product['total_ventes'], 2) ?> <?php echo $company_currency ?></td>
                    <td><?php echo number_format($product['total_couts'], 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-success"><strong><?php echo number_format($product['profit_total'], 2) ?> <?php echo $company_currency ?></strong></td>
                    <td><span class="badge <?php echo $product['marge_moyenne_pct'] > 30 ? 'bg-green' : 'bg-yellow' ?>"><?php echo $product['marge_moyenne_pct'] ?>%</span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">Aucune donnée disponible</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 3: VENTES PAR TYPE DE CLIENT -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-users"></i> Rapport 3: Ventes par Type de Client</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Type Client</th>
                <th>Nb Commandes</th>
                <th>Quantité</th>
                <th>Ventes</th>
                <th>Profit</th>
                <th>% du Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($sales_by_type): ?>
                <?php
                $total_ventes_type = array_sum(array_column($sales_by_type, 'total_ventes'));
                foreach ($sales_by_type as $type):
                  $pct = $total_ventes_type > 0 ? round(($type['total_ventes'] / $total_ventes_type) * 100, 2) : 0;
                ?>
                  <tr>
                    <td><strong><?php echo $type['type_client'] ?></strong></td>
                    <td><?php echo $type['nb_commandes'] ?></td>
                    <td><?php echo $type['quantite_totale'] ?></td>
                    <td><?php echo number_format($type['total_ventes'], 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-success"><?php echo number_format($type['profit_total'], 2) ?> <?php echo $company_currency ?></td>
                    <td>
                      <div class="progress">
                        <div class="progress-bar progress-bar-primary" style="width: <?php echo $pct ?>%">
                          <?php echo $pct ?>%
                        </div>
                      </div>
                    </td>
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

    <!-- RAPPORT 4: ÉTAT DES PAIEMENTS -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-credit-card"></i> Rapport 4: État des Paiements</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Période</th>
                <th>Nb Commandes</th>
                <th>Montant Total</th>
                <th>Payé</th>
                <th>Crédit/Dû</th>
                <th>Taux Paiement</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($payment_status): ?>
                <?php
                $total_montant = 0;
                $total_paye = 0;
                $total_credit = 0;
                foreach ($payment_status as $row):
                  $periode = isset($row['date']) ? $row['date'] : $selected_year . '-' . str_pad($row['mois'], 2, '0', STR_PAD_LEFT);
                  $montant = floatval($row['montant_total']);
                  $paye = floatval($row['montant_paye']);
                  $credit = floatval($row['montant_credit']);
                  $taux = floatval($row['taux_paiement']);

                  $total_montant += $montant;
                  $total_paye += $paye;
                  $total_credit += $credit;
                ?>
                  <tr>
                    <td><?php echo $periode ?></td>
                    <td><?php echo $row['nb_commandes'] ?></td>
                    <td><?php echo number_format($montant, 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-success"><?php echo number_format($paye, 2) ?> <?php echo $company_currency ?></td>
                    <td class="text-danger"><?php echo number_format($credit, 2) ?> <?php echo $company_currency ?></td>
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
                  <td class="text-danger"><strong><?php echo number_format($total_credit, 2) ?> <?php echo $company_currency ?></strong></td>
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

  </section>
</div>

<style>
  .info-box-number {
    font-size: 22px !important;
  }

  .progress {
    margin-bottom: 0;
  }
</style>