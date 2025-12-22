<!-- Content Wrapper -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Manage <small>Purchases / Achats</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchases</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <!-- Statistics -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo isset($purchase_stats['total_purchases']) ? $purchase_stats['total_purchases'] : 0 ?></h3>
            <p>Total Purchases</p>
          </div>
          <div class="icon"><i class="fa fa-shopping-cart"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo isset($purchase_stats['pending_purchases']) ? $purchase_stats['pending_purchases'] : 0 ?></h3>
            <p>Pending</p>
          </div>
          <div class="icon"><i class="fa fa-clock-o"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo isset($purchase_stats['received_purchases']) ? $purchase_stats['received_purchases'] : 0 ?></h3>
            <p>Received</p>
          </div>
          <div class="icon"><i class="fa fa-check"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo isset($purchase_stats['total_spent']) ? number_format($purchase_stats['total_spent'], 0) : 0 ?> DZD</h3>
            <p>Total Spent</p>
          </div>
          <div class="icon"><i class="fa fa-money"></i></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php if(in_array('createPurchase', $user_permission)): ?>
          <a href="<?php echo base_url('purchases/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Create Purchase</a>
          <br /><br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Purchase Orders</h3>
          </div>
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Purchase No</th>
                <th>Supplier</th>
                <th>Date</th>
                <th>Total Amount</th>
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

<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {
  $("#purchaseNav").addClass('active');

  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'purchases/fetchPurchasesData',
    'order': [[0, 'desc']]
  });
});

function receivePurchase(id) {
  if(confirm('Are you sure you want to mark this purchase as received? This will update product stock quantities.')) {
    $.ajax({
      url: base_url + 'purchases/receive/' + id,
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if(response.success) {
          $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>'+
            '<strong><i class="fa fa-check"></i></strong> '+response.message+'</div>');
          manageTable.ajax.reload();
        } else {
          alert(response.message);
        }
      }
    });
  }
}
</script>