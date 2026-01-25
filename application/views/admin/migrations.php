<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-database"></i> Database Migrations
            <small>Manage tenant database schema versions</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Migrations</li>
        </ol>
    </section>

    <section class="content">
        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('warning') ?>
        </div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tenants</span>
                        <span class="info-box-number"><?= $total_tenants ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Up-to-date</span>
                        <span class="info-box-number">
                            <?php 
                            $uptodate = 0;
                            foreach ($migration_status as $key => $status) {
                                if ($key !== 'stock_template' && isset($status['version']) && $status['version'] >= 1) {
                                    $uptodate++;
                                }
                            }
                            echo $uptodate;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending</span>
                        <span class="info-box-number"><?= $total_tenants - $uptodate ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-file-code-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Migration Files</span>
                        <span class="info-box-number"><?= count($migration_files) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Run Migrations Box -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-play"></i> Run Migrations</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Info:</strong> This will apply all pending database migrations to <strong>ALL active tenants</strong> and the template database.
                    <br>
                    <small>Current target version: <strong>1 (add_losses_tracking)</strong></small>
                </div>
                
                <form method="POST" onsubmit="return confirm('⚠️ Are you sure you want to run migrations on ALL tenant databases?\n\nThis action cannot be undone!');">
                    <button type="submit" name="run_migrations" value="1" class="btn btn-warning btn-lg">
                        <i class="fa fa-play"></i> Run Migrations on All Tenants
                    </button>
                </form>
            </div>
        </div>

        <!-- Migration Results -->
        <?php if (isset($migration_results)): ?>
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-check-circle"></i> Migration Results</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Database</th>
                            <th>Tenant</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Version</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($migration_results as $key => $result): ?>
                        <tr class="<?= isset($result['success']) && $result['success'] ? 'success' : 'danger' ?>">
                            <td>
                                <?php 
                                if ($key === 'stock_template') {
                                    echo '<strong>stock</strong> (Template)';
                                } else {
                                    echo isset($migration_status[$key]['database_name']) ? $migration_status[$key]['database_name'] : 'Unknown';
                                }
                                ?>
                            </td>
                            <td>
                                <?= $key === 'stock_template' ? 'Template DB' : (isset($result['tenant_name']) ? $result['tenant_name'] : 'N/A') ?>
                            </td>
                            <td class="text-center">
                                <?php if (isset($result['success']) && $result['success']): ?>
                                    <span class="label label-success"><i class="fa fa-check"></i> Success</span>
                                <?php else: ?>
                                    <span class="label label-danger"><i class="fa fa-times"></i> Failed</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <strong><?= isset($result['version']) ? $result['version'] : '0' ?></strong>
                            </td>
                            <td>
                                <?php 
                                if (isset($result['error'])) {
                                    echo '<span class="text-danger">' . htmlspecialchars($result['error']) . '</span>';
                                } else {
                                    echo '<span class="text-success">Migration applied successfully</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Migration Status -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Current Migration Status</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Database</th>
                            <th>Tenant Name</th>
                            <th class="text-center">Current Version</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Template DB -->
                        <tr class="info">
                            <td><strong>stock</strong></td>
                            <td><em>Template Database</em></td>
                            <td class="text-center"><strong><?= isset($migration_status['stock_template']) ? $migration_status['stock_template'] : 0 ?></strong></td>
                            <td class="text-center">
                                <?php $version = isset($migration_status['stock_template']) ? $migration_status['stock_template'] : 0; ?>
                                <?php if ($version >= 1): ?>
                                    <span class="label label-success"><i class="fa fa-check"></i> Up-to-date</span>
                                <?php else: ?>
                                    <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Tenants -->
                        <?php foreach ($migration_status as $key => $status): ?>
                            <?php if ($key === 'stock_template') continue; ?>
                            <tr>
                                <td><?= isset($status['database_name']) ? $status['database_name'] : 'Unknown' ?></td>
                                <td><?= isset($status['tenant_name']) ? $status['tenant_name'] : 'N/A' ?></td>
                                <td class="text-center"><strong><?= isset($status['version']) ? $status['version'] : 0 ?></strong></td>
                                <td class="text-center">
                                    <?php $version = isset($status['version']) ? $status['version'] : 0; ?>
                                    <?php if ($version >= 1): ?>
                                        <span class="label label-success"><i class="fa fa-check"></i> Up-to-date</span>
                                    <?php else: ?>
                                        <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Available Migration Files -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-file-code-o"></i> Available Migration Files</h3>
            </div>
            <div class="box-body">
                <?php if (empty($migration_files)): ?>
                    <p class="text-muted">No migration files found in <code>application/migrations/</code></p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($migration_files as $file): ?>
                            <li class="list-group-item">
                                <i class="fa fa-file-code-o text-blue"></i> 
                                <strong><?= $file ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
