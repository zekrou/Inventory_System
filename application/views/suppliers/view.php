<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Supplier Details
      <small><?php echo htmlspecialchars($supplier_data['name']); ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url('suppliers'); ?>">Suppliers</a></li>
      <li class="active">View</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- Informations de base -->
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Basic Information</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <tr>
                <th style="width:40%">Supplier Code</th>
                <td><?php echo htmlspecialchars($supplier_data['supplier_code']); ?></td>
              </tr>
              <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($supplier_data['name']); ?></td>
              </tr>
              <tr>
                <th>Contact Person</th>
                <td><?php echo htmlspecialchars($supplier_data['contact_person']); ?></td>
              </tr>
              <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($supplier_data['phone']); ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($supplier_data['email']); ?></td>
              </tr>
              <tr>
                <th>Address</th>
                <td><?php echo nl2br(htmlspecialchars($supplier_data['address'])); ?></td>
              </tr>
              <tr>
                <th>Country</th>
                <td><?php echo htmlspecialchars($supplier_data['country']); ?></td>
              </tr>
              <tr>
                <th>Payment Terms</th>
                <td><?php echo htmlspecialchars($supplier_data['payment_terms']); ?></td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                  <?php if ($supplier_data['active'] == 1): ?>
                    <span class="label label-success">Active</span>
                  <?php else: ?>
                    <span class="label label-default">Inactive</span>
                  <?php endif; ?>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="col-md-6">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Statistics</h3>
          </div>
          <div class="box-body">
            <?php if (isset($supplier_stats) && !empty($supplier_stats)): ?>
              <table class="table table-bordered">
                <tr>
                  <th>Total Purchases</th>
                  <td><?php echo isset($supplier_stats['total_purchases']) ? $supplier_stats['total_purchases'] : 0; ?></td>
                </tr>
                <tr>
                  <th>Total Spent</th>
                  <td><?php echo isset($supplier_stats['total_spent']) ? number_format($supplier_stats['total_spent'], 2) : '0.00'; ?> DZD</td>
                </tr>
                <tr>
                  <th>Last Purchase</th>
                  <td><?php echo isset($supplier_stats['last_purchase_date']) && $supplier_stats['last_purchase_date'] ? date('d-m-Y', strtotime($supplier_stats['last_purchase_date'])) : 'Never'; ?></td>
                </tr>
                <tr>
                  <th>Products Supplied</th>
                  <td><?php echo isset($supplier_products) ? count($supplier_products) : 0; ?></td>
                </tr>
              </table>
            <?php else: ?>
              <p>No statistics available</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Notes -->
        <?php if (isset($supplier_data['notes']) && !empty($supplier_data['notes'])): ?>
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Notes</h3>
            </div>
            <div class="box-body">
              <p><?php echo nl2br(htmlspecialchars($supplier_data['notes'])); ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>


      <!-- Notes -->
      <?php if (!empty($supplier_data['notes'])): ?>
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Notes</h3>
          </div>
          <div class="box-body">
            <p><?php echo nl2br(htmlspecialchars($supplier_data['notes'])); ?></p>
          </div>
        </div>
      <?php endif; ?>
    </div>
</div>

<!-- Produits fournis -->
<?php if (isset($supplier_products) && !empty($supplier_products)): ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Products Supplied</h3>
        </div>
        <div class="box-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Supplier Price</th>
                <th>Lead Time (days)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($supplier_products as $product): ?>
                <tr>
                  <td><?php echo htmlspecialchars($product['name']); ?></td>
                  <td><?php echo htmlspecialchars($product['sku']); ?></td>
                  <td><?php echo number_format($product['supplier_price'], 2); ?> DZD</td>
                  <td><?php echo $product['lead_time_days']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Boutons d'action -->
<div class="row">
  <div class="col-md-12">
    <a href="<?php echo base_url('suppliers'); ?>" class="btn btn-default">
      <i class="fa fa-arrow-left"></i> Back to List
    </a>
    <?php if (isset($user_permission['updateSupplier'])): ?>
      <button class="btn btn-primary" onclick="editSupplier(<?php echo $supplier_data['id']; ?>)">
        <i class="fa fa-edit"></i> Edit
      </button>
    <?php endif; ?>
  </div>
</div>
</section>
</div>