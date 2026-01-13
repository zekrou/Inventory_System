<div class="content-wrapper">
    <section class="content-header">
        <h1>
            System Administration
            <small>Manage all tenants and users</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tenants</span>
                        <span class="info-box-number"><?php echo $total_tenants; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Users</span>
                        <span class="info-box-number"><?php echo $total_users; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Tenants</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant Name</th>
                                    <th>Database</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($tenants)): ?>
                                    <?php foreach($tenants as $tenant): ?>
                                        <tr>
                                            <td><?php echo $tenant['id']; ?></td>
                                            <td><?php echo $tenant['tenant_name']; ?></td>
                                            <td><?php echo $tenant['database_name']; ?></td>
                                            <td>
                                                <span class="label label-<?php echo $tenant['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($tenant['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('Y-m-d', strtotime($tenant['created_at'])); ?></td>
                                            <td>
                                                <a href="#" class="btn btn-xs btn-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No tenants found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
