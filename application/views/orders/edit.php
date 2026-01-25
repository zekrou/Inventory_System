<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit"></i> Edit Order
      <small>Modify order details</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('orders'); ?>">Orders</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <div id="messages">
          <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('success'); ?>
            </div>
          <?php elseif($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('error'); ?>
            </div>
          <?php endif; ?>
        </div>

        <?php echo form_open('orders/update/' . $order_data['order_id'], array('id' => 'editOrderForm')); ?>

        <!-- Customer Information -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user"></i> Customer Information</h3>
          </div>
          <div class="box-body">

            <!-- Select Customer Dropdown -->
            <div class="form-group">
              <label>Select Customer <span class="text-danger">*</span></label>
              <select class="form-control select2-customer" id="customer_id" name="customer_id" style="width:100%" required>
                <option value="">-- Select Customer or Create New --</option>

                <?php foreach ($customers as $customer): ?>
                  <option value="<?php echo $customer['id']; ?>"
                          data-name="<?php echo htmlspecialchars($customer['customer_name']); ?>"
                          data-phone="<?php echo htmlspecialchars($customer['phone']); ?>"
                          data-address="<?php echo htmlspecialchars($customer['address']); ?>"
                          data-type="<?php echo htmlspecialchars($customer['customer_type']); ?>"
                          <?php echo ($order_data['order_customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                    <?php echo $customer['customer_name']; ?> - <?php echo $customer['phone']; ?> (<?php echo ucfirst($customer['customer_type']); ?>)
                  </option>
                <?php endforeach; ?>

                <option value="new" style="background-color:#d4edda;font-weight:bold;color:#155724;">
                  + Create New Customer
                </option>
              </select>
            </div>

            <!-- New Customer Form (Hidden by default) -->
            <div id="newCustomerForm" style="display:none;margin-top:20px;padding:15px;background:#f9f9f9;border:2px solid #3c8dbc;border-radius:5px;">
              <h4 style="margin-top:0;color:#3c8dbc;"><i class="fa fa-plus-circle"></i> New Customer Details</h4>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Customer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new_customer_name" name="new_customer_name" placeholder="Full Name">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new_customer_phone" name="new_customer_phone" placeholder="Phone Number">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Address</label>
                <textarea class="form-control" id="new_customer_address" name="new_customer_address" rows="2" placeholder="Full Address"></textarea>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" id="new_customer_email" name="new_customer_email" placeholder="Email Address">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Customer Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="new_customer_type" name="new_customer_type">
                      <option value="retail" selected>Retail (Normal Price)</option>
                      <option value="wholesale">Wholesale</option>
                      <option value="superwholesale">Super Wholesale</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Existing Customer Info Display -->
            <div id="existingCustomerInfo"
                 style="display:<?php echo (!empty($order_data['order_customer_id']) ? 'block' : 'none'); ?>;margin-top:15px;padding:15px;background:#e8f5e9;border-left:4px solid #4caf50;">
              <h4 style="margin-top:0;color:#2e7d32;"><i class="fa fa-check-circle"></i> Selected Customer</h4>
              <div class="row">
                <div class="col-md-6">
                  <p style="margin:5px 0;"><strong>Name:</strong> <span id="displayCustomerName"><?php echo $order_data['order_customer_name']; ?></span><br>
                    <strong>Phone:</strong> <span id="displayCustomerPhone"><?php echo $order_data['order_customer_phone']; ?></span><br>
                    <strong>Address:</strong> <span id="displayCustomerAddress"><?php echo $order_data['order_customer_address']; ?></span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p style="margin:5px 0;">
                    <strong>Default Type:</strong>
                    <span id="displayCustomerType" class="label label-success"><?php echo strtoupper($order_data['order_customer_type']); ?></span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Price Type Override -->
            <div id="priceTypeSection"
                 style="display:<?php echo (!empty($order_data['order_customer_id']) ? 'block' : 'none'); ?>;margin-top:20px;padding:15px;background:#fff3cd;border-left:4px solid #ffc107;">
              <h4 style="margin-top:0;color:#856404;"><i class="fa fa-exchange"></i> Price Type for This Order</h4>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Select Price Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="customer_type_override" name="customer_type_override">
                      <option value="retail"
                        <?php echo (!empty($order_data['order_price_type_override']) && $order_data['order_price_type_override'] == 'retail') ||
                                 (empty($order_data['order_price_type_override']) && $order_data['order_customer_type'] == 'retail') ? 'selected' : ''; ?>>
                        Retail (Normal Price)
                      </option>
                      <option value="wholesale"
                        <?php echo (!empty($order_data['order_price_type_override']) && $order_data['order_price_type_override'] == 'wholesale') ||
                                 (empty($order_data['order_price_type_override']) && $order_data['order_customer_type'] == 'wholesale') ? 'selected' : ''; ?>>
                        Wholesale
                      </option>
                      <option value="superwholesale"
                        <?php echo (!empty($order_data['order_price_type_override']) && $order_data['order_price_type_override'] == 'superwholesale') ||
                                 (empty($order_data['order_price_type_override']) && $order_data['order_customer_type'] == 'superwholesale') ? 'selected' : ''; ?>>
                        Super Wholesale
                      </option>
                    </select>

                    <input type="hidden" id="customer_original_type" value="<?php echo $order_data['order_customer_type']; ?>">
                    <small class="text-muted"><i class="fa fa-info-circle"></i> You can change the price type for this order</small>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group" id="overrideReasonGroup" style="display:<?php echo (!empty($order_data['order_override_reason']) ? 'block' : 'none'); ?>;">
                    <label>Reason for Change</label>
                    <input type="text" class="form-control" id="override_reason" name="override_reason"
                           value="<?php echo !empty($order_data['order_override_reason']) ? $order_data['order_override_reason'] : ''; ?>"
                           placeholder="Ex: Special promotion, VIP customer...">
                    <small class="text-muted">Explain why you changed the price type</small>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Products Section -->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes"></i> Products</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-success btn-sm" id="addRow">
                <i class="fa fa-plus"></i> Add Product
              </button>
            </div>
          </div>

          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover" id="productInfoTable">
                <thead style="background:#f4f4f4">
                  <tr>
                    <th style="width:35%">Product</th>
                    <th style="width:10%">Available</th>
                    <th style="width:10%">Quantity</th>
                    <th style="width:15%">Unit Price</th>
                    <th style="width:10%">Loss</th>
                    <th style="width:15%">Amount</th>
                    <th style="width:5%"><i class="fa fa-trash"></i></th>
                  </tr>
                </thead>
                <tbody>
                <?php if (isset($order_data['order_item'])): ?>
                  <?php $x = 1; foreach ($order_data['order_item'] as $val): ?>
                    <?php
                      $product = null;
                      foreach ($products as $p) {
                        if ($p['id'] == $val['product_id']) { $product = $p; break; }
                      }
                    ?>
                    <tr id="row<?php echo $x; ?>">
                      <td>
                        <select class="form-control select2-product" name="product[]" data-row-id="<?php echo $x; ?>"
                                id="product<?php echo $x; ?>" onchange="getProductData(<?php echo $x; ?>)" style="width:100%" required>
                          <option value="">-- Select Product --</option>
                          <?php foreach ($products as $p): ?>
                            <option value="<?php echo $p['id']; ?>"
                              data-sku="<?php echo $p['sku']; ?>"
                              data-qty="<?php echo $p['qty']; ?>"
                              data-name="<?php echo htmlspecialchars($p['name']); ?>"
                              data-price-retail="<?php echo isset($p['price_retail']) ? $p['price_retail'] : $p['price_default']; ?>"
                              data-price-wholesale="<?php echo isset($p['price_wholesale']) ? $p['price_wholesale'] : $p['price_default']; ?>"
                              data-price-super-wholesale="<?php echo isset($p['price_superwholesale']) ? $p['price_superwholesale'] : $p['price_default']; ?>"
                              <?php echo ($val['product_id'] == $p['id']) ? 'selected' : ''; ?>>
                              <?php echo $p['name']; ?> (<?php echo $p['sku']; ?>) - Stock: <?php echo $p['qty']; ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <td>
                        <span class="badge bg-green available-qty" id="availableQty<?php echo $x; ?>">
                          <?php echo ($product ? $product['qty'] : 0); ?>
                        </span>
                      </td>

                      <td>
                        <input type="number" name="qty[]" id="qty<?php echo $x; ?>"
                               value="<?php echo $val['qty']; ?>"
                               class="form-control"
                               onkeyup="getTotal(<?php echo $x; ?>); checkPriceLoss(<?php echo $x; ?>);"
                               onchange="getTotal(<?php echo $x; ?>); checkPriceLoss(<?php echo $x; ?>);"
                               min="1" required>
                      </td>

                      <td>
                        <input type="number" step="0.01"
                               name="rate_value[]" id="rate_value<?php echo $x; ?>"
                               value="<?php echo $val['rate']; ?>"
                               class="form-control"
                               onkeyup="getTotal(<?php echo $x; ?>); checkPriceLoss(<?php echo $x; ?>);"
                               onchange="getTotal(<?php echo $x; ?>); checkPriceLoss(<?php echo $x; ?>);"
                               required>

                        <input type="hidden" id="expected_price<?php echo $x; ?>" value="0">
                        <input type="hidden" id="product_cost<?php echo $x; ?>" value="0">
                      </td>

                      <td class="text-center">
                        <span id="loss_badge<?php echo $x; ?>" class="label label-default">...</span>
                      </td>

                      <td>
                        <input type="text" name="amount[]" id="amount<?php echo $x; ?>"
                               value="<?php echo $val['amount']; ?>"
                               class="form-control" readonly>
                        <input type="hidden" name="amount_value[]" id="amount_value<?php echo $x; ?>"
                               value="<?php echo $val['amount']; ?>">
                      </td>

                      <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(<?php echo $x; ?>)">
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php $x++; endforeach; ?>
                <?php endif; ?>
                </tbody>
              </table>
            </div>

            <div id="lossWarningBox" class="alert alert-warning" style="display:none;margin-top:10px;">
              <strong>Attention:</strong>
              <span id="lossWarningText"></span>
            </div>
            <input type="hidden" name="loss_reason" id="loss_reason" value="">
          </div>
        </div>

        <!-- Payment Section -->
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-money"></i> Payment</h3>
          </div>

          <div class="box-body">
            <div class="row">
              <div class="col-md-6">

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-5 control-label">Gross Amount</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="gross_amount" name="gross_amount" readonly>
                    <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value">
                  </div>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="form-group">
                  <label for="discount" class="col-sm-5 control-label">Discount</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="discount" name="discount" onkeyup="subAmount()" min="0" step="0.01"
                           value="<?php echo $order_data['order_discount']; ?>">
                  </div>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="form-group">
                  <label for="net_amount" class="col-sm-5 control-label" style="font-size:16px;"><strong>Total Amount</strong></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="net_amount" name="net_amount" readonly style="font-size:18px;font-weight:bold;color:#00a65a;">
                    <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value">
                  </div>
                </div>
                <div class="clearfix"></div>

              </div>

              <div class="col-md-6">
                <div class="alert alert-info">
                  <h4><i class="fa fa-info-circle"></i> Current Payment Status</h4>
                  <table class="table table-condensed" style="background:white;margin-bottom:0;">
                    <tr>
                      <th width="50%">Paid Amount</th>
                      <td class="text-success"><strong><?php echo number_format($order_data['order_paid_amount'], 2); ?> DZD</strong></td>
                    </tr>
                    <tr>
                      <th>Due Amount</th>
                      <td class="text-danger"><strong><?php echo number_format($order_data['order_due_amount'], 2); ?> DZD</strong></td>
                    </tr>
                    <tr>
                      <th>Payment Status</th>
                      <td>
                        <?php if ($order_data['order_paid_status'] == 1): ?>
                          <span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span>
                        <?php elseif ($order_data['order_paid_status'] == 3): ?>
                          <span class="label label-warning"><i class="fa fa-clock-o"></i> Partially Paid</span>
                        <?php else: ?>
                          <span class="label label-danger"><i class="fa fa-times"></i> Unpaid</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  </table>
                </div>

                <div class="alert alert-warning">
                  <i class="fa fa-lightbulb-o"></i> <strong>Note:</strong>
                  To add/modify payments, use the <strong>View Details</strong> button from the orders list.
                </div>

                <!-- Hidden fields to maintain payment data -->
                <input type="hidden" name="paid_amount" value="<?php echo $order_data['order_paid_amount']; ?>">
                <input type="hidden" name="payment_method" value="<?php echo $order_data['order_payment_method']; ?>">
                <input type="hidden" name="payment_notes" value="<?php echo $order_data['order_payment_notes']; ?>">
              </div>
            </div>
          </div>

          <div class="box-footer">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save Changes</button>
            <a href="<?php echo base_url('orders'); ?>" class="btn btn-warning btn-lg"><i class="fa fa-arrow-left"></i> Back to Orders</a>
          </div>
        </div>

        <?php echo form_close(); ?>

      </div>
    </div>
  </section>
</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
  .available-qty { font-size: 16px; padding: 8px 12px; }
  #productInfoTable input[type=number] { text-align: center; }
</style>

<script type="text/javascript">
  var rowNum = <?php echo (isset($order_data['order_item']) ? count($order_data['order_item']) : 1); ?>;
  var baseurl = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $('#ordersMainNav').addClass('active');
    $('#manageOrdersNav').addClass('active');

    initCustomerSelect2();
    initProductSelect2();

    subAmount();

    $('#customer_id').on('change', handleCustomerSelection);

    $('#customer_type_override').on('change', function() {
      var selectedType = $(this).val();
      var originalType = $('#customer_original_type').val();

      if (originalType && selectedType && selectedType !== originalType) {
        $('#overrideReasonGroup').slideDown();
      } else {
        $('#overrideReasonGroup').slideUp();
        $('#override_reason').val('');
      }
      updateAllProductPrices(selectedType);
    });

    $('#addRow').on('click', function() {
      addRow();
    });

    // validate
    $('#editOrderForm').on('submit', function(e) {
      var netamount = parseFloat($('#net_amount_value').val()) || 0;
      if (netamount <= 0) {
        e.preventDefault();
        alert('Please add at least one product!');
        return false;
      }

      // Validate stock
      var valid = true;
      $('#productInfoTable tbody tr').each(function() {
        var rowid = $(this).attr('id').replace('row','');
        var qty = parseFloat($('#qty' + rowid).val()) || 0;
        var available = parseFloat($('#availableQty' + rowid).text()) || 0;
        if (qty > available) {
          alert('Insufficient stock for product in row ' + rowid);
          valid = false;
          return false;
        }
      });

      if (!valid) {
        e.preventDefault();
        return false;
      }
      return true;
    });

    // trigger initial loss check for existing rows
    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row','');
      // populate expected+cost from selected option
      var productid = $('#product' + rowid).val();
      if (productid) {
        var opt = $('#product' + rowid + ' option:selected');
        var expected = computeExpectedPriceFromOption(opt);
        $('#expected_price' + rowid).val(expected);
        fetchProductCost(rowid, productid);
      }
      checkPriceLoss(rowid);
    });
  });

  function initCustomerSelect2() {
    $('.select2-customer').select2({
      placeholder: "Search customer by name or phone...",
      allowClear: true,
      width: '100%'
    });
  }

  function initProductSelect2() {
    $('.select2-product').select2({
      placeholder: "Type to search product...",
      allowClear: true,
      width: '100%'
    });
  }

  function handleCustomerSelection() {
    var selectedValue = $('#customer_id').val();

    if (selectedValue === 'new') {
      $('#newCustomerForm').slideDown();
      $('#existingCustomerInfo').slideUp();
      $('#priceTypeSection').slideDown();

      $('#new_customer_name').prop('required', true);
      $('#new_customer_phone').prop('required', true);
      $('#new_customer_type').prop('required', true);

      $('#customer_type_override').val('retail');
      $('#customer_original_type').val('retail');
      $('#overrideReasonGroup').hide();
      $('#override_reason').val('');

      updateAllProductPrices('retail');
      return;
    }

    if (selectedValue) {
      var selectedOption = $('#customer_id option:selected');
      var name = selectedOption.data('name') || '';
      var phone = selectedOption.data('phone') || '';
      var address = selectedOption.data('address') || '';
      var type = selectedOption.data('type') || 'retail';

      $('#displayCustomerName').text(name);
      $('#displayCustomerPhone').text(phone);
      $('#displayCustomerAddress').text(address || 'N/A');
      $('#displayCustomerType').text(type.toUpperCase());

      $('#existingCustomerInfo').slideDown();
      $('#newCustomerForm').slideUp();
      $('#priceTypeSection').slideDown();

      $('#customer_type_override').val(type);
      $('#customer_original_type').val(type);

      $('#overrideReasonGroup').hide();
      $('#override_reason').val('');

      $('#new_customer_name').prop('required', false);
      $('#new_customer_phone').prop('required', false);
      $('#new_customer_type').prop('required', false);

      updateAllProductPrices(type);
      return;
    }

    $('#newCustomerForm').slideUp();
    $('#existingCustomerInfo').slideUp();
    $('#priceTypeSection').slideUp();

    $('#new_customer_name').prop('required', false);
    $('#new_customer_phone').prop('required', false);
    $('#new_customer_type').prop('required', false);
  }

  function addRow() {
    rowNum++;
    var html = '';
    html += '<tr id="row' + rowNum + '">';
    html += '<td>';
    html += '<select class="form-control select2-product" name="product[]" id="product' + rowNum + '" onchange="getProductData(' + rowNum + ')" style="width:100%" required>';
    html += '<option value="">-- Select Product --</option>';
    <?php foreach ($products as $product): ?>
      html += '<option value="<?php echo $product['id']; ?>" ' +
              'data-sku="<?php echo $product['sku']; ?>" ' +
              'data-qty="<?php echo $product['qty']; ?>" ' +
              'data-name="<?php echo htmlspecialchars($product['name']); ?>" ' +
              'data-price-retail="<?php echo isset($product['price_retail']) ? $product['price_retail'] : $product['price_default']; ?>" ' +
              'data-price-wholesale="<?php echo isset($product['price_wholesale']) ? $product['price_wholesale'] : $product['price_default']; ?>" ' +
              'data-price-super-wholesale="<?php echo isset($product['price_superwholesale']) ? $product['price_superwholesale'] : $product['price_default']; ?>">' +
              '<?php echo $product['name']; ?> (<?php echo $product['sku']; ?>) - Stock: <?php echo $product['qty']; ?>' +
              '</option>';
    <?php endforeach; ?>
    html += '</select>';
    html += '</td>';

    html += '<td><span class="badge bg-green available-qty" id="availableQty' + rowNum + '">0</span></td>';

    html += '<td><input type="number" name="qty[]" id="qty' + rowNum + '" class="form-control" min="1" value="1" ' +
            'onkeyup="getTotal(' + rowNum + '); checkPriceLoss(' + rowNum + ');" ' +
            'onchange="getTotal(' + rowNum + '); checkPriceLoss(' + rowNum + ');" required></td>';

    html += '<td>' +
            '<input type="number" step="0.01" name="rate_value[]" id="rate_value' + rowNum + '" class="form-control" value="0" ' +
            'onkeyup="getTotal(' + rowNum + '); checkPriceLoss(' + rowNum + ');" ' +
            'onchange="getTotal(' + rowNum + '); checkPriceLoss(' + rowNum + ');" required>' +
            '<input type="hidden" id="expected_price' + rowNum + '" value="0">' +
            '<input type="hidden" id="product_cost' + rowNum + '" value="0">' +
            '</td>';

    html += '<td class="text-center"><span id="loss_badge' + rowNum + '" class="label label-default">...</span></td>';

    html += '<td>' +
            '<input type="text" name="amount[]" id="amount' + rowNum + '" class="form-control" readonly>' +
            '<input type="hidden" name="amount_value[]" id="amount_value' + rowNum + '">' +
            '</td>';

    html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(' + rowNum + ')"><i class="fa fa-trash"></i></button></td>';
    html += '</tr>';

    $('#productInfoTable tbody').append(html);
    initProductSelect2();
  }

  function removeRow(rowid) {
    if ($('#productInfoTable tbody tr').length <= 1) {
      alert('Cannot remove the last row!');
      return;
    }
    $('#row' + rowid).remove();
    subAmount();
    updateLossWarning();
  }

  function getCustomerType() {
    return $('#customer_type_override').val() || 'retail';
  }

  function computeExpectedPriceFromOption(selectedOption) {
    var customerType = getCustomerType();
    var typeMap = { superwholesale: 'super-wholesale', wholesale: 'wholesale', retail: 'retail' };
    var mappedType = typeMap[customerType] || 'retail';

    var priceAttr = 'data-price-' + mappedType;
    var expected = selectedOption.attr(priceAttr);

    if (!expected) expected = selectedOption.attr('data-price-retail');
    expected = expected ? parseFloat(expected) : 0;

    return expected;
  }

  function getProductData(rowid) {
    var productid = $('#product' + rowid).val();
    var selectedOption = $('#product' + rowid + ' option:selected');

    if (!productid) {
      $('#availableQty' + rowid).text('0');
      $('#rate_value' + rowid).val('0');
      $('#amount' + rowid).val('0.00 DZD');
      $('#amount_value' + rowid).val('0.00');
      $('#expected_price' + rowid).val('0');
      $('#product_cost' + rowid).val('0');
      checkPriceLoss(rowid);
      return;
    }

    var qty = parseFloat(selectedOption.attr('data-qty')) || 0;
    $('#availableQty' + rowid).text(qty);
    $('#qty' + rowid).attr('max', qty);

    // expected price for loss comparison
    var expected = computeExpectedPriceFromOption(selectedOption);
    $('#expected_price' + rowid).val(expected);

    // set default unit price based on current type (still editable)
    var customerType = getCustomerType();
    var typeMap = { superwholesale: 'super-wholesale', wholesale: 'wholesale', retail: 'retail' };
    var mappedType = typeMap[customerType] || 'retail';
    var priceAttr = 'data-price-' + mappedType;

    var price = selectedOption.attr(priceAttr);
    if (!price) price = selectedOption.attr('data-price-retail');
    price = price ? parseFloat(price) : 0;

    $('#rate_value' + rowid).val(parseFloat(price).toFixed(2));

    // fetch product cost from backend
    fetchProductCost(rowid, productid);

    getTotal(rowid);
    checkPriceLoss(rowid);
  }

  function fetchProductCost(rowid, productId) {
    $.ajax({
      url: baseurl + 'orders/getProductCost',
      type: 'POST',
      dataType: 'json',
      data: { product_id: productId },
      success: function(res) {
        var cost = (res && res.cost) ? parseFloat(res.cost) : 0;
        $('#product_cost' + rowid).val(cost);
        checkPriceLoss(rowid);
      },
      error: function() {
        $('#product_cost' + rowid).val('0');
        checkPriceLoss(rowid);
      }
    });
  }

  function updateAllProductPrices(customerType) {
    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row', '');
      var productid = $('#product' + rowid).val();
      if (productid) {
        // re-run getProductData to update suggested price + expected
        getProductData(rowid);
      }
    });
  }

  function getTotal(rowid) {
    var qty = parseFloat($('#qty' + rowid).val()) || 0;
    var rate = parseFloat($('#rate_value' + rowid).val()) || 0;
    var amount = qty * rate;

    $('#amount' + rowid).val(amount.toFixed(2) + ' DZD');
    $('#amount_value' + rowid).val(amount.toFixed(2));

    subAmount();
  }

  function subAmount() {
    var grossamount = 0;
    $('input[name="amount_value[]"]').each(function() {
      grossamount += parseFloat($(this).val()) || 0;
    });

    $('#gross_amount').val(grossamount.toFixed(2) + ' DZD');
    $('#gross_amount_value').val(grossamount.toFixed(2));

    var discount = parseFloat($('#discount').val()) || 0;
    var netamount = grossamount - discount;

    $('#net_amount').val(netamount.toFixed(2) + ' DZD');
    $('#net_amount_value').val(netamount.toFixed(2));

    updateLossWarning();
  }

  function checkPriceLoss(rowid) {
    var price = parseFloat($('#rate_value' + rowid).val()) || 0;
    var expected = parseFloat($('#expected_price' + rowid).val()) || 0;
    var cost = parseFloat($('#product_cost' + rowid).val()) || 0;

    var badge = $('#loss_badge' + rowid);
    badge.removeClass('label-danger label-warning label-success label-default');

    if (price <= 0) {
      badge.addClass('label-warning').text('PRICE?');
      updateLossWarning();
      return;
    }

    if (cost > 0 && price < cost) {
      badge.addClass('label-danger').text('REAL LOSS');
      updateLossWarning();
      return;
    }

    if (expected > 0 && price < expected) {
      badge.addClass('label-warning').text('MARGIN LOSS');
      updateLossWarning();
      return;
    }

    badge.addClass('label-success').text('OK');
    updateLossWarning();
  }

  function updateLossWarning() {
    var hasReal = false;
    var hasMargin = false;

    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row','');
      var text = ($('#loss_badge' + rowid).text() || '').trim();
      if (text === 'REAL LOSS') hasReal = true;
      if (text === 'MARGIN LOSS') hasMargin = true;
    });

    if (!hasReal && !hasMargin) {
      $('#lossWarningBox').hide();
      $('#lossWarningText').text('');
      $('#loss_reason').val('');
      return;
    }

    var msg = [];
    if (hasReal) msg.push('Certaines lignes sont vendues sous le coût (perte réelle).');
    if (hasMargin) msg.push('Certaines lignes ont une remise sous le prix attendu (perte de marge).');

    $('#lossWarningText').text(msg.join(' '));
    $('#lossWarningBox').show();
    $('#loss_reason').val(msg.join(' '));
  }
</script>
