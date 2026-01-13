<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
      <small>System Overview</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php if($is_admin == true): ?>

      <!-- ========== SECTION 1: KEY BUSINESS METRICS ========== -->
      <div class="row">
        <div class="col-md-12">
          <h3 class="page-header">
            <i class="fa fa-line-chart"></i> Key Performance Indicators
          </h3>
        </div>
      </div>

      <div class="row">
        <!-- Revenue/Sales Today -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-dollar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Sales Today</span>
              <span class="info-box-number">
                <?php echo isset($today_sales) ? number_format($today_sales, 0) : 0; ?> DZD
              </span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo isset($today_sales_percent) ? $today_sales_percent : 0; ?>%"></div>
              </div>
              <span class="progress-description">
                <?php echo isset($today_orders_count) ? $today_orders_count : 0; ?> orders today
              </span>
            </div>
          </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Sales This Month</span>
              <span class="info-box-number">
                <?php echo isset($monthly_sales) ? number_format($monthly_sales, 0) : 0; ?> DZD
              </span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo isset($monthly_sales_percent) ? $monthly_sales_percent : 0; ?>%"></div>
              </div>
              <span class="progress-description">
                <?php echo isset($monthly_orders_count) ? $monthly_orders_count : 0; ?> orders this month
              </span>
            </div>
          </div>
        </div>

        <!-- Pending Orders Value -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Pending Orders</span>
              <span class="info-box-number">
                <?php echo isset($pending_orders_count) ? $pending_orders_count : 0; ?>
              </span>
              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                <?php echo isset($pending_orders_value) ? number_format($pending_orders_value, 0) : 0; ?> DZD
              </span>
            </div>
          </div>
        </div>

        <!-- Profit Margin -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-percent"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Profit Margin</span>
              <span class="info-box-number">
                <?php echo isset($profit_margin) ? number_format($profit_margin, 1) : 0; ?>%
              </span>
              <div class="progress">
                <div class="progress-bar" style="width: <?php echo isset($profit_margin) ? $profit_margin : 0; ?>%"></div>
              </div>
              <span class="progress-description">
                Average margin
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ========== SECTION 2: INVENTORY HEALTH ========== -->
      <div class="row">
        <div class="col-md-12">
          <h3 class="page-header">
            <i class="fa fa-cubes"></i> Inventory Status
          </h3>
        </div>
      </div>

      <div class="row">
        <!-- Total Stock Value -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo isset($total_stock_value) ? number_format($total_stock_value, 0) : 0; ?> <sup style="font-size: 20px;">DZD</sup></h3>
              <p>Total Stock Value</p>
            </div>
            <div class="icon"><i class="fa fa-money"></i></div>
            <a href="<?php echo base_url('stock/'); ?>" class="small-box-footer">
              View details <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Products Count -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_products; ?></h3>
              <p>Active Products</p>
            </div>
            <div class="icon"><i class="ion ion-bag"></i></div>
            <a href="<?php echo base_url('products/'); ?>" class="small-box-footer">
              Manage products <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo isset($stock_stats['low_stock']) ? $stock_stats['low_stock'] : 0; ?></h3>
              <p>Low Stock Alerts</p>
            </div>
            <div class="icon"><i class="fa fa-warning"></i></div>
            <a href="<?php echo base_url('stock/?filter=low'); ?>" class="small-box-footer">
              Action required <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Out of Stock -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo isset($stock_stats['out_of_stock']) ? $stock_stats['out_of_stock'] : 0; ?></h3>
              <p>Out of Stock</p>
            </div>
            <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
            <a href="<?php echo base_url('stock/?filter=out'); ?>" class="small-box-footer">
              Urgent <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- ========== SECTION 3: OPERATIONS SUMMARY ========== -->
      <div class="row">
        <div class="col-md-12">
          <h3 class="page-header">
            <i class="fa fa-exchange"></i> Operations Summary
          </h3>
        </div>
      </div>

      <div class="row">
        <!-- Total Paid Orders -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_paid_orders; ?></h3>
              <p>Paid Orders</p>
            </div>
            <div class="icon"><i class="ion ion-stats-bars"></i></div>
            <a href="<?php echo base_url('orders/?status=paid'); ?>" class="small-box-footer">
              View orders <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Pending Purchases -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-orange">
            <div class="inner">
              <h3><?php echo isset($purchase_stats['pending_purchases']) ? $purchase_stats['pending_purchases'] : 0; ?></h3>
              <p>Pending Purchases</p>
            </div>
            <div class="icon"><i class="fa fa-shopping-cart"></i></div>
            <a href="<?php echo base_url('purchases/?status=pending'); ?>" class="small-box-footer">
              Process <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Completed Purchases This Month -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo isset($purchase_stats['completed_month']) ? $purchase_stats['completed_month'] : 0; ?></h3>
              <p>Completed Purchases (Month)</p>
            </div>
            <div class="icon"><i class="fa fa-check-circle"></i></div>
            <a href="<?php echo base_url('purchases/'); ?>" class="small-box-footer">
              View history <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Stock Movements Today -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3><?php echo isset($stock_movements_today) ? $stock_movements_today : 0; ?></h3>
              <p>Movements Today</p>
            </div>
            <div class="icon"><i class="fa fa-exchange"></i></div>
            <a href="<?php echo base_url('orders/?status=unpaid'); ?>" class="small-box-footer">
              History <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- ========== SECTION 4: CONTACTS & RELATIONSHIPS ========== -->
      <div class="row">
        <div class="col-md-12">
          <h3 class="page-header">
            <i class="fa fa-users"></i> Business Relationships
          </h3>
        </div>
      </div>

      <div class="row">
        <!-- Total Customers -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo isset($total_customers) ? $total_customers : 0; ?></h3>
              <p>Total Customers</p>
            </div>
            <div class="icon"><i class="ion ion-person-stalker"></i></div>
            <a href="<?php echo base_url('customers/'); ?>" class="small-box-footer">
              Manage customers <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Super Wholesale Customers -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3>
                <?php 
                  $super_wholesale_count = 0;
                  if(isset($customer_types)) {
                    foreach($customer_types as $type) {
                      if($type['customer_type'] == 'super_wholesale') {
                        $super_wholesale_count = $type['count'];
                      }
                    }
                  }
                  echo $super_wholesale_count;
                ?>
              </h3>
              <p>Super Wholesale</p>
            </div>
            <div class="icon"><i class="ion ion-briefcase"></i></div>
            <a href="<?php echo base_url('customers/?type=super_wholesale'); ?>" class="small-box-footer">
              View details <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Wholesale Customers -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-orange">
            <div class="inner">
              <h3>
                <?php 
                  $wholesale_count = 0;
                  if(isset($customer_types)) {
                    foreach($customer_types as $type) {
                      if($type['customer_type'] == 'wholesale') {
                        $wholesale_count = $type['count'];
                      }
                    }
                  }
                  echo $wholesale_count;
                ?>
              </h3>
              <p>Wholesale</p>
            </div>
            <div class="icon"><i class="ion ion-bag"></i></div>
            <a href="<?php echo base_url('customers/?type=wholesale'); ?>" class="small-box-footer">
              View details <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Retail Customers -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3>
                <?php 
                  $retail_count = 0;
                  if(isset($customer_types)) {
                    foreach($customer_types as $type) {
                      if($type['customer_type'] == 'retail') {
                        $retail_count = $type['count'];
                      }
                    }
                  }
                  echo $retail_count;
                ?>
              </h3>
              <p>Retail</p>
            </div>
            <div class="icon"><i class="ion ion-person"></i></div>
            <a href="<?php echo base_url('customers/?type=retail'); ?>" class="small-box-footer">
              View details <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Total Suppliers -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-navy">
            <div class="inner">
              <h3><?php echo isset($total_suppliers) ? $total_suppliers : 0; ?></h3>
              <p>Active Suppliers</p>
            </div>
            <div class="icon"><i class="fa fa-truck"></i></div>
            <a href="<?php echo base_url('suppliers/'); ?>" class="small-box-footer">
              Manage suppliers <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Active Suppliers (with purchases this month) -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-light-blue">
            <div class="inner">
              <h3><?php echo isset($active_suppliers_month) ? $active_suppliers_month : 0; ?></h3>
              <p>Active Suppliers (Month)</p>
            </div>
            <div class="icon"><i class="fa fa-handshake-o"></i></div>
            <a href="<?php echo base_url('reports/purchases'); ?>" class="small-box-footer">
              Report <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Top Customer This Month -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-green">
            <div class="inner">
              <h3 style="font-size: 18px;">
                <?php echo isset($top_customer_name) ? substr($top_customer_name, 0, 15) : 'N/A'; ?>
              </h3>
              <p>Top Customer (Month)</p>
            </div>
            <div class="icon"><i class="fa fa-star"></i></div>
            <a href="<?php echo base_url('reports/customers'); ?>" class="small-box-footer">
              View report <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- System Users -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
          <div class="small-box bg-gray">
            <div class="inner">
              <h3><?php echo $total_users; ?></h3>
              <p>System Users</p>
            </div>
            <div class="icon"><i class="ion ion-android-people"></i></div>
            <a href="<?php echo base_url('users/'); ?>" class="small-box-footer">
              Manage users <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- ========== SECTION 5: QUICK ACTIONS & RECENT ACTIVITY ========== -->
      <div class="row">
        <!-- Quick Actions Card -->
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="box-body">
              <a href="<?php echo base_url('orders/create'); ?>" class="btn btn-app">
                <i class="fa fa-plus"></i> New Order
              </a>
              <a href="<?php echo base_url('purchases/create'); ?>" class="btn btn-app">
                <i class="fa fa-cart-plus"></i> New Purchase
              </a>
              <a href="<?php echo base_url('products/create'); ?>" class="btn btn-app">
                <i class="fa fa-cube"></i> Add Product
              </a>
              <a href="<?php echo base_url('reports/'); ?>" class="btn btn-app">
                <i class="fa fa-line-chart"></i> Reports
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-4">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Recent Orders</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <?php if(isset($recent_orders) && !empty($recent_orders)): ?>
                  <?php foreach($recent_orders as $order): ?>
                    <li class="item">
                      <div class="product-info">
                        <a href="<?php echo base_url('orders/update/'.$order['id']); ?>" class="product-title">
                          Order #<?php echo $order['id']; ?>
                          <span class="label label-<?php echo $order['paid_status'] == 1 ? 'success' : 'warning'; ?> pull-right">
                            <?php echo $order['paid_status'] == 1 ? 'Paid' : 'Pending'; ?>
                          </span>
                        </a>
                        <span class="product-description">
                          <?php echo isset($order['customer_name']) ? $order['customer_name'] : 'N/A'; ?> - 
                          <?php echo number_format($order['net_amount'], 0); ?> DZD
                        </span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="item"><span class="product-description">No recent orders</span></li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="box-footer text-center">
              <a href="<?php echo base_url('orders/'); ?>" class="uppercase">View All Orders</a>
            </div>
          </div>
        </div>

        <!-- Stock Alerts -->
        <div class="col-md-4">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-warning"></i> Stock Alerts</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <ul class="products-list product-list-in-box">
                <?php if(isset($low_stock_products) && !empty($low_stock_products)): ?>
                  <?php foreach($low_stock_products as $product): ?>
                    <li class="item">
                      <div class="product-info">
                        <a href="<?php echo base_url('products/update/'.$product['id']); ?>" class="product-title">
                          <?php echo $product['name']; ?>
                          <span class="label label-danger pull-right">
                            <?php echo $product['qty']; ?> remaining
                          </span>
                        </a>
                        <span class="product-description">
                          SKU: <?php echo $product['sku']; ?> - Threshold: <?php echo isset($product['alert_qty']) ? $product['alert_qty'] : 10; ?>
                        </span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="item"><span class="product-description">No stock alerts</span></li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="box-footer text-center">
              <a href="<?php echo base_url('stock/?filter=low'); ?>" class="uppercase">View All Alerts</a>
            </div>
          </div>
        </div>
      </div>

    <?php endif; ?>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<style>
/* Custom Dashboard Styles */
.page-header {
  border-bottom: 2px solid #f4f4f4;
  margin-top: 0;
  padding-bottom: 10px;
  margin-bottom: 20px;
  font-weight: 600;
}

.info-box {
  min-height: 110px;
}

.info-box-content {
  padding: 10px 10px;
}

.info-box-text {
  text-transform: uppercase;
  font-weight: 600;
  font-size: 13px;
}

.info-box-number {
  font-weight: bold;
  font-size: 22px;
}

.progress-description {
  font-size: 12px;
  color: rgba(255,255,255,0.8);
}

.bg-orange {
  background-color: #ff851b !important;
  color: #fff;
}

.bg-teal {
  background-color: #39cccc !important;
  color: #fff;
}

.bg-navy {
  background-color: #001f3f !important;
  color: #fff;
}

.bg-light-blue {
  background-color: #3c8dbc !important;
  color: #fff;
}

.btn-app {
  margin: 5px;
}

.products-list .item {
  padding: 10px 0;
}
</style>

<script type="text/javascript">
  $(document).ready(function() {
    $("#dashboardMainMenu").addClass('active');
  }); 
</script>
