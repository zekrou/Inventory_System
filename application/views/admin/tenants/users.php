<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Merchant Users
      <small><?php echo $tenant['company_name']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('tenants') ?>">Merchants</a></li>
      <li class="active">Users</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">Merchant Information</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <strong>Merchant Name:</strong> <?php echo $tenant['tenant_name']; ?><br>
                <strong>Company Name:</strong> <?php echo $tenant['company_name']; ?><br>
                <strong>Database:</strong> <code><?php echo $tenant['database_name']; ?></code>
              </div>
              <div class="col-md-6">
                <strong>Status:</strong> 
                <?php if($tenant['status'] == 'active'): ?>
                  <span class="label label-success">Active</span>
                <?php elseif($tenant['status'] == 'suspended'): ?>
                  <span class="label label-warning">Suspended</span>
                <?php else: ?>
                  <span class="label label-info">Trial</span>
                <?php endif; ?>
                <br>
                <strong>Plan:</strong> <?php echo ucfirst($tenant['plan']); ?><br>
                <strong>Created:</strong> <?php echo date('Y-m-d H:i', strtotime($tenant['created_at'])); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Users with Access</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($users)): ?>
                  <?php foreach($users as $user): ?>
                    <tr>
                      <td><?php echo $user['id']; ?></td>
                      <td><?php echo $user['username']; ?></td>
                      <td><?php echo $user['email']; ?></td>
                      <td>
                        <?php if($user['role'] == 'admin'): ?>
                          <span class="label label-danger">Admin</span>
                        <?php elseif($user['role'] == 'manager'): ?>
                          <span class="label label-warning">Manager</span>
                        <?php else: ?>
                          <span class="label label-default">User</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center">No users found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <a href="<?php echo base_url('tenants') ?>" class="btn btn-default">
              <i class="fa fa-arrow-left"></i> Back to Merchants
            </a>
          </div>
        </div>
        <!-- /.box -->
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
