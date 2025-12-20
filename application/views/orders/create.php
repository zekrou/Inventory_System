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
          
          <form role="form" action="<?php base_url('orders/create') ?>" method="post" class="form-horizontal">
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
                      <select class="form-control select_group" id="customer_select" name="customer_id" style="width:100%;" onchange="loadCustomerData()">
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
                      <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter Customer Name" autocomplete="off" onblur="checkDuplicateCustomer()" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_phone" class="col-sm-5 control-label" style="text-align:left;">Customer Phone</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Enter Customer Phone" autocomplete="off" onblur="checkDuplicateCustomer()">
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
  var base_url = "<?php echo base_url(); ?>";
  var current_customer_type = "retail";
  var selectedCustomerId = null;
  var duplicateCheckTimeout = null;

  $(document).ready(function() {
    $(".select_group").select2();

    $("#mainOrdersNav").addClass('active');
    $("#addOrderNav").addClass('active');
  
    // Add new row in the table 
    $("#add_row").unbind('click').bind('click', function() {
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      var row_id = count_table_tbody_tr + 1;

      $.ajax({
          url: base_url + '/orders/getTableProductRow/',
          type: 'post',
          data: {customer_type: current_customer_type},
          dataType: 'json',
          success:function(response) {
            
               var html = '<tr id="row_'+row_id+'">'+
                   '<td>'+ 
                    '<select class="form-control select_group product" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" style="width:100%;" onchange="getProductData('+row_id+')">'+
                        '<option value="">Select Product</option>';
                        $.each(response, function(index, value) {
                          html += '<option value="'+value.id+'">'+value.name+'</option>';             
                        });
                        
                      html += '</select>'+
                    '</td>'+ 
                    '<td><input type="number" name="qty[]" id="qty_'+row_id+'" class="form-control" onkeyup="getTotal('+row_id+')"></td>'+
                    '<td><input type="text" name="rate[]" id="rate_'+row_id+'" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>'+
                    '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
                    '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
                    '</tr>';

                if(count_table_tbody_tr >= 1) {
                $("#product_info_table tbody tr:last").after(html);  
              }
              else {
                $("#product_info_table tbody").html(html);
              }

              $(".product").select2();

          }
        });

      return false;
    });

  });

  // Check for duplicate customers
  function checkDuplicateCustomer() {
    // Only check if we're creating a new customer
    var selectedCustomer = $("#customer_select").val();
    if(selectedCustomer !== "new" && selectedCustomer !== "") {
      return; // Don't check if existing customer is selected
    }

    var customerName = $("#customer_name").val().trim();
    var customerPhone = $("#customer_phone").val().trim();
    
    // Need at least one field to check
    if(!customerName && !customerPhone) {
      $("#duplicate_warning").hide();
      return;
    }

    // Clear previous timeout
    if(duplicateCheckTimeout) {
      clearTimeout(duplicateCheckTimeout);
    }

    // Delay the check to avoid too many requests while typing
    duplicateCheckTimeout = setTimeout(function() {
      $.ajax({
        url: base_url + 'orders/checkDuplicateCustomer',
        type: 'POST',
        data: {
          customer_name: customerName,
          customer_phone: customerPhone
        },
        dataType: 'json',
        success: function(response) {
          if(response.exists && response.suggestions.length > 0) {
            showDuplicateWarning(response.suggestions);
          } else {
            $("#duplicate_warning").hide();
          }
        },
        error: function() {
          console.log('Error checking for duplicates');
        }
      });
    }, 500); // Wait 500ms after user stops typing
  }

  // Show duplicate warning with suggestions
  function showDuplicateWarning(suggestions) {
    var html = '<p><strong>Similar customers found:</strong></p>';
    html += '<div class="list-group" style="max-height: 300px; overflow-y: auto;">';
    
    $.each(suggestions, function(index, customer) {
      var typeLabel = '';
      if(customer.type == 'super_wholesale') {
        typeLabel = '<span class="label label-danger">Super Gros</span>';
      } else if(customer.type == 'wholesale') {
        typeLabel = '<span class="label label-warning">Gros</span>';
      } else {
        typeLabel = '<span class="label label-info">Détail</span>';
      }
      
      html += '<a href="javascript:void(0)" class="list-group-item" onclick="selectSuggestedCustomer(' + customer.id + ')">';
      html += '<h4 class="list-group-item-heading">';
      html += '<i class="fa fa-user"></i> ' + customer.name + ' ' + typeLabel;
      html += '</h4>';
      html += '<p class="list-group-item-text">';
      html += '<strong>Code:</strong> ' + customer.code + '<br>';
      html += '<strong>Phone:</strong> ' + customer.phone + '<br>';
      html += '<strong>Address:</strong> ' + customer.address + '<br>';
      html += '<small class="text-muted"><i class="fa fa-info-circle"></i> ' + customer.match_reason + '</small>';
      html += '</p>';
      html += '</a>';
    });
    
    html += '</div>';
    html += '<p class="text-muted" style="margin-top: 10px;"><small><i class="fa fa-lightbulb-o"></i> Click on a customer to use their information, or close this warning to create a new customer.</small></p>';
    
    $("#duplicate_suggestions").html(html);
    $("#duplicate_warning").slideDown();
  }

  // Select suggested customer
  function selectSuggestedCustomer(customerId) {
    selectedCustomerId = customerId;
    
    // Set the customer in the dropdown
    $("#customer_select").val(customerId).trigger('change');
    
    // Load customer data
    loadCustomerData();
    
    // Hide duplicate warning
    $("#duplicate_warning").slideUp();
  }

  // Use existing customer from modal
  function useExistingCustomer() {
    if(selectedCustomerId) {
      $("#customer_select").val(selectedCustomerId).trigger('change');
      loadCustomerData();
      $("#useCustomerModal").modal('hide');
      $("#duplicate_warning").slideUp();
    }
  }

  // Load customer data when selected
  function loadCustomerData() {
    var customer_id = $("#customer_select").val();
    
    if(customer_id === "new") {
      $("#customer_name").val("").prop('readonly', false);
      $("#customer_address").val("").prop('readonly', false);
      $("#customer_phone").val("").prop('readonly', false);
      $("#customer_type").val("retail");
      current_customer_type = "retail";
      $("#customer_type_display").hide();
      $("#duplicate_warning").hide();
      
      $("#product_info_table tbody").html('<tr id="row_1"><td><select class="form-control select_group product" data-row-id="row_1" id="product_1" name="product[]" style="width:100%;" onchange="getProductData(1)" required><option value="">Select Product</option><?php foreach ($products as $k => $v): ?><option value="<?php echo $v["id"] ?>"><?php echo $v["name"] ?></option><?php endforeach ?></select></td><td><input type="number" name="qty[]" id="qty_1" class="form-control" required onkeyup="getTotal(1)"></td><td><input type="text" name="rate[]" id="rate_1" class="form-control" disabled autocomplete="off"><input type="hidden" name="rate_value[]" id="rate_value_1" class="form-control" autocomplete="off"></td><td><input type="text" name="amount[]" id="amount_1" class="form-control" disabled autocomplete="off"><input type="hidden" name="amount_value[]" id="amount_value_1" class="form-control" autocomplete="off"></td><td><button type="button" class="btn btn-default" onclick="removeRow(\'1\')"><i class="fa fa-close"></i></button></td></tr>');
      $(".product").select2();
      
    } else if(customer_id) {
      var selected = $("#customer_select option:selected");
      var customer_type = selected.data('type');
      var customer_phone = selected.data('phone');
      var customer_address = selected.data('address');
      var customer_name = selected.text().split('(')[0].trim();
      
      $("#customer_name").val(customer_name).prop('readonly', true);
      $("#customer_phone").val(customer_phone).prop('readonly', true);
      $("#customer_address").val(customer_address).prop('readonly', true);
      $("#customer_type").val(customer_type);
      current_customer_type = customer_type;
      $("#duplicate_warning").hide();
      
      $("#customer_type_display").show();
      if(customer_type === 'super_wholesale') {
        $("#customer_type_badge").removeClass().addClass('label label-danger').text('Super Gros');
        $("#pricing_info").text(' - Super wholesale pricing applied');
      } else if(customer_type === 'wholesale') {
        $("#customer_type_badge").removeClass().addClass('label label-warning').text('Gros');
        $("#pricing_info").text(' - Wholesale pricing applied');
      } else {
        $("#customer_type_badge").removeClass().addClass('label label-info').text('Détail');
        $("#pricing_info").text(' - Retail pricing applied');
      }
      
      refreshAllProductPrices();
      
    } else {
      $("#customer_name").val("").prop('readonly', false);
      $("#customer_address").val("").prop('readonly', false);
      $("#customer_phone").val("").prop('readonly', false);
      $("#customer_type").val("retail");
      current_customer_type = "retail";
      $("#customer_type_display").hide();
      $("#duplicate_warning").hide();
    }
  }

  function refreshAllProductPrices() {
    var tableProductLength = $("#product_info_table tbody tr").length;
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);
      
      var product_id = $("#product_"+count).val();
      if(product_id) {
        getProductData(count);
      }
    }
  }

  function getTotal(row = null) {
    if(row) {
      var total = Number($("#rate_value_"+row).val()) * Number($("#qty_"+row).val());
      total = total.toFixed(2);
      $("#amount_"+row).val(total);
      $("#amount_value_"+row).val(total);
      
      subAmount();

    } else {
      alert('no row !! please refresh the page');
    }
  }

  function getProductData(row_id)
  {
    var product_id = $("#product_"+row_id).val();
    var customer_type = $("#customer_type").val();
    
    if(product_id == "") {
      $("#rate_"+row_id).val("");
      $("#rate_value_"+row_id).val("");
      $("#qty_"+row_id).val("");           
      $("#amount_"+row_id).val("");
      $("#amount_value_"+row_id).val("");

    } else {
      $.ajax({
        url: base_url + 'orders/getProductValueById',
        type: 'post',
        data: {
          product_id: product_id,
          customer_type: customer_type
        },
        dataType: 'json',
        success:function(response) {
          $("#rate_"+row_id).val(response.price);
          $("#rate_value_"+row_id).val(response.price);

          $("#qty_"+row_id).val(1);
          $("#qty_value_"+row_id).val(1);

          var total = Number(response.price) * 1;
          total = total.toFixed(2);
          $("#amount_"+row_id).val(total);
          $("#amount_value_"+row_id).val(total);
          
          subAmount();
        }
      });
    }
  }

  function subAmount() {
    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());
    }

    totalSubAmount = totalSubAmount.toFixed(2);

    $("#gross_amount").val(totalSubAmount);
    $("#gross_amount_value").val(totalSubAmount);

    var discount = $("#discount").val();
    if(discount) {
      var grandTotal = Number(totalSubAmount) - Number(discount);
      grandTotal = grandTotal.toFixed(2);
      $("#net_amount").val(grandTotal);
      $("#net_amount_value").val(grandTotal);
    } else {
      $("#net_amount").val(totalSubAmount);
      $("#net_amount_value").val(totalSubAmount);
    }

    calculateDue();
  }

  function calculateDue() {
    var netAmount = Number($("#net_amount_value").val()) || 0;
    var paidAmount = Number($("#paid_amount").val()) || 0;
    
    var dueAmount = netAmount - paidAmount;
    dueAmount = Math.max(0, dueAmount);
    
    $("#due_amount").val(dueAmount.toFixed(2));
    $("#due_amount_value").val(dueAmount.toFixed(2));
  }

  function removeRow(tr_id)
  {
    $("#product_info_table tbody tr#row_"+tr_id).remove();
    subAmount();
  }
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