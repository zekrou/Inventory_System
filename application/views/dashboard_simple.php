<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Dashboard <small>System Overview</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <!-- ========== KEY PERFORMANCE INDICATORS ========== -->
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header"><i class="fa fa-line-chart"></i> Key Performance Indicators</h3>
            </div>
        </div>

        <div class="row">
            <!-- Sales Today -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo number_format($sales_today, 0); ?> <small>DZD</small></h3>
                        <p>Sales Today</p>
                    </div>
                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                    <div class="small-box-footer"><?php echo $orders_today_count; ?> orders today</div>
                </div>
            </div>

            <!-- Sales This Month -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo number_format($sales_this_month, 0); ?> <small>DZD</small></h3>
                        <p>Sales This Month</p>
                    </div>
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                    <div class="small-box-footer"><?php echo $orders_this_month_count; ?> orders this month</div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo $total_pending_orders; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                    <div class="small-box-footer"><?php echo number_format($pending_orders_amount, 0); ?> DZD</div>
                </div>
            </div>

            <!-- Profit Margin -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo $average_margin; ?>%</h3>
                        <p>Profit Margin</p>
                    </div>
                    <div class="icon"><i class="fa fa-percent"></i></div>
                    <div class="small-box-footer">Average margin</div>
                </div>
            </div>
        </div>

        <!-- ========== INVENTORY STATUS ========== -->
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header"><i class="fa fa-cubes"></i> Inventory Status</h3>
            </div>
        </div>

        <div class="row">
            <!-- Total Stock Value -->
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-teal">
                    <span class="info-box-icon"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Stock Value</span>
                        <span class="info-box-number"><?php echo number_format($total_stock_value, 0); ?> DZD</span>
                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                        <span class="progress-description">View details</span>
                    </div>
                </div>
            </div>

            <!-- Active Products -->
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-light-blue">
                    <span class="info-box-icon"><i class="fa fa-cube"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Products</span>
                        <span class="info-box-number"><?php echo $total_products; ?></span>
                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                        <span class="progress-description">Manage products</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-orange">
                    <span class="info-box-icon"><i class="fa fa-warning"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Low Stock Alerts</span>
                        <span class="info-box-number"><?php echo $low_stock_count; ?></span>
                        <div class="progress"><div class="progress-bar" style="width: 70%"></div></div>
                        <span class="progress-description">Action required</span>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Out of Stock</span>
                        <span class="info-box-number"><?php echo $out_of_stock_count; ?></span>
                        <div class="progress"><div class="progress-bar" style="width: 30%"></div></div>
                        <span class="progress-description">Urgent</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== RECENT ORDERS ========== -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Recent Orders</h3>
                    </div>
                    <div class="box-body">
                        <?php if(empty($recent_orders)): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No recent orders found. Start by creating your first order!
                            </div>
                        <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['bill_no']; ?></td>
                                            <td><?php echo $order['customer_name']; ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($order['date_time'])); ?></td>
                                            <td><?php echo number_format($order['net_amount'], 2); ?> DZD</td>
                                            <td>
                                                <?php if($order['paid_status'] == 1): ?>
                                                    <span class="label label-success">Paid</span>
                                                <?php elseif($order['paid_status'] == 3): ?>
                                                    <span class="label label-warning">Partial</span>
                                                <?php else: ?>
                                                    <span class="label label-danger">Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== STOCK ALERTS ========== -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-warning"></i> Stock Alerts</h3>
                    </div>
                    <div class="box-body">
                        <?php if(empty($stock_alerts)): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> No stock alerts. All products are well stocked!
                            </div>
                        <?php else: ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Current Stock</th>
                                        <th>Threshold</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($stock_alerts as $alert): ?>
                                        <tr>
                                            <td><?php echo $alert['name']; ?></td>
                                            <td><?php echo $alert['sku']; ?></td>
                                            <td><strong><?php echo $alert['qty']; ?></strong></td>
                                            <td><?php echo $alert['low_stock_threshold']; ?></td>
                                            <td>
                                                <?php if($alert['qty'] == 0): ?>
                                                    <span class="label label-danger">Out of Stock</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Low Stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<style>
.page-header {
    border-bottom: 2px solid #f4f4f4;
    margin-top: 0;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-weight: 600;
}
.bg-orange {
    background-color: #ff851b !important;
    color: #fff;
}
.bg-teal {
    background-color: #39cccc !important;
    color: #fff;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    $("#dashboardMainMenu").addClass('active');
});
</script>
