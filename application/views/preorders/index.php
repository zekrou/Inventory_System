<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-mobile"></i> Commandes Mobile
            <small>Gestion des pré-commandes</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Accueil</a></li>
            <li class="active">Commandes Mobile</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <!-- Messages -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check"></i> <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-warning"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Boxes -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo isset($stats['total_orders']) ? $stats['total_orders'] : 0; ?></h3>
                        <p>Total Commandes</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <a href="<?php echo base_url('preorders'); ?>" class="small-box-footer">
                        Voir tout <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo isset($stats['pending_count']) ? $stats['pending_count'] : 0; ?></h3>
                        <p>En attente</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <a href="<?php echo base_url('preorders?status=pending'); ?>" class="small-box-footer">
                        Voir <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo isset($stats['confirmed_count']) ? $stats['confirmed_count'] : 0; ?></h3>
                        <p>Confirmées</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <a href="<?php echo base_url('preorders?status=confirmed'); ?>" class="small-box-footer">
                        Voir <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo isset($stats['cancelled_count']) ? $stats['cancelled_count'] : 0; ?></h3>
                        <p>Annulées</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <a href="<?php echo base_url('preorders?status=cancelled'); ?>" class="small-box-footer">
                        Voir <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="btn-group" style="margin-bottom: 15px;">
            <a href="<?php echo base_url('preorders'); ?>" 
               class="btn btn-default <?php echo empty($status_filter) ? 'active' : ''; ?>">
                <i class="fa fa-list"></i> Toutes
            </a>
            <a href="<?php echo base_url('preorders?status=pending'); ?>" 
               class="btn btn-warning <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                <i class="fa fa-clock-o"></i> En attente
            </a>
            <a href="<?php echo base_url('preorders?status=confirmed'); ?>" 
               class="btn btn-success <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">
                <i class="fa fa-check"></i> Confirmées
            </a>
            <a href="<?php echo base_url('preorders?status=cancelled'); ?>" 
               class="btn btn-danger <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
                <i class="fa fa-times"></i> Annulées
            </a>
        </div>

        <!-- Orders Table -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des commandes</h3>
            </div>
            <div class="box-body">
                
                <?php if(empty($preorders)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-3x"></i>
                        <h4>Aucune commande</h4>
                        <p>Il n'y a aucune commande pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="preordersTable">
                            <thead>
                                <tr>
                                    <th width="8%">N° Commande</th>
                                    <th width="15%">Client</th>
                                    <th width="10%">Téléphone</th>
                                    <th width="10%">Commercial</th>
                                    <th width="8%">Articles</th>
                                    <th width="12%">Montant</th>
                                    <th width="12%">Date</th>
                                    <th width="10%">Statut</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($preorders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo $order['order_number']; ?></strong></td>
                                        <td><?php echo $order['customer_name']; ?></td>
                                        <td><?php echo $order['customer_phone']; ?></td>
                                        <td>
                                            <?php 
                                            if(!empty($order['firstname']) || !empty($order['lastname'])) {
                                                echo $order['firstname'] . ' ' . $order['lastname'];
                                            } else {
                                                echo $order['username'];
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $items = $this->model_preorders->getPreOrderItems($order['id']);
                                            echo count($items);
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <strong><?php echo number_format($order['total_amount'], 2); ?> DZD</strong>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'default';
                                            $status_label = 'Inconnu';
                                            
                                            switch($order['status']) {
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
                                            <span class="label label-<?php echo $status_class; ?>">
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- View Button -->
                                            <a href="<?php echo base_url('preorders/view/'.$order['id']); ?>" 
                                               class="btn btn-info btn-sm" 
                                               title="Voir détails">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            
                                            <!-- Update Status Buttons -->
                                            <?php if(in_array('updatePreorders', $user_permission) && $order['status'] == 'pending'): ?>
                                                <button class="btn btn-success btn-sm btn-confirm" 
                                                        data-id="<?php echo $order['id']; ?>"
                                                        title="Confirmer">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm btn-cancel" 
                                                        data-id="<?php echo $order['id']; ?>"
                                                        title="Annuler">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Delete Button -->
                                            <?php if(in_array('deletePreorders', $user_permission)): ?>
                                                <button class="btn btn-danger btn-sm btn-delete" 
                                                        data-id="<?php echo $order['id']; ?>"
                                                        data-number="<?php echo $order['order_number']; ?>"
                                                        title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
    // ✅ Initialize DataTable si plus de 10 lignes
    <?php if(count($preorders) > 10): ?>
    $('#preordersTable').DataTable({
        "order": [[6, "desc"]], // Trier par date décroissante
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        }
    });
    <?php endif; ?>

    // Confirm order
    $('.btn-confirm').click(function() {
        const id = $(this).data('id');
        if(confirm('Confirmer cette commande ?')) {
            updateStatus(id, 'confirmed');
        }
    });

    // Cancel order
    $('.btn-cancel').click(function() {
        const id = $(this).data('id');
        if(confirm('Annuler cette commande ?')) {
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
                if(response.success) {
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

<style>
.btn-group .active {
    background-color: #3c8dbc !important;
    color: white !important;
}
</style>