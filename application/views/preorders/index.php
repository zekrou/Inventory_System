<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pre-Orders Mobile
      <small>Commandes depuis l'application mobile</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Pre-Orders</li>
    </ol>
  </section>

  <section class="content">
    
    <!-- Flash messages -->
    <?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $this->session->flashdata('success'); ?>
    </div>
    <?php endif; ?>
    
    <?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $this->session->flashdata('error'); ?>
    </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total Pre-Orders</p>
          </div>
          <div class="icon">
            <i class="fa fa-shopping-cart"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $stats['pending']; ?></h3>
            <p>En attente</p>
          </div>
          <div class="icon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $stats['approved']; ?></h3>
            <p>Approuvées</p>
          </div>
          <div class="icon">
            <i class="fa fa-check"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue">
          <div class="inner">
            <h3><?php echo number_format($stats['total_amount'], 2); ?> DZD</h3>
            <p>Total montant</p>
          </div>
          <div class="icon">
            <i class="fa fa-money"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Pre-orders table -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Liste des Pre-Orders</h3>
      </div>
      <div class="box-body">
        <table id="preordersTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>N° Commande</th>
              <th>Client</th>
              <th>Téléphone</th>
              <th>Commercial</th>
              <th>Total</th>
              <th>Statut</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($preorders)): ?>
              <?php foreach($preorders as $order): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                <td><?php echo !empty($order['username']) ? htmlspecialchars($order['username']) : '-'; ?></td>
                <td><strong><?php echo number_format($order['total_amount'], 2); ?> DZD</strong></td>
                <td>
                  <span class="label label-<?php 
                    echo $order['status'] == 'pending' ? 'warning' : 
                         ($order['status'] == 'approved' ? 'success' : 
                         ($order['status'] == 'completed' ? 'info' : 'danger')); 
                  ?>">
                    <?php 
                      $status_labels = [
                        'pending' => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Rejetée',
                        'completed' => 'Complétée'
                      ];
                      echo $status_labels[$order['status']] ?? ucfirst($order['status']);
                    ?>
                  </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                <td>
                  <a href="<?php echo base_url('preorders/view/'.$order['id']); ?>" class="btn btn-sm btn-primary" title="Voir détails">
                    <i class="fa fa-eye"></i>
                  </a>
                  
                  <?php if(in_array('deletePreOrder', $user_permission)): ?>
                  <a href="<?php echo base_url('preorders/delete/'.$order['id']); ?>" 
                     class="btn btn-sm btn-danger" 
                     onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette pre-order ?');"
                     title="Supprimer">
                    <i class="fa fa-trash"></i>
                  </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center">Aucune pre-order trouvée</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
  $('#preordersTable').DataTable({
    "order": [[6, "desc"]],
    "pageLength": 25,
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
    }
  });
});
</script>
