<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Edit Merchant
      <small><?php echo $tenant['company_name']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('admin/tenants'); ?>">Merchants</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">

        <?php echo validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>', '</div>'); ?>

        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Merchant Details</h3>
          </div>

          <form action="<?php echo base_url('admin/edit_tenant/'.$tenant['id']); ?>" method="post">
            <div class="box-body">

              <div class="form-group">
                <label>Merchant ID</label>
                <input type="text" class="form-control" value="<?php echo $tenant['id']; ?>" disabled>
              </div>

              <div class="form-group">
                <label>Database Name</label>
                <input type="text" class="form-control" value="<?php echo $tenant['database_name']; ?>" disabled>
                <small class="text-muted">Cannot be changed</small>
              </div>

              <div class="form-group">
                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="company_name" name="company_name" 
                       value="<?php echo set_value('company_name', $tenant['company_name']); ?>" required>
              </div>

              <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="status" name="status" required>
                  <option value="active" <?php echo ($tenant['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                  <option value="inactive" <?php echo ($tenant['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                  <option value="suspended" <?php echo ($tenant['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                </select>
              </div>

              <div class="form-group">
                <label for="plan">Plan <span class="text-danger">*</span></label>
                <select class="form-control" id="plan" name="plan" required>
                  <option value="free" <?php echo ($tenant['plan'] == 'free') ? 'selected' : ''; ?>>Free</option>
                  <option value="basic" <?php echo ($tenant['plan'] == 'basic') ? 'selected' : ''; ?>>Basic</option>
                  <option value="premium" <?php echo ($tenant['plan'] == 'premium') ? 'selected' : ''; ?>>Premium</option>
                  <option value="enterprise" <?php echo ($tenant['plan'] == 'enterprise') ? 'selected' : ''; ?>>Enterprise</option>
                </select>
              </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-warning">
                <i class="fa fa-save"></i> Update Merchant
              </button>
              <a href="<?php echo base_url('admin/tenants'); ?>" class="btn btn-default">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>

        </div>

      </div>
    </div>
  </section>
</div>
