<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Create <small>Purchase Order</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('purchases') ?>">Purchases</a></li>
      <li class="active">Create</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">New Purchase Order</h3>
          </div>

          <form role="form" action="<?php echo base_url('purchases/create') ?>" method="post" class="form-horizontal">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label class="col-sm-12 control-label"><strong>Date: <?php echo date('d-m-Y H:i') ?></strong></label>
              </div>

              <div class="col-md-6 col-xs-12">
                <div class="form-group">
                  <label for="supplier_id" class="col-sm-4 control-label" style="text-align:left;">
                    Select Supplier <span class="text-danger">*</span>
                  </label>
                  <div class="col-sm-8">
                    <select class="form-control select_group" id="supplier_id" name="supplier_id" style="width:100%;" required>
                      <option value="">-- Select Supplier --</option>
                      <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['id'] ?>">
                          <?php echo $supplier['name'] ?> (<?php echo $supplier['supplier_code'] ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <br /><br /><br />

              <table class="table table-bordered" id="product_info_table">
                <thead>
                  <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:15%">Quantity</th>
                    <th style="width:15%">Unit Price</th>
                    <th style="width:15%">Subtotal</th>
                    <th style="width:5%"><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                  </tr>
                </thead>
                <tbody>
                  <tr id="row_1">
                    <td>
                      <select class="form-control select_group product" data-row-id="row_1" id="product_1" name="product[]" style="width:100%;" onchange="getProductData(1)" required>
                        <option value="">Select Product</option>
                        <?php foreach ($products as $product): ?>
                          <option value="<?php echo $product['id'] ?>"><?php echo $product['name'] ?> (<?php echo $product['sku'] ?>)</option>
                        <?php endforeach ?>
                      </select>
                    </td>
                    <td><input type="number" name="qty[]" id="qty_1" class="form-control" required onkeyup="getTotal(1)" min="1"></td>
                    <td>
                      <input type="text" name="price_display[]" id="price_1" class="form-control" disabled>
                      <input type="hidden" name="price[]" id="price_value_1" class="form-control">
                    </td>
                    <td>
                      <input type="text" name="subtotal_display[]" id="subtotal_1" class="form-control" disabled>
                      <input type="hidden" name="subtotal[]" id="subtotal_value_1" class="form-control">
                    </td>
                    <td><button type="button" class="btn btn-default" onclick="removeRow('1')"><i class="fa fa-close"></i></button></td>
                  </tr>
                </tbody>
              </table>

              <br />

              <div class="col-md-6 col-xs-12 pull-right">
                <div class="form-group">
                  <label for="total_amount" class="col-sm-5 control-label"><strong>Total Amount</strong></label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control input-lg" id="total_amount" disabled style="font-weight:bold; font-size:18px;">
                    <input type="hidden" name="total_amount" id="total_amount_value">
                  </div>
                </div>

                <div class="form-group">
                  <label for="notes" class="col-sm-5 control-label">Notes</label>
                  <div class="col-sm-7">
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes"></textarea>
                  </div>
                </div>
              </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Create Purchase</button>
              <a href="<?php echo base_url('purchases/') ?>" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
            </div>
          </form>
        </div>

      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {
  $(".select_group").select2();
  $("#purchaseNav").addClass('active');

  $("#add_row").unbind('click').bind('click', function() {
    var table = $("#product_info_table");
    var count = $("#product_info_table tbody tr").length;
    var row_id = count + 1;

    var html = '<tr id="row_'+row_id+'">'+
      '<td>'+
        '<select class="form-control select_group product" data-row-id="row_'+row_id+'" id="product_'+row_id+'" name="product[]" style="width:100%;" onchange="getProductData('+row_id+')" required>'+
          '<option value="">Select Product</option>';
          <?php foreach ($products as $product): ?>
            html += '<option value="<?php echo $product['id'] ?>"><?php echo $product['name'] ?> (<?php echo $product['sku'] ?>)</option>';
          <?php endforeach ?>
      html += '</select>'+
      '</td>'+
      '<td><input type="number" name="qty[]" id="qty_'+row_id+'" class="form-control" required onkeyup="getTotal('+row_id+')" min="1"></td>'+
      '<td><input type="text" name="price_display[]" id="price_'+row_id+'" class="form-control" disabled><input type="hidden" name="price[]" id="price_value_'+row_id+'"></td>'+
      '<td><input type="text" name="subtotal_display[]" id="subtotal_'+row_id+'" class="form-control" disabled><input type="hidden" name="subtotal[]" id="subtotal_value_'+row_id+'"></td>'+
      '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
    '</tr>';

    $("#product_info_table tbody").append(html);
    $(".product").select2();
  });
});

function getProductData(row_id) {
  var product_id = $("#product_"+row_id).val();
  var supplier_id = $("#supplier_id").val();
  
  if(product_id == "") return;

  $.ajax({
    url: base_url + 'purchases/getProductPrice',
    type: 'post',
    data: {product_id: product_id, supplier_id: supplier_id},
    dataType: 'json',
    success:function(response) {
      var price = response.price_default || 0;
      $("#price_"+row_id).val(price);
      $("#price_value_"+row_id).val(price);
      $("#qty_"+row_id).val(1);
      getTotal(row_id);
    }
  });
}

function getTotal(row) {
  var qty = Number($("#qty_"+row).val()) || 0;
  var price = Number($("#price_value_"+row).val()) || 0;
  var total = (qty * price).toFixed(2);
  
  $("#subtotal_"+row).val(total);
  $("#subtotal_value_"+row).val(total);
  
  calculateGrandTotal();
}

function calculateGrandTotal() {
  var grandTotal = 0;
  $("#product_info_table tbody tr").each(function(){
    var id = $(this).attr('id').replace('row_','');
    grandTotal += Number($("#subtotal_value_"+id).val()) || 0;
  });
  
  grandTotal = grandTotal.toFixed(2);
  $("#total_amount").val(grandTotal + " DZD");
  $("#total_amount_value").val(grandTotal);
}

function removeRow(tr_id) {
  $("#product_info_table tbody tr#row_"+tr_id).remove();
  calculateGrandTotal();
}
</script>