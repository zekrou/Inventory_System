<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-shopping-cart"></i> New Order
      <small>Create a new sale</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('orders/') ?>">Orders</a></li>
      <li class="active">Create</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php echo form_open('orders/create', array('id' => 'createOrderForm')); ?>

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
                    data-type="<?php echo $customer['customer_type']; ?>">
                    <?php echo $customer['customer_name']; ?> - <?php echo $customer['phone']; ?> (<?php echo ucfirst($customer['customer_type']); ?>)
                  </option>
                <?php endforeach; ?>
                <option value="new" style="background-color:#d4edda; font-weight:bold; color:#155724;">
                  ✚ Create New Customer
                </option>
              </select>
            </div>

            <!-- New Customer Form (Hidden by default) -->
            <div id="newCustomerForm" style="display:none; margin-top:20px; padding:15px; background:#f9f9f9; border:2px solid #3c8dbc; border-radius:5px;">
              <h4 style="margin-top:0; color:#3c8dbc;">
                <i class="fa fa-plus-circle"></i> New Customer Details
              </h4>

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
                      <option value="super_wholesale">Super Wholesale</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Existing Customer Info Display -->
            <div id="existingCustomerInfo" style="display:none; margin-top:15px; padding:15px; background:#e8f5e9; border-left:4px solid #4caf50;">
              <h4 style="margin-top:0; color:#2e7d32;">
                <i class="fa fa-check-circle"></i> Selected Customer
              </h4>
              <div class="row">
                <div class="col-md-6">
                  <p style="margin:5px 0;">
                    <strong>Name:</strong> <span id="display_customer_name"></span><br>
                    <strong>Phone:</strong> <span id="display_customer_phone"></span><br>
                    <strong>Address:</strong> <span id="display_customer_address"></span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p style="margin:5px 0;">
                    <strong>Default Type:</strong>
                    <span id="display_customer_type" class="label label-success"></span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Price Type Override (Show for both new and existing customers) -->
            <div id="priceTypeSection" style="display:none; margin-top:20px; padding:15px; background:#fff3cd; border-left:4px solid #ffc107;">
              <h4 style="margin-top:0; color:#856404;">
                <i class="fa fa-exchange"></i> Price Type for This Order
              </h4>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Select Price Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="customer_type_override" name="customer_type_override">
                      <option value="retail">Retail (Normal Price)</option>
                      <option value="wholesale">Wholesale</option>
                      <option value="super_wholesale">Super Wholesale</option>
                    </select>
                    <input type="hidden" id="customer_original_type" value="retail">
                    <small class="text-muted">
                      <i class="fa fa-info-circle"></i> You can change the price type for this specific order
                    </small>
                  </div>
                </div>

                <div class="col-md-6">
                  <!-- Reason for override (shows when changing from original) -->
                  <div class="form-group" id="override_reason_group" style="display:none;">
                    <label>Reason for Change</label>
                    <input type="text" class="form-control" id="override_reason" name="override_reason" placeholder="Ex: Special promotion, VIP customer...">
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
              <button type="button" class="btn btn-success btn-sm" id="addrow">
                <i class="fa fa-plus"></i> Add Product
              </button>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover" id="product_info_table">
                <thead style="background:#f4f4f4;">
                  <tr>
                    <th style="width:40%;">Product</th>
                    <th style="width:15%;">Available</th>
                    <th style="width:15%;">Quantity</th>
                    <th style="width:20%;">
                      Prix Unitaire (DZD)
                      <br><small class="text-muted">Suggéré → Vous pouvez modifier</small>
                    </th>
                    <th style="width:5%;" class="text-center">
                      <i class="fa fa-warning text-warning" title="Alerte perte"></i>
                    </th>

                    <th style="width:15%;">Amount</th>
                    <th style="width:5%;"><i class="fa fa-trash"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <tr id="row_1"> <!-- 🔴 AJOUT underscore -->
                    <td>
                      <select class="form-control select2-product" name="product[]" id="product_1" onchange="getProductData(1)" data-row-id="1" style="width:100%" required> <!-- 🔴 AJOUT underscore -->
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product): ?>
                          <option value="<?php echo $product['id']; ?>"
                            data-sku="<?php echo $product['sku']; ?>"
                            data-qty="<?php echo $product['qty']; ?>"
                            data-name="<?php echo $product['name']; ?>"
                            data-price-retail="<?php echo isset($product['price_retail']) ? $product['price_retail'] : $product['price_default']; ?>"
                            data-price-wholesale="<?php echo isset($product['price_wholesale']) ? $product['price_wholesale'] : $product['price_default']; ?>"
                            data-price-super-wholesale="<?php echo isset($product['price_super_wholesale']) ? $product['price_super_wholesale'] : $product['price_default']; ?>">
                            <?php echo $product['name']; ?> (<?php echo $product['sku']; ?>) - Stock: <?php echo $product['qty']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <span class="badge bg-green available-qty" id="availableqty_1">0</span> <!-- 🔴 AJOUT underscore -->
                    </td>
                    <td>
                      <input type="number" name="qty[]" id="qty_1" class="form-control" onkeyup="getTotal(1)" min="1" value="1" required> <!-- 🔴 AJOUT underscore -->
                    </td>
                    <td>
                      <div class="input-group">
                        <input type="number" name="rate_value[]" id="rate_value_1"
                          class="form-control price-input"
                          step="0.01" min="0"
                          onkeyup="checkPriceLoss(1); getTotal(1)"
                          data-expected="0"
                          data-cost="0"
                          placeholder="0.00"
                          required>
                        <span class="input-group-addon">DZD</span>
                      </div>
                      <small class="text-muted price-suggestion" id="price_suggest_1"></small>
                    </td>
                    <td class="text-center alert-cell" id="alert_cell_1">
                      <!-- Alerte perte ici -->
                    </td>
                    <td>
                      <input type="text" name="amount[]" id="amount_1" class="form-control" readonly> <!-- 🔴 AJOUT underscore -->
                      <input type="hidden" name="amount_value[]" id="amount_value_1" class="form-control"> <!-- 🔴 AJOUT underscore -->
                    </td>
                    <td>
                      <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(1)" disabled>
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Loss Warning Section -->
        <div class="box box-warning" id="loss_warning_box" style="display:none;">
          <div class="box-header with-border" style="background:#fcf8e3; border-left:4px solid #f0ad4e;">
            <h3 class="box-title">
              <i class="fa fa-exclamation-triangle text-warning"></i>
              <strong>Vente à Perte Détectée</strong>
            </h3>
          </div>
          <div class="box-body">
            <div class="alert alert-warning" style="margin-bottom:15px;">
              <strong>⚠️ Attention!</strong> Vous vendez au moins un produit à un prix inférieur au prix normal.
              <div id="loss_details_list" style="margin-top:10px; font-size:13px;"></div>
            </div>

            <div class="form-group">
              <label>
                <i class="fa fa-edit"></i> Raison de la vente à perte
                <span class="text-danger">*</span>
              </label>
              <textarea name="loss_reason" id="loss_reason" class="form-control" rows="3"
                placeholder="Ex: Client fidèle, Promotion spéciale, Produit endommagé, Proche expiration, Négociation commerciale..."></textarea>
              <small class="text-muted">
                <i class="fa fa-info-circle"></i> Cette raison sera enregistrée dans les rapports de pertes
              </small>
            </div>
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
                    <input type="text" class="form-control" id="gross_amount" name="gross_amount"
                      readonly>
                    <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value">
                  </div>
                </div>

                <div class="form-group">
                  <label for="discount" class="col-sm-5 control-label">Discount</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="discount" name="discount"
                      onkeyup="subAmount()" min="0" step="0.01" value="0">
                  </div>
                </div>

                <div class="form-group">
                  <label for="net_amount" class="col-sm-5 control-label" style="font-size:16px;">
                    <strong>Total Amount</strong>
                  </label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="net_amount" name="net_amount"
                      readonly style="font-size:18px; font-weight:bold; color:#00a65a;">
                    <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="paid_amount" class="col-sm-5 control-label">Amount Paid</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="paid_amount" name="paid_amount"
                      min="0" step="0.01" value="0" onkeyup="calculateDue()">
                  </div>
                </div>

                <!-- Payment Method -->
                <div class="form-group">
                  <label for="payment_method" class="col-sm-5 control-label">Payment Method</label>
                  <div class="col-sm-7">
                    <select class="form-control" id="payment_method" name="payment_method">
                      <option value="">-- Select --</option>
                      <option value="cash" selected>💵 Cash</option>
                      <option value="cheque">📝 Cheque</option>
                      <option value="credit_card">💳 Credit Card</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="due_amount" class="col-sm-5 control-label">Due Amount</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="due_amount" name="due_amount"
                      readonly style="color:#dd4b39;">
                  </div>
                </div>

                <!-- AUTO: Payment Status (calculated automatically) -->
                <div class="form-group">
                  <label for="paid_status" class="col-sm-5 control-label">Payment Status</label>
                  <div class="col-sm-7">
                    <select class="form-control" id="paid_status" name="paid_status" readonly disabled style="background-color:#f4f4f4;">
                      <option value="1">✓ Fully Paid</option>
                      <option value="2" selected>✗ Unpaid</option>
                      <option value="3">⚠ Partial Payment</option>
                    </select>
                    <input type="hidden" id="paid_status_hidden" name="paid_status" value="2">
                    <small class="text-muted"><i class="fa fa-magic"></i> Calculated automatically</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Real-time Date and Time -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="order_date" class="col-sm-5 control-label">Order Date</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="order_date" name="order_date" readonly
                      style="background-color:#f9f9f9;">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="order_time" class="col-sm-5 control-label">Order Time</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="order_time" name="order_time" readonly
                      style="background-color:#f9f9f9;">
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="payment_notes" class="col-sm-2 control-label">Notes</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="payment_notes" name="payment_notes"
                  rows="3" placeholder="Payment notes (optional)"></textarea>
              </div>
            </div>

          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-success btn-lg pull-right">
              <i class="fa fa-check"></i> Create Order
            </button>
            <a href="<?php echo base_url('orders/') ?>" class="btn btn-default btn-lg">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
        </div>

        <?php echo form_close(); ?>
      </div>
    </div>
  </section>
</div>

<!-- Add Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
  /* Custom styling for better UX */
  .customer-info {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
  }

  .select2-container {
    font-size: 14px;
  }

  .select2-container .select2-results__option .product-option {
    padding: 8px 0;
    border-bottom: 1px solid #f4f4f4;
  }

  .select2-container .select2-results__option .product-name {
    font-size: 14px;
    margin-bottom: 5px;
    font-weight: 600;
  }

  .select2-container .select2-results__option .product-details {
    font-size: 12px;
  }

  .select2-container .select2-results__option .label {
    margin-right: 5px;
    font-size: 11px;
  }

  .select2-container--default .select2-results__option--highlighted {
    background-color: #3c8dbc !important;
  }

  .available-qty {
    font-size: 16px;
    padding: 8px 12px;
  }

  #product_info_table input[type="number"] {
    text-align: center;
  }

  .alert-info {
    animation: slideDown 0.3s ease-out;
  }

  /* Loss detection styles */
  #product_info_table tbody tr.danger {
    background-color: #f2dede !important;
    border-left: 4px solid #d9534f;
    animation: pulseRed 2s infinite;
  }

  #product_info_table tbody tr.warning {
    background-color: #fcf8e3 !important;
    border-left: 4px solid #f0ad4e;
  }

  @keyframes pulseRed {

    0%,
    100% {
      border-left-color: #d9534f;
    }

    50% {
      border-left-color: #c9302c;
    }
  }

  .price-input {
    font-weight: bold;
    font-size: 14px;
  }

  .price-suggestion {
    display: block;
    margin-top: 5px;
    font-size: 11px;
  }

  #loss_warning_box {
    animation: slideDown 0.5s ease-out;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<!-- Add Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
  var rowNum = 1;
  var baseurl = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $('#ordersMainNav').addClass('active');

    // Initialize Select2
    initCustomerSelect2();
    initProductSelect2();

    // Set paid amount to 0 by default
    $('#paid_amount').val(0);

    // Initialize real-time date and time
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Auto-calculate payment status
    $('#paid_amount').on('keyup change', function() {
      calculateDue();
      autoUpdatePaymentStatus();
    });

    // Handle customer selection
    $('#customer_id').on('change', function() {
      handleCustomerSelection();
    });

    // Detect price type override
    $('#customer_type_override').on('change', function() {
      var selectedType = $(this).val();
      var originalType = $('#customer_original_type').val();

      // Show reason field if changing from original type
      if (originalType && selectedType != originalType) {
        $('#override_reason_group').slideDown();
      } else {
        $('#override_reason_group').slideUp();
        $('#override_reason').val('');
      }

      // Update all product prices
      updateAllProductPrices(selectedType);
    });

    // Add row button
    $('#addrow').on('click', function() {
      addRow();
    });

    // Form validation
    // Form validation
    $('#createOrderForm').on('submit', function(e) {
      var netamount = parseFloat($('#net_amount_value').val()) || 0;

      if (netamount <= 0) {
        e.preventDefault();
        alert('Please add at least one product!');
        return false;
      }

      // ✅ NOUVEAU: Vérifier raison perte
      var hasLoss = $('#product_info_table tbody tr.danger, #product_info_table tbody tr.warning').length > 0;
      if (hasLoss && !$('#loss_reason').val().trim()) {
        e.preventDefault();
        alert('⚠️ Veuillez indiquer la raison de la vente à perte!');
        $('#loss_reason').focus();
        return false;
      }

      // Validate stock availability
      var valid = true;
      $('#product_info_table tbody tr').each(function() {
        var row = $(this);
        var rowid = row.attr('id').split('_')[1];
        var qty = parseFloat($('#qty_' + rowid).val()) || 0;
        var available = parseFloat($('#availableqty_' + rowid).text()) || 0;

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
    });

  });

  // ===== CUSTOMER SELECT2 =====
  function initCustomerSelect2() {
    $('.select2-customer').select2({
      placeholder: 'Search customer by name or phone...',
      allowClear: true,
      width: '100%'
    });
  }

  function handleCustomerSelection() {
    var selectedValue = $('#customer_id').val();

    if (selectedValue === 'new') {
      // Show new customer form
      $('#newCustomerForm').slideDown();
      $('#existingCustomerInfo').slideUp();
      $('#priceTypeSection').slideDown();

      // Make fields required
      $('#new_customer_name').prop('required', true);
      $('#new_customer_phone').prop('required', true);
      $('#new_customer_type').prop('required', true);

      // Set default type for new customer
      $('#customer_type_override').val('retail');
      $('#customer_original_type').val('retail');
      $('#override_reason_group').hide();

    } else if (selectedValue) {
      // Existing customer selected
      var selectedOption = $('#customer_id option:selected');
      var name = selectedOption.data('name');
      var phone = selectedOption.data('phone');
      var address = selectedOption.data('address');
      var type = selectedOption.data('type');

      console.log('✅ Customer selected:', name, 'Type:', type);

      // Display customer info
      $('#display_customer_name').text(name);
      $('#display_customer_phone').text(phone);
      $('#display_customer_address').text(address || 'N/A');
      $('#display_customer_type').text(type.toUpperCase());

      $('#existingCustomerInfo').slideDown();
      $('#newCustomerForm').slideUp();
      $('#priceTypeSection').slideDown();

      // Set customer type
      $('#customer_type_override').val(type);
      $('#customer_original_type').val(type);
      $('#override_reason_group').hide();

      // Remove required from new customer fields
      $('#new_customer_name').prop('required', false);
      $('#new_customer_phone').prop('required', false);

      // Update product prices based on customer type
      updateAllProductPrices(type);

      // Show notification
      $('#customer_exists_alert').remove();
      $('#existingCustomerInfo').prepend(`
        <div id="customer_exists_alert" class="alert alert-success alert-dismissible" style="margin-bottom:10px;">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <i class="fa fa-check-circle"></i> <strong>Customer loaded!</strong> 
          <span class="label label-success">${type.toUpperCase()}</span>
          <small>You can change the price type below if needed</small>
        </div>
      `);

    } else {
      // Nothing selected
      $('#newCustomerForm').slideUp();
      $('#existingCustomerInfo').slideUp();
      $('#priceTypeSection').slideUp();
      $('#new_customer_name').prop('required', false);
      $('#new_customer_phone').prop('required', false);
    }
  }

  // ===== PRODUCT SELECT2 =====
  function initProductSelect2() {
    $('.select2-product').select2({
      placeholder: 'Type to search product...',
      allowClear: true,
      width: '100%'
    });
  }

  // ===== ADD/REMOVE ROWS =====
  function addRow() {
    rowNum++;

    var html = '<tr id="row_' + rowNum + '">';

    // Column 1: Product Select
    html += '<td>';
    html += '<select class="form-control select2-product" name="product[]" id="product_' + rowNum + '" onchange="getProductData(' + rowNum + ')" data-row-id="' + rowNum + '" style="width:100%" required>';
    html += '<option value="">-- Select Product --</option>';

    <?php foreach ($products as $product): ?>
      html += '<option value="<?php echo $product['id']; ?>"';
      html += ' data-sku="<?php echo $product['sku']; ?>"';
      html += ' data-qty="<?php echo $product['qty']; ?>"';
      html += ' data-name="<?php echo $product['name']; ?>"';
      html += ' data-price-retail="<?php echo isset($product['price_retail']) ? $product['price_retail'] : $product['price_default']; ?>"';
      html += ' data-price-wholesale="<?php echo isset($product['price_wholesale']) ? $product['price_wholesale'] : $product['price_default']; ?>"';
      html += ' data-price-super-wholesale="<?php echo isset($product['price_super_wholesale']) ? $product['price_super_wholesale'] : $product['price_default']; ?>">';
      html += '<?php echo $product['name']; ?> (<?php echo $product['sku']; ?>) - Stock: <?php echo $product['qty']; ?>';
      html += '</option>';
    <?php endforeach; ?>

    html += '</select>';
    html += '</td>';

    // Column 2: Available Stock
    html += '<td><span class="badge bg-green available-qty" id="availableqty_' + rowNum + '">0</span></td>';

    // Column 3: Quantity
    html += '<td><input type="number" name="qty[]" id="qty_' + rowNum + '" class="form-control" onkeyup="getTotal(' + rowNum + ')" min="1" value="1" required></td>';

    // ✅ Column 4: PRIX MODIFIABLE (NOUVEAU)
    html += '<td>';
    html += '<div class="input-group">';
    html += '<input type="number" name="rate_value[]" id="rate_value_' + rowNum + '" class="form-control price-input" step="0.01" min="0" onkeyup="checkPriceLoss(' + rowNum + '); getTotal(' + rowNum + ')" data-expected="0" data-cost="0" placeholder="0.00" required>';
    html += '<span class="input-group-addon">DZD</span>';
    html += '</div>';
    html += '<small class="text-muted price-suggestion" id="price_suggest_' + rowNum + '"></small>';
    html += '</td>';

    // Column 5: Amount
    html += '<td>';
    html += '<input type="text" name="amount[]" id="amount_' + rowNum + '" class="form-control" readonly>';
    html += '<input type="hidden" name="amount_value[]" id="amount_value_' + rowNum + '">';
    html += '</td>';

    // ✅ Column 6: ALERTE PERTE (NOUVEAU)
    html += '<td class="text-center alert-cell" id="alert_cell_' + rowNum + '">';
    html += '<!-- Alerte perte ici -->';
    html += '</td>';

    // Column 7: Remove Button
    html += '<td>';
    html += '<button type="button" class="btn btn-danger btn-sm" onclick="removeRow(' + rowNum + ')">';
    html += '<i class="fa fa-trash"></i>';
    html += '</button>';
    html += '</td>';

    html += '</tr>';

    $('#product_info_table tbody').append(html);
    initProductSelect2();
  }


  function removeRow(rowid) {
    if ($('#product_info_table tbody tr').length > 1) { // ✅ CORRIGÉ
      $('#row_' + rowid).remove(); // ✅ AVEC underscore
      subAmount();
    } else {
      alert('Cannot remove the last row!');
    }
  }

  // ===== PRODUCT DATA & PRICING =====
  function getProductData(rowid) {
    var productid = $('#product_' + rowid).val();
    var selectedOption = $('#product_' + rowid + ' option:selected');
    var customerType = getCustomerType();

    if (productid) {
      var qty = selectedOption.attr('data-qty');

      // Mapper le type vers le bon nom d'attribut
      var typeMap = {
        'super_wholesale': 'super-wholesale',
        'superwholesale': 'super-wholesale',
        'wholesale': 'wholesale',
        'retail': 'retail'
      };

      var mappedType = typeMap[customerType] || 'retail';
      var priceAttr = 'data-price-' + mappedType;
      var expectedPrice = selectedOption.attr(priceAttr);

      if (!expectedPrice || expectedPrice === '' || parseFloat(expectedPrice) === 0) {
        expectedPrice = selectedOption.attr('data-price-retail');
      }
      expectedPrice = parseFloat(expectedPrice) || 0;

      // ✅ NOUVEAU: Get product cost via AJAX
      $.ajax({
        url: baseurl + 'orders/getProductCost',
        type: 'POST',
        data: {
          product_id: productid
        },
        dataType: 'json',
        success: function(response) {
          var productCost = response.cost || 0;

          // Display stock
          $('#availableqty_' + rowid).text(qty);
          $('#qty_' + rowid).attr('max', qty);

          // ✅ Set expected price as suggestion
          $('#rate_value_' + rowid).val(expectedPrice.toFixed(2));
          $('#rate_value_' + rowid).attr('data-expected', expectedPrice);
          $('#rate_value_' + rowid).attr('data-cost', productCost);
          $('#price_suggest_' + rowid).html(
            '<i class="fa fa-info-circle"></i> Prix suggéré: <strong>' +
            expectedPrice.toFixed(2) + ' DZD</strong> | Coût: ' +
            productCost.toFixed(2) + ' DZD'
          );

          // Check for loss
          checkPriceLoss(rowid);
          getTotal(rowid);
        }
      });

    } else {
      $('#rate_value_' + rowid).val('0');
      $('#rate_value_' + rowid).attr('data-expected', '0');
      $('#rate_value_' + rowid).attr('data-cost', '0');
      $('#availableqty_' + rowid).text('0');
      $('#price_suggest_' + rowid).html('');
      $('#alert_cell_' + rowid).html('');
      getTotal(rowid);
    }
  }


  function getCustomerType() {
    // Get the selected price type (can be overridden)
    return $('#customer_type_override').val() || 'retail';
  }

  function updateAllProductPrices(customerType) {
    var typeMap = {
      'super_wholesale': 'super-wholesale', // ✅ AVEC underscore
      'superwholesale': 'super-wholesale',
      'wholesale': 'wholesale',
      'retail': 'retail'
    };

    var mappedType = typeMap[customerType] || 'retail';

    console.log('🔄 Updating all prices - Type:', customerType, 'Mapped:', mappedType);

    $('#product_info_table tbody tr').each(function() {
      var row = $(this);
      var rowid = row.attr('id').split('row_')[1];
      var selectedOption = $('#product_' + rowid + ' option:selected');
      var productid = $('#product_' + rowid).val();

      if (productid) {
        var priceAttr = 'data-price-' + mappedType;
        var price = selectedOption.attr(priceAttr);

        console.log('📍 Row', rowid, 'Attr:', priceAttr, 'Value:', price);

        if (!price || price === '' || parseFloat(price) === 0) {
          price = selectedOption.attr('data-price-retail');
        }
        price = parseFloat(price) || 0;

        $('#rate_' + rowid).val(parseFloat(price).toFixed(2) + ' DZD'); // ✅ AVEC underscore
        $('#rate_value_' + rowid).val(price); // ✅ AVEC underscore

        getTotal(rowid);
      }
    });
  }

  // ===== CALCULATIONS =====
  function getTotal(rowid) {
    var qty = parseFloat($('#qty_' + rowid).val()) || 0;
    var rate = parseFloat($('#rate_value_' + rowid).val()) || 0;
    var amount = qty * rate;

    $('#amount_' + rowid).val(amount.toFixed(2) + ' DZD');
    $('#amount_value_' + rowid).val(amount.toFixed(2));

    subAmount();
  }


  function subAmount() {
    var grossamount = 0;

    $('input[name="amount_value[]"]').each(function() {
      var amount = parseFloat($(this).val()) || 0;
      grossamount += amount;
    });

    $('#gross_amount').val(grossamount.toFixed(2) + ' DZD');
    $('#gross_amount_value').val(grossamount.toFixed(2));

    var discount = parseFloat($('#discount').val()) || 0;
    var netamount = grossamount - discount;

    $('#net_amount').val(netamount.toFixed(2) + ' DZD');
    $('#net_amount_value').val(netamount.toFixed(2));

    calculateDue();
    autoUpdatePaymentStatus();
  }

  function calculateDue() {
    var netamount = parseFloat($('#net_amount_value').val()) || 0;
    var paidamount = parseFloat($('#paid_amount').val()) || 0;
    var dueamount = netamount - paidamount;

    $('#due_amount').val(dueamount.toFixed(2) + ' DZD');
  }

  function autoUpdatePaymentStatus() {
    var netamount = parseFloat($('#net_amount_value').val()) || 0;
    var paidamount = parseFloat($('#paid_amount').val()) || 0;
    var status;

    if (paidamount == 0) {
      status = 2; // Unpaid
      $('#paid_status').val('2');
      $('#payment_method').prop('required', false);
    } else if (paidamount >= netamount) {
      status = 1; // Fully Paid
      $('#paid_status').val('1');
      $('#payment_method').prop('required', true);
    } else {
      status = 3; // Partial
      $('#paid_status').val('3');
      $('#payment_method').prop('required', true);
    }

    $('#paid_status_hidden').val(status);
  }

  // ===== DATE/TIME =====
  function updateDateTime() {
    var now = new Date();
    var day = String(now.getDate()).padStart(2, '0');
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var year = now.getFullYear();
    var dateStr = day + '/' + month + '/' + year;

    var hours = now.getHours();
    var minutes = String(now.getMinutes()).padStart(2, '0');
    var seconds = String(now.getSeconds()).padStart(2, '0');
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    var timeStr = String(hours).padStart(2, '0') + ':' + minutes + ':' + seconds + ' ' + ampm;

    $('#order_date').val(dateStr);
    $('#order_time').val(timeStr);
  }
  // ========================================
  // DÉTECTION PERTES & ALERTES
  // ========================================

  function checkPriceLoss(rowid) {
    var priceInput = $('#rate_value_' + rowid);
    var actualPrice = parseFloat(priceInput.val()) || 0;
    var expectedPrice = parseFloat(priceInput.attr('data-expected')) || 0;
    var productCost = parseFloat(priceInput.attr('data-cost')) || 0;
    var alertCell = $('#alert_cell_' + rowid);
    var row = $('#row_' + rowid);

    // Clear previous
    alertCell.html('');
    row.removeClass('danger warning');

    if (actualPrice > 0 && expectedPrice > 0) {
      if (actualPrice < productCost) {
        // ❌ PERTE RÉELLE (rouge)
        row.addClass('danger');
        alertCell.html('<i class="fa fa-times-circle text-danger" style="font-size:20px;" title="PERTE RÉELLE: Prix < Coût achat"></i>');
      } else if (actualPrice < expectedPrice) {
        // ⚠️ PERTE DE MARGE (orange)
        row.addClass('warning');
        var loss = expectedPrice - actualPrice;
        alertCell.html('<i class="fa fa-exclamation-triangle text-warning" style="font-size:20px;" title="Perte marge: -' + loss.toFixed(2) + ' DZD"></i>');
      } else {
        // ✅ OK
        alertCell.html('<i class="fa fa-check-circle text-success" style="font-size:18px;" title="Prix OK"></i>');
      }

      updateLossWarning();
    }
  }

  function updateLossWarning() {
    var lossRows = $('#product_info_table tbody tr.danger, #product_info_table tbody tr.warning');

    if (lossRows.length > 0) {
      var html = '<ul style="margin:0; padding-left:20px;">';
      var hasRealLoss = false;

      lossRows.each(function() {
        var rowid = $(this).attr('id').split('_')[1];
        var productName = $('#product_' + rowid + ' option:selected').text().split('(')[0].trim();
        var expected = parseFloat($('#rate_value_' + rowid).attr('data-expected'));
        var actual = parseFloat($('#rate_value_' + rowid).val());
        var cost = parseFloat($('#rate_value_' + rowid).attr('data-cost'));
        var loss = expected - actual;
        var lossType = actual < cost ? 'RÉELLE' : 'MARGE';
        var icon = actual < cost ? '❌' : '⚠️';

        if (actual < cost) hasRealLoss = true;

        html += '<li style="margin:5px 0;">';
        html += icon + ' <strong>' + productName + '</strong>: ';
        html += 'Prix normal <span class="label label-default">' + expected.toFixed(2) + ' DZD</span> → ';
        html += 'Prix vendu <span class="label label-warning">' + actual.toFixed(2) + ' DZD</span> ';
        html += '<span class="label label-danger">Perte ' + lossType + ': -' + loss.toFixed(2) + ' DZD</span>';
        html += '</li>';
      });
      html += '</ul>';

      if (hasRealLoss) {
        html = '<div class="alert alert-danger" style="padding:8px; margin-bottom:10px;"><strong>🚨 ALERTE: Perte réelle détectée!</strong> Vous vendez moins cher que le prix d\'achat.</div>' + html;
      }

      $('#loss_details_list').html(html);
      $('#loss_warning_box').slideDown();
      $('#loss_reason').prop('required', true);
    } else {
      $('#loss_warning_box').slideUp();
      $('#loss_reason').prop('required', false);
      $('#loss_reason').val('');
    }
  }

  function updateAllProductPrices(customerType) {
    var typeMap = {
      'super_wholesale': 'super-wholesale',
      'superwholesale': 'super-wholesale',
      'wholesale': 'wholesale',
      'retail': 'retail'
    };

    var mappedType = typeMap[customerType] || 'retail';

    $('#product_info_table tbody tr').each(function() {
      var row = $(this);
      var rowid = row.attr('id').split('_')[1];
      var selectedOption = $('#product_' + rowid + ' option:selected');
      var productid = $('#product_' + rowid).val();

      if (productid) {
        var priceAttr = 'data-price-' + mappedType;
        var price = selectedOption.attr(priceAttr);

        if (!price || price === '' || parseFloat(price) === 0) {
          price = selectedOption.attr('data-price-retail');
        }
        price = parseFloat(price) || 0;

        $('#rate_value_' + rowid).val(price.toFixed(2));
        $('#rate_value_' + rowid).attr('data-expected', price);

        checkPriceLoss(rowid);
        getTotal(rowid);
      }
    });
  }
</script>



<script type="text/javascript">
  $(document).ready(function() {
    $("#ordersMainNav").addClass('active');
  });
</script>