<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Products</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Products</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif ($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($user_permission['createProduct'])): ?>
          <a href="<?php echo base_url('products/create') ?>" class="btn btn-primary">Add Product</a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Products</h3>
          </div>
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>SKU</th>
                  <th>Product Name</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Store</th>
                  <th>Availability</th>
                  <?php if (isset($user_permission['updateProduct']) || isset($user_permission['deleteProduct'])): ?>
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

<?php if (isset($user_permission['deleteProduct'])): ?>
  <!-- Remove Modal -->
  <div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><i class="fa fa-trash"></i> Supprimer le produit</h4>
        </div>
        <div class="modal-body" id="removeModalBody">
          <p>Êtes-vous sûr de vouloir supprimer ce produit ?</p>
        </div>
        <div class="modal-footer" id="removeModalFooter">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-danger" onclick="confirmRemove()">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";
  var removeProductId = null;

  $(document).ready(function() {
    $("#mainProductNav").addClass('active');
    manageTable = $('#manageTable').DataTable({
      'ajax': base_url + 'products/fetchProductData',
      'order': []
    });
  });

  // ✅ Ouvrir le modal
  function removeFunc(id) {
    removeProductId = id; // Stocker l'ID
    console.log("Product ID to delete:", id); // Debug

    $("#removeModalBody").html('<p>Etes-vous sur de vouloir supprimer ce produit ?</p>');
    $("#removeModalFooter").html(
      '<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>' +
      '<button type="button" class="btn btn-danger" onclick="confirmRemove()">Supprimer</button>'
    );
    $("#removeModal").modal({
      backdrop: 'static',
      show: true
    });
  }

  // ✅ Confirmer suppression
  function confirmRemove() {
    console.log("Confirming delete for product:", removeProductId); // Debug

    $.ajax({
      url: base_url + 'products/remove',
      type: 'POST',
      data: {
        product_id: removeProductId
      },
      dataType: 'json',
      success: function(response) {
        console.log("Response:", response); // Debug

        if (response.success === true) {
          $("#messages").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
          $("#removeModal").modal('hide');
          manageTable.ajax.reload(null, false);
        } else if (response.type === 'has_relations') {
          $("#removeModalBody").html(
            '<div class="alert alert-warning"><i class="fa fa-warning"></i> <strong>Attention!</strong><br>' + response.messages + '</div>' +
            '<p><strong>Options:</strong></p>' +
            '<ul>' +
            '<li><strong>Desactiver:</strong> Le produit sera cache mais les donnees seront preservees</li>' +
            '<li><strong>Forcer la suppression:</strong> SUPPRESSION PERMANENTE</li>' +
            '</ul>'
          );
          $("#removeModalFooter").html(
            '<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>' +
            '<button type="button" class="btn btn-warning" onclick="deactivateProduct()"><i class="fa fa-ban"></i> Desactiver</button>' +
            '<button type="button" class="btn btn-danger" onclick="forceDeleteProduct()"><i class="fa fa-trash"></i> Forcer suppression</button>'
          );
        } else {
          $("#messages").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
          $("#removeModal").modal('hide');
        }
      },
      error: function(xhr, status, error) {
        console.log("AJAX Error:", error);
        console.log("Response:", xhr.responseText);
        $("#messages").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Erreur: ' + error + '</div>');
        $("#removeModal").modal('hide');
      }
    });
  }

  // ✅ Desactiver produit
  function deactivateProduct() {
    console.log("Deactivating product:", removeProductId); // Debug

    $.ajax({
      url: base_url + 'products/remove',
      type: 'POST',
      data: {
        product_id: removeProductId,
        deactivate_only: 'yes'
      },
      dataType: 'json',
      success: function(response) {
        $("#messages").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
        $("#removeModal").modal('hide');
        manageTable.ajax.reload(null, false);
      }
    });
  }

  // ✅ Forcer suppression
  function forceDeleteProduct() {
    if (confirm("ATTENTION! Suppression permanente!\n\nEtes-vous sur ?")) {
      console.log("Force deleting product:", removeProductId); // Debug

      $.ajax({
        url: base_url + 'products/remove',
        type: 'POST',
        data: {
          product_id: removeProductId,
          force_delete: 'yes'
        },
        dataType: 'json',
        success: function(response) {
          console.log("Force delete response:", response); // Debug
          $("#messages").html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.messages + '</div>');
          $("#removeModal").modal('hide');
          manageTable.ajax.reload(null, false);
        },
        error: function(xhr, status, error) {
          console.log("Force delete error:", error);
          console.log("Response:", xhr.responseText);
          $("#messages").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Erreur: ' + error + '</div>');
          $("#removeModal").modal('hide');
        }
      });
    }
  }
</script>