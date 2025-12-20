<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <?php if($is_admin == true): ?>

        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $total_products ?></h3>

                <p>Total Products</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="<?php echo base_url('products/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $total_paid_orders ?></h3>

                <p>Total Paid Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo base_url('orders/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3><?php echo $total_users; ?></h3>

                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-people"></i>
              </div>
              <a href="<?php echo base_url('users/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3><?php echo $total_stores ?></h3>

                <p>Total Stores</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-home"></i>
              </div>
              <a href="<?php echo base_url('stores/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Second Row - NEW -->
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
              <div class="inner">
                <h3><?php echo isset($total_customers) ? $total_customers : 0 ?></h3>

                <p>Total Customers</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-stalker"></i>
              </div>
              <a href="<?php echo base_url('customers/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->

          <!-- Customer Type Breakdown -->
          <?php if(isset($customer_types)): ?>
            <?php 
              $super_wholesale_count = 0;
              $wholesale_count = 0;
              $retail_count = 0;
              
              foreach($customer_types as $type) {
                if($type['customer_type'] == 'super_wholesale') {
                  $super_wholesale_count = $type['count'];
                } elseif($type['customer_type'] == 'wholesale') {
                  $wholesale_count = $type['count'];
                } elseif($type['customer_type'] == 'retail') {
                  $retail_count = $type['count'];
                }
              }
            ?>

            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-maroon">
                <div class="inner">
                  <h3><?php echo $super_wholesale_count ?></h3>
                  <p>Super Gros Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-briefcase"></i>
                </div>
                <a href="<?php echo base_url('customers/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-orange">
                <div class="inner">
                  <h3><?php echo $wholesale_count ?></h3>
                  <p>Gros Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="<?php echo base_url('customers/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-teal">
                <div class="inner">
                  <h3><?php echo $retail_count ?></h3>
                  <p>Détail Customers</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
                <a href="<?php echo base_url('customers/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          <?php endif; ?>

        </div>
        <!-- /.row -->

      <?php endif; ?>
      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script type="text/javascript">
    $(document).ready(function() {
      $("#dashboardMainMenu").addClass('active');
    }); 
  </script>