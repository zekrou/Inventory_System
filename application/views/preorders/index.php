<div class="content-wrapper">
    <section class="content-header">
        <h1>Pré-commandes Mobile <small>Liste complète</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Toutes les pré-commandes</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Téléphone</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($preorders as $preorder): ?>
                                <tr>
                                    <td><?php echo $preorder['id']; ?></td>
                                    <td><?php echo $preorder['customer_name']; ?></td>
                                    <td><?php echo $preorder['phone']; ?></td>
                                    <td><?php echo number_format($preorder['total_amount'], 2); ?> TND</td>
                                    <td>
                                        <span class="label label-<?php echo $preorder['status']=='pending' ? 'warning' : 'success'; ?>">
                                            <?php echo ucfirst($preorder['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($preorder['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo site_url('preorders/view/'.$preorder['id']); ?>" class="btn btn-xs btn-info">Voir</a>
                                        <a href="<?php echo site_url('preorders/update/'.$preorder['id']); ?>" class="btn btn-xs btn-warning">Modifier</a>
                                        <?php if(isset($this->permission['deletePreorders'])): ?>
                                        <a href="<?php echo site_url('preorders/delete/'.$preorder['id']); ?>" 
                                           class="btn btn-xs btn-danger" 
                                           onclick="return confirm('Confirmer suppression?')">Supprimer</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
