<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Manage
      <small>Category</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Category</li>
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

        <?php if(isset($user_permission['createCategory'])): ?>
          <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add Category</button>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Categories</h3>
          </div>
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Category Name</th>
                  <th>Status</th>
                  <th>Stock</th>
                  <?php if(isset($user_permission['updateCategory']) || isset($user_permission['deleteCategory'])): ?>
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

<?php if(isset($user_permission['createCategory'])): ?>
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Category</h4>
      </div>
      <form role="form" action="<?php echo base_url('category/create') ?>" method="post" id="createForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="stock_id">Stock *</label>
            <select class="form-control" id="stock_id" name="stock_id" required>
              <option value="">-- Select Stock --</option>
              <?php foreach($stocks as $stock): ?>
                <option value="<?php echo $stock['id'] ?>"><?php echo $stock['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" autocomplete="off">
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
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(isset($user_permission['updateCategory'])): ?>
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Category</h4>
      </div>
      <form role="form" action="<?php echo base_url('category/update') ?>" method="post" id="updateForm">
        <div class="modal-body">
          <div id="messages"></div>
          <div class="form-group">
            <label for="edit_stock_id">Stock *</label>
            <select class="form-control" id="edit_stock_id" name="edit_stock_id" required>
              <option value="">-- Select Stock --</option>
              <?php foreach($stocks as $stock): ?>
                <option value="<?php echo $stock['id'] ?>"><?php echo $stock['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="edit_category_name">Category Name</label>
            <input type="text" class="form-control" id="edit_category_name" name="edit_category_name" placeholder="Enter category name" autocomplete="off">
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
    </div>
  </div>
</div>
<?php endif; ?>

<?php if(isset($user_permission['deleteCategory'])): ?>
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Remove Category</h4>
      </div>
      <form role="form" action="<?php echo base_url('category/remove') ?>" method="post" id="removeForm">
        <div class="modal-body">
          <p>Do you really want to remove?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script type="text/javascript">
var manageTable;

$(document).ready(function() {
  $("#categoryNav").addClass('active');
  
  manageTable = $('#manageTable').DataTable({
    'ajax': 'fetchCategoryData',
    'order': []
  });

  $("#createForm").unbind('submit').on('submit', function() {
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
          $("#messages").html('<div class="alert alert-success alert-dismissible">'+
            '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
          '</div>');
          $("#addModal").modal('hide');
          $("#createForm")[0].reset();
          $("#createForm .form-group").removeClass('has-error').removeClass('has-success');
        } else {
          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);
              id.closest('.form-group').removeClass('has-error').removeClass('has-success').addClass(value.length > 0 ? 'has-error' : 'has-success');
              id.after(value);
            });
          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible">'+
              '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>');
          }
        }
      }
    }); 
    return false;
  });
});

// edit, remove functions
function editFunc(id) {
  $.ajax({
    url: 'fetchCategoryDataById/'+id,
    type: 'post',
    dataType: 'json',
    success:function(response) {
      $("#edit_stock_id").val(response.stock_id);
      $("#edit_category_name").val(response.name);
      $("#edit_active").val(response.active);

      $("#updateForm").unbind('submit').bind('submit', function() {
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
              $("#messages").html('<div class="alert alert-success alert-dismissible">'+
                '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
              '</div>');
              $("#editModal").modal('hide');
              $("#updateForm .form-group").removeClass('has-error').removeClass('has-success');
            } else {
              if(response.messages instanceof Object) {
                $.each(response.messages, function(index, value) {
                  var id = $("#"+index);
                  id.closest('.form-group').removeClass('has-error').removeClass('has-success').addClass(value.length > 0 ? 'has-error' : 'has-success');
                  id.after(value);
                });
              } else {
                $("#messages").html('<div class="alert alert-warning alert-dismissible">'+
                  '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
                  '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
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

function removeFunc(id) {
  if(id) {
    $("#removeForm").on('submit', function() {
      var form = $(this);
      $(".text-danger").remove();
      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { category_id:id }, 
        dataType: 'json',
        success:function(response) {
          manageTable.ajax.reload(null, false); 
          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible">'+
              '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');
            $("#removeModal").modal('hide');
          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible">'+
              '<button type="button" class="close" data-dismiss="alert">&times;</button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>'); 
          }
        }
      }); 
      return false;
    });
  }
}
</script>
