
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Customers / Clients</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Customers</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
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

        <?php if(isset($user_permission['createCustomer'])): ?>
          <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal"><i class="fa fa-plus"></i> Add Customer</button>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Customers</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
                <th>Balance</th>
                <th>Credit Limit</th>
                <th>Status</th>
                <?php if(isset($user_permission['updateCustomer']) || isset($user_permission['deleteCustomer'])): ?>
                  <th>Action</th>
                <?php endif; ?>
              </tr>
              </thead>

            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
    

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php if(isset($user_permission['createCustomer'])): ?>
<!-- create customer modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addCustomerModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url('customers/create') ?>" method="post" id="createCustomerForm">

        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter customer name" autocomplete="off" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="customer_type">Customer Type *</label>
                <select class="form-control" id="customer_type" name="customer_type" required>
                  <option value="">-- Select Type --</option>
                  <option value="super_wholesale">Super Gros (Super Wholesale)</option>
                  <option value="wholesale">Gros (Wholesale)</option>
                  <option value="retail">Détail (Retail)</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" autocomplete="off" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="credit_limit">Credit Limit (DZD)</label>
                <input type="number" step="0.01" class="form-control" id="credit_limit" name="credit_limit" placeholder="0.00" value="0">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="payment_terms">Payment Terms</label>
                <input type="text" class="form-control" id="payment_terms" name="payment_terms" placeholder="e.g., 30 days, Cash">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="tax_number">Tax Number</label>
                <input type="text" class="form-control" id="tax_number" name="tax_number" placeholder="Enter tax number">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-9">
              <div class="form-group">
                <label for="notes">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Additional notes"></textarea>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="active">Status *</label>
                <select class="form-control" id="active" name="active" required>
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>
                </select>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Customer</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if(isset($user_permission['updateCustomer'])): ?>
<!-- edit customer modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editCustomerModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url('customers/update') ?>" method="post" id="updateCustomerForm">

        <div class="modal-body">
          <div id="edit_messages"></div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_customer_name">Customer Name *</label>
                <input type="text" class="form-control" id="edit_customer_name" name="edit_customer_name" placeholder="Enter customer name" autocomplete="off" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_customer_type">Customer Type *</label>
                <select class="form-control" id="edit_customer_type" name="edit_customer_type" required>
                  <option value="">-- Select Type --</option>
                  <option value="super_wholesale">Super Gros (Super Wholesale)</option>
                  <option value="wholesale">Gros (Wholesale)</option>
                  <option value="retail">Détail (Retail)</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_phone">Phone *</label>
                <input type="text" class="form-control" id="edit_phone" name="edit_phone" placeholder="Enter phone number" autocomplete="off" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" class="form-control" id="edit_email" name="edit_email" placeholder="Enter email" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="edit_address">Address</label>
                <textarea class="form-control" id="edit_address" name="edit_address" rows="2" placeholder="Enter address"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_credit_limit">Credit Limit (DZD)</label>
                <input type="number" step="0.01" class="form-control" id="edit_credit_limit" name="edit_credit_limit" placeholder="0.00">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_current_balance">Current Balance (DZD)</label>
                <input type="number" step="0.01" class="form-control" id="edit_current_balance" name="edit_current_balance" placeholder="0.00">
                <small class="text-muted">Positive = Customer owes you</small>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_payment_terms">Payment Terms</label>
                <input type="text" class="form-control" id="edit_payment_terms" name="edit_payment_terms" placeholder="e.g., 30 days, Cash">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_tax_number">Tax Number</label>
                <input type="text" class="form-control" id="edit_tax_number" name="edit_tax_number" placeholder="Enter tax number">
              </div>
            </div>

            <div class="col-md-5">
              <div class="form-group">
                <label for="edit_notes">Notes</label>
                <textarea class="form-control" id="edit_notes" name="edit_notes" rows="2" placeholder="Additional notes"></textarea>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="edit_active">Status *</label>
                <select class="form-control" id="edit_active" name="edit_active" required>
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>
                </select>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Customer</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if(isset($user_permission['deleteCustomer'])): ?>
<!-- remove customer modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeCustomerModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url('customers/remove') ?>" method="post" id="removeCustomerForm">
        <div class="modal-body">
          <p>Do you really want to remove this customer?</p>
          <p class="text-warning"><small>Note: If customer has orders, it will be deactivated instead of deleted.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Remove</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>



<script type="text/javascript">
var manageTable;

$(document).ready(function() {

  // Activate customers menu
  $("#customerNav").addClass('active');

  // Initialize DataTable
  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url('customers/fetchCustomerData') ?>',
    'order': [[0, 'desc']], // Order by code descending
    'columnDefs': [
        { 'orderable': false, 'targets': -1 }
    ]
  });

  // Submit create form
  $("#createCustomerForm").unbind('submit').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);

    // Remove previous error messages
    $(".text-danger").remove();

    $.ajax({
      url: form.attr('action'),
      type: form.attr('method'),
      data: form.serialize(),
      dataType: 'json',
      success:function(response) {

        manageTable.ajax.reload(null, false); 

        if(response.success === true) {
          $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
          '</div>');

          // Hide modal
          $("#addCustomerModal").modal('hide');

          // Reset form
          $("#createCustomerForm")[0].reset();
          $("#createCustomerForm .form-group").removeClass('has-error').removeClass('has-success');

        } else {

          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);

              id.closest('.form-group')
              .removeClass('has-error')
              .removeClass('has-success')
              .addClass(value.length > 0 ? 'has-error' : 'has-success');
              
              id.after(value);

            });
          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>');
          }
        }
      }
    }); 

    return false;
  });

});

function editCustomer(id)
{ 
  $.ajax({
    url: '<?php echo base_url('customers/fetchCustomerDataById/') ?>' + id,
    type: 'post',
    dataType: 'json',
    success:function(response) {

      $("#edit_customer_name").val(response.customer_name);
      $("#edit_customer_type").val(response.customer_type);
      $("#edit_phone").val(response.phone);
      $("#edit_email").val(response.email);
      $("#edit_address").val(response.address);
      $("#edit_credit_limit").val(response.credit_limit);
      $("#edit_current_balance").val(response.current_balance);
      $("#edit_payment_terms").val(response.payment_terms);
      $("#edit_tax_number").val(response.tax_number);
      $("#edit_notes").val(response.notes);
      $("#edit_active").val(response.active);

      // Submit edit form
      $("#updateCustomerForm").unbind('submit').bind('submit', function(e) {
        e.preventDefault();
        var form = $(this);

        // Remove previous errors
        $(".text-danger").remove();

        $.ajax({
          url: form.attr('action') + '/' + id,
          type: form.attr('method'),
          data: form.serialize(),
          dataType: 'json',
          success:function(response) {

            manageTable.ajax.reload(null, false); 

            if(response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
              '</div>');

              // Hide modal
              $("#editCustomerModal").modal('hide');
              // Reset form groups
              $("#updateCustomerForm .form-group").removeClass('has-error').removeClass('has-success');

            } else {

              if(response.messages instanceof Object) {
                $.each(response.messages, function(index, value) {
                  var id = $("#"+index);

                  id.closest('.form-group')
                  .removeClass('has-error')
                  .removeClass('has-success')
                  .addClass(value.length > 0 ? 'has-error' : 'has-success');
                  
                  id.after(value);

                });
              } else {
                $("#edit_messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                  '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
                '</div>');
              }
            }
          }
        }); 

        return false;
      });

    }
  });
}

function removeCustomer(id)
{
  if(id) {
    $("#removeCustomerForm").unbind('submit').on('submit', function(e) {
      e.preventDefault();

      var form = $(this);

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { customer_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            // Hide modal
            $("#removeCustomerModal").modal('hide');

          } else {

            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>'); 
          }
        }
      }); 

      return false;
    });
  }
}

</script>