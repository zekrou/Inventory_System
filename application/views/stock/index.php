<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Stock Management <small>Overview</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Stock</li>
    </ol>
  </section>

  <section class="content">
    
    <!-- Statistics Boxes -->
    <div class="row">
      <div class="col-md-3 col-sm-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $statistics['total_products']; ?></h3>
            <p>Total Products</p>
          </div>
          <div class="icon"><i class="fa fa-cubes"></i></div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $statistics['good_stock']; ?></h3>
            <p>Good Stock</p>
          </div>
          <div class="icon"><i class="fa fa-check"></i></div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $statistics['low_stock']; ?></h3>
            <p>Low Stock</p>
          </div>
          <div class="icon"><i class="fa fa-warning"></i></div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $statistics['out_of_stock']; ?></h3>
            <p>Out of Stock</p>
          </div>
          <div class="icon"><i class="fa fa-times"></i></div>
        </div>
      </div>
    </div>

    <?php if(in_array('createStock', $user_permission)): ?>
      <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">
        <i class="fa fa-plus"></i> Add Stock
      </button>
      <br /><br />
    <?php endif; ?>

    <!-- Stock Management -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Manage Stock</h3>
      </div>
      <div class="box-body">
        <table id="manageTable" class="table table-bordered table-striped">
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
    </div>

    <!-- Filters -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Filter Products</h3>
      </div>
      <div class="box-body">
        <form method="get" action="<?php echo base_url('stock'); ?>">
          <div class="row">
            <div class="col-md-3">
              <select name="category" class="form-control">
                <option value="">All Categories</option>
                <?php foreach($categories as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>" <?php echo ($current_category == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <select name="stock" class="form-control">
                <option value="">All Stocks</option>
                <?php foreach($stocks as $stock): ?>
                  <option value="<?php echo $stock['id']; ?>" <?php echo ($current_stock == $stock['id']) ? 'selected' : ''; ?>>
                    <?php echo $stock['name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="good" <?php echo ($current_status == 'good') ? 'selected' : ''; ?>>Good Stock</option>
                <option value="low" <?php echo ($current_status == 'low') ? 'selected' : ''; ?>>Low Stock</option>
                <option value="critical" <?php echo ($current_status == 'critical') ? 'selected' : ''; ?>>Critical</option>
                <option value="out_of_stock" <?php echo ($current_status == 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
              <a href="<?php echo base_url('stock'); ?>" class="btn btn-default">Clear</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Products Table -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Products Overview</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Product</th>
              <th>SKU</th>
              <th>Stock</th>
              <th>Quantity</th>
              <th>Status</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($products)): ?>
              <?php foreach($products as $product): ?>
                <tr>
                  <td><?php echo $product['name']; ?></td>
                  <td><?php echo $product['sku'] ?: '-'; ?></td>
                  <td><?php echo $product['stock_name']; ?></td>
                  <td><strong><?php echo $product['qty']; ?></strong></td>
                  <td>
                    <span class="label label-<?php echo $product['stock_status_class']; ?>">
                      <?php echo $product['stock_status_label']; ?>
                    </span>
                  </td>
                  <td><?php echo number_format($product['price_retail'], 2); ?> DZD</td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No products found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Create Stock Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Stock</h4>
      </div>
      <form id="createForm" action="<?php echo base_url('stock/create') ?>" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label>Stock Name *</label>
            <input type="text" class="form-control" name="stock_name" required>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select class="form-control" name="active" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Stock</h4>
      </div>
      <form id="updateForm" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label>Stock Name *</label>
            <input type="text" class="form-control" id="edit_stock_name" name="edit_stock_name" required>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" id="edit_description" name="edit_description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select class="form-control" id="edit_active" name="edit_active" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Remove Modal -->
<div class="modal fade" id="removeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Remove Stock</h4>
      </div>
      <form id="removeForm" method="post">
        <div class="modal-body">
          <p>Do you really want to remove this stock?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Remove</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {
  $("#stockNav").addClass('active');
  
  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'stock/fetchStockData',
    'order': []
  });

  // Create form
  $("#createForm").on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if(response.success) {
          $("#addModal").modal('hide');
          manageTable.ajax.reload();
          alert(response.messages);
        } else {
          alert(response.messages);
        }
      }
    });
  });
});

function editFunc(id) {
  $.ajax({
    url: base_url + 'stock/fetchStockDataById/' + id,
    type: 'POST',
    dataType: 'json',
    success: function(data) {
      $("#edit_stock_name").val(data.name);
      $("#edit_description").val(data.description);
      $("#edit_active").val(data.active);
      
      $("#updateForm").attr('action', base_url + 'stock/update/' + id);
      $("#editModal").modal('show');
    }
  });
  
  $("#updateForm").on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(response) {
        if(response.success) {
          $("#editModal").modal('hide');
          manageTable.ajax.reload();
          alert(response.messages);
        }
      }
    });
  });
}

function removeFunc(id) {
  $("#removeForm").off('submit').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: base_url + 'stock/remove',
      type: 'POST',
      data: {stock_id: id},
      dataType: 'json',
      success: function(response) {
        $("#removeModal").modal('hide');
        manageTable.ajax.reload();
        alert(response.messages);
      }
    });
  });
  $("#removeModal").modal('show');
}
</script>