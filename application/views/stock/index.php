<div class="content-wrapper">
  <section class="content-header">
    <h1>Stock Management <small>Overview</small></h1>
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

    <!-- Filters -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Filter Stock</h3>
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
              <select name="store" class="form-control">
                <option value="">All Stores</option>
                <?php foreach($stores as $store): ?>
                  <option value="<?php echo $store['id']; ?>" <?php echo ($current_store == $store['id']) ? 'selected' : ''; ?>>
                    <?php echo $store['name']; ?>
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

    <!-- Stock Table -->
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Stock Overview</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Product</th>
              <th>SKU</th>
              <th>Store</th>
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
                  <td><?php echo $product['sku']; ?></td>
                  <td><?php echo $product['store_name']; ?></td>
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