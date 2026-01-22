<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-eye"></i> Détails Commande
            <small><?php echo $preorder['order_number']; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Accueil</a></li>
            <li><a href="<?php echo base_url('preorders') ?>">Commandes Mobile</a></li>
            <li class="active">Détails</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <!-- Order Info -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Informations Commande</h3>
                        
                        <div class="box-tools pull-right">
                            <?php
                            $status_class = 'default';
                            $status_label = 'Inconnu';
                            
                            switch($preorder['status']) {
                                case 'pending':
                                    $status_class = 'warning';
                                    $status_label = 'En attente';
                                    break;
                                case 'confirmed':
                                    $status_class = 'success';
                                    $status_label = 'Confirmée';
                                    break;
                                case 'cancelled':
                                    $status_class = 'danger';
                                    $status_label = 'Annulée';
                                    break;
                            }
                            ?>
                            <span class="label label-<?php echo $status_class; ?>" style="font-size: 14px;">
                                <?php echo $status_label; ?>
                            </span>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">N° Commande:</th>
                                <td><strong><?php echo $preorder['order_number']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Client:</th>
                                <td><?php echo $preorder['customer_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Téléphone:</th>
                                <td><?php echo $preorder['customer_phone']; ?></td>
                            </tr>
                            <tr>
                                <th>Adresse:</th>
                                <td><?php echo $preorder['customer_address'] ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Commercial:</th>
                                <td>
                                    <?php 
                                    if (!empty($preorder['firstname']) || !empty($preorder['lastname'])) {
                                        echo $preorder['firstname'] . ' ' . $preorder['lastname'];
                                    } else {
                                        echo $preorder['username'];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Date création:</th>
                                <td><?php echo date('d/m/Y H:i', strtotime($preorder['created_at'])); ?></td>
                            </tr>
                            <?php if(!empty($preorder['notes'])): ?>
                            <tr>
                                <th>Notes:</th>
                                <td><?php echo nl2br($preorder['notes']); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Articles commandés</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Produit</th>
                                        <th width="15%">SKU</th>
                                        <th width="10%">Quantité</th>
                                        <th width="15%">Prix unitaire</th>
                                        <th width="15%">Sous-total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($items)): ?>
                                        <?php foreach ($items as $index => $item): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <strong><?php echo $item['product_name']; ?></strong>
                                                </td>
                                                <td><?php echo $item['sku'] ?: '-'; ?></td>
                                                <td class="text-center"><?php echo $item['qty']; ?></td>
                                                <td class="text-right">
                                                    <?php echo number_format($item['price'], 2); ?> DZD
                                                </td>
                                                <td class="text-right">
                                                    <strong><?php echo number_format($item['subtotal'], 2); ?> DZD</strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-light-blue">
                                            <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                            <td class="text-right">
                                                <strong style="font-size: 16px;">
                                                    <?php echo number_format($preorder['total_amount'], 2); ?> DZD
                                                </strong>
                                            </td>
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

            <!-- Actions Sidebar -->
            <div class="col-md-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Actions</h3>
                    </div>

                    <div class="box-body">
                        <a href="<?php echo base_url('preorders'); ?>" class="btn btn-default btn-block">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                        
                        <?php if(in_array('updatePreorders', $user_permission) && $preorder['status'] == 'pending'): ?>
                            <hr>
                            <button class="btn btn-success btn-block btn-confirm" data-id="<?php echo $preorder['id']; ?>">
                                <i class="fa fa-check"></i> Confirmer la commande
                            </button>
                            <button class="btn btn-danger btn-block btn-cancel" data-id="<?php echo $preorder['id']; ?>">
                                <i class="fa fa-times"></i> Annuler la commande
                            </button>
                        <?php endif; ?>
                        
                        <?php if(in_array('deletePreorders', $user_permission)): ?>
                            <hr>
                            <button class="btn btn-danger btn-block btn-delete" 
                                    data-id="<?php echo $preorder['id']; ?>" 
                                    data-number="<?php echo $preorder['order_number']; ?>">
                                <i class="fa fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Résumé</h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Articles:</strong> <?php echo count($items); ?></p>
                        <p><strong>Quantité totale:</strong> 
                            <?php 
                            $total_qty = 0;
                            foreach($items as $item) {
                                $total_qty += $item['qty'];
                            }
                            echo $total_qty;
                            ?>
                        </p>
                        <p><strong>Montant total:</strong> 
                            <span class="text-success" style="font-size: 18px;">
                                <?php echo number_format($preorder['total_amount'], 2); ?> DZD
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmer la suppression</h4>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la commande <strong id="deleteOrderNumber"></strong> ?</p>
                <p class="text-danger"><i class="fa fa-warning"></i> Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Confirm order
    $('.btn-confirm').click(function() {
        const id = $(this).data('id');
        if (confirm('Confirmer cette commande ?')) {
            updateStatus(id, 'confirmed');
        }
    });

    // Cancel order
    $('.btn-cancel').click(function() {
        const id = $(this).data('id');
        if (confirm('Annuler cette commande ?')) {
            updateStatus(id, 'cancelled');
        }
    });

    // Delete order
    $('.btn-delete').click(function() {
        const id = $(this).data('id');
        const number = $(this).data('number');
        $('#deleteOrderNumber').text(number);
        $('#deleteForm').attr('action', '<?php echo base_url("preorders/delete/"); ?>' + id);
        $('#deleteModal').modal('show');
    });

    // Update status function
    function updateStatus(id, status) {
        $.ajax({
            url: '<?php echo base_url("preorders/update_status/"); ?>' + id,
            method: 'POST',
            data: { status: status },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
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
});
</script>
