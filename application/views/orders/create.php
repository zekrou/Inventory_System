<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Manage
      <small>Orders</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Orders</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Add Order</h3>
          </div>
          
          <form role="form" action="<?php echo base_url('orders/create') ?>" method="post" class="form-horizontal">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Date: <?php echo date('Y-m-d') ?></label>
                </div>
                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Time: <?php echo date('h:i a') ?></label>
                </div>

                <div class="col-md-6 col-xs-12 pull pull-left">

                  <!-- Customer Selection -->
                  <div class="form-group">
                    <label for="customer_select" class="col-sm-5 control-label" style="text-align:left;">
                      Select Customer <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-7">
                      <select class="form-control select_group" id="customer_select" name="customer_id" style="width:100%;" onchange="loadCustomerData()" required>
                        <option value="">-- Select Customer --</option>
                        <option value="new">+ Add New Customer (Walk-in)</option>
                        <?php if(isset($customers) && is_array($customers)): ?>
                          <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id'] ?>" 
                                    data-type="<?php echo $customer['customer_type'] ?>"
                                    data-phone="<?php echo $customer['phone'] ?>"
                                    data-address="<?php echo $customer['address'] ?>">
                              <?php echo $customer['customer_name'] ?> (<?php echo $customer['customer_code'] ?>)
                              <?php if($customer['customer_type'] == 'super_wholesale'): ?>
                                - Super Gros
                              <?php elseif($customer['customer_type'] == 'wholesale'): ?>
                                - Gros
                              <?php else: ?>
                                - Détail
                              <?php endif; ?>
                            </option>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </select>
                    </div>
                  </div>

                  <!-- Hidden field for customer type -->
                  <input type="hidden" id="customer_type" name="customer_type" value="retail">

                  <div class="form-group">
                    <label for="customer_name" class="col-sm-5 control-label" style="text-align:left;">Customer Name</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter Customer Name" autocomplete="off" onblur="checkDuplicateCustomer()" required />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_phone" class="col-sm-5 control-label" style="text-align:left;">Customer Phone</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Enter Customer Phone" autocomplete="off" onblur="checkDuplicateCustomer()" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_address" class="col-sm-5 control-label" style="text-align:left;">Customer Address</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Enter Customer Address" autocomplete="off">
                    </div>
                  </div>

                  <!-- Duplicate Warning Alert -->
                  <div id="duplicate_warning" class="col-sm-12" style="display:none; margin-bottom: 15px;">
                    <div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" onclick="$('#duplicate_warning').hide()"><span>&times;</span></button>
                      <h4><i class="fa fa-warning"></i> Similar Customer Found!</h4>
                      <p id="duplicate_message">A customer with similar information already exists.</p>
                      <div id="duplicate_suggestions"></div>
                    </div>
                  </div>

                  <!-- Customer Type Display -->
                  <div class="form-group" id="customer_type_display" style="display:none;">
                    <label class="col-sm-5 control-label" style="text-align:left;">Customer Type</label>
                    <div class="col-sm-7">
                      <p class="form-control-static">
                        <span id="customer_type_badge" class="label label-info">Détail</span>
                        <small class="text-muted" id="pricing_info"> - Retail pricing applied</small>
                      </p>
                    </div>
                  </div>

                </div>
                
                <br /> <br/>
                <table class="table table-bordered" id="product_info_table">
                  <thead>
                    <tr>
                      <th style="width:50%">Product</th>
                      <th style="width:15%">Qty</th>
                      <th style="width:15%">Rate</th>
                      <th style="width:15%">Amount</th>
                      <th style="width:5%"><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                    </tr>
                  </thead>

                   <tbody>
                     <tr id="row_1">
                       <td>
                        <select class="form-control select_group product" data-row-id="row_1" id="product_1" name="product[]" style="width:100%;" onchange="getProductData(1)" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $k => $v): ?>
                              <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                            <?php endforeach ?>
                          </select>
                        </td>
                        <td><input type="number" name="qty[]" id="qty_1" class="form-control" required onkeyup="getTotal(1)"></td>
                        <td>
                          <input type="text" name="rate[]" id="rate_1" class="form-control" disabled autocomplete="off">
                          <input type="hidden" name="rate_value[]" id="rate_value_1" class="form-control" autocomplete="off">
                        </td>
                        <td>
                          <input type="text" name="amount[]" id="amount_1" class="form-control" disabled autocomplete="off">
                          <input type="hidden" name="amount_value[]" id="amount_value_1" class="form-control" autocomplete="off">
                        </td>
                        <td><button type="button" class="btn btn-default" onclick="removeRow('1')"><i class="fa fa-close"></i></button></td>
                     </tr>
                   </tbody>
                </table>

                <br /> <br/>

                <div class="col-md-6 col-xs-12 pull pull-right">

                  <div class="form-group">
                    <label for="gross_amount" class="col-sm-5 control-label">Gross Amount</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="gross_amount" name="gross_amount" disabled autocomplete="off">
                      <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="discount" class="col-sm-5 control-label">Discount</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount" onkeyup="subAmount()" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="net_amount" class="col-sm-5 control-label"><strong>Net Amount</strong></label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control input-lg" id="net_amount" name="net_amount" disabled autocomplete="off" style="font-weight:bold; font-size:18px;">
                      <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value" autocomplete="off">
                    </div>
                  </div>

                  <hr>

                  <!-- Payment Section -->
                  <div class="form-group">
                    <label for="paid_amount" class="col-sm-5 control-label">Amount Paid</label>
                    <div class="col-sm-7">
                      <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" placeholder="0.00" onkeyup="calculateDue()" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="due_amount" class="col-sm-5 control-label">Due Amount (Remaining)</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="due_amount" name="due_amount" disabled autocomplete="off" style="background-color: #fff3cd;">
                      <input type="hidden" id="due_amount_value" name="due_amount_value" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="payment_method" class="col-sm-5 control-label">Payment Method</label>
                    <div class="col-sm-7">
                      <select class="form-control" id="payment_method" name="payment_method">
                        <option value="">-- Select --</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="mobile_payment">Mobile Payment</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="payment_notes" class="col-sm-5 control-label">Payment Notes</label>
                    <div class="col-sm-7">
                      <textarea class="form-control" id="payment_notes" name="payment_notes" rows="2" placeholder="Optional payment notes"></textarea>
                    </div>
                  </div>

                </div>
              </div>

              <div class="box-footer">
                <input type="hidden" name="service_charge_rate" value="0" autocomplete="off">
                <input type="hidden" name="service_charge_value" value="0" autocomplete="off">
                <input type="hidden" name="vat_charge_rate" value="0" autocomplete="off">
                <input type="hidden" name="vat_charge_value" value="0" autocomplete="off">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="<?php echo base_url('orders/') ?>" class="btn btn-warning">Back</a>
              </div>
            </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Use Selected Customer Modal -->
<div class="modal fade" id="useCustomerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #f39c12; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-user"></i> Use Existing Customer?</h4>
      </div>
      <div class="modal-body">
        <p>Do you want to use this existing customer information?</p>
        <div id="selected_customer_info" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">
          <!-- Customer info will be populated here -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No, Create New Customer</button>
        <button type="button" class="btn btn-success" onclick="useExistingCustomer()"><i class="fa fa-check"></i> Yes, Use This Customer</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  // ... JavaScript code remains unchanged ...
</script>

<style>
#duplicate_warning .list-group-item {
  cursor: pointer;
  transition: all 0.3s;
}
#duplicate_warning .list-group-item:hover {
  background-color: #f0f0f0;
  border-left: 4px solid #f39c12;
}
#duplicate_warning .list-group-item-heading {
  color: #333;
  font-size: 16px;
  margin-bottom: 5px;
}
</style>
