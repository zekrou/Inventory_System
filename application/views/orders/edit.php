<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage <small>Orders</small></h1>
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
            <h3 class="box-title">Edit Order - <?php echo $order_data['order']['bill_no'] ?></h3>
          </div>

          <form role="form" action="<?php echo base_url('orders/update/'.$order_data['order']['id']); ?>" method="post" class="form-horizontal">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label class="col-sm-12 control-label">Date: <?php echo date('d-m-Y h:i a', $order_data['order']['date_time']) ?></label>
              </div>

              <div class="col-md-4 col-xs-12 pull-left">
                <div class="form-group">
                  <label class="col-sm-5 control-label">Customer Name</label>
                  <div class="col-sm-7">
                    <input type="text" name="customer_name" class="form-control" value="<?php echo $order_data['order']['customer_name'] ?>" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label">Customer Address</label>
                  <div class="col-sm-7">
                    <input type="text" name="customer_address" class="form-control" value="<?php echo $order_data['order']['customer_address'] ?>" required>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label">Customer Phone</label>
                  <div class="col-sm-7">
                    <input type="text" name="customer_phone" class="form-control" value="<?php echo $order_data['order']['customer_phone'] ?>" required>
                  </div>
                </div>
              </div>

              <br /><br/>
              <table class="table table-bordered" id="product_info_table">
                <thead>
                  <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:10%">Qty</th>
                    <th style="width:10%">Rate</th>
                    <th style="width:20%">Amount</th>
                    <th style="width:10%">
                      <button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(isset($order_data['order_item'])): ?>
                    <?php $x = 1; foreach($order_data['order_item'] as $val): ?>
                      <tr id="row_<?php echo $x; ?>">
                        <td>
                          <select class="form-control select_group product" name="product[]" data-row-id="<?php echo $x; ?>" id="product_<?php echo $x; ?>" onchange="getProductData(<?php echo $x; ?>)" required>
                            <option value=""></option>
                            <?php foreach ($products as $p): ?>
                              <option value="<?php echo $p['id']; ?>" <?php echo ($val['product_id'] == $p['id'])?'selected':''; ?>><?php echo $p['name']; ?></option>
                            <?php endforeach; ?>
                          </select>
                        </td>
                        <td><input type="number" name="qty[]" id="qty_<?php echo $x; ?>" value="<?php echo $val['qty']; ?>" class="form-control" onkeyup="getTotal(<?php echo $x; ?>)" required></td>
                        <td>
                          <input type="text" id="rate_<?php echo $x; ?>" value="<?php echo $val['rate']; ?>" class="form-control" disabled>
                          <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" value="<?php echo $val['rate']; ?>">
                        </td>
                        <td>
                          <input type="text" id="amount_<?php echo $x; ?>" value="<?php echo $val['amount']; ?>" class="form-control" disabled>
                          <input type="hidden" name="amount_value[]" id="amount_value_<?php echo $x; ?>" value="<?php echo $val['amount']; ?>">
                        </td>
                        <td><button type="button" class="btn btn-default" onclick="removeRow('<?php echo $x; ?>')"><i class="fa fa-close"></i></button></td>
                      </tr>
                    <?php $x++; endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>

              <br/><br/>
              <div class="col-md-6 col-xs-12 pull-right">
                <div class="form-group">
                  <label class="col-sm-5 control-label">Gross Amount</label>
                  <div class="col-sm-7">
                    <input type="text" id="gross_amount" disabled class="form-control" value="<?php echo $order_data['order']['gross_amount'] ?>">
                    <input type="hidden" name="gross_amount_value" id="gross_amount_value" value="<?php echo $order_data['order']['gross_amount'] ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-5 control-label">Discount</label>
                  <div class="col-sm-7">
                    <input type="text" id="discount" name="discount" class="form-control" onkeyup="subAmount()" value="<?php echo $order_data['order']['discount'] ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-5 control-label"><strong>Net Amount</strong></label>
                  <div class="col-sm-7">
                    <input type="text" id="net_amount" disabled class="form-control input-lg" value="<?php echo $order_data['order']['net_amount'] ?>" style="font-weight:bold; font-size:18px;">
                    <input type="hidden" name="net_amount_value" id="net_amount_value" value="<?php echo $order_data['order']['net_amount'] ?>">
                  </div>
                </div>

                <hr>

                <!-- Current Payment Status (Read-Only) -->
                <div class="alert alert-info">
                  <h4><i class="fa fa-info-circle"></i> Current Payment Status</h4>
                  <table class="table table-condensed" style="background: white; margin-bottom: 0;">
                    <tr>
                      <th width="50%">Total Amount:</th>
                      <td><strong><?php echo number_format($order_data['order']['net_amount'], 2) ?> DZD</strong></td>
                    </tr>
                    <tr>
                      <th>Paid Amount:</th>
                      <td class="text-success"><strong><?php echo number_format($order_data['order']['paid_amount'], 2) ?> DZD</strong></td>
                    </tr>
                    <tr>
                      <th>Due Amount:</th>
                      <td class="text-danger"><strong><?php echo number_format($order_data['order']['due_amount'], 2) ?> DZD</strong></td>
                    </tr>
                    <tr>
                      <th>Payment Status:</th>
                      <td>
                        <?php if($order_data['order']['paid_status'] == 1): ?>
                          <span class="label label-success"><i class="fa fa-check"></i> Fully Paid</span>
                        <?php elseif($order_data['order']['paid_status'] == 3): ?>
                          <span class="label label-warning"><i class="fa fa-clock-o"></i> Partially Paid</span>
                        <?php else: ?>
                          <span class="label label-danger"><i class="fa fa-times"></i> Unpaid</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  </table>
                </div>

                <div class="alert alert-warning">
                  <i class="fa fa-lightbulb-o"></i> <strong>Note:</strong> To add payments or view payment history, 
                  <a href="<?php echo base_url('orders/') ?>" class="alert-link">go back to orders list</a> 
                  and click the <strong>View Details</strong> button (eye icon).
                </div>

                <!-- Hidden fields to maintain payment data -->
                <input type="hidden" name="paid_amount" value="<?php echo $order_data['order']['paid_amount'] ?>">
                <input type="hidden" name="payment_method" value="<?php echo $order_data['order']['payment_method'] ?>">
                <input type="hidden" name="payment_notes" value="<?php echo $order_data['order']['payment_notes'] ?>">
                <input type="hidden" name="customer_id" value="<?php echo $order_data['order']['customer_id'] ?>">
                <input type="hidden" name="customer_type" value="<?php echo $order_data['order']['customer_type'] ?>">
              </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes to Order Items</button>
              <a href="<?php echo base_url('orders/invoice/'.$order_data['order']['id']); ?>" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</a>
              <a href="<?php echo base_url('orders/'); ?>" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back to Orders</a>
            </div>
          </form>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {
  $(".select_group").select2();

  $("#mainOrdersNav").addClass('active');
  $("#manageOrdersNav").addClass('active');
  
  // Calculate initial totals
  subAmount();
});

function getTotal(row) {
  var total = Number($("#rate_value_"+row).val()) * Number($("#qty_"+row).val());
  total = total.toFixed(2);
  $("#amount_"+row).val(total);
  $("#amount_value_"+row).val(total);
  subAmount();
}

function getProductData(row_id) {
  var product_id = $("#product_"+row_id).val();
  if(product_id == "") return;
  
  $.post(base_url+'orders/getProductValueById', {product_id: product_id}, function(response) {
    $("#rate_"+row_id).val(response.price);
    $("#rate_value_"+row_id).val(response.price);
    $("#qty_"+row_id).val(1);
    var total = (Number(response.price) * 1).toFixed(2);
    $("#amount_"+row_id).val(total);
    $("#amount_value_"+row_id).val(total);
    subAmount();
  }, 'json');
}

function subAmount() {
  var totalSubAmount = 0;
  $("#product_info_table tbody tr").each(function(){
    var id = $(this).attr('id').replace('row_','');
    totalSubAmount += Number($("#amount_"+id).val());
  });

  totalSubAmount = totalSubAmount.toFixed(2);
  $("#gross_amount").val(totalSubAmount);
  $("#gross_amount_value").val(totalSubAmount);

  var discount = $("#discount").val() ? Number($("#discount").val()) : 0;
  var net = (totalSubAmount - discount).toFixed(2);
  $("#net_amount").val(net);
  $("#net_amount_value").val(net);
}

function removeRow(tr_id) {
  $("#product_info_table tbody tr#row_"+tr_id).remove();
  subAmount();
}
</script>

<style>
.alert-info {
  background-color: #d9edf7;
  border-color: #bce8f1;
}
.alert-warning {
  background-color: #fcf8e3;
  border-color: #faebcc;
}
</style>