<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Manage Merchants | Inventory System</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/skins/_all-skins.min.css'); ?>">
    <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js'); ?>"></script>
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
        <!-- HEADER ET SIDEBAR ICI (gardez votre code existant) -->

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Manage Tenants</h1>
            </section>

            <section class="content">
                <!-- ✅ Messages flash -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fa fa-check"></i> <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fa fa-times"></i> <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">All Tenants</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/create_tenant'); ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Create New Merchant
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-bordered table-striped data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant Name</th>
                                    <th>Database</th>
                                    <th>Users</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tenants)): ?>
                                    <?php foreach ($tenants as $tenant): ?>
                                        <tr>
                                            <td><?php echo $tenant['id']; ?></td>
                                            <td><strong><?php echo $tenant['tenant_name']; ?></strong></td>
                                            <td><code><?php echo $tenant['database_name']; ?></code></td>
                                            <td><span class="badge bg-blue"><?php echo $tenant['user_count']; ?></span></td>
                                            <td>
                                                <span class="label label-<?php echo $tenant['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($tenant['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('Y-m-d', strtotime($tenant['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo site_url('admin/edit_tenant/' . $tenant['id']); ?>" class="btn btn-xs btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?php echo site_url('admin/tenant_users/' . $tenant['id']); ?>" class="btn btn-xs btn-info" title="View Users">
                                                    <i class="fa fa-users"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="deleteTenant(<?php echo $tenant['id']; ?>)" class="btn btn-xs btn-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No tenants found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="pull-right hidden-xs"><b>Version</b> 2.0.0</div>
            <strong>System Administration &copy; 2026 <a href="#">Inventory System</a>.</strong> All rights reserved.
        </footer>
    </div>

    <!-- Scripts -->
    <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bower_components/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/dist/js/adminlte.min.js'); ?>"></script>

    <script>
        function deleteTenant(tenantId) {
            if (confirm('⚠️ WARNING: This will permanently delete:\n\n' +
                    '• The merchant account\n' +
                    '• The entire database\n' +
                    '• All data (products, orders, users, etc.)\n\n' +
                    'This action CANNOT be undone!\n\n' +
                    'Are you absolutely sure?')) {

                if (confirm('🚨 FINAL WARNING!\n\n' +
                        'You are about to DELETE EVERYTHING for this merchant.\n\n' +
                        'Click OK to proceed with deletion.')) {

                    window.location.href = '<?php echo site_url("admin/delete_tenant/"); ?>' + tenantId;
                }
            }
        }
    </script>

</body>

</html>




</body>

</html>