<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-edit"></i> Edit Order
            <small>Modify order details</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('orders') ?>">Orders</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div id="messages"></div>

                <!-- Messages -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php elseif ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open('orders/update/' . $order_data['order']['id'], array('id' => 'editOrderForm')); ?>

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
                                        data-type="<?php echo $customer['customer_type']; ?>"
                                        <?php echo ($order_data['order']['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
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
                        <div id="existingCustomerInfo" style="display:<?php echo !empty($order_data['order']['customer_id']) ? 'block' : 'none'; ?>; margin-top:15px; padding:15px; background:#e8f5e9; border-left:4px solid #4caf50;">
                            <h4 style="margin-top:0; color:#2e7d32;">
                                <i class="fa fa-check-circle"></i> Selected Customer
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin:5px 0;">
                                        <strong>Name:</strong> <span id="display_customer_name"><?php echo $order_data['order']['customer_name']; ?></span><br>
                                        <strong>Phone:</strong> <span id="display_customer_phone"><?php echo $order_data['order']['customer_phone']; ?></span><br>
                                        <strong>Address:</strong> <span id="display_customer_address"><?php echo $order_data['order']['customer_address']; ?></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p style="margin:5px 0;">
                                        <strong>Default Type:</strong>
                                        <span id="display_customer_type" class="label label-success"><?php echo strtoupper($order_data['order']['customer_type']); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Price Type Override -->
                        <div id="priceTypeSection" style="display:<?php echo !empty($order_data['order']['customer_id']) ? 'block' : 'none'; ?>; margin-top:20px; padding:15px; background:#fff3cd; border-left:4px solid #ffc107;">
                            <h4 style="margin-top:0; color:#856404;">
                                <i class="fa fa-exchange"></i> Price Type for This Order
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select Price Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="customer_type_override" name="customer_type_override">
                                            <option value="retail" <?php echo (!empty($order_data['order']['price_type_override']) && $order_data['order']['price_type_override'] == 'retail') || (empty($order_data['order']['price_type_override']) && $order_data['order']['customer_type'] == 'retail') ? 'selected' : ''; ?>>Retail (Normal Price)</option>
                                            <option value="wholesale" <?php echo (!empty($order_data['order']['price_type_override']) && $order_data['order']['price_type_override'] == 'wholesale') || (empty($order_data['order']['price_type_override']) && $order_data['order']['customer_type'] == 'wholesale') ? 'selected' : ''; ?>>Wholesale</option>
                                            <option value="superwholesale" <?php echo (!empty($order_data['order']['price_type_override']) && $order_data['order']['price_type_override'] == 'superwholesale') || (empty($order_data['order']['price_type_override']) && $order_data['order']['customer_type'] == 'superwholesale') ? 'selected' : ''; ?>>Super Wholesale</option>
                                        </select>
                                        <input type="hidden" id="customer_original_type" value="<?php echo $order_data['order']['customer_type']; ?>">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> You can change the price type for this order
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" id="override_reason_group" style="display:<?php echo !empty($order_data['order']['override_reason']) ? 'block' : 'none'; ?>;">
                                        <label>Reason for Change</label>
                                        <input type="text" class="form-control" id="override_reason" name="override_reason" value="<?php echo !empty($order_data['order']['override_reason']) ? $order_data['order']['override_reason'] : ''; ?>" placeholder="Ex: Special promotion, VIP customer...">
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
                            <table class="table table-bordered table-hover" id="productinfotable">
                                <thead style="background:#f4f4f4">
                                    <tr>
                                        <th style="width:40%">Product</th>
                                        <th style="width:15%">Available</th>
                                        <th style="width:15%">Quantity</th>
                                        <th style="width:15%">Unit Price</th>
                                        <th style="width:15%">Amount</th>
                                        <th style="width:5%"><i class="fa fa-trash"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($order_data['order_item'])): ?>
                                        <?php $x = 1;
                                        foreach ($order_data['order_item'] as $val):
                                            $product = null;
                                            foreach ($products as $p) {
                                                if ($p['id'] == $val['product_id']) {
                                                    $product = $p;
                                                    break;
                                                }
                                            }
                                        ?>
                                            <tr id="row<?php echo $x; ?>">
                                                <td>
                                                    <select class="form-control select2-product" name="product[]" data-row-id="<?php echo $x; ?>" id="product<?php echo $x; ?>" onchange="getProductData(<?php echo $x; ?>)" style="width:100%" required>
                                                        <option value="">-- Select Product --</option>
                                                        <?php foreach ($products as $p): ?>
                                                            <option value="<?php echo $p['id']; ?>"
                                                                data-sku="<?php echo $p['sku']; ?>"
                                                                data-qty="<?php echo $p['qty']; ?>"
                                                                data-name="<?php echo $p['name']; ?>"
                                                                data-price-retail="<?php echo isset($p['price_retail']) ? $p['price_retail'] : $p['price_default']; ?>"
                                                                data-price-wholesale="<?php echo isset($p['price_wholesale']) ? $p['price_wholesale'] : $p['price_default']; ?>"
                                                                data-price-super-wholesale="<?php echo isset($p['price_super_wholesale']) ? $p['price_super_wholesale'] : $p['price_default']; ?>"
                                                                <?php echo ($val['product_id'] == $p['id']) ? 'selected' : ''; ?>>
                                                                <?php echo $p['name']; ?> (<?php echo $p['sku']; ?>) - Stock: <?php echo $p['qty']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <span class="badge bg-green available-qty" id="availableqty<?php echo $x; ?>"><?php echo $product ? $product['qty'] + $val['qty'] : 0; ?></span>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[]" id="qty<?php echo $x; ?>" value="<?php echo $val['qty']; ?>" class="form-control" onkeyup="getTotal(<?php echo $x; ?>)" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="rate" id="rate<?php echo $x; ?>" value="<?php echo $val['rate']; ?>" class="form-control" readonly>
                                                    <input type="hidden" name="rate_value[]" id="ratevalue<?php echo $x; ?>" value="<?php echo $val['rate']; ?>">
                                                </td>
                                                <td>
                                                    <input type="text" name="amount" id="amount<?php echo $x; ?>" value="<?php echo $val['amount']; ?>" class="form-control" readonly>
                                                    <input type="hidden" name="amount_value[]" id="amountvalue<?php echo $x; ?>" value="<?php echo $val['amount']; ?>">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(<?php echo $x; ?>)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php $x++;
                                        endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
                                        <input type="text" class="form-control" id="gross_amount" name="gross_amount" readonly>
                                        <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="discount" class="col-sm-5 control-label">Discount</label>
                                    <div class="col-sm-7">
                                        <input type="number" class="form-control" id="discount" name="discount" onkeyup="subAmount()" min="0" step="0.01" value="<?php echo $order_data['order']['discount']; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="net_amount" class="col-sm-5 control-label" style="font-size:16px"><strong>Total Amount</strong></label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="net_amount" name="net_amount" readonly style="font-size:18px; font-weight:bold; color:#00a65a;">
                                        <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Current Payment Status - Read Only -->
                                <div class="alert alert-info">
                                    <h4><i class="fa fa-info-circle"></i> Current Payment Status</h4>
                                    <table class="table table-condensed" style="background:white; margin-bottom:0;">
                                        <tr>
                                            <th width="50%">Paid Amount</th>
                                            <td class="text-success"><strong><?php echo number_format($order_data['order']['paid_amount'], 2); ?> DZD</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Due Amount</th>
                                            <td class="text-danger"><strong><?php echo number_format($order_data['order']['due_amount'], 2); ?> DZD</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Payment Status</th>
                                            <td>
                                                <?php if ($order_data['order']['paid_status'] == 1): ?>
                                                    <span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span>
                                                <?php elseif ($order_data['order']['paid_status'] == 3): ?>
                                                    <span class="label label-warning"><i class="fa fa-clock-o"></i> Partially Paid</span>
                                                <?php else: ?>
                                                    <span class="label label-danger"><i class="fa fa-times"></i> Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fa fa-lightbulb-o"></i> <strong>Note:</strong> To add/modify payments, use the <strong>View Details</strong> button from the orders list.
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields to maintain payment data -->
                        <input type="hidden" name="paid_amount" value="<?php echo $order_data['order']['paid_amount']; ?>">
                        <input type="hidden" name="payment_method" value="<?php echo $order_data['order']['payment_method']; ?>">
                        <input type="hidden" name="payment_notes" value="<?php echo $order_data['order']['payment_notes']; ?>">

                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                        <a href="<?php echo base_url('orders/invoice/' . $order_data['order']['id']); ?>" target="_blank" class="btn btn-default btn-lg">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>
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

<!-- Add Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* Custom styling */
    .available-qty {
        font-size: 16px;
        padding: 8px 12px;
    }

    #productinfotable input[type="number"] {
        text-align: center;
    }

    .alert-info {
        background-color: #d9edf7;
        border-color: #bce8f1;
    }

    .alert-warning {
        background-color: #fcf8e3;
        border-color: #faebcc;
    }
</style>

<!-- Add Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    var rowNum = <?php echo isset($order_data['order_item']) ? count($order_data['order_item']) : 1; ?>;
    var baseurl = "<?php echo base_url(); ?>";

    $(document).ready(function() {
        $('#ordersMainNav').addClass('active');
        $('#manageOrdersNav').addClass('active');

        // Initialize Select2
        initCustomerSelect2();
        initProductSelect2();

        // Calculate initial totals
        subAmount();

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
        $('#editOrderForm').on('submit', function(e) {
            var netamount = parseFloat($('#net_amount_value').val()) || 0;

            if (netamount <= 0) {
                e.preventDefault();
                alert('Please add at least one product!');
                return false;
            }

            // Validate stock
            var valid = true;
            $('#productinfotable tbody tr').each(function() {
                var row = $(this);
                var rowid = row.attr('id').replace('row', ''); // ✅ SANS underscore
                var qty = parseFloat($('#qty' + rowid).val()) || 0; // ✅ SANS underscore
                var available = parseFloat($('#availableqty' + rowid).text()) || 0; // ✅ SANS underscore

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
            placeholder: "Search customer by name or phone...",
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

            // Set default type
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

            // Update product prices
            updateAllProductPrices(type);

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
            placeholder: "Type to search product...",
            allowClear: true,
            width: '100%'
        });
    }

    // ===== ADD/REMOVE ROWS =====
    function addRow() {
        rowNum++;
        var html = '<tr id="row' + rowNum + '">'; // ✅ SANS underscore

        html += '<td>';
        html += '<select class="form-control select2-product" name="product[]" id="product' + rowNum + '" onchange="getProductData(' + rowNum + ')" data-row-id="' + rowNum + '" style="width:100%" required>'; // ✅ SANS underscore
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

        html += '<td><span class="badge bg-green available-qty" id="availableqty' + rowNum + '">0</span></td>'; // ✅ SANS underscore
        html += '<td><input type="number" name="qty[]" id="qty' + rowNum + '" class="form-control" onkeyup="getTotal(' + rowNum + ')" min="1" value="1" required></td>'; // ✅ SANS underscore
        html += '<td><input type="text" name="rate[]" id="rate' + rowNum + '" class="form-control" readonly><input type="hidden" name="rate_value[]" id="ratevalue' + rowNum + '"></td>'; // ✅ SANS underscore
        html += '<td><input type="text" name="amount[]" id="amount' + rowNum + '" class="form-control" readonly><input type="hidden" name="amount_value[]" id="amountvalue' + rowNum + '"></td>'; // ✅ SANS underscore
        html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(' + rowNum + ')"><i class="fa fa-trash"></i></button></td>';
        html += '</tr>';

        $('#productinfotable tbody').append(html);
        initProductSelect2();
    }

    function removeRow(rowid) {
        if ($('#productinfotable tbody tr').length > 1) {
            $('#row' + rowid).remove(); // ✅ SANS underscore
            subAmount();
        } else {
            alert('Cannot remove the last row!');
        }
    }

    // ===== PRODUCT DATA & PRICING =====
    function getProductData(rowid) {
        var productid = $('#product' + rowid).val(); // ✅ SANS underscore
        var selectedOption = $('#product' + rowid + ' option:selected'); // ✅ SANS underscore
        var customerType = getCustomerType();

        if (productid) {
            var qty = selectedOption.attr('data-qty');

            // Mapper le type vers le bon nom d'attribut
            var typeMap = {
                'super_wholesale': 'super-wholesale', // ✅ AVEC underscore dans la BDD
                'superwholesale': 'super-wholesale',
                'wholesale': 'wholesale',
                'retail': 'retail'
            };

            var mappedType = typeMap[customerType] || 'retail';
            var priceAttr = 'data-price-' + mappedType;
            var price = selectedOption.attr(priceAttr);

            console.log('🔍 Type:', customerType, 'Attr:', priceAttr, 'Price:', price);

            if (!price || price === '' || parseFloat(price) === 0) {
                price = selectedOption.attr('data-price-retail');
            }
            price = parseFloat(price) || 0;

            // Display stock
            $('#availableqty' + rowid).text(qty); // ✅ SANS underscore
            $('#qty' + rowid).attr('max', qty); // ✅ SANS underscore

            // Set price
            $('#rate' + rowid).val(parseFloat(price).toFixed(2) + ' DZD'); // ✅ SANS underscore
            $('#ratevalue' + rowid).val(price); // ✅ SANS underscore (ratevalue, pas rate_value)

            getTotal(rowid);
        } else {
            $('#rate' + rowid).val('0');
            $('#ratevalue' + rowid).val('0');
            $('#availableqty' + rowid).text('0');
            getTotal(rowid);
        }
    }

    function getCustomerType() {
        return $('#customer_type_override').val() || 'retail';
    }

    function updateAllProductPrices(customerType) {
        var typeMap = {
            'super_wholesale': 'super-wholesale', // ✅ AVEC underscore dans la BDD
            'superwholesale': 'super-wholesale',
            'wholesale': 'wholesale',
            'retail': 'retail'
        };

        var mappedType = typeMap[customerType] || 'retail';

        console.log('🔄 Updating all prices - Type:', customerType, 'Mapped:', mappedType);

        $('#productinfotable tbody tr').each(function() {
            var row = $(this);
            var rowid = row.attr('id').replace('row', ''); // ✅ SANS underscore
            var selectedOption = $('#product' + rowid + ' option:selected'); // ✅ SANS underscore
            var productid = $('#product' + rowid).val(); // ✅ SANS underscore

            if (productid) {
                var priceAttr = 'data-price-' + mappedType;
                var price = selectedOption.attr(priceAttr);

                console.log('📍 Row', rowid, 'Attr:', priceAttr, 'Value:', price);

                if (!price || price === '' || parseFloat(price) === 0) {
                    price = selectedOption.attr('data-price-retail');
                }
                price = parseFloat(price) || 0;

                $('#rate' + rowid).val(parseFloat(price).toFixed(2) + ' DZD'); // ✅ SANS underscore
                $('#ratevalue' + rowid).val(price); // ✅ SANS underscore
                getTotal(rowid);
            }
        });
    }

    // ===== CALCULATIONS =====
    function getTotal(rowid) {
        var qty = parseFloat($('#qty' + rowid).val()) || 0; // ✅ SANS underscore
        var rate = parseFloat($('#ratevalue' + rowid).val()) || 0; // ✅ SANS underscore (ratevalue)
        var amount = qty * rate;

        $('#amount' + rowid).val(amount.toFixed(2) + ' DZD'); // ✅ SANS underscore
        $('#amountvalue' + rowid).val(amount.toFixed(2)); // ✅ SANS underscore (amountvalue)

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
        var netamount = (grossamount - discount).toFixed(2);

        $('#net_amount').val(netamount + ' DZD');
        $('#net_amount_value').val(netamount);
    }
</script>