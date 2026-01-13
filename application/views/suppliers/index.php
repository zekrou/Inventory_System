<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-truck"></i> Manage Suppliers</h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Suppliers</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div id="messages"></div>

                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php elseif($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Manage Suppliers</h3>
                        <?php if(isset($user_permission['createSupplier'])): ?>
                            <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addSupplierModal">
                                <i class="fa fa-plus"></i> Add Supplier
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="box-body">
                        <table id="supplierTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addSupplierForm" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add New Supplier</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supplier_name" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country">
                    </div>
                    <div class="form-group">
                        <label>Tax Number</label>
                        <input type="text" class="form-control" name="tax_number">
                    </div>
                    <div class="form-group">
                        <label>Payment Terms</label>
                        <input type="text" class="form-control" name="payment_terms">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editSupplierForm" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Supplier</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_supplier_id" name="edit_supplier_id">
                    <div class="form-group">
                        <label>Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_supplier_name" name="edit_supplier_name" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" id="edit_contact_person" name="edit_contact_person">
                    </div>
                    <div class="form-group">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_phone" name="edit_phone" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit_email" name="edit_email">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" id="edit_address" name="edit_address" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" id="edit_country" name="edit_country">
                    </div>
                    <div class="form-group">
                        <label>Tax Number</label>
                        <input type="text" class="form-control" id="edit_tax_number" name="edit_tax_number">
                    </div>
                    <div class="form-group">
                        <label>Payment Terms</label>
                        <input type="text" class="form-control" id="edit_payment_terms" name="edit_payment_terms">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" id="edit_notes" name="edit_notes" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="edit_active" name="edit_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-trash"></i> Supprimer le fournisseur</h4>
            </div>
            <div class="modal-body" id="removeModalBody">
                <p>Êtes-vous sûr de vouloir supprimer ce fournisseur ?</p>
            </div>
            <div class="modal-footer" id="removeModalFooter">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="confirmRemoveSupplier()">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var baseUrl = "<?php echo base_url(); ?>";
var removeSupplierId = null;

$(document).ready(function() {
    var supplierTable = $('#supplierTable').DataTable({
        'ajax': baseUrl + 'suppliers/fetchSuppliersData',
        'order': []
    });

    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: baseUrl + 'suppliers/create',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success === true) {
                    $('#messages').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
                    $('#addSupplierModal').modal('hide');
                    $('#addSupplierForm')[0].reset();
                    supplierTable.ajax.reload(null, false);
                } else {
                    $('#messages').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
                }
            }
        });
    });

    window.editSupplier = function(id) {
        $.ajax({
            url: baseUrl + 'suppliers/fetchSupplierDataById/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#edit_supplier_id').val(data.id);
                $('#edit_supplier_name').val(data.name);
                $('#edit_contact_person').val(data.contact_person);
                $('#edit_phone').val(data.phone);
                $('#edit_email').val(data.email);
                $('#edit_address').val(data.address);
                $('#edit_country').val(data.country);
                $('#edit_tax_number').val(data.tax_number);
                $('#edit_payment_terms').val(data.payment_terms);
                $('#edit_notes').val(data.notes);
                $('#edit_active').val(data.active);
                $('#editSupplierModal').modal('show');
            }
        });
    };

    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_supplier_id').val();
        $.ajax({
            url: baseUrl + 'suppliers/update/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success === true) {
                    $('#messages').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
                    $('#editSupplierModal').modal('hide');
                    supplierTable.ajax.reload(null, false);
                } else {
                    $('#messages').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
                }
            }
        });
    });

    $('#suppliersMainNav').addClass('active');
});

function removeSupplier(id) {
  if(id) {
    removeSupplierId = id;
    $("#removeModalBody").html('<p>Êtes-vous sûr de vouloir supprimer ce fournisseur ?</p>');
    $("#removeModalFooter").html(`
      <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
      <button type="button" class="btn btn-danger" onclick="confirmRemoveSupplier()">Supprimer</button>
    `);
    $("#removeModal").modal('show');
  }
}

function confirmRemoveSupplier() {
  $.ajax({
    url: baseUrl + 'suppliers/remove',
    type: 'POST',
    data: {supplier_id: removeSupplierId},
    dataType: 'json',
    success: function(response) {
      if(response.success === true) {
        $('#messages').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><i class="fa fa-check"></i></strong> ' + response.messages + '</div>');
        $('#removeModal').modal('hide');
        $('#supplierTable').DataTable().ajax.reload(null, false);
      } else if(response.type === 'has_purchases') {
        $("#removeModalBody").html(`
          <div class="alert alert-warning">
            <i class="fa fa-warning"></i> <strong>Attention!</strong><br>
            ${response.messages}
          </div>
          <p><strong>Que voulez-vous faire ?</strong></p>
          <ul>
            <li><strong>Désactiver:</strong> Le fournisseur sera caché mais les achats seront préservés</li>
            <li><strong>Forcer la suppression:</strong> ⚠️ SUPPRESSION PERMANENTE</li>
          </ul>
        `);
        $("#removeModalFooter").html(`
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-warning" onclick="deactivateSupplier()">
            <i class="fa fa-ban"></i> Désactiver
          </button>
          <button type="button" class="btn btn-danger" onclick="forceDeleteSupplier()">
            <i class="fa fa-trash"></i> Forcer la suppression
          </button>
        `);
      } else {
        $('#messages').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
        $('#removeModal').modal('hide');
      }
    }
  });
}

function deactivateSupplier() {
  $.ajax({
    url: baseUrl + 'suppliers/remove',
    type: 'POST',
    data: {supplier_id: removeSupplierId, deactivate_only: 'yes'},
    dataType: 'json',
    success: function(response) {
      $('#messages').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><i class="fa fa-check"></i></strong> ' + response.messages + '</div>');
      $('#removeModal').modal('hide');
      $('#supplierTable').DataTable().ajax.reload(null, false);
    }
  });
}

function forceDeleteSupplier() {
  if(confirm("⚠️ ATTENTION! Suppression permanente!\n\nÊtes-vous sûr ?")) {
    $.ajax({
      url: baseUrl + 'suppliers/remove',
      type: 'POST',
      data: {supplier_id: removeSupplierId, force_delete: 'yes'},
      dataType: 'json',
      success: function(response) {
        $('#messages').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><i class="fa fa-check"></i></strong> ' + response.messages + '</div>');
        $('#removeModal').modal('hide');
        $('#supplierTable').DataTable().ajax.reload(null, false);
      }
    });
  }
}
</script>
