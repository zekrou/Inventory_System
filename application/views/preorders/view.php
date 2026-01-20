<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pre-Order Details
      <small><?php echo htmlspecialchars($preorder['order_number']); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('preorders'); ?>">Pre-Orders</a></li>
      <li class="active">Détails</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      
      <!-- Order Info -->
      <div class="col-md-4">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Informations Commande</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <tr>
                <th style="width: 40%">N° Commande:</th>
                <td><strong><?php echo htmlspecialchars($preorder['order_number']); ?></strong></td>
              </tr>
              <tr>
                <th>Statut:</th>
                <td>
                  <span class="label label-<?php 
                    echo $preorder['status'] == 'pending' ? 'warning' : 
                         ($preorder['status'] == 'approved' ? 'success' : 
                         ($preorder['status'] == 'completed' ? 'info' : 'danger')); 
                  ?>">
                    <?php 
                      $status_labels = [
                        'pending' => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Rejetée',
                        'completed' => 'Complétée'
                      ];
                      echo $status_labels[$preorder['status']] ?? ucfirst($preorder['status']);
                    ?>
                  </span>
                </td>
              </tr>
              <tr>
                <th>Date création:</th>
                <td><?php echo date('d/m/Y H:i', strtotime($preorder['created_at'])); ?></td>
              </tr>
              <tr>
                <th>Commercial:</th>
                <td><?php echo !empty($preorder['username']) ? htmlspecialchars($preorder['firstname'] . ' ' . $preorder['lastname']) : '-'; ?></td>
              </tr>
              <tr>
                <th>Total:</th>
                <td><strong style="font-size: 18px;"><?php echo number_format($preorder['total_amount'], 2); ?> DZD</strong></td>
              </tr>
            </table>
            
            <?php if(in_array('updatePreOrder', $user_permission)): ?>
            <div class="form-group" style="margin-top: 20px;">
              <label>Changer le statut:</label>
              <select id="statusSelect" class="form-control">
                <option value="pending" <?php echo $preorder['status'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                <option value="approved" <?php echo $preorder['status'] == 'approved' ? 'selected' : ''; ?>>Approuvée</option>
                <option value="completed" <?php echo $preorder['status'] == 'completed' ? 'selected' : ''; ?>>Complétée</option>
                <option value="rejected" <?php echo $preorder['status'] == 'rejected' ? 'selected' : ''; ?>>Rejetée</option>
              </select>
              <button onclick="updateStatus()" class="btn btn-primary btn-block" style="margin-top: 10px;">
                <i class="fa fa-save"></i> Mettre à jour
              </button>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Customer Info -->
      <div class="col-md-4">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Informations Client</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <tr>
                <th style="width: 40%">Nom:</th>
                <td><?php echo htmlspecialchars($preorder['customer_name']); ?></td>
              </tr>
              <tr>
                <th>Téléphone:</th>
                <td><a href="tel:<?php echo htmlspecialchars($preorder['customer_phone']); ?>"><?php echo htmlspecialchars($preorder['customer_phone']); ?></a></td>
              </tr>
              <tr>
                <th>Adresse:</th>
                <td><?php echo !empty($preorder['customer_address']) ? nl2br(htmlspecialchars($preorder['customer_address'])) : '-'; ?></td>
              </tr>
            </table>
            
            <?php if(!empty($preorder['notes'])): ?>
            <div class="alert alert-info" style="margin-top: 15px;">
              <strong><i class="fa fa-info-circle"></i> Notes:</strong><br>
              <?php echo nl2br(htmlspecialchars($preorder['notes'])); ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Actions -->
      <div class="col-md-4">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Actions</h3>
          </div>
          <div class="box-body">
            <a href="<?php echo base_url('preorders'); ?>" class="btn btn-default btn-block">
              <i class="fa fa-arrow-left"></i> Retour à la liste
            </a>
            
            <a href="javascript:window.print();" class="btn btn-info btn-block">
              <i class="fa fa-print"></i> Imprimer
            </a>
            
            <?php if(in_array('deletePreOrder', $user_permission)): ?>
            <a href="<?php echo base_url('preorders/delete/'.$preorder['id']); ?>" 
               class="btn btn-danger btn-block" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette pre-order ?');">
              <i class="fa fa-trash"></i> Supprimer
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Order Items -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Articles commandés</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Produit</th>
                  <th>SKU</th>
                  <th style="width: 100px;">Quantité</th>
                  <th style="width: 120px;">Prix unitaire</th>
                  <th style="width: 120px;">Sous-total</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($items)): ?>
                  <?php $num = 1; foreach($items as $item): ?>
                  <tr>
                    <td><?php echo $num++; ?></td>
                    <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($item['sku'] ?? '-'); ?></td>
                    <td class="text-center"><span class="badge bg-blue"><?php echo $item['qty']; ?></span></td>
                    <td class="text-right"><?php echo number_format($item['price'], 2); ?> DZD</td>
                    <td class="text-right"><strong><?php echo number_format($item['subtotal'], 2); ?> DZD</strong></td>
                  </tr>
                  <?php endforeach; ?>
                  
                  <tr style="background: #f9f9f9;">
                    <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong style="font-size: 16px; color: #00a65a;"><?php echo number_format($preorder['total_amount'], 2); ?> DZD</strong></td>
                  </tr>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center">Aucun article</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function updateStatus() {
  var status = $('#statusSelect').val();
  var preorderId = <?php echo $preorder['id']; ?>;
  
  $.ajax({
    url: '<?php echo base_url('preorders/update_status/'); ?>' + preorderId,
    method: 'POST',
    data: { status: status },
    dataType: 'json',
    success: function(response) {
      if(response.success) {
        alert('Statut mis à jour avec succès !');
        location.reload();
      } else {
        alert('Erreur: ' + response.message);
      }
    },
    error: function() {
      alert('Erreur de connexion');
    }
  });
}
</script>
