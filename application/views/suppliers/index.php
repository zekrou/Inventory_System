<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage <small>Suppliers / Fournisseurs</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Suppliers</li>
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

        <?php if(in_array('createSupplier', $user_permission)): ?>
          <button class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal"><i class="fa fa-plus"></i> Add Supplier</button>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Suppliers</h3>
          </div>
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
                <?php if(in_array('updateSupplier', $user_permission) || in_array('deleteSupplier', $user_permission)): ?>
                  <th>Action</th>
                <?php endif; ?>
              </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php if(in_array('createSupplier', $user_permission)): ?>
<!-- Add Supplier Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addSupplierModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Supplier</h4>
      </div>

      <form role="form" action="<?php echo base_url('suppliers/create') ?>" method="post" id="createSupplierForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="supplier_name">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter supplier name" autocomplete="off" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Contact person name" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone number" autocomplete="off" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email address" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full address"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="country">Country</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="Country" autocomplete="off">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="tax_number">Tax Number</label>
                <input type="text" class="form-control" id="tax_number" name="tax_number" placeholder="Tax/VAT number" autocomplete="off">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="payment_terms">Payment Terms</label>
                <input type="text" class="form-control" id="payment_terms" name="payment_terms" placeholder="e.g., 30 days, COD" autocomplete="off">
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
                <label for="active">Status <span class="text-danger">*</span></label>
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
          <button type="submit" class="btn btn-primary">Save Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(in_array('updateSupplier', $user_permission)): ?>
<!-- Edit Supplier Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Supplier</h4>
      </div>

      <form role="form" action="<?php echo base_url('suppliers/update') ?>" method="post" id="updateSupplierForm">
        <div class="modal-body">
          <div id="edit_messages"></div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_supplier_name">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_supplier_name" name="edit_supplier_name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_contact_person">Contact Person</label>
                <input type="text" class="form-control" id="edit_contact_person" name="edit_contact_person">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_phone">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_phone" name="edit_phone" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" class="form-control" id="edit_email" name="edit_email">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="edit_address">Address</label>
                <textarea class="form-control" id="edit_address" name="edit_address" rows="2"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_country">Country</label>
                <input type="text" class="form-control" id="edit_country" name="edit_country">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_tax_number">Tax Number</label>
                <input type="text" class="form-control" id="edit_tax_number" name="edit_tax_number">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_payment_terms">Payment Terms</label>
                <input type="text" class="form-control" id="edit_payment_terms" name="edit_payment_terms">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-9">
              <div class="form-group">
                <label for="edit_notes">Notes</label>
                <textarea class="form-control" id="edit_notes" name="edit_notes" rows="2"></textarea>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="edit_active">Status <span class="text-danger">*</span></label>
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
          <button type="submit" class="btn btn-primary">Update Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(in_array('deleteSupplier', $user_permission)): ?>
<!-- Remove Supplier Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Supplier</h4>
      </div>

      <form role="form" action="<?php echo base_url('suppliers/remove') ?>" method="post" id="removeSupplierForm">
        <div class="modal-body">
          <p>Do you really want to remove this supplier?</p>
          <p class="text-warning"><strong>Note:</strong> If supplier has purchases, it will be deactivated instead of deleted.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Remove</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script type="text/javascript">
var manageTable;

$(document).ready(function() {
  $("#supplierNav").addClass('active');

  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url('suppliers/fetchSuppliersData') ?>',
    'order': [[0, 'desc']]
  });

  $("#createSupplierForm").unbind('submit').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
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
            '<strong><i class="fa fa-check"></i></strong> '+response.messages+'</div>');
          $("#addSupplierModal").modal('hide');
          $("#createSupplierForm")[0].reset();
        } else {
          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);
              id.after(value);
            });
          }
        }
      }
    });
  });
});

function editSupplier(id) {
  $.ajax({
    url: '<?php echo base_url('suppliers/fetchSupplierDataById/') ?>' + id,
    type: 'post',
    dataType: 'json',
    success:function(response) {
      $("#edit_supplier_name").val(response.name);
      $("#edit_contact_person").val(response.contact_person);
      $("#edit_phone").val(response.phone);
      $("#edit_email").val(response.email);
      $("#edit_address").val(response.address);
      $("#edit_country").val(response.country);
      $("#edit_tax_number").val(response.tax_number);
      $("#edit_payment_terms").val(response.payment_terms);
      $("#edit_notes").val(response.notes);
      $("#edit_active").val(response.active);

      $("#updateSupplierForm").unbind('submit').bind('submit', function(e) {
        e.preventDefault();
        var form = $(this);
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
                '<strong><i class="fa fa-check"></i></strong> '+response.messages+'</div>');
              $("#editModal").modal('hide');
            }
          }
        });
        return false;
      });
    }
  });
}

function removeSupplier(id) {
  if(id) {
    $("#removeSupplierForm").unbind('submit').on('submit', function(e) {
      e.preventDefault();
      var form = $(this);

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { supplier_id:id },
        dataType: 'json',
        success:function(response) {
          manageTable.ajax.reload(null, false);
          
          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong><i class="fa fa-check"></i></strong> '+response.messages+'</div>');
            $("#removeModal").modal('hide');
          }
        }
      });
      return false;
    });
  }
}
</script>