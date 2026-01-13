<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-database"></i> Database Backup
            <small>Manage system backups</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Backup</li>
        </ol>
    </section>

    <section class="content">
        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <!-- Create Backup -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create New Backup</h3>
            </div>
            <div class="box-body">
                <form method="post" action="<?php echo site_url('index.php/admin/backup'); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Backup Type</label>
                                <select name="backup_type" class="form-control" required>
                                    <option value="master">Master Database Only</option>
                                    <option value="all">All Tenant Databases</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <button type="submit" name="create_backup" class="btn btn-success btn-block">
                                <i class="fa fa-hdd-o"></i> Create Backup Now
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Backup History -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-history"></i> Backup History</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Backup Name</th>
                                <th>Type</th>
                                <th>Merchant</th>
                                <th>File Size</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($backups)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No backups found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($backups as $index => $backup): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><code><?php echo $backup['backup_name']; ?></code></td>
                                    <td>
                                        <?php if($backup['backup_type'] == 'master'): ?>
                                            <span class="label label-primary">Master DB</span>
                                        <?php else: ?>
                                            <span class="label label-info">Tenant DB</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $backup['tenant_name'] ? $backup['tenant_name'] : '-'; ?></td>
                                    <td><?php echo number_format($backup['file_size'] / 1024, 2); ?> KB</td>
                                    <td><?php echo $backup['username']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($backup['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo site_url('admin/backup?download=' . $backup['id']); ?>" 
                                           class="btn btn-success btn-xs" title="Download">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <a href="<?php echo site_url('admin/backup?delete=' . $backup['id']); ?>" 
                                           class="btn btn-danger btn-xs" 
                                           onclick="return confirm('Delete this backup?')" 
                                           title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Backup Info -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Backup Information</h3>
            </div>
            <div class="box-body">
                <ul>
                    <li><strong>Master Database:</strong> Contains tenants, users, and system configuration</li>
                    <li><strong>Tenant Databases:</strong> Contains merchant-specific data (products, orders, customers, etc.)</li>
                    <li><strong>Backup Location:</strong> <code>backups/</code> folder in application root</li>
                    <li><strong>Recommended:</strong> Create daily backups and store them in a secure location</li>
                    <li><strong>Retention:</strong> Keep at least 30 days of backups</li>
                </ul>
            </div>
        </div>
    </section>
</div>
