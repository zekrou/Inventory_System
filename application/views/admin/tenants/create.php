<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Create New Merchant
      <small>Setup new client account</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('admin/tenants'); ?>">Merchants</a></li>
      <li class="active">Create</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">

        <?php if (isset($errors) && !empty($errors)): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            <?php echo $errors; ?>
          </div>
        <?php endif; ?>

        <?php echo validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>', '</div>'); ?>

        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Merchant Information</h3>
          </div>

          <form action="<?php echo base_url('admin/tenants/create'); ?>" method="post">
            <div class="box-body">

              <h4 class="text-primary"><i class="fa fa-building"></i> Company Details</h4>
              <hr>

              <div class="form-group">
                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="company_name" name="company_name"
                  placeholder="e.g. ABC Corporation" value="<?php echo set_value('company_name'); ?>" required>
                <small class="text-muted">The official company name</small>
              </div>

              <div class="form-group">
                <label for="tenant_name">Merchant Identifier (Database Name) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="tenant_name" name="tenant_name"
                  placeholder="e.g. abc_corp (lowercase, no spaces)" value="<?php echo set_value('tenant_name'); ?>" required>
                <small class="text-muted">Unique identifier for this merchant. Will be used as database prefix. Only lowercase letters, numbers and underscores.</small>
              </div>

              <div class="form-group">
                <label for="plan">Subscription Plan <span class="text-danger">*</span></label>
                <select class="form-control" id="plan" name="plan" required>
                  <option value="">-- Select Plan --</option>
                  <option value="free">Free (Limited features)</option>
                  <option value="basic">Basic ($29/month)</option>
                  <option value="premium">Premium ($79/month)</option>
                  <option value="enterprise">Enterprise ($199/month)</option>
                </select>
              </div>

              <br>
              <h4 class="text-success"><i class="fa fa-user-plus"></i> Admin User Account</h4>
              <hr>

              <div class="form-group">
                <label for="admin_username">Admin Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="admin_username" name="admin_username"
                  placeholder="e.g. admin_abc" value="<?php echo set_value('admin_username'); ?>" required>
                <small class="text-muted">Login username for the merchant admin</small>
              </div>

              <div class="form-group">
                <label for="admin_email">Admin Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="admin_email" name="admin_email"
                  placeholder="e.g. admin@abccorp.com" value="<?php echo set_value('admin_email'); ?>" required>
                <small class="text-muted">Login credentials will be sent to this email</small>
              </div>

              <div class="form-group">
                <label for="admin_password">Admin Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="admin_password" name="admin_password"
                  placeholder="Minimum 6 characters" required>
                <small class="text-muted">Temporary password. User should change after first login.</small>
              </div>

              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> <strong>What happens next?</strong>
                <ul>
                  <li>A new database will be created automatically</li>
                  <li>All necessary tables will be created</li>
                  <li>Admin user account will be created</li>
                  <li>Default settings will be configured</li>
                  <li>Login credentials will be ready immediately</li>
                </ul>
              </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> Create Merchant
              </button>
              <a href="<?php echo base_url('admin/tenants'); ?>" class="btn btn-default btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>

        </div>

      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function() {
    // Auto-generate tenant_name from company_name
    $('#company_name').on('keyup', function() {
      var companyName = $(this).val();
      var tenantName = companyName.toLowerCase()
        .replace(/[^a-z0-9]/g, '_')
        .replace(/_+/g, '_')
        .replace(/^_|_$/g, '');
      $('#tenant_name').val(tenantName);
    });
  });
</script>