<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Select Merchant Account</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/AdminLTE.min.css') ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>Select</b> Merchant Account
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Choose which merchant account to access</p>

    <?php if($this->session->flashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo $this->session->flashdata('error'); ?>
      </div>
    <?php endif; ?>

    <div class="list-group">
      <?php if(!empty($tenants)): ?>
        <?php foreach($tenants as $tenant): ?>
          <a href="<?php echo base_url('auth/set_tenant/'.$tenant['id']) ?>" class="list-group-item list-group-item-action">
            <h4 class="list-group-item-heading">
              <i class="fa fa-building"></i> <?php echo $tenant['company_name']; ?>
            </h4>
            <p class="list-group-item-text">
              <small>
                <i class="fa fa-user"></i> Role: <?php echo ucfirst($tenant['role']); ?> | 
                <i class="fa fa-tag"></i> Plan: <?php echo ucfirst($tenant['plan']); ?>
              </small>
            </p>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-warning">No merchant accounts available</div>
      <?php endif; ?>
    </div>

    <a href="<?php echo base_url('auth/logout') ?>" class="btn btn-default btn-block btn-flat">Logout</a>

  </div>
</div>

<script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
</body>
</html>
