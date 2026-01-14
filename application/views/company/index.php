<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            Company Information
            <small>Manage your company details</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Company</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <?php echo isset($company_data['id']) && !empty($company_data['company_name']) ? 'Update' : 'Create'; ?> Company Information
                        </h3>
                    </div>

                    <?php echo form_open('company/index', array('id' => 'companyForm')); ?>
                    
                    <div class="box-body">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>', '</div>'); ?>

                        <?php if($this->session->flashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $this->session->flashdata('success'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if($this->session->flashdata('errors')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $this->session->flashdata('errors'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="<?php echo isset($company_data['company_name']) ? $company_data['company_name'] : ''; ?>" 
                                           placeholder="Enter company name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo isset($company_data['phone']) ? $company_data['phone'] : ''; ?>" 
                                       placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="2" 
                                              placeholder="Enter company address" required><?php echo isset($company_data['address']) ? $company_data['address'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="<?php echo isset($company_data['country']) ? $company_data['country'] : 'Algeria'; ?>" 
                                           placeholder="Enter country">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Currency</label>
                                    <select class="form-control" id="currency" name="currency">
                                        <?php foreach ($currency_symbols as $key => $value): ?>
                                            <option value="<?php echo $key ?>" 
                                                <?php if(isset($company_data['currency']) && $company_data['currency'] == $key) { echo 'selected'; } ?>>
                                                <?php echo $value ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_charge_value">Service Charge (%)</label>
                                    <input type="number" class="form-control" id="service_charge_value" name="service_charge_value" 
                                           value="<?php echo isset($company_data['service_charge_value']) ? $company_data['service_charge_value'] : 0; ?>" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vat_charge_value">VAT Charge (%)</label>
                                    <input type="number" class="form-control" id="vat_charge_value" name="vat_charge_value" 
                                           value="<?php echo isset($company_data['vat_charge_value']) ? $company_data['vat_charge_value'] : 0; ?>" 
                                           step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Invoice Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="3" 
                                              placeholder="Enter message to display on invoices"><?php echo isset($company_data['message']) ? $company_data['message'] : 'Thank you for your business!'; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> 
                            <?php echo isset($company_data['id']) && !empty($company_data['company_name']) ? 'Update' : 'Create'; ?> Company
                        </button>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
