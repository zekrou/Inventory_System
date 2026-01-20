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
    <?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php echo $this->session->flashdata('success'); ?>
    </div>
    <?php endif; ?>

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
                <td><strong><?php echo $order['order_number']; ?></strong></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                <td><?php echo number_format($order['total_amount'], 2); ?> DZD</td>
                <td>
                  <span class="label label-<?php 
                    echo $order['status'] == 'pending' ? 'warning' : 
                         ($order['status'] == 'approved' ? 'success' : 'danger'); 
                  ?>">
                    <?php echo ucfirst($order['status']); ?>
                  </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                <td>
                  <a href="<?php echo base_url('preorders/view/'.$order['id']); ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-eye"></i> Voir
                  </a>
                  <?php if(in_array('deletePreOrder', $user_permission)): ?>
                  <a href="<?php echo base_url('preorders/delete/'.$order['id']); ?>" 
                     class="btn btn-sm btn-danger" 
                     onclick="return confirm('Supprimer cette pre-order ?');">
                    <i class="fa fa-trash"></i>
                  </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">Aucune pre-order trouvée</td>
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
    "order": [[5, "desc"]]
  });
});
</script>
