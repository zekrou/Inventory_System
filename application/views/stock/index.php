<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Stock
      <small>Management</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Stock</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Statistics Row -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $statistics['total_products']; ?></h3>
            <p>Total Products</p>
          </div>
          <div class="icon">
            <i class="fa fa-cubes"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $statistics['good_stock']; ?></h3>
            <p>Good Stock</p>
          </div>
          <div class="icon">
            <i class="fa fa-check-circle"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $statistics['low_stock']; ?></h3>
            <p>Low Stock</p>
          </div>
          <div class="icon">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $statistics['out_of_stock']; ?></h3>
            <p>Out of Stock</p>
          </div>
          <div class="icon">
            <i class="fa fa-times-circle"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Stock Locations Table -->
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

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Stock</h3>
            <?php if (isset($user_permission['createStock'])): ?>
              <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Add Stock</button>
            <?php endif; ?>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="stockTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Stock Name</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
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

    <!-- Products Overview -->
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cube"></i> Filter Products</h3>
          </div>
          <div class="box-body">
            <form method="get" action="<?php echo base_url('stock'); ?>">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control">
                      <option value="">All Categories</option>
                      <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($current_category == $cat['id']) ? 'selected' : ''; ?>>
                          <?php echo $cat['name']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Stock Location</label>
                    <select name="stock" class="form-control">
                      <option value="">All Stocks</option>
                      <?php foreach ($stocks as $stock): ?>
                        <option value="<?php echo $stock['id']; ?>" <?php echo ($current_stock == $stock['id']) ? 'selected' : ''; ?>>
                          <?php echo $stock['name']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <option value="">All Status</option>
                      <option value="good" <?php echo ($current_status == 'good') ? 'selected' : ''; ?>>Good Stock</option>
                      <option value="low" <?php echo ($current_status == 'low') ? 'selected' : ''; ?>>Low Stock</option>
                      <option value="out_of_stock" <?php echo ($current_status == 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-cubes"></i> Products Overview</h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr style="background: #f8f9fa;">
                    <th style="width: 80px;">Image</th>
                    <th>Product</th>
                    <th style="width: 120px;">SKU</th>
                    <th style="width: 150px;">Stock</th>
                    <th style="width: 100px;" class="text-center">Quantity</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 130px;" class="text-right">Price</th>
                    <th style="width: 100px;" class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                      <tr>
                        <td class="text-center">
                          <img src="<?php echo base_url($product['image']); ?>" width="50" height="50" class="img-circle">
                        </td>
                        <td>
                          <strong><?php echo $product['name']; ?></strong>
                        </td>
                        <td><?php echo $product['sku']; ?></td>
                        <td><?php echo $product['stock_name']; ?></td>
                        <td class="text-center">
                          <strong style="font-size: 16px;"><?php echo $product['qty']; ?></strong>
                        </td>
                        <td>
                          <span class="label label-<?php echo $product['stock_status_class']; ?>">
                            <?php echo $product['stock_status_label']; ?>
                          </span>
                        </td>
                        <td class="text-right">
                          <strong><?php echo number_format($product['price_retail'], 2); ?> DZD</strong>
                        </td>
                        <td class="text-center">
                          <!-- ✅ BOUTON VIEW DETAILS -->
                          <button type="button" class="btn btn-info btn-sm"
                            onclick="viewProductDetails(<?php echo $product['id']; ?>)"
                            title="View Full Details">
                            <i class="fa fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">
                        <em class="text-muted">No products found</em>
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- ✅ MODAL POUR AFFICHER LES DÉTAILS DU PRODUIT -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
          <i class="fa fa-info-circle"></i> Product Full Details
        </h4>
      </div>
      <div class="modal-body" id="productDetailsContent">
        <!-- Contenu chargé dynamiquement -->
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-3x"></i>
          <p>Loading product details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Stock</h4>
      </div>

      <form role="form" action="<?php echo base_url('stock/create') ?>" method="post" id="createForm">

        <div class="modal-body">

          <div class="form-group">
            <label for="stock_name">Stock Name</label>
            <input type="text" class="form-control" id="stock_name" name="stock_name" placeholder="Enter stock name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" placeholder="Enter description"></textarea>
          </div>

          <div class="form-group">
            <label for="active">Status</label>
            <select class="form-control" id="active" name="active">
              <option value="1">Active</option>
              <option value="2">Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Edit Stock Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Stock</h4>
      </div>

      <form role="form" action="<?php echo base_url('stock/update') ?>" method="post" id="updateForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label for="edit_stock_name">Stock Name</label>
            <input type="text" class="form-control" id="edit_stock_name" name="edit_stock_name" placeholder="Enter stock name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="edit_description">Description</label>
            <textarea class="form-control" id="edit_description" name="edit_description" placeholder="Enter description"></textarea>
          </div>

          <div class="form-group">
            <label for="edit_active">Status</label>
            <select class="form-control" id="edit_active" name="edit_active">
              <option value="1">Active</option>
              <option value="2">Inactive</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Remove Stock Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Stock</h4>
      </div>

      <form role="form" action="<?php echo base_url('stock/remove') ?>" method="post" id="removeForm">
        <div class="modal-body">
          <p>Do you really want to remove?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
  var stockTable;

  $(document).ready(function() {

    $("#mainStockNav").addClass('active');

    stockTable = $('#stockTable').DataTable({
      'ajax': '<?php echo base_url('stock/fetchStockData') ?>',
      'order': []
    });

    // submit the create from 
    $("#createForm").unbind('submit').on('submit', function() {
      var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {

          stockTable.ajax.reload(null, false);

          if (response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">' +
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' + response.messages +
              '</div>');

            // hide the modal
            $("#addModal").modal('hide');

            // reset the form
            $("#createForm .form-control").val('');

          } else {

            if (response.messages instanceof Object) {
              $.each(response.messages, function(index, value) {
                var id = $("#" + index);

                id.closest('.form-group')
                  .removeClass('has-error')
                  .removeClass('has-success')
                  .addClass(value.length > 0 ? 'has-error' : 'has-success');

                id.after(value);

              });
            } else {
              $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' + response.messages +
                '</div>');
            }
          }
        }
      });

      return false;
    });

  });

  // edit function
  function editFunc(id) {
    $.ajax({
      url: '<?php echo base_url('stock/fetchStockDataById') ?>/' + id,
      type: 'post',
      dataType: 'json',
      success: function(response) {

        $("#edit_stock_name").val(response.name);
        $("#edit_description").val(response.description);
        $("#edit_active").val(response.active);

        // submit the edit from 
        $("#updateForm").unbind('submit').bind('submit', function() {
          var form = $(this);

          // remove the text-danger
          $(".text-danger").remove();

          $.ajax({
            url: form.attr('action') + '/' + id,
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {

              stockTable.ajax.reload(null, false);

              if (response.success === true) {
                $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">' +
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                  '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' + response.messages +
                  '</div>');

                // hide the modal
                $("#editModal").modal('hide');
                // reset the form 
                $("#updateForm .form-control").val('');

              } else {

                if (response.messages instanceof Object) {
                  $.each(response.messages, function(index, value) {
                    var id = $("#" + index);

                    id.closest('.form-group')
                      .removeClass('has-error')
                      .removeClass('has-success')
                      .addClass(value.length > 0 ? 'has-error' : 'has-success');

                    id.after(value);

                  });
                } else {
                  $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                    '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' + response.messages +
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

  // remove functions 
  function removeFunc(id) {
    if (id) {
      $("#removeForm").on('submit', function() {

        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();

        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: {
            stock_id: id
          },
          dataType: 'json',
          success: function(response) {

            stockTable.ajax.reload(null, false);

            if (response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' + response.messages +
                '</div>');

              // hide the modal
              $("#removeModal").modal('hide');

            } else {

              $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' + response.messages +
                '</div>');
            }
          }
        });

        return false;
      });
    }
  }

  /**
   * ✅ NOUVELLE FONCTION: Afficher les détails complets d'un produit
   */
  function viewProductDetails(productId) {
    // Ouvrir le modal
    $('#productDetailsModal').modal('show');

    // Reset le contenu
    $('#productDetailsContent').html(
      '<div class="text-center" style="padding: 50px;">' +
      '<i class="fa fa-spinner fa-spin fa-3x text-info"></i>' +
      '<p style="margin-top: 20px;">Loading product details...</p>' +
      '</div>'
    );

    // Charger les données via AJAX
    $.ajax({
      url: '<?php echo base_url("stock/getProductDetails"); ?>',
      type: 'POST',
      data: {
        product_id: productId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#productDetailsContent').html(response.html);
        } else {
          $('#productDetailsContent').html(
            '<div class="alert alert-danger">' +
            '<i class="fa fa-exclamation-triangle"></i> ' +
            '<strong>Error:</strong> ' + response.message +
            '</div>'
          );
        }
      },
      error: function(xhr, status, error) {
        $('#productDetailsContent').html(
          '<div class="alert alert-danger">' +
          '<i class="fa fa-exclamation-triangle"></i> ' +
          '<strong>Connection Error:</strong> Unable to load product details. Please try again.' +
          '<br><small>Error: ' + error + '</small>' +
          '</div>'
        );
      }
    });
  }
</script>