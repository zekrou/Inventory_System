<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <i class="fa fa-user-circle fa-3x" style="color: #fff;"></i>
      </div>
      <div class="pull-left info">
        <p><?php echo isset($user_data['username']) ? $user_data['username'] : 'Admin'; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> System Admin</a>
      </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">SYSTEM ADMINISTRATION</li>
      
      <!-- Dashboard -->
      <li id="adminDashboardNav">
        <a href="<?php echo base_url('admin/dashboard') ?>">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      
      <!-- Tenants Management -->
      <li id="adminTenantsNav">
        <a href="<?php echo base_url('admin/tenants') ?>">
          <i class="fa fa-building"></i> <span>Merchants</span>
          <span class="pull-right-container">
            <small class="label pull-right bg-blue"><?php echo isset($total_tenants) ? $total_tenants : '0'; ?></small>
          </span>
        </a>
      </li>
      
      <!-- Users Management -->
      <li id="adminUsersNav">
        <a href="<?php echo base_url('admin/users') ?>">
          <i class="fa fa-users"></i> <span>Users</span>
          <span class="pull-right-container">
            <small class="label pull-right bg-green"><?php echo isset($total_users) ? $total_users : '0'; ?></small>
          </span>
        </a>
      </li>
      
      <li class="header">SYSTEM TOOLS</li>
      
      <!-- System Settings -->
      <li>
        <a href="<?php echo base_url('admin/settings') ?>">
          <i class="fa fa-cogs"></i> <span>System Settings</span>
        </a>
      </li>
      
      <!-- Database Backup -->
      <li>
        <a href="<?php echo base_url('admin/backup') ?>">
          <i class="fa fa-database"></i> <span>Database Backup</span>
        </a>
      </li>
      
      <!-- Activity Logs -->
      <li>
        <a href="<?php echo base_url('admin/logs') ?>">
          <i class="fa fa-history"></i> <span>Activity Logs</span>
        </a>
      </li>
      
      <li class="header">ACTIONS</li>
      
      <!-- Back to Merchant View -->
      <li>
        <a href="<?php echo base_url('dashboard') ?>">
          <i class="fa fa-reply"></i> <span>Switch to Merchant View</span>
        </a>
      </li>
      
      <!-- Logout -->
      <li>
        <a href="<?php echo base_url('auth/logout') ?>">
          <i class="fa fa-sign-out text-red"></i> <span>Logout</span>
        </a>
      </li>
      
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
