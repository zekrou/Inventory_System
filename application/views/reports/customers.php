<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      <i class="fa fa-users"></i> Rapports Clients
      <small>Analyse Complète des Clients</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="<?php echo base_url('reports') ?>">Rapports</a></li>
      <li class="active">Clients</li>
    </ol>
  </section>

  <section class="content">
    
    <!-- Filtres -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filtres</h3>
      </div>
      <div class="box-body">
        <?php echo form_open('reports/customers', array('class' => 'form-horizontal')) ?>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="col-sm-3 control-label">Année</label>
                <div class="col-sm-9">
                  <select class="form-control" name="select_year">
                    <?php foreach ($report_years as $year): ?>
                      <option value="<?php echo $year ?>" <?php if($selected_year == $year) { echo "selected"; } ?>><?php echo $year ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-search"></i> Filtrer
              </button>
            </div>
          </div>
        <?php echo form_close() ?>
      </div>
    </div>

    <!-- RAPPORT 1: TOP CLIENTS -->
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-trophy"></i> Top 10 Meilleurs Clients</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Nb Commandes</th>
                <th>Total Achats</th>
                <th>Panier Moyen</th>
                <th>Dû</th>
              </tr>
            </thead>
            <tbody>
              <?php if($top_customers): ?>
                <?php $rank = 1; foreach ($top_customers as $customer): ?>
                <tr>
                  <td><strong><?php echo $rank++ ?></strong></td>
                  <td><?php echo $customer['customer_name'] ?></td>
                  <td><?php echo $customer['customer_phone'] ?></td>
                  <td><?php echo $customer['nb_commandes'] ?></td>
                  <td><?php echo number_format($customer['total_achats'], 2) ?> <?php echo $company_currency ?></td>
                  <td><?php echo number_format($customer['panier_moyen'], 2) ?> <?php echo $company_currency ?></td>
                  <td class="<?php echo $customer['total_du'] > 0 ? 'text-danger' : 'text-success' ?>">
                    <?php echo number_format($customer['total_du'], 2) ?> <?php echo $company_currency ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center">Aucune donnée</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 2: CLIENTS PAR TYPE -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Clients par Type</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Type</th>
                <th>Nb Clients</th>
                <th>Nb Commandes</th>
                <th>Total Ventes</th>
              </tr>
            </thead>
            <tbody>
              <?php if($customers_by_type): ?>
                <?php foreach ($customers_by_type as $type): ?>
                <tr>
                  <td><strong><?php echo $type['type_client'] ?></strong></td>
                  <td><?php echo $type['nb_clients'] ?></td>
                  <td><?php echo $type['nb_commandes'] ?></td>
                  <td><?php echo number_format($type['total_ventes'], 2) ?> <?php echo $company_currency ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center">Aucune donnée</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RAPPORT 3: CLIENTS AVEC DETTES -->
    <div class="box box-danger">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Clients avec Dettes</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Factures Impayées</th>
                <th>Total Facturé</th>
                <th>Payé</th>
                <th>Dû</th>
              </tr>
            </thead>
            <tbody>
              <?php if($customer_debt): ?>
                <?php foreach ($customer_debt as $debt): ?>
                <tr>
                  <td><?php echo $debt['customer_name'] ?></td>
                  <td><?php echo $debt['customer_phone'] ?></td>
                  <td><?php echo $debt['nb_factures_impayees'] ?></td>
                  <td><?php echo number_format($debt['total_facture'], 2) ?> <?php echo $company_currency ?></td>
                  <td class="text-success"><?php echo number_format($debt['total_paye'], 2) ?> <?php echo $company_currency ?></td>
                  <td class="text-danger"><strong><?php echo number_format($debt['total_du'], 2) ?> <?php echo $company_currency ?></strong></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-success">Aucun client avec dette! ✅</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </section>
</div>
