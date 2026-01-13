<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-cogs"></i> System Settings
            <small>Configure global system parameters</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Settings</li>
        </ol>
    </section>

    <section class="content">
        <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?php echo $this->session->flashdata('success'); ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- System Statistics -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Merchants</span>
                        <span class="info-box-number"><?php echo $stats['total_tenants']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Merchants</span>
                        <span class="info-box-number"><?php echo $stats['active_tenants']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Users</span>
                        <span class="info-box-number"><?php echo $stats['total_users']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-hdd-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Database Size</span>
                        <span class="info-box-number"><?php echo $stats['database_size']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="<?php echo site_url('index.php/admin/settings'); ?>">
            <div class="row">
                <!-- General Settings -->
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-info-circle"></i> General Settings</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Application Name</label>
                                <input type="text" name="app_name" class="form-control" 
                                    value="<?php echo isset($settings['app_name']) ? $settings['app_name'] : 'Inventory System'; ?>">
                            </div>

                            <div class="form-group">
                                <label>System Email</label>
                                <input type="email" name="app_email" class="form-control" 
                                    value="<?php echo isset($settings['app_email']) ? $settings['app_email'] : ''; ?>">
                                <small class="text-muted">Email for system notifications</small>
                            </div>

                            <div class="form-group">
                                <label>Timezone</label>
                                <select name="timezone" class="form-control">
                                    <option value="Africa/Algiers" <?php echo (isset($settings['timezone']) && $settings['timezone'] == 'Africa/Algiers') ? 'selected' : ''; ?>>Africa/Algiers (WAT)</option>
                                    <option value="Europe/Paris" <?php echo (isset($settings['timezone']) && $settings['timezone'] == 'Europe/Paris') ? 'selected' : ''; ?>>Europe/Paris (CET)</option>
                                    <option value="America/New_York" <?php echo (isset($settings['timezone']) && $settings['timezone'] == 'America/New_York') ? 'selected' : ''; ?>>America/New_York (EST)</option>
                                    <option value="UTC" <?php echo (isset($settings['timezone']) && $settings['timezone'] == 'UTC') ? 'selected' : ''; ?>>UTC</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date Format</label>
                                <select name="date_format" class="form-control">
                                    <option value="Y-m-d" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'Y-m-d') ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                    <option value="d/m/Y" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'd/m/Y') ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                    <option value="m/d/Y" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'm/d/Y') ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Currency</label>
                                <select name="currency" class="form-control">
                                    <option value="DZD" <?php echo (isset($settings['currency']) && $settings['currency'] == 'DZD') ? 'selected' : ''; ?>>DZD (Algerian Dinar)</option>
                                    <option value="USD" <?php echo (isset($settings['currency']) && $settings['currency'] == 'USD') ? 'selected' : ''; ?>>USD (US Dollar)</option>
                                    <option value="EUR" <?php echo (isset($settings['currency']) && $settings['currency'] == 'EUR') ? 'selected' : ''; ?>>EUR (Euro)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Limits & Security -->
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-shield"></i> System Limits & Security</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label>Maximum Merchants</label>
                                <input type="number" name="max_tenants" class="form-control" 
                                    value="<?php echo isset($settings['max_tenants']) ? $settings['max_tenants'] : '100'; ?>">
                                <small class="text-muted">Maximum number of merchants allowed</small>
                            </div>

                            <div class="form-group">
                                <label>Max Users Per Merchant</label>
                                <input type="number" name="max_users_per_tenant" class="form-control" 
                                    value="<?php echo isset($settings['max_users_per_tenant']) ? $settings['max_users_per_tenant'] : '50'; ?>">
                            </div>

                            <div class="form-group">
                                <label>Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" class="form-control" 
                                    value="<?php echo isset($settings['session_timeout']) ? $settings['session_timeout'] : '30'; ?>">
                            </div>

                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="maintenance_mode" value="1" 
                                            <?php echo (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : ''; ?>>
                                        <strong>Maintenance Mode</strong>
                                    </label>
                                    <small class="text-muted d-block">Block all tenant access (admin still accessible)</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="allow_registration" value="1" 
                                            <?php echo (isset($settings['allow_registration']) && $settings['allow_registration'] == '1') ? 'checked' : ''; ?>>
                                        <strong>Allow Self-Registration</strong>
                                    </label>
                                    <small class="text-muted d-block">Allow new merchants to register themselves</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Settings -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-database"></i> Backup Settings</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="backup_enabled" value="1" 
                                            <?php echo (isset($settings['backup_enabled']) && $settings['backup_enabled'] == '1') ? 'checked' : ''; ?>>
                                        <strong>Enable Automatic Backups</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Backup Frequency</label>
                                <select name="backup_frequency" class="form-control">
                                    <option value="daily" <?php echo (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'daily') ? 'selected' : ''; ?>>Daily</option>
                                    <option value="weekly" <?php echo (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="monthly" <?php echo (isset($settings['backup_frequency']) && $settings['backup_frequency'] == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                            <a href="<?php echo site_url('admin/dashboard'); ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
