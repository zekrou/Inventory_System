<aside class="main-sidebar">
  <section class="sidebar">
    <ul class="sidebar-menu" data-widget="tree">
      <!-- Dashboard -->
      <li id="dashboardMainMenu">
        <a href="<?php echo base_url('dashboard') ?>">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

      <?php if (!empty($user_permission) && is_array($user_permission)): ?>

        <!-- ========== 📱 MOBILE ORDERS ========== -->
        <li class="header">MOBILE ORDERS</li>

        <!-- Pre-Orders Menu -->
        <?php if (in_array('viewPreOrder', $user_permission)): ?>
          <li class="treeview <?php echo $this->uri->segment(1) == 'preorders' ? 'active' : ''; ?>">
            <a href="<?php echo base_url('preorders'); ?>">
              <i class="fa fa-mobile"></i>
              <span>Pre-Orders Mobile</span>
              <?php
              // Badge count (optionnel - avec vérification)
              if(class_exists('Model_preorders')) {
                $this->load->model('model_preorders');
                if($this->db->table_exists('pre_orders')) {
                  $pending_count = $this->db->where('status', 'pending')->count_all_results('pre_orders');
                  if ($pending_count > 0):
              ?>
                <span class="pull-right-container">
                  <small class="label pull-right bg-yellow"><?php echo $pending_count; ?></small>
                </span>
              <?php 
                  endif;
                }
              }
              ?>
            </a>
          </li>
        <?php endif; ?>

        <!-- ========== 📦 INVENTORY MANAGEMENT ========== -->
        <li class="header">INVENTORY MANAGEMENT</li>

        <?php if (isset($user_permission['createProduct']) || isset($user_permission['updateProduct']) || isset($user_permission['viewProduct']) || isset($user_permission['deleteProduct'])): ?>
          <li class="treeview" id="mainProductNav">
            <a href="#">
              <i class="fa fa-cube"></i>
              <span>Products</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['createProduct'])): ?>
                <li id="addProductNav"><a href="<?php echo base_url('products/create') ?>"><i class="fa fa-circle-o"></i> Add Product</a></li>
              <?php endif; ?>
              <?php if (isset($user_permission['updateProduct']) || isset($user_permission['viewProduct']) || isset($user_permission['deleteProduct'])): ?>
                <li id="manageProductNav"><a href="<?php echo base_url('products') ?>"><i class="fa fa-circle-o"></i> Manage Products</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['createBrand']) || isset($user_permission['updateBrand']) || isset($user_permission['viewBrand']) || isset($user_permission['deleteBrand'])): ?>
          <li id="brandNav">
            <a href="<?php echo base_url('brands/') ?>">
              <i class="glyphicon glyphicon-tags"></i> <span>Brands</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['createCategory']) || isset($user_permission['updateCategory']) || isset($user_permission['viewCategory']) || isset($user_permission['deleteCategory'])): ?>
          <li id="categoryNav">
            <a href="<?php echo base_url('category/') ?>">
              <i class="fa fa-files-o"></i> <span>Category</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['viewStock']) || isset($user_permission['viewStockHistory'])): ?>
          <li class="treeview" id="mainStockNav">
            <a href="#">
              <i class="fa fa-cubes"></i>
              <span>Stock</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['viewStock'])): ?>
                <li id="viewStockNav"><a href="<?php echo base_url('stock') ?>"><i class="fa fa-circle-o"></i> Stock Overview</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <!-- ========== 💼 BUSINESS OPERATIONS ========== -->
        <li class="header">BUSINESS OPERATIONS</li>

        <?php if (isset($user_permission['viewPurchase']) || isset($user_permission['createPurchase'])): ?>
          <li class="treeview" id="mainPurchaseNav">
            <a href="#">
              <i class="fa fa-shopping-cart"></i>
              <span>Purchases</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['createPurchase'])): ?>
                <li id="addPurchaseNav"><a href="<?php echo base_url('purchases/create') ?>"><i class="fa fa-circle-o"></i> Create Purchase</a></li>
              <?php endif; ?>
              <?php if (isset($user_permission['viewPurchase'])): ?>
                <li id="managePurchaseNav"><a href="<?php echo base_url('purchases') ?>"><i class="fa fa-circle-o"></i> Manage Purchases</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['createOrder']) || isset($user_permission['updateOrder']) || isset($user_permission['viewOrder']) || isset($user_permission['deleteOrder'])): ?>
          <li class="treeview" id="mainOrdersNav">
            <a href="#">
              <i class="fa fa-dollar"></i>
              <span>Sales Orders</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['createOrder'])): ?>
                <li id="addOrderNav"><a href="<?php echo base_url('orders/create') ?>"><i class="fa fa-circle-o"></i> Add Order</a></li>
              <?php endif; ?>
              <?php if (isset($user_permission['updateOrder']) || isset($user_permission['viewOrder']) || isset($user_permission['deleteOrder'])): ?>
                <li id="manageOrdersNav"><a href="<?php echo base_url('orders') ?>"><i class="fa fa-circle-o"></i> Manage Orders</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <!-- ========== 👥 CONTACTS ========== -->
        <li class="header">CONTACTS</li>

        <?php if (isset($user_permission['viewCustomer']) || isset($user_permission['createCustomer'])): ?>
          <li id="customerNav">
            <a href="<?php echo base_url('customers') ?>">
              <i class="fa fa-users"></i> <span>Customers</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['viewSupplier']) || isset($user_permission['createSupplier'])): ?>
          <li id="supplierNav">
            <a href="<?php echo base_url('suppliers') ?>">
              <i class="fa fa-truck"></i> <span>Suppliers</span>
            </a>
          </li>
        <?php endif; ?>

        <!-- ========== 📊 ANALYTICS ========== -->
        <li class="header">ANALYTICS</li>

        <li class="treeview <?php echo $this->uri->segment(1) == 'reports' ? 'active' : '' ?>" id="mainReportsNav">
          <a href="#">
            <i class="fa fa-pie-chart"></i> <span>Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (isset($user_permission['viewReports'])): ?>
              <li id="reportSubMenu">
                <a href="<?php echo base_url('reports/') ?>">
                  <i class="fa fa-line-chart"></i> Sales Reports
                </a>
              </li>
              <li id="reportSuppliersSubMenu">
                <a href="<?php echo base_url('reports/purchases') ?>">
                  <i class="fa fa-shopping-cart"></i> Purchase Reports
                </a>
              </li>
              <li id="reportCustomersSubMenu">
                <a href="<?php echo base_url('reports/customers') ?>">
                  <i class="fa fa-users"></i> Customer Reports
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>

        <!-- ========== ⚙️ SYSTEM SETTINGS ========== -->
        <li class="header">SYSTEM SETTINGS</li>

        <?php if (isset($user_permission['createUser']) || isset($user_permission['updateUser']) || isset($user_permission['viewUser']) || isset($user_permission['deleteUser'])): ?>
          <li class="treeview" id="mainUserNav">
            <a href="#">
              <i class="fa fa-users"></i>
              <span>Users</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['createUser'])): ?>
                <li id="createUserNav"><a href="<?php echo base_url('users/create') ?>"><i class="fa fa-circle-o"></i> Add User</a></li>
              <?php endif; ?>
              <?php if (isset($user_permission['updateUser']) || isset($user_permission['viewUser']) || isset($user_permission['deleteUser'])): ?>
                <li id="manageUserNav"><a href="<?php echo base_url('users') ?>"><i class="fa fa-circle-o"></i> Manage Users</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['createGroup']) || isset($user_permission['updateGroup']) || isset($user_permission['viewGroup']) || isset($user_permission['deleteGroup'])): ?>
          <li class="treeview" id="mainGroupNav">
            <a href="#">
              <i class="fa fa-shield"></i>
              <span>Groups & Permissions</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <?php if (isset($user_permission['createGroup'])): ?>
                <li id="addGroupNav"><a href="<?php echo base_url('groups/create') ?>"><i class="fa fa-circle-o"></i> Add Group</a></li>
              <?php endif; ?>
              <?php if (isset($user_permission['updateGroup']) || isset($user_permission['viewGroup']) || isset($user_permission['deleteGroup'])): ?>
                <li id="manageGroupNav"><a href="<?php echo base_url('groups') ?>"><i class="fa fa-circle-o"></i> Manage Groups</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (isset($user_permission['updateCompany'])): ?>
          <li id="companyNav"><a href="<?php echo base_url('company/') ?>"><i class="fa fa-building"></i> <span>Company</span></a></li>
        <?php endif; ?>

        <?php if (isset($user_permission['viewProfile'])): ?>
          <li><a href="<?php echo base_url('users/profile/') ?>"><i class="fa fa-user-o"></i> <span>Profile</span></a></li>
        <?php endif; ?>

        <?php if (isset($user_permission['updateSetting'])): ?>
          <li><a href="<?php echo base_url('users/setting/') ?>"><i class="fa fa-wrench"></i> <span>Settings</span></a></li>
        <?php endif; ?>

      <?php endif; ?>

      <!-- Logout -->
      <li><a href="<?php echo base_url('auth/logout') ?>"><i class="glyphicon glyphicon-log-out"></i> <span>Logout</span></a></li>

    </ul>
  </section>
</aside>
