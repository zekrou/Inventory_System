<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-cube"></i> Product Details
            <small><?php echo $product['name']; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('products') ?>">Products</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">

            <!-- Left Column: Product Info -->
            <div class="col-md-6">

                <!-- Basic Information -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-info-circle"></i> Basic Information</h3>
                        <div class="box-tools pull-right">
                            <?php if (isset($user_permission['updateProduct'])): ?>
                                <a href="<?php echo base_url('products/update/' . $product['id']); ?>" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i> Edit Product
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="box-body">

                        <!-- Product Image -->
                        <div class="text-center" style="margin-bottom: 20px;">
                            <?php
                            $img_url = !empty($product['image']) ? base_url($product['image']) : base_url('assets/images/no-image.png');
                            ?>
                            <img src="<?php echo $img_url; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                        </div>

                        <table class="table table-bordered">
                            <tr>
                                <th style="width:40%">Product Name</th>
                                <td><strong><?php echo $product['name']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>SKU</th>
                                <td><span class="label label-default"><?php echo $product['sku']; ?></span></td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td><?php echo $product['category_names']; ?></td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td><?php echo $product['brand_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Stock Location</th>
                                <td><?php echo isset($product['stock_name']) ? $product['stock_name'] : '-'; ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if ($product['availability'] == 1): ?>
                                        <span class="label label-success"><i class="fa fa-check"></i> Active</span>
                                    <?php else: ?>
                                        <span class="label label-danger"><i class="fa fa-times"></i> Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td><?php echo !empty($product['description']) ? $product['description'] : '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Pricing Information with Cost Analysis -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> Pricing & Margins</h3>
                    </div>
                    <div class="box-body">

                        <!-- Purchase Cost Info -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-sm-6">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Average Cost</span>
                                        <span class="info-box-number"><?php echo number_format(isset($product['average_cost']) ? $product['average_cost'] : 0, 2); ?> DZD</span>
                                        <small>Calculated from purchases</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-history"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Last Purchase Price</span>
                                        <span class="info-box-number"><?php echo number_format(isset($product['last_purchase_price']) ? $product['last_purchase_price'] : 0, 2); ?> DZD</span>
                                        <?php if (!empty($product['purchase_price_updated_at'])): ?>
                                            <small><?php echo date('d/m/Y', strtotime($product['purchase_price_updated_at'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Price Type</th>
                                    <th class="text-right">Price (DZD)</th>
                                    <th class="text-right">Margin</th>
                                    <th class="text-right">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Use average_cost as the base for margin calculation
                                $cost = (isset($product['average_cost']) && $product['average_cost'] > 0) ? $product['average_cost'] : (isset($product['price_default']) ? $product['price_default'] : 0);

                                // ✅ CORRECTION: Utiliser les noms avec underscores
                                $prices = array(
                                    'Cost Price' => array(
                                        'price' => $cost,
                                        'class' => 'default'
                                    ),
                                    'Super Wholesale' => array(
                                        'price' => isset($product['price_super_wholesale']) ? $product['price_super_wholesale'] : 0,
                                        'class' => 'info'
                                    ),
                                    'Wholesale' => array(
                                        'price' => isset($product['price_wholesale']) ? $product['price_wholesale'] : 0,
                                        'class' => 'primary'
                                    ),
                                    'Retail' => array(
                                        'price' => isset($product['price_retail']) ? $product['price_retail'] : 0,
                                        'class' => 'success'
                                    ),
                                );

                                foreach ($prices as $type => $info):
                                    $price = $info['price'];
                                    $margin = $price - $cost;
                                    $margin_percent = ($cost > 0) ? round(($margin / $cost) * 100, 2) : 0;
                                    $color_class = ($margin >= 0) ? 'text-green' : 'text-red';
                                ?>
                                    <tr>
                                        <td><span class="label label-<?php echo $info['class']; ?>"><?php echo $type; ?></span></td>
                                        <td class="text-right"><strong><?php echo number_format($price, 2); ?> DZD</strong></td>
                                        <td class="text-right <?php echo $color_class; ?>">
                                            <?php echo $margin >= 0 ? '+' : ''; ?><?php echo number_format($margin, 2); ?> DZD
                                        </td>
                                        <td class="text-right <?php echo $color_class; ?>">
                                            <?php echo $margin_percent; ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (isset($product['last_purchase_price']) && $product['last_purchase_price'] > 0): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Last Purchase:</strong> <?php echo number_format($product['last_purchase_price'], 2); ?> DZD
                                <?php if (!empty($product['purchase_price_updated_at'])): ?>
                                    <br><small>Updated on <?php echo date('d/m/Y H:i', strtotime($product['purchase_price_updated_at'])); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>



            </div>

            <!-- Right Column: Stock & History -->
            <div class="col-md-6">

                <!-- Stock Information -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-cubes"></i> Stock Information</h3>
                    </div>
                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Current Stock</span>
                                        <span class="info-box-number"><?php echo $product['qty']; ?> units</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-warning"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Alert Threshold</span>
                                        <span class="info-box-number"><?php echo $product['alert_threshold']; ?> units</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <?php if ($product['qty'] == 0): ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i> <strong>Out of Stock!</strong> This product needs to be restocked immediately.
                            </div>
                        <?php elseif ($product['qty'] <= $product['alert_threshold']): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-warning"></i> <strong>Low Stock Alert!</strong> Only <?php echo $product['qty']; ?> units remaining.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check"></i> <strong>In Stock</strong> - <?php echo $product['qty']; ?> units available.
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- ✅ NOUVEAU: Purchase History -->
                <?php if (!empty($purchase_history)): ?>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Purchase History</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-condensed table-hover">
                                    <thead>
                                        <tr style="background: #f4f4f4;">
                                            <th>Date</th>
                                            <th>Purchase #</th>
                                            <th>Supplier</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Unit Price</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $previous_price = null;
                                        foreach ($purchase_history as $purchase):
                                            // Detect price change
                                            $price_change = '';
                                            if ($previous_price !== null && $purchase['unit_price'] != $previous_price) {
                                                $diff = $purchase['unit_price'] - $previous_price;
                                                if ($diff > 0) {
                                                    $price_change = '<i class="fa fa-arrow-up text-red"></i> +' . number_format($diff, 2);
                                                } else {
                                                    $price_change = '<i class="fa fa-arrow-down text-green"></i> ' . number_format($diff, 2);
                                                }
                                            }
                                            $previous_price = $purchase['unit_price'];
                                        ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($purchase['purchase_date'])); ?></td>
                                                <td>
                                                    <span class="label label-default"><?php echo $purchase['purchase_no']; ?></span>
                                                </td>
                                                <td><?php echo $purchase['supplier_name']; ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-blue"><?php echo $purchase['quantity']; ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <strong><?php echo number_format($purchase['unit_price'], 2); ?> DZD</strong>
                                                    <?php if ($price_change): ?>
                                                        <br><small><?php echo $price_change; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php echo number_format($purchase['total_price'], 2); ?> DZD
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Product Statistics -->
                <?php if (!empty($product_stats)): ?>
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-bar-chart"></i> Sales Statistics</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Sold</span>
                                            <span class="info-box-number"><?php echo $product_stats['total_sold']; ?> units</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Revenue</span>
                                            <span class="info-box-number"><?php echo number_format($product_stats['total_revenue'], 0); ?> DZD</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <a href="<?php echo base_url('products'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                    <?php if (isset($user_permission['updateProduct'])): ?>
                        <a href="<?php echo base_url('products/update/' . $product['id']); ?>" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit Product
                        </a>
                    <?php endif; ?>
                    <?php if (isset($user_permission['createPurchase'])): ?>
                        <a href="<?php echo base_url('purchases/create'); ?>" class="btn btn-success">
                            <i class="fa fa-shopping-cart"></i> Purchase Stock
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>
</div>

<style>
    .info-box {
        min-height: 90px;
        margin-bottom: 15px;
    }

    .info-box-number {
        font-size: 24px !important;
    }
</style>