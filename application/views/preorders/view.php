<div class="content-wrapper">
    <section class="content-header">
        <h1>Détail Pré-commande #<?php echo $preorder['id']; ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Informations Client</h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Client:</strong> <?php echo $preorder['customer_name']; ?></p>
                        <p><strong>Téléphone:</strong> <?php echo $preorder['customer_phone']; ?></p>
                        <p><strong>Adresse:</strong> <?php echo $preorder['customer_address']; ?></p>
                        <p><strong>Total:</strong> <?php echo number_format($preorder['total_amount'], 2); ?> TND</p>
                        <p><strong>Notes:</strong> <?php echo $preorder['notes']; ?></p>
                        <span class="label label-<?php echo $preorder['status'] == 'pending' ? 'warning' : 'success'; ?>">
                            <?php echo ucfirst($preorder['status']); ?>
                        </span>
                        </p>
                        <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($preorder['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Articles</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Qté</th>
                                    <th>Prix Unitaire</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo $item['product_name']; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['unit_price'], 2); ?> TND</td>
                                        <td><?php echo number_format($item['subtotal'], 2); ?> TND</td>
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