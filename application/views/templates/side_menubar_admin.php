<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?php echo base_url('assets/dist/img/avatar5.png') ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo $this->session->userdata('username'); ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> System Admin</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">SYSTEM ADMINISTRATION</li>
            
            <li <?php echo $this->uri->segment(2) == 'dashboard' || $this->uri->segment(2) == '' ? 'class="active"' : ''; ?>>
                <a href="<?php echo base_url('admin/dashboard') ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <li <?php echo $this->uri->segment(2) == 'tenants' ? 'class="active"' : ''; ?>>
                <a href="<?php echo base_url('admin/tenants') ?>">
                    <i class="fa fa-building"></i> <span>Manage Tenants</span>
                </a>
            </li>

            <li <?php echo $this->uri->segment(2) == 'users' ? 'class="active"' : ''; ?>>
                <a href="<?php echo base_url('admin/users') ?>">
                    <i class="fa fa-users"></i> <span>Manage Users</span>
                </a>
            </li>

            <li>
                <a href="<?php echo base_url('auth/logout') ?>">
                    <i class="fa fa-sign-out"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </section>
</aside>
