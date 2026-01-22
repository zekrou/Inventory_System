<div class="content-wrapper">
    <section class="content-header">
        <h1>Modifier Pré-commande #<?php echo $preorder['id']; ?></h1>
    </section>

    <section class="content">
        <form action="<?php echo site_url('preorders/update/'.$preorder['id']); ?>" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header">
                            <h3 class="box-title">Changer Statut</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Statut</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?php echo $preorder['status']=='pending' ? 'selected' : ''; ?>>En attente</option>
                                    <option value="confirmed" <?php echo $preorder['status']=='confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                                    <option value="shipped" <?php echo $preorder['status']=='shipped' ? 'selected' : ''; ?>>Expédiée</option>
                                    <option value="delivered" <?php echo $preorder['status']=='delivered' ? 'selected' : ''; ?>>Livrée</option>
                                    <option value="cancelled" <?php echo $preorder['status']=='cancelled' ? 'selected' : ''; ?>>Annulée</option>
                                </select>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning">Mettre à jour</button>
                            <a href="<?php echo site_url('preorders'); ?>" class="btn btn-default">Annuler</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
