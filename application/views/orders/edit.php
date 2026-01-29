<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
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

        <!-- CUSTOMER SECTION -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user"></i> Customer Information</h3>
          </div>
          <div class="box-body">

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

            <!-- New Customer Form -->
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
                      <option value="retail" selected>Retail (Détail)</option>
                      <option value="wholesale">Wholesale (Gros)</option>
                      <option value="superwholesale">Super Wholesale</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Existing Customer Info -->
            <div id="existingCustomerInfo"
                 style="display:<?php echo (!empty($order_data['order_customer_id']) ? 'block' : 'none'); ?>;margin-top:15px;padding:15px;background:#e8f5e9;border-left:4px solid #4caf50;">
              <h4 style="margin-top:0;color:#2e7d32;"><i class="fa fa-check-circle"></i> Selected Customer</h4>
              <div class="row">
                <div class="col-md-6">
                  <p style="margin:5px 0;">
                    <strong>Name:</strong> <span id="displayCustomerName"><?php echo $order_data['order_customer_name']; ?></span><br>
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
                        Retail (Détail)
                      </option>
                      <option value="wholesale"
                        <?php echo (!empty($order_data['order_price_type_override']) && $order_data['order_price_type_override'] == 'wholesale') ||
                                 (empty($order_data['order_price_type_override']) && $order_data['order_customer_type'] == 'wholesale') ? 'selected' : ''; ?>>
                        Wholesale (Gros)
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

        <!-- PRODUCTS SECTION -->
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
                    <th style="width:23%">Product</th>
                    <th style="width:8%">Available</th>
                    <th style="width:8%">Quantity</th>
                    <th style="width:12%">Unit Price (DZD)</th>
                    <th style="width:12%">Amount</th>
                    <th style="width:12%">Loss Status</th>
                    <th style="width:18%">Loss Reason</th>
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
                              data-cost="<?php echo isset($p['cost']) ? $p['cost'] : 0; ?>"
                              data-price-retail="<?php echo isset($p['price_retail']) ? $p['price_retail'] : ($p['price_default'] ?? 0); ?>"
                              data-price-wholesale="<?php echo isset($p['price_wholesale']) ? $p['price_wholesale'] : ($p['price_default'] ?? 0); ?>"
                              data-price-super-wholesale="<?php echo isset($p['price_superwholesale']) ? $p['price_superwholesale'] : ($p['price_default'] ?? 0); ?>"
                              <?php echo ($val['product_id'] == $p['id']) ? 'selected' : ''; ?>>
                              <?php echo $p['name']; ?> (<?php echo $p['sku']; ?>) - Stock: <?php echo $p['qty']; ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <td class="text-center">
                        <span class="badge bg-green available-qty" id="availableQty<?php echo $x; ?>">
                          <?php echo ($product ? $product['qty'] : 0); ?>
                        </span>
                      </td>

                      <td>
                        <input type="number" name="qty[]" id="qty<?php echo $x; ?>"
                               value="<?php echo $val['qty']; ?>"
                               class="form-control"
                               onkeyup="getTotal(<?php echo $x; ?>); checkLoss(<?php echo $x; ?>);"
                               onchange="getTotal(<?php echo $x; ?>); checkLoss(<?php echo $x; ?>);"
                               min="1" required>
                      </td>

                      <td>
                        <input type="number" step="0.01"
                               name="rate_value[]" id="rate_value<?php echo $x; ?>"
                               value="<?php echo $val['rate']; ?>"
                               class="form-control"
                               onkeyup="getTotal(<?php echo $x; ?>); checkLoss(<?php echo $x; ?>);"
                               onchange="getTotal(<?php echo $x; ?>); checkLoss(<?php echo $x; ?>);"
                               required>

                        <input type="hidden" id="expected_price<?php echo $x; ?>" value="0">
                        <input type="hidden" id="product_cost<?php echo $x; ?>" value="0">
                      </td>

                      <td>
                        <input type="text" name="amount[]" id="amount<?php echo $x; ?>"
                               value="<?php echo $val['amount']; ?> DZD"
                               class="form-control" readonly>
                        <input type="hidden" name="amount_value[]" id="amount_value<?php echo $x; ?>"
                               value="<?php echo $val['amount']; ?>">
                      </td>

                      <td class="text-center">
                        <span id="loss_badge<?php echo $x; ?>" class="loss-badge-green">...</span>
                        <input type="hidden" id="loss_type<?php echo $x; ?>" name="loss_type[]" value="none">
                        <input type="hidden" id="loss_amount<?php echo $x; ?>" name="loss_amount[]" value="0">
                      </td>

                      <td>
                        <select class="form-control" id="loss_reason<?php echo $x; ?>" name="loss_reason[]" style="display:none;">
                          <option value="">-- Select Reason --</option>
                          <optgroup label="🔴 Real Loss Reasons">
                            <option value="damaged">Endommagé</option>
                            <option value="expired">Périmé</option>
                            <option value="defective">Défectueux</option>
                            <option value="error">Erreur de prix</option>
                            <option value="exceptional_promo">Promo exceptionnelle</option>
                          </optgroup>
                          <optgroup label="🟠 Margin Loss Reasons">
                            <option value="promo">Promotion</option>
                            <option value="loyalty">Fidélité client</option>
                            <option value="negotiation">Négociation</option>
                            <option value="clearance">Déstockage</option>
                            <option value="vip">Client VIP</option>
                          </optgroup>
                        </select>
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

            <!-- Global Loss Warning -->
            <div id="globalLossWarning" class="alert" style="display:none;margin-top:15px;">
              <strong><i class="fa fa-exclamation-triangle"></i> Attention:</strong>
              <span id="globalLossText"></span>
            </div>

          </div>
        </div>

        <!-- PAYMENT SECTION -->
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
                <div class="clearfix"></div><br>

                <div class="form-group">
                  <label for="discount" class="col-sm-5 control-label">Discount</label>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" id="discount" name="discount" onkeyup="subAmount()" min="0" step="0.01"
                           value="<?php echo $order_data['order_discount']; ?>">
                  </div>
                </div>
                <div class="clearfix"></div><br>

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

                <input type="hidden" name="paid_amount" value="<?php echo $order_data['order_paid_amount']; ?>">
                <input type="hidden" name="payment_method" value="<?php echo $order_data['order_payment_method']; ?>">
                <input type="hidden" name="payment_notes" value="<?php echo $order_data['order_payment_notes']; ?>">
              </div>
            </div>
          </div>

          <div class="box-footer">
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
              <i class="fa fa-save"></i> Save Changes
            </button>
            <a href="<?php echo base_url('orders'); ?>" class="btn btn-warning btn-lg">
              <i class="fa fa-arrow-left"></i> Back to Orders
            </a>
          </div>
        </div>

        <?php echo form_close(); ?>

      </div>
    </div>
  </section>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
  .available-qty { font-size: 16px; padding: 8px 12px; }
  #productInfoTable input[type=number] { text-align: center; }
  .loss-badge-red { background: #d9534f; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
  .loss-badge-orange { background: #f0ad4e; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
  .loss-badge-green { background: #5cb85c; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
  .loss-reason-select { border: 2px solid #f0ad4e; }
  .loss-reason-select-red { border: 2px solid #d9534f; }
</style>

<script type="text/javascript">
  var rowNum = <?php echo (isset($order_data['order_item']) ? count($order_data['order_item']) + 1 : 1); ?>;
  var baseurl = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $('#ordersMainNav').addClass('active');
    $('#manageOrdersNav').addClass('active');

    initCustomerSelect2();
    initAllProductSelect2();

    // Initialize existing rows
    <?php if (isset($order_data['order_item'])): ?>
      <?php $x = 1; foreach ($order_data['order_item'] as $val): ?>
        getProductData(<?php echo $x; ?>);
        <?php $x++; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    subAmount();

    // Customer selection
    $('#customer_id').on('change', handleCustomerSelection);

    // Price type override
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

    // Add row button
    $('#addRow').on('click', function() {
      addRow();
    });

    // Form validation
    $('#editOrderForm').on('submit', function(e) {
      var netAmount = parseFloat($('#net_amount_value').val()) || 0;
      
      if (netAmount <= 0) {
        e.preventDefault();
        alert('Please add at least one product!');
        return false;
      }

      // Check for real losses without reason
      var hasUnexplainedLoss = false;
      $('#productInfoTable tbody tr').each(function() {
        var rowid = $(this).attr('id').replace('row','');
        var lossType = $('#loss_type' + rowid).val();
        var lossReason = $('#loss_reason' + rowid).val();

        if (lossType === 'real_loss' && !lossReason) {
          hasUnexplainedLoss = true;
          $('#loss_reason' + rowid).focus();
          return false;
        }

        // Check stock availability
        var qty = parseFloat($('#qty' + rowid).val()) || 0;
        var available = parseFloat($('#availableQty' + rowid).text()) || 0;
        if (qty > available) {
          e.preventDefault();
          alert('Insufficient stock for row ' + rowid + '! Available: ' + available);
          return false;
        }
      });

      if (hasUnexplainedLoss) {
        e.preventDefault();
        alert('⚠️ PERTE RÉELLE détectée! Vous devez indiquer la raison (endommagé, périmé, etc.)');
        return false;
      }

      return true;
    });
  });

  // ===================================================================
  // SELECT2 INIT
  // ===================================================================
  function initCustomerSelect2() {
    $('.select2-customer').select2({
      placeholder: "Select or search customer",
      allowClear: true,
      width: '100%'
    });
  }

  function initAllProductSelect2() {
    $('.select2-product').each(function() {
      $(this).select2({
        placeholder: "Select or search product",
        allowClear: true,
        width: '100%'
      });
    });
  }

  function initProductSelect2(rowid) {
    $('#product' + rowid).select2({
      placeholder: "Select or search product",
      allowClear: true,
      width: '100%'
    });
  }

  // ===================================================================
  // CUSTOMER SELECTION
  // ===================================================================
  function handleCustomerSelection() {
    var selectedValue = $(this).val();
    
    if (selectedValue === 'new') {
      $('#newCustomerForm').slideDown();
      $('#existingCustomerInfo').slideUp();
      $('#priceTypeSection').slideUp();
    } else if (selectedValue) {
      var selectedOption = $(this).find('option:selected');
      var name = selectedOption.data('name');
      var phone = selectedOption.data('phone');
      var address = selectedOption.data('address');
      var type = selectedOption.data('type');

      $('#displayCustomerName').text(name);
      $('#displayCustomerPhone').text(phone);
      $('#displayCustomerAddress').text(address || 'N/A');
      $('#displayCustomerType').text(type.toUpperCase());

      $('#customer_original_type').val(type);
      $('#customer_type_override').val(type);

      $('#newCustomerForm').slideUp();
      $('#existingCustomerInfo').slideDown();
      $('#priceTypeSection').slideDown();

      updateAllProductPrices(type);
    } else {
      $('#newCustomerForm').slideUp();
      $('#existingCustomerInfo').slideUp();
      $('#priceTypeSection').slideUp();
    }
  }

  // ===================================================================
  // ADD ROW
  // ===================================================================
  function addRow() {
    var html = '<tr id="row' + rowNum + '">';
    
    // Product select
    html += '<td>';
    html += '<select class="form-control select2-product" name="product[]" data-row-id="' + rowNum + '" id="product' + rowNum + '" onchange="getProductData(' + rowNum + ')" style="width:100%" required>';
    html += '<option value="">-- Select Product --</option>';
    <?php foreach ($products as $p): ?>
      html += '<option value="<?php echo $p['id']; ?>" ';
      html += 'data-sku="<?php echo $p['sku']; ?>" ';
      html += 'data-qty="<?php echo $p['qty']; ?>" ';
      html += 'data-name="<?php echo htmlspecialchars($p['name']); ?>" ';
      html += 'data-cost="<?php echo isset($p['cost']) ? $p['cost'] : 0; ?>" ';
      html += 'data-price-retail="<?php echo isset($p['price_retail']) ? $p['price_retail'] : ($p['price_default'] ?? 0); ?>" ';
      html += 'data-price-wholesale="<?php echo isset($p['price_wholesale']) ? $p['price_wholesale'] : ($p['price_default'] ?? 0); ?>" ';
      html += 'data-price-super-wholesale="<?php echo isset($p['price_superwholesale']) ? $p['price_superwholesale'] : ($p['price_default'] ?? 0); ?>">';
      html += '<?php echo $p['name']; ?> (<?php echo $p['sku']; ?>) - Stock: <?php echo $p['qty']; ?></option>';
    <?php endforeach; ?>
    html += '</select>';
    html += '</td>';

    // Available qty
    html += '<td class="text-center">';
    html += '<span class="badge bg-green available-qty" id="availableQty' + rowNum + '">0</span>';
    html += '</td>';

    // Quantity
    html += '<td>';
    html += '<input type="number" name="qty[]" id="qty' + rowNum + '" class="form-control" min="1" required ';
    html += 'onkeyup="getTotal(' + rowNum + '); checkLoss(' + rowNum + ');" ';
    html += 'onchange="getTotal(' + rowNum + '); checkLoss(' + rowNum + ');">';
    html += '</td>';

    // Unit Price
    html += '<td>';
    html += '<input type="number" step="0.01" name="rate_value[]" id="rate_value' + rowNum + '" class="form-control" required ';
    html += 'onkeyup="getTotal(' + rowNum + '); checkLoss(' + rowNum + ');" ';
    html += 'onchange="getTotal(' + rowNum + '); checkLoss(' + rowNum + ');">';
    html += '<input type="hidden" id="expected_price' + rowNum + '" value="0">';
    html += '<input type="hidden" id="product_cost' + rowNum + '" value="0">';
    html += '</td>';

    // Amount
    html += '<td>';
    html += '<input type="text" name="amount[]" id="amount' + rowNum + '" class="form-control" readonly>';
    html += '<input type="hidden" name="amount_value[]" id="amount_value' + rowNum + '">';
    html += '</td>';

    // Loss Status
    html += '<td class="text-center">';
    html += '<span id="loss_badge' + rowNum + '" class="loss-badge-green">OK</span>';
    html += '<input type="hidden" id="loss_type' + rowNum + '" name="loss_type[]" value="none">';
    html += '<input type="hidden" id="loss_amount' + rowNum + '" name="loss_amount[]" value="0">';
    html += '</td>';

    // Loss Reason
    html += '<td>';
    html += '<select class="form-control" id="loss_reason' + rowNum + '" name="loss_reason[]" style="display:none;">';
    html += '<option value="">-- Select Reason --</option>';
    html += '<optgroup label="🔴 Real Loss Reasons">';
    html += '<option value="damaged">Endommagé</option>';
    html += '<option value="expired">Périmé</option>';
    html += '<option value="defective">Défectueux</option>';
    html += '<option value="error">Erreur de prix</option>';
    html += '<option value="exceptional_promo">Promo exceptionnelle</option>';
    html += '</optgroup>';
    html += '<optgroup label="🟠 Margin Loss Reasons">';
    html += '<option value="promo">Promotion</option>';
    html += '<option value="loyalty">Fidélité client</option>';
    html += '<option value="negotiation">Négociation</option>';
    html += '<option value="clearance">Déstockage</option>';
    html += '<option value="vip">Client VIP</option>';
    html += '</optgroup>';
    html += '</select>';
    html += '</td>';

    // Remove button
    html += '<td>';
    html += '<button type="button" class="btn btn-danger btn-sm" onclick="removeRow(' + rowNum + ')"><i class="fa fa-trash"></i></button>';
    html += '</td>';

    html += '</tr>';

    $('#productInfoTable tbody').append(html);
    initProductSelect2(rowNum);
    rowNum++;
  }

  // ===================================================================
  // REMOVE ROW
  // ===================================================================
  function removeRow(rowid) {
    $('#row' + rowid).remove();
    subAmount();
    checkGlobalLoss();
  }

  // ===================================================================
  // GET PRODUCT DATA
  // ===================================================================
  function getProductData(rowid) {
    var productSelect = $('#product' + rowid);
    var selectedOption = productSelect.find('option:selected');

    if (!selectedOption.val()) {
      return;
    }

    var availableQty = parseFloat(selectedOption.data('qty')) || 0;
    var productCost = parseFloat(selectedOption.data('cost')) || 0;
    var priceRetail = parseFloat(selectedOption.data('price-retail')) || 0;
    var priceWholesale = parseFloat(selectedOption.data('price-wholesale')) || 0;
    var priceSuperWholesale = parseFloat(selectedOption.data('price-super-wholesale')) || 0;

    $('#availableQty' + rowid).text(availableQty);
    $('#product_cost' + rowid).val(productCost);

    // Set price based on customer type
    var customerType = $('#customer_type_override').val() || 'retail';
    var suggestedPrice = 0;

    if (customerType === 'superwholesale') {
      suggestedPrice = priceSuperWholesale;
    } else if (customerType === 'wholesale') {
      suggestedPrice = priceWholesale;
    } else {
      suggestedPrice = priceRetail;
    }

    $('#expected_price' + rowid).val(suggestedPrice);
    
    // Only update price if it's a new row (empty rate_value)
    if (!$('#rate_value' + rowid).val() || $('#rate_value' + rowid).val() == 0) {
      $('#rate_value' + rowid).val(suggestedPrice.toFixed(2));
    }

    getTotal(rowid);
    checkLoss(rowid);
  }

  // ===================================================================
  // UPDATE ALL PRODUCT PRICES
  // ===================================================================
  function updateAllProductPrices(customerType) {
    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row', '');
      var productSelect = $('#product' + rowid);
      var selectedOption = productSelect.find('option:selected');

      if (selectedOption.val()) {
        var priceRetail = parseFloat(selectedOption.data('price-retail')) || 0;
        var priceWholesale = parseFloat(selectedOption.data('price-wholesale')) || 0;
        var priceSuperWholesale = parseFloat(selectedOption.data('price-super-wholesale')) || 0;
        var suggestedPrice = 0;

        if (customerType === 'superwholesale') {
          suggestedPrice = priceSuperWholesale;
        } else if (customerType === 'wholesale') {
          suggestedPrice = priceWholesale;
        } else {
          suggestedPrice = priceRetail;
        }

        $('#expected_price' + rowid).val(suggestedPrice);
        
        // Update rate_value (you can comment this if you don't want auto-update on type change)
        // $('#rate_value' + rowid).val(suggestedPrice.toFixed(2));

        getTotal(rowid);
        checkLoss(rowid);
      }
    });
  }

  // ===================================================================
  // GET TOTAL (PER ROW)
  // ===================================================================
  function getTotal(rowid) {
    var qty = parseFloat($('#qty' + rowid).val()) || 0;
    var rate = parseFloat($('#rate_value' + rowid).val()) || 0;
    var amount = qty * rate;

    $('#amount' + rowid).val(amount.toFixed(2) + ' DZD');
    $('#amount_value' + rowid).val(amount.toFixed(2));

    subAmount();
  }

  // ===================================================================
  // CHECK LOSS (PER ROW)
  // ===================================================================
  function checkLoss(rowid) {
    var actualPrice = parseFloat($('#rate_value' + rowid).val()) || 0;
    var expectedPrice = parseFloat($('#expected_price' + rowid).val()) || 0;
    var productCost = parseFloat($('#product_cost' + rowid).val()) || 0;
    var qty = parseFloat($('#qty' + rowid).val()) || 0;

    var lossAmount = 0;
    var lossType = 'none';
    var badgeHtml = '';
    var showReasonDropdown = false;

    if (actualPrice < productCost) {
      // 🔴 REAL LOSS (price < cost)
      lossAmount = (productCost - actualPrice) * qty;
      lossType = 'real_loss';
      badgeHtml = '<span class="loss-badge-red">🔴 PERTE RÉELLE<br>-' + lossAmount.toFixed(2) + ' DA</span>';
      showReasonDropdown = true;
      $('#loss_reason' + rowid).removeClass('loss-reason-select').addClass('loss-reason-select-red');
      
    } else if (actualPrice < expectedPrice) {
      // 🟠 MARGIN LOSS (cost < price < expected)
      lossAmount = (expectedPrice - actualPrice) * qty;
      lossType = 'margin_loss';
      badgeHtml = '<span class="loss-badge-orange">🟠 PERTE MARGE<br>-' + lossAmount.toFixed(2) + ' DA</span>';
      showReasonDropdown = true;
      $('#loss_reason' + rowid).removeClass('loss-reason-select-red').addClass('loss-reason-select');
      
    } else {
      // ✅ OK (no loss)
      badgeHtml = '<span class="loss-badge-green">✅ OK</span>';
      $('#loss_reason' + rowid).val('');
    }

    $('#loss_badge' + rowid).html(badgeHtml);
    $('#loss_type' + rowid).val(lossType);
    $('#loss_amount' + rowid).val(lossAmount.toFixed(2));

    if (showReasonDropdown) {
      $('#loss_reason' + rowid).show().prop('required', lossType === 'real_loss');
    } else {
      $('#loss_reason' + rowid).hide().prop('required', false);
    }

    checkGlobalLoss();
  }

  // ===================================================================
  // CHECK GLOBAL LOSS
  // ===================================================================
  function checkGlobalLoss() {
    var totalRealLoss = 0;
    var totalMarginLoss = 0;
    var nbRealLoss = 0;
    var nbMarginLoss = 0;

    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row','');
      var lossType = $('#loss_type' + rowid).val();
      var lossAmount = parseFloat($('#loss_amount' + rowid).val()) || 0;

      if (lossType === 'real_loss') {
        totalRealLoss += lossAmount;
        nbRealLoss++;
      } else if (lossType === 'margin_loss') {
        totalMarginLoss += lossAmount;
        nbMarginLoss++;
      }
    });

    if (nbRealLoss > 0 || nbMarginLoss > 0) {
      var warningText = '';
      var alertClass = 'alert-warning';

      if (nbRealLoss > 0) {
        warningText += '<strong>🔴 ' + nbRealLoss + ' produit(s) avec PERTE RÉELLE: -' + totalRealLoss.toFixed(2) + ' DA</strong><br>';
        alertClass = 'alert-danger';
      }

      if (nbMarginLoss > 0) {
        warningText += '🟠 ' + nbMarginLoss + ' produit(s) avec Perte de Marge: -' + totalMarginLoss.toFixed(2) + ' DA';
      }

      $('#globalLossText').html(warningText);
      $('#globalLossWarning').removeClass('alert-warning alert-danger').addClass(alertClass).show();
    } else {
      $('#globalLossWarning').hide();
    }
  }

  // ===================================================================
  // SUB AMOUNT (CALCULATE TOTALS)
  // ===================================================================
  function subAmount() {
    var grossAmount = 0;

    $('#productInfoTable tbody tr').each(function() {
      var rowid = $(this).attr('id').replace('row','');
      var amount = parseFloat($('#amount_value' + rowid).val()) || 0;
      grossAmount += amount;
    });

    var discount = parseFloat($('#discount').val()) || 0;
    var netAmount = grossAmount - discount;

    $('#gross_amount').val(grossAmount.toFixed(2) + ' DZD');
    $('#gross_amount_value').val(grossAmount.toFixed(2));
    $('#net_amount').val(netAmount.toFixed(2) + ' DZD');
    $('#net_amount_value').val(netAmount.toFixed(2));
  }
</script>
