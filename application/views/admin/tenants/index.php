<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage Merchants
      <small>View all merchant accounts</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Merchants</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Merchant Accounts</h3>
            <?php if(isset($user_permission['createTenant'])): ?>
              <a href="<?php echo base_url('tenants/create') ?>" class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i> Create New Merchant
              </a>
            <?php endif; ?>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <?php if($this->session->flashdata('success')): ?>
              <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('success'); ?>
              </div>
            <?php endif; ?>

            <?php if($this->session->flashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
              </div>
            <?php endif; ?>

            <table id="tenantsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Merchant Name</th>
                  <th>Company Name</th>
                  <th>Database</th>
                  <th>Status</th>
                  <th>Plan</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($tenants)): ?>
                  <?php foreach($tenants as $tenant): ?>
                    <tr>
                      <td><?php echo $tenant['id']; ?></td>
                      <td><?php echo $tenant['tenant_name']; ?></td>
                      <td><?php echo $tenant['company_name']; ?></td>
                      <td><code><?php echo $tenant['database_name']; ?></code></td>
                      <td>
                        <?php if($tenant['status'] == 'active'): ?>
                          <span class="label label-success">Active</span>
                        <?php elseif($tenant['status'] == 'suspended'): ?>
                          <span class="label label-warning">Suspended</span>
                        <?php else: ?>
                          <span class="label label-info">Trial</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo ucfirst($tenant['plan']); ?></td>
                      <td><?php echo date('Y-m-d H:i', strtotime($tenant['created_at'])); ?></td>
                      <td>
                        <a href="<?php echo base_url('tenants/users/'.$tenant['id']) ?>" class="btn btn-default btn-xs">
                          <i class="fa fa-users"></i> Users
                        </a>
                        <?php if(isset($user_permission['updateTenant'])): ?>
                          <a href="<?php echo base_url('tenants/edit/'.$tenant['id']) ?>" class="btn btn-info btn-xs">
                            <i class="fa fa-edit"></i> Edit
                          </a>
                        <?php endif; ?>
                        <?php if(isset($user_permission['deleteTenant'])): ?>
                          <a href="<?php echo base_url('tenants/delete/'.$tenant['id']) ?>" 
                             class="btn btn-danger btn-xs" 
                             onclick="return confirm('WARNING: This will permanently delete the merchant and their entire database! Are you sure?')">
                            <i class="fa fa-trash"></i> Delete
                          </a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
$(document).ready(function() {
    $('#tenantsTable').DataTable({
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false
    });
});
</script>
