<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-list-alt"></i> Activity Logs
            <small>Monitor user activities</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Logs</li>
        </ol>
    </section>

    <section class="content">
        <!-- Statistics -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $stats['today_logins']; ?></h3>
                        <p>Today's Logins</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-sign-in"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?php echo $stats['today_actions']; ?></h3>
                        <p>Today's Activities</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tasks"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo number_format($stats['total_logs']); ?></h3>
                        <p>Total Logs</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-database"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo $stats['failed_logins']; ?></h3>
                        <p>Failed Logins (Today)</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
            </div>
            <div class="box-body">
                <form method="get" action="<?php echo site_url('index.php/admin/logs'); ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    <?php foreach($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo ($this->input->get('user_id') == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo $user['username'] . ' (' . $user['email'] . ')'; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Merchant</label>
                                <select name="tenant_id" class="form-control">
                                    <option value="">All Merchants</option>
                                    <?php foreach($tenants as $tenant): ?>
                                    <option value="<?php echo $tenant['id']; ?>" <?php echo ($this->input->get('tenant_id') == $tenant['id']) ? 'selected' : ''; ?>>
                                        <?php echo $tenant['tenant_name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Action Type</label>
                                <select name="action_type" class="form-control">
                                    <option value="">All Actions</option>
                                    <?php foreach($action_types as $action): ?>
                                    <option value="<?php echo $action; ?>" <?php echo ($this->input->get('action_type') == $action) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($action); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="<?php echo $this->input->get('date'); ?>">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Recent Activities (Last 500)</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Merchant</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No activity logs found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($logs as $index => $log): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td>
                                        <strong><?php echo $log['username']; ?></strong><br>
                                        <small class="text-muted"><?php echo $log['email']; ?></small>
                                    </td>
                                    <td><?php echo $log['tenant_name'] ? $log['tenant_name'] : '<span class="text-muted">N/A</span>'; ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'default';
                                        switch($log['action_type']) {
                                            case 'login': $badge_class = 'success'; break;
                                            case 'logout': $badge_class = 'info'; break;
                                            case 'create': $badge_class = 'primary'; break;
                                            case 'update': $badge_class = 'warning'; break;
                                            case 'delete': $badge_class = 'danger'; break;
                                            case 'failed_login': $badge_class = 'danger'; break;
                                        }
                                        ?>
                                        <span class="label label-<?php echo $badge_class; ?>">
                                            <?php echo strtoupper($log['action_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $log['description']; ?></td>
                                    <td>
                                        <code><?php echo $log['ip_address']; ?></code>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
