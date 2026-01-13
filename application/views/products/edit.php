<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <h1>
      Manage
      <small>Edit Product</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('products') ?>">Products</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

  <!-- Main content -->
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
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php elseif($this->session->flashdata('errors')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('errors'); ?>
          </div>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Edit Product</h3>
          </div>
          
          <?php echo form_open_multipart('products/update/' . $product_data['id'], array('id' => 'updateForm')); ?>
          
          <div class="box-body">

            <?php echo validation_errors(); ?>

            <!-- ✅ IMAGE SECTION - EN HAUT AVEC PREVIEW -->
            <div class="form-group">
              <label><i class="fa fa-camera"></i> Product Image</label>
              <div class="row">
                <!-- Current Image Preview -->
                <div class="col-md-3">
                  <div class="text-center">
                    <?php 
                      $current_image = !empty($product_data['image']) ? base_url($product_data['image']) : base_url('assets/images/no_image.png');
                    ?>
                    <img id="imagePreview" src="<?php echo $current_image; ?>" class="img-thumbnail" style="max-width: 100%; max-height: 250px; margin-bottom: 10px;">
                    <br>
                    <small class="text-muted">Current Image</small>
                  </div>
                </div>

                <!-- Upload Controls -->
                <div class="col-md-9">
                  <div class="well">
                    <h4 class="text-primary">Change Product Image</h4>
                    <p class="text-muted">
                      <i class="fa fa-info-circle"></i> Choose a new image to replace the current one
                    </p>
                    
                    <div class="form-group">
                      <label class="btn btn-primary btn-file">
                        <i class="fa fa-folder-open"></i> Browse Image
                        <input type="file" id="product_image" name="product_image" accept="image/*" style="display: none;" onchange="previewImage(event)">
                      </label>
                      <button type="button" class="btn btn-danger" onclick="removeImage()" id="removeImageBtn" style="display: none;">
                        <i class="fa fa-trash"></i> Remove
                      </button>
                    </div>

                    <div id="selectedFileName" class="alert alert-info" style="display: none;">
                      <i class="fa fa-file-image-o"></i> Selected: <strong id="fileName"></strong>
                    </div>

                    <p class="help-block">
                      <i class="fa fa-check-circle text-success"></i> Allowed formats: JPG, PNG, GIF<br>
                      <i class="fa fa-check-circle text-success"></i> Maximum size: 1MB<br>
                      <i class="fa fa-check-circle text-success"></i> Recommended: 500x500 pixels
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <!-- BASIC INFORMATION -->
            <h4 class="text-primary"><i class="fa fa-info-circle"></i> Basic Information</h4>
            
            <div class="form-group">
              <label for="product_name">Product Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" value="<?php echo $product_data['name']; ?>" autocomplete="off" required />
            </div>

            <div class="form-group">
              <label for="sku">SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter SKU" value="<?php echo $product_data['sku']; ?>" autocomplete="off" required />
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter product description"><?php echo $product_data['description']; ?></textarea>
            </div>

            <hr>

            <!-- PRICING INFORMATION -->
            <h4 class="text-success"><i class="fa fa-money"></i> Pricing Information</h4>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price_default">Cost Price (Prix d'Achat) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="price_default" name="price_default" placeholder="0.00" value="<?php echo $product_data['price_default']; ?>" required />
                    <span class="input-group-addon">DZD</span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price_retail">Retail Price (Détail) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="price_retail" name="price_retail" placeholder="0.00" value="<?php echo $product_data['price_retail']; ?>" required />
                    <span class="input-group-addon">DZD</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price_wholesale">Wholesale Price (Gros) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="price_wholesale" name="price_wholesale" placeholder="0.00" value="<?php echo $product_data['price_wholesale']; ?>" required />
                    <span class="input-group-addon">DZD</span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="price_super_wholesale">Super Wholesale Price (Super Gros) <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="price_super_wholesale" name="price_super_wholesale" placeholder="0.00" value="<?php echo $product_data['price_super_wholesale']; ?>" required />
                    <span class="input-group-addon">DZD</span>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <!-- INVENTORY INFORMATION -->
            <h4 class="text-warning"><i class="fa fa-cubes"></i> Inventory Information</h4>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="qty">Quantity <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" id="qty" name="qty" placeholder="0" value="<?php echo $product_data['qty']; ?>" required />
                  <p class="help-block">Current stock quantity</p>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="category">Category</label>
                  <select class="form-control select_group" id="category" name="category[]" multiple="multiple">
                    <?php foreach ($categories as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>" <?php if(isset($product_data['category_ids']) && in_array($v['id'], $product_data['category_ids'])): ?> selected <?php endif; ?>><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="stock">Stock Location <span class="text-danger">*</span></label>
                  <select class="form-control select_group" id="stock" name="stock" required>
                    <option value="">-- Select Stock --</option>
                    <?php foreach ($stocks as $k => $v): ?>
                      <option value="<?php echo $v['id'] ?>" <?php if($product_data['stock_id'] == $v['id']) { echo 'selected'; } ?>><?php echo $v['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="availability">Availability Status</label>
              <select class="form-control" id="availability" name="availability">
                <option value="1" <?php if($product_data['availability'] == 1) { echo "selected"; } ?>>Active</option>
                <option value="2" <?php if($product_data['availability'] != 1) { echo "selected"; } ?>>Inactive</option>
              </select>
            </div>

          </div>
          <!-- /.box-body -->

          <div class="box-footer">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fa fa-check"></i> Update Product
            </button>
            <a href="<?php echo base_url('products/') ?>" class="btn btn-default btn-lg">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>
          <?php echo form_close(); ?>
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

<style>
.btn-file {
  position: relative;
  overflow: hidden;
}
.well {
  background-color: #f9f9f9;
  border: 1px solid #e3e3e3;
}
</style>

<script type="text/javascript">
  $(document).ready(function() {
    $(".select_group").select2();
    $("#description").wysihtml5();

    $("#mainProductNav").addClass('active');
    $("#manageProductNav").addClass('active');

    $("#updateForm").unbind('submit').bind('submit', function() {
      // Validation
      if($("#product_name").val() == '') {
        alert('Product name is required');
        $("#product_name").focus();
        return false;
      }

      if($("#sku").val() == '') {
        alert('SKU is required');
        $("#sku").focus();
        return false;
      }

      if($("#price_default").val() == '' || parseFloat($("#price_default").val()) <= 0) {
        alert('Cost price is required and must be greater than 0');
        $("#price_default").focus();
        return false;
      }

      if($("#stock").val() == '') {
        alert('Please select a stock location');
        $("#stock").focus();
        return false;
      }

      return true;
    });
  });

  // ✅ Preview image before upload
  function previewImage(event) {
    var input = event.target;
    var file = input.files[0];
    
    if (file) {
      // Check file size (1MB = 1048576 bytes)
      if (file.size > 1048576) {
        alert('File size must be less than 1MB!');
        input.value = '';
        return;
      }

      // Check file type
      var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!validTypes.includes(file.type)) {
        alert('Only JPG, PNG and GIF images are allowed!');
        input.value = '';
        return;
      }

      // Show preview
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#imagePreview').attr('src', e.target.result);
      };
      reader.readAsDataURL(file);

      // Show file name
      $('#fileName').text(file.name);
      $('#selectedFileName').fadeIn();
      $('#removeImageBtn').fadeIn();
    }
  }

  // ✅ Remove selected image
  function removeImage() {
    $('#product_image').val('');
    $('#imagePreview').attr('src', '<?php echo $current_image; ?>');
    $('#selectedFileName').fadeOut();
    $('#removeImageBtn').fadeOut();
  }
</script>
