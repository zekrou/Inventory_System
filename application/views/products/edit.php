<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
            <h3 class="box-title">Edit Product</h3>
          </div>

          <form role="form" action="<?php echo base_url('products/update') ?>" method="post" enctype="multipart/form-data">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label>Image Preview: </label>
                <img src="<?php echo base_url() . $product_data['image'] ?>" width="150" height="150" class="img-circle">
              </div>

              <div class="form-group">
                <label for="product_image">Update Image</label>
                <div class="kv-avatar">
                  <div class="file-loading">
                    <input id="product_image" name="product_image" type="file">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="product_name">Product name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_data['name']; ?>" autocomplete="off"/>
              </div>

              <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo $product_data['sku']; ?>" autocomplete="off" />
              </div>

              <!-- MULTI-LEVEL PRICING -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Pricing Levels</h3>
                  <small class="text-muted"> - Update prices for different customer types</small>
                </div>
                <div class="box-body">
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <strong>Pricing Guide:</strong> 
                    Cost Price is your purchase price. Set selling prices manually.
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="price_default"><i class="fa fa-money"></i> Cost Price <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                          <span class="input-group-addon">DZD</span>
                          <input type="number" step="0.01" class="form-control" id="price_default" name="price_default" value="<?php echo $product_data['price_default']; ?>" autocomplete="off" required />
                        </div>
                      </div>
                    </div>
                  </div>

                  <hr>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="price_super_wholesale"><i class="fa fa-truck"></i> Super Gros Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-addon">DZD</span>
                          <input type="number" step="0.01" class="form-control" id="price_super_wholesale" name="price_super_wholesale" value="<?php echo $product_data['price_super_wholesale']; ?>" autocomplete="off" required />
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="price_wholesale"><i class="fa fa-building"></i> Gros Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-addon">DZD</span>
                          <input type="number" step="0.01" class="form-control" id="price_wholesale" name="price_wholesale" value="<?php echo $product_data['price_wholesale']; ?>" autocomplete="off" required />
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="price_retail"><i class="fa fa-shopping-cart"></i> Détail Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-addon">DZD</span>
                          <input type="number" step="0.01" class="form-control" id="price_retail" name="price_retail" value="<?php echo $product_data['price_retail']; ?>" autocomplete="off" required />
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="form-group">
                <label for="qty">Qty</label>
                <input type="text" class="form-control" id="qty" name="qty" value="<?php echo $product_data['qty']; ?>" autocomplete="off" />
              </div>

              <div class="form-group">
                <label for="description">Description</label>
                <textarea type="text" class="form-control" id="description" name="description" autocomplete="off"><?php echo $product_data['description']; ?></textarea>
              </div>

              <div class="form-group">
                <label for="brands">Brands</label>
                <?php $brand_data = (array) json_decode($product_data['brand_id']); ?>
                <select class="form-control select_group" id="brands" name="brands[]" multiple="multiple">
                  <?php foreach ($brands as $k => $v): ?>
                    <option value="<?php echo $v['id'] ?>" <?php if(in_array($v['id'], $brand_data)) echo 'selected'; ?>><?php echo $v['name'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>

              <div class="form-group">
                <label for="category">Category</label>
                <?php $category_data = (array) json_decode($product_data['category_id']); ?>
                <select class="form-control select_group" id="category" name="category[]" multiple="multiple">
                  <?php foreach ($category as $k => $v): ?>
                    <option value="<?php echo $v['id'] ?>" <?php if(in_array($v['id'], $category_data)) echo 'selected'; ?>><?php echo $v['name'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>

              <div class="form-group">
                <label for="stock">Stock</label>
                <select class="form-control select_group" id="stock" name="stock">
                  <?php foreach ($stocks as $k => $v): ?>
                    <option value="<?php echo $v['id'] ?>" <?php if($product_data['stock_id'] == $v['id']) echo 'selected'; ?>><?php echo $v['name'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>

              <div class="form-group">
                <label for="availability">Availability</label>
                <select class="form-control" id="availability" name="availability">
                  <option value="1" <?php if($product_data['availability'] == 1) echo 'selected'; ?>>Yes</option>
                  <option value="2" <?php if($product_data['availability'] != 1) echo 'selected'; ?>>No</option>
                </select>
              </div>

            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <a href="<?php echo base_url('products/') ?>" class="btn btn-warning">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $(".select_group").select2();
    $("#description").wysihtml5();
    $("#mainProductNav").addClass('active');
    $("#manageProductNav").addClass('active');

    var btnCust = '<button type="button" class="btn btn-secondary" title="Add picture tags" onclick="alert(\'Call your custom code here.\')">' +
                  '<i class="glyphicon glyphicon-tag"></i></button>'; 
    $("#product_image").fileinput({
      overwriteInitial: true,
      maxFileSize: 1500,
      showClose: false,
      showCaption: false,
      browseLabel: '',
      removeLabel: '',
      browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
      removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
      removeTitle: 'Cancel or reset changes',
      elErrorContainer: '#kv-avatar-errors-1',
      msgErrorClass: 'alert alert-block alert-danger',
      layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
      allowedFileExtensions: ["jpg", "png", "gif"]
    });
  });
</script>
