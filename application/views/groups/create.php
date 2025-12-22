<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage <small>Groups</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">groups</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">
        
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
            <h3 class="box-title">Add Group</h3>
          </div>
          <form role="form" action="<?php base_url('groups/create') ?>" method="post">
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="group_name">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Enter group name">
              </div>
              
              <div class="form-group">
                <label for="permission">Permissions - Sélectionner TOUTES les autorisations nécessaires</label>

                <table class="table table-responsive table-bordered">
                  <thead>
                    <tr style="background: #f4f4f4;">
                      <th style="width: 20%">Module</th>
                      <th style="width: 16%">Create</th>
                      <th style="width: 16%">Update</th>
                      <th style="width: 16%">View</th>
                      <th style="width: 16%">Delete</th>
                      <th style="width: 16%">Special</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Users -->
                    <tr>
                      <td><strong>Users</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createUser" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateUser" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewUser" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteUser" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Groups -->
                    <tr>
                      <td><strong>Groups</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createGroup" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateGroup" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewGroup" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteGroup" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Brands -->
                    <tr>
                      <td><strong>Brands</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createBrand" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateBrand" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewBrand" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteBrand" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Category -->
                    <tr>
                      <td><strong>Category</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createCategory" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateCategory" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewCategory" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteCategory" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Stores -->
                    <tr>
                      <td><strong>Stores</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createStore" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateStore" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewStore" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteStore" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    
                    <!-- Products -->
                    <tr>
                      <td><strong>Products</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createProduct" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateProduct" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewProduct" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteProduct" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- ⭐ CUSTOMERS - NOUVEAU -->
                    <tr style="background: #e8f5e9;">
                      <td><strong>Customers</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createCustomer" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateCustomer" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewCustomer" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteCustomer" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Orders -->
                    <tr>
                      <td><strong>Orders</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createOrder" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateOrder" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewOrder" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteOrder" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- ⭐ SUPPLIERS - NOUVEAU -->
                    <tr style="background: #fff3e0;">
                      <td><strong>Suppliers</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createSupplier" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateSupplier" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewSupplier" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteSupplier" class="minimal"></td>
                      <td>-</td>
                    </tr>
                    
                    <!-- ⭐ PURCHASES - NOUVEAU -->
                    <tr style="background: #e3f2fd;">
                      <td><strong>Purchases</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createPurchase" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updatePurchase" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewPurchase" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deletePurchase" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="receivePurchase" class="minimal" title="Receive Purchase"></td>
                    </tr>
                    
                    <!-- ⭐ STOCK - NOUVEAU -->
                    <tr style="background: #fce4ec;">
                      <td><strong>Stock</strong></td>
                      <td><input type="checkbox" name="permission[]" value="createStock" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="updateStock" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewStock" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="deleteStock" class="minimal"></td>
                      <td><input type="checkbox" name="permission[]" value="viewStockHistory" class="minimal" title="View Stock History"></td>
                    </tr>
                    
                    <!-- Reports -->
                    <tr>
                      <td><strong>Reports</strong></td>
                      <td>-</td>
                      <td>-</td>
                      <td><input type="checkbox" name="permission[]" value="viewReports" class="minimal"></td>
                      <td>-</td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Company -->
                    <tr>
                      <td><strong>Company</strong></td>
                      <td>-</td>
                      <td><input type="checkbox" name="permission[]" value="updateCompany" class="minimal"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Profile -->
                    <tr>
                      <td><strong>Profile</strong></td>
                      <td>-</td>
                      <td>-</td>
                      <td><input type="checkbox" name="permission[]" value="viewProfile" class="minimal"></td>
                      <td>-</td>
                      <td>-</td>
                    </tr>
                    
                    <!-- Setting -->
                    <tr>
                      <td><strong>Setting</strong></td>
                      <td>-</td>
                      <td><input type="checkbox" name="permission[]" value="updateSetting" class="minimal"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                    </tr>
                  </tbody>
                </table>
                
                <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i> <strong>Note:</strong> Les lignes colorées sont les NOUVEAUX modules ajoutés (Customers, Suppliers, Purchases, Stock)
                </div>
                
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <a href="<?php echo base_url('groups/') ?>" class="btn btn-warning">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $("#mainGroupNav").addClass('active');
    $("#addGroupNav").addClass('active');

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    });
  });
</script>