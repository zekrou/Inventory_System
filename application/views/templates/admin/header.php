<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo isset($page_title) ? $page_title : 'System Admin'; ?> | Inventory System</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/AdminLTE.min.css') ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/skins/_all-skins.min.css') ?>">
  
  <!-- jQuery 3 -->
  <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js') ?>"></script>
  
  <style>
    .skin-blue .main-header .navbar {
      background-color: #dd4814;
    }
    .skin-blue .main-header .logo {
      background-color: #c43f11;
    }
    .skin-blue .main-header .logo:hover {
      background-color: #dd4814;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo base_url('admin/dashboard') ?>" class="logo">
      <span class="logo-mini"><b>S</b>A</span>
      <span class="logo-lg"><b>System</b> Admin</span>
    </a>
    
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-user-circle"></i>
              <span class="hidden-xs"><?php echo isset($user_data['username']) ? $user_data['username'] : 'Admin'; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header" style="background-color: #dd4814;">
                <p>
                  System Administrator
                  <small><?php echo isset($user_data['email']) ? $user_data['email'] : ''; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-right">
                  <a href="<?php echo base_url('auth/logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
