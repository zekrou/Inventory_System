<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1>
            <i class="fa fa-shopping-cart"></i> Purchase Order Details
            <small>#<?php echo isset($purchase['purchase_no']) ? $purchase['purchase_no'] : 'N/A'; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('purchases') ?>">Purchases</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php if(isset($purchase) && !empty($purchase)): ?>
            
            <!-- Messages -->
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php elseif($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-exclamation-triangle"></i> <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="btn-group" style="margin-bottom:15px;">
                <a href="<?php echo base_url('purchases') ?>" class="btn btn-default btn-flat">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <a href="<?php echo base_url('purchases/invoice/'.$purchase['id']) ?>" target="_blank" class="btn btn-primary btn-flat">
                    <i class="fa fa-print"></i> Print Invoice
                </a>
                
                <?php if($purchase['status'] == 'pending' && isset($user_permission['updatePurchase'])): ?>
                    <button type="button" class="btn btn-success btn-flat" onclick="receivePurchase(<?php echo $purchase['id']; ?>)">
                        <i class="fa fa-check"></i> Receive
                    </button>
                    <button type="button" class="btn btn-warning btn-flat" onclick="cancelPurchase(<?php echo $purchase['id']; ?>)">
                        <i class="fa fa-ban"></i> Cancel
                    </button>
                <?php endif; ?>
                
                <!-- ✅ BOUTON ADD PAYMENT -->
                <?php 
                $payment_status = isset($purchase['payment_status']) ? $purchase['payment_status'] : 'unpaid';
                if($payment_status != 'paid' && isset($user_permission['updatePurchase'])): 
                ?>
                    <button type="button" class="btn btn-info btn-flat" data-toggle="modal" data-target="#addPaymentModal">
                        <i class="fa fa-money"></i> Add Payment
                    </button>
                <?php endif; ?>
                
                <?php if($purchase['status'] != 'received' && isset($user_permission['deletePurchase'])): ?>
                    <button type="button" class="btn btn-danger btn-flat" onclick="showDeleteModal(<?php echo $purchase['id']; ?>)">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                <?php endif; ?>
            </div>

            <!-- Rest of the view... (same as before) -->
            <div class="row">
                <!-- Purchase Info -->
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-info-circle"></i> Purchase Information</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:40%">Purchase No:</th>
                                    <td><strong><?php echo $purchase['purchase_no']; ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td><?php echo isset($purchase['supplier_name']) ? $purchase['supplier_name'] : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?php echo isset($purchase['supplier_phone']) ? $purchase['supplier_phone'] : '-'; ?></td>
                                </tr>
                                <tr>
                                    <th>Purchase Date:</th>
                                    <td><?php echo date('d-m-Y H:i', strtotime($purchase['purchase_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php if($purchase['status'] == 'pending'): ?>
                                            <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                        <?php elseif($purchase['status'] == 'received'): ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Received</span>
                                        <?php else: ?>
                                            <span class="label label-danger"><i class="fa fa-times"></i> Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-money"></i> Payment Summary</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:40%">Total Amount:</th>
                                    <td style="font-size:18px;"><strong><?php echo number_format($purchase['total_amount'], 2); ?> DZD</strong></td>
                                </tr>
                                <tr style="background:#d4edda;">
                                    <th>Total Paid:</th>
                                    <td style="font-size:18px;color:#28a745;">
                                        <strong><?php echo number_format(isset($purchase['paid_amount']) ? $purchase['paid_amount'] : 0, 2); ?> DZD</strong>
                                    </td>
                                </tr>
                                <tr style="background:#f8d7da;">
                                    <th>Remaining (Due):</th>
                                    <td style="font-size:18px;color:#dc3545;">
                                        <strong>
                                            <?php 
                                            $due = isset($purchase['due_amount']) 
                                                   ? $purchase['due_amount'] 
                                                   : ($purchase['total_amount'] - (isset($purchase['paid_amount']) ? $purchase['paid_amount'] : 0));
                                            echo number_format($due, 2); 
                                            ?> DZD
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        <?php if($payment_status == 'paid'): ?>
                                            <span class="label label-success" style="font-size:14px;">Fully Paid</span>
                                        <?php elseif($payment_status == 'partial'): ?>
                                            <span class="label label-warning" style="font-size:14px;">Partial Payment</span>
                                        <?php else: ?>
                                            <span class="label label-danger" style="font-size:14px;">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Items -->
            <?php if(isset($items) && !empty($items)): ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cubes"></i> Purchase Items</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background:#f4f4f4;">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            $grandTotal = 0; 
                            foreach($items as $item): 
                                $totalPrice = $item['quantity'] * $item['unit_price'];
                                $grandTotal += $totalPrice;
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo isset($item['sku']) ? htmlspecialchars($item['sku']) : '-'; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['unit_price'], 2); ?> DZD</td>
                                <td><?php echo number_format($totalPrice, 2); ?> DZD</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot style="background:#f9f9f9;">
                            <tr>
                                <th colspan="5" class="text-right">Grand Total:</th>
                                <th><?php echo number_format($grandTotal, 2); ?> DZD</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- ✅ PAYMENT HISTORY -->
            <?php if(isset($payment_history) && !empty($payment_history)): ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-history"></i> Payment History</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach($payment_history as $payment): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($payment['payment_date'])); ?></td>
                                <td><strong><?php echo number_format($payment['amount_paid'], 2); ?> DZD</strong></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                <td><?php echo $payment['reference_number'] ?: '-'; ?></td>
                                <td><?php echo $payment['notes'] ?: '-'; ?></td>
                                <td><?php echo $payment['username']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fa fa-warning"></i> Purchase not found!
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- ✅ ADD PAYMENT MODAL -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addPaymentForm" method="post" action="<?php echo base_url('purchases/addPayment') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-money"></i> Add Payment</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="purchase_id" value="<?php echo $purchase['id']; ?>">
                    
                    <div class="alert alert-info">
                        <strong>Remaining Balance:</strong> <?php echo number_format($due, 2); ?> DZD
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="payment_date" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount to Pay <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount_paid" step="0.01" min="0.01" max="<?php echo $due; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Reference Number</label>
                        <input type="text" class="form-control" name="reference_number" placeholder="Cheque/Transfer reference">
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="payment_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-warning"></i> Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Warning!</strong> This cannot be undone.
                </div>
                <p>Are you sure you want to delete this purchase?</p>
                <input type="hidden" id="delete_purchase_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fa fa-trash"></i> Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var baseurl = "<?php echo base_url(); ?>";

function receivePurchase(id) {
    if(confirm('Mark as received? This will update stock.')) {
        window.location.href = baseurl + 'purchases/receive/' + id;
    }
}

function cancelPurchase(id) {
    if(confirm('Cancel this purchase?')) {
        window.location.href = baseurl + 'purchases/cancel/' + id;
    }
}

function showDeleteModal(id) {
    $('#delete_purchase_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    var id = $('#delete_purchase_id').val();
    var btn = $('button[onclick="confirmDelete()"]');
    btn.html('<i class="fa fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);
    
    $.ajax({
        url: baseurl + 'purchases/remove',
        type: 'POST',
        data: {purchase_id: id},
        dataType: 'json',
        success: function(response) {
            if(response.success === true) {
                $('#deleteModal').modal('hide');
                alert('✓ ' + response.messages);
                window.location.href = baseurl + 'purchases';
            } else {
                alert('✗ ' + response.messages);
                btn.html('<i class="fa fa-trash"></i> Yes, Delete').prop('disabled', false);
            }
        },
        error: function() {
            alert('Error deleting purchase');
            btn.html('<i class="fa fa-trash"></i> Yes, Delete').prop('disabled', false);
        }
    });
}
</script>
