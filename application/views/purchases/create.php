<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i> Create Purchase Order
            <small>New Purchase</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('purchases') ?>">Purchases</a></li>
            <li class="active">Create</li>
        </ol>
    </section>
    
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                
                <!-- Messages -->
                <div id="messages"></div>
                
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php elseif($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <form role="form" action="<?php echo base_url('purchases/create') ?>" method="post" id="createPurchaseForm">
                    
                    <div class="row">
                        <!-- Supplier Information -->
                        <div class="col-md-6">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-truck"></i> Supplier Information</h3>
                                </div>
                                <div class="box-body">
                                    <!-- Select Supplier -->
                                    <div class="form-group">
                                        <label>Select Supplier <span class="text-danger">*</span></label>
                                        <select class="form-control selectgroup" id="supplier_id" name="supplier_id" style="width:100%" required>
                                            <option value="">-- Select Supplier or Create New --</option>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?php echo $supplier['id']; ?>">
                                                    <?php echo $supplier['name']; ?> 
                                                    <?php if(!empty($supplier['supplier_code'])): ?>
                                                        (<?php echo $supplier['supplier_code']; ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="new" style="background-color:#d4edda;font-weight:bold;color:#155724;">
                                                ➕ Create New Supplier
                                            </option>
                                        </select>
                                    </div>

                                    <!-- New Supplier Form (Hidden) -->
                                    <div id="newSupplierForm" style="display:none; margin-top:20px; padding:15px; background:#f9f9f9; border:2px solid #3c8dbc; border-radius:5px;">
                                        <h4 style="margin-top:0; color:#3c8dbc;">
                                            <i class="fa fa-plus-circle"></i> New Supplier Details
                                        </h4>
                                        
                                        <div class="form-group">
                                            <label>Supplier Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="new_supplier_name" name="new_supplier_name" placeholder="Company Name">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Contact Person</label>
                                            <input type="text" class="form-control" id="new_contact_person" name="new_contact_person" placeholder="Contact Person Name">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="new_phone" name="new_phone" placeholder="Phone Number">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" id="new_email" name="new_email" placeholder="Email Address">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea class="form-control" id="new_address" name="new_address" rows="2" placeholder="Full Address"></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Payment Terms</label>
                                            <input type="text" class="form-control" id="new_payment_terms" name="new_payment_terms" placeholder="Ex: Net 30 days">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Information -->
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-calendar"></i> Purchase Information</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label>Purchase Date</label>
                                        <input type="text" class="form-control" value="<?php echo date('d-m-Y H:i') ?>" readonly style="background:#f4f4f4;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Expected Delivery Date</label>
                                        <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes (optional)"></textarea>
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
                                    <thead style="background:#f4f4f4;">
                                        <tr>
                                            <th style="width:35%">Product</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:20%">Unit Price (DZD)</th>
                                            <th style="width:20%">Subtotal (DZD)</th>
                                            <th style="width:10%"><i class="fa fa-trash"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row1">
                                            <td>
                                                <select class="form-control selectgroup product" data-row-id="row1" id="product1" name="product[]" style="width:100%" onchange="getProductData(1)" required>
                                                    <option value="">Select Product</option>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?php echo $product['id'] ?>">
                                                            <?php echo $product['name'] ?> (<?php echo $product['sku'] ?>)
                                                        </option>
                                                    <?php endforeach ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="qty[]" id="qty1" class="form-control" required onkeyup="getTotal(1)" min="1" value="1">
                                                <small class="text-muted">Stock: <span id="stock1" style="font-weight:bold;color:#3c8dbc;">-</span></small>
                                            </td>
                                            <td>
                                                <input type="number" name="price[]" id="price1" class="form-control" required onkeyup="getTotal(1)" min="0" step="0.01" placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="text" id="subtotal1" class="form-control" disabled style="background:#f4f4f4;font-weight:bold;">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(1)" disabled>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot style="background:#f9f9f9;">
                                        <tr>
                                            <th colspan="3" class="text-right" style="font-size:16px;">TOTAL:</th>
                                            <th colspan="2">
                                                <input type="text" id="total_amount_display" class="form-control input-lg" disabled style="font-size:18px;font-weight:bold;color:#00a65a;background:#fff;border:2px solid #00a65a;">
                                                <input type="hidden" id="total_amount_value" name="total_amount">
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-money"></i> Payment Details</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Total Amount</strong></label>
                                        <input type="text" class="form-control input-lg" id="total_amount_display_2" disabled style="font-size:20px;font-weight:bold;color:#00a65a;background:#d4edda;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Amount Paid Now</label>
                                        <input type="number" class="form-control" id="paid_amount" name="paid_amount" value="0" min="0" step="0.01">
                                        <small class="text-muted">Enter 0 if payment will be made later</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="">-- Not Paid Yet --</option>
                                            <option value="cash">Cash</option>
                                            <option value="credit">Credit (On Account)</option>
                                            <option value="cheque">Cheque</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Due Amount (Remaining)</strong></label>
                                        <input type="text" class="form-control input-lg" id="due_amount_display" disabled style="font-size:20px;font-weight:bold;color:#dd4b39;background:#f8d7da;">
                                        <input type="hidden" name="due_amount" id="due_amount_value">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Payment Status</label>
                                        <select class="form-control" id="payment_status_display" disabled style="background:#f4f4f4;">
                                            <option value="unpaid">Unpaid</option>
                                            <option value="partial">Partial Payment</option>
                                            <option value="paid">Fully Paid</option>
                                        </select>
                                        <input type="hidden" name="payment_status" id="payment_status">
                                        <small class="text-muted"><i class="fa fa-magic"></i> Auto-calculated</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Reference Number</label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Cheque/Transfer ref (optional)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary btn-lg pull-right">
                                <i class="fa fa-save"></i> Create Purchase Order
                            </button>
                            <a href="<?php echo base_url('purchases') ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
var baseurl = "<?php echo base_url(); ?>";
var rowNum = 1;

$(document).ready(function() {
    $('.selectgroup').select2();
    $('#purchasesMainNav').addClass('active');
    
    // Show/Hide new supplier form
    $('#supplier_id').on('change', function() {
        if($(this).val() === 'new') {
            $('#newSupplierForm').slideDown();
            $('#new_supplier_name').prop('required', true);
            $('#new_phone').prop('required', true);
        } else {
            $('#newSupplierForm').slideUp();
            $('#new_supplier_name').prop('required', false);
            $('#new_phone').prop('required', false);
        }
    });
    
    // Auto-calculate payment status
    $('#paid_amount').on('keyup change', function() {
        calculatePaymentStatus();
    });
    
    // Add row button
    $('#addrow').unbind('click').bind('click', function() {
        addRow();
    });
});

function addRow() {
    rowNum++;
    var html = '<tr id="row' + rowNum + '">';
    html += '<td>';
    html += '<select class="form-control selectgroup product" data-row-id="row' + rowNum + '" id="product' + rowNum + '" name="product[]" style="width:100%" onchange="getProductData(' + rowNum + ')" required>';
    html += '<option value="">Select Product</option>';
    <?php foreach ($products as $product): ?>
    html += '<option value="<?php echo $product['id'] ?>"><?php echo $product['name'] ?> (<?php echo $product['sku'] ?>)</option>';
    <?php endforeach ?>
    html += '</select>';
    html += '</td>';
    html += '<td>';
    html += '<input type="number" name="qty[]" id="qty' + rowNum + '" class="form-control" required onkeyup="getTotal(' + rowNum + ')" min="1" value="1">';
    html += '<small class="text-muted">Stock: <span id="stock' + rowNum + '" style="font-weight:bold;color:#3c8dbc;">-</span></small>';
    html += '</td>';
    html += '<td><input type="number" name="price[]" id="price' + rowNum + '" class="form-control" required onkeyup="getTotal(' + rowNum + ')" min="0" step="0.01" placeholder="0.00"></td>';
    html += '<td><input type="text" id="subtotal' + rowNum + '" class="form-control" disabled style="background:#f4f4f4;font-weight:bold;"></td>';
    html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(' + rowNum + ')"><i class="fa fa-trash"></i></button></td>';
    html += '</tr>';
    
    $('#productinfotable tbody').append(html);
    $('.selectgroup').select2();
}

function removeRow(row) {
    if($('#productinfotable tbody tr').length > 1) {
        $('#row' + row).remove();
        calculateGrandTotal();
    } else {
        alert('Cannot remove the last row!');
    }
}

function getProductData(row) {
    var productId = $('#product' + row).val();
    
    if(productId) {
        $.ajax({
            url: baseurl + 'purchases/getProductPrice',
            type: 'post',
            data: {product_id: productId},
            dataType: 'json',
            success: function(response) {
                console.log('Product Data:', response);
                
                // Display current stock
                var currentStock = response.qty || 0;
                $('#stock' + row).text(currentStock + ' units');
                
                // Default price (last purchase price or default price)
                var price = response.last_purchase_price || response.price_default || 0;
                $('#price' + row).val(price);
                
                // Default quantity
                $('#qty' + row).val(1);
                
                // Calculate total
                getTotal(row);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#price' + row).val(0);
                $('#stock' + row).text('Error');
            }
        });
    } else {
        $('#price' + row).val(0);
        $('#stock' + row).text('-');
        getTotal(row);
    }
}

function getTotal(row) {
    var qty = Number($('#qty' + row).val()) || 0;
    var price = Number($('#price' + row).val()) || 0;
    var total = (qty * price).toFixed(2);
    
    $('#subtotal' + row).val(total);
    
    calculateGrandTotal();
}

function calculateGrandTotal() {
    var grandTotal = 0;
    
    $('input[name="qty[]"]').each(function(index) {
        var rowId = index + 1;
        var qty = Number($(this).val()) || 0;
        var price = Number($('#price' + rowId).val()) || 0;
        grandTotal += (qty * price);
    });
    
    grandTotal = grandTotal.toFixed(2);
    $('#total_amount_display').val(grandTotal + ' DZD');
    $('#total_amount_display_2').val(grandTotal + ' DZD');
    $('#total_amount_value').val(grandTotal);
    
    calculatePaymentStatus();
}

function calculatePaymentStatus() {
    var total = parseFloat($('#total_amount_value').val()) || 0;
    var paid = parseFloat($('#paid_amount').val()) || 0;
    
    // Cap paid amount at total
    if(paid > total) {
        paid = total;
        $('#paid_amount').val(total);
    }
    
    var due = total - paid;
    
    $('#due_amount_display').val(due.toFixed(2) + ' DZD');
    $('#due_amount_value').val(due.toFixed(2));
    
    if(paid === 0) {
        $('#payment_status').val('unpaid');
        $('#payment_status_display').val('unpaid');
    } else if(paid >= total) {
        $('#payment_status').val('paid');
        $('#payment_status_display').val('paid');
    } else {
        $('#payment_status').val('partial');
        $('#payment_status_display').val('partial');
    }
}
</script>
