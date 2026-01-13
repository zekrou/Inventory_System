<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      System Administration Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <!-- Stats Boxes -->
    <div class="row">
      <!-- Total Tenants -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $total_tenants; ?></h3>
            <p>Total Merchants</p>
          </div>
          <div class="icon">
            <i class="fa fa-building"></i>
          </div>
          <a href="<?php echo base_url('admin/tenants'); ?>" class="small-box-footer">
            View All <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Active Tenants -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $active_tenants; ?></h3>
            <p>Active Merchants</p>
          </div>
          <div class="icon">
            <i class="fa fa-check-circle"></i>
          </div>
          <a href="<?php echo base_url('admin/tenants'); ?>" class="small-box-footer">
            Details <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Inactive Tenants -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $inactive_tenants; ?></h3>
            <p>Inactive Merchants</p>
          </div>
          <div class="icon">
            <i class="fa fa-pause-circle"></i>
          </div>
          <a href="<?php echo base_url('admin/tenants'); ?>" class="small-box-footer">
            View <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Total Users -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <a href="<?php echo base_url('admin/users'); ?>" class="small-box-footer">
            View All <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Quick Actions</h3>
          </div>
          <div class="box-body">
            <a href="<?php echo base_url('admin/tenants/create'); ?>" class="btn btn-primary btn-lg">
              <i class="fa fa-plus"></i> Create New Merchant
            </a>
            <a href="<?php echo base_url('admin/users/create'); ?>" class="btn btn-success btn-lg">
              <i class="fa fa-user-plus"></i> Add New User
            </a>
            <a href="<?php echo base_url('admin/backup'); ?>" class="btn btn-warning btn-lg">
              <i class="fa fa-database"></i> Backup Database
            </a>
            <a href="<?php echo base_url('admin/logs'); ?>" class="btn btn-info btn-lg">
              <i class="fa fa-history"></i> View Activity Logs
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Merchants -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Recent Merchants</h3>
            <div class="box-tools pull-right">
              <a href="<?php echo base_url('admin/tenants'); ?>" class="btn btn-box-tool">
                View All <i class="fa fa-arrow-right"></i>
              </a>
            </div>
          </div>
          <div class="box-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Company Name</th>
                  <th>Database</th>
                  <th>Plan</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($recent_tenants)): ?>
                  <?php foreach($recent_tenants as $tenant): ?>
                    <tr>
                      <td><?php echo $tenant['id']; ?></td>
                      <td><strong><?php echo $tenant['company_name']; ?></strong></td>
                      <td><code><?php echo $tenant['database_name']; ?></code></td>
                      <td>
                        <span class="label label-info"><?php echo ucfirst($tenant['plan']); ?></span>
                      </td>
                      <td>
                        <?php if($tenant['status'] == 'active'): ?>
                          <span class="label label-success">Active</span>
                        <?php else: ?>
                          <span class="label label-danger">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo date('d/m/Y H:i', strtotime($tenant['created_at'])); ?></td>
                      <td>
                        <a href="<?php echo base_url('admin/tenants/edit/'.$tenant['id']); ?>" class="btn btn-xs btn-primary" title="Edit">
                          <i class="fa fa-edit"></i>
                        </a>
                        <a href="<?php echo base_url('admin/tenants/users/'.$tenant['id']); ?>" class="btn btn-xs btn-info" title="View Users">
                          <i class="fa fa-users"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center">No merchants found</td>
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
$(document).ready(function() {
  $("#adminDashboardNav").addClass('active');
});
</script>
