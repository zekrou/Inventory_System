<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Orders</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Orders</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
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

        <!-- Order Statistics -->
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo isset($order_stats['total_orders']) ? $order_stats['total_orders'] : 0; ?></h3>
                <p>Total Orders</p>
              </div>
              <div class="icon">
                <i class="fa fa-shopping-cart"></i>
              </div>
              <a href="#" class="small-box-footer" onclick="filterOrders('all'); return false;">
                View All <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo isset($order_stats['paid_orders']) ? $order_stats['paid_orders'] : 0; ?></h3>
                <p>Paid Orders</p>
              </div>
              <div class="icon">
                <i class="fa fa-check-circle"></i>
              </div>
              <a href="#" class="small-box-footer" onclick="filterOrders('paid'); return false;">
                View Paid <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3><?php echo isset($order_stats['partial_orders']) ? $order_stats['partial_orders'] : 0; ?></h3>
                <p>Partially Paid</p>
              </div>
              <div class="icon">
                <i class="fa fa-minus-circle"></i>
              </div>
              <a href="#" class="small-box-footer" onclick="filterOrders('partial'); return false;">
                View Partial <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
              <div class="inner">
                <h3><?php echo isset($order_stats['unpaid_orders']) ? $order_stats['unpaid_orders'] : 0; ?></h3>
                <p>Unpaid Orders</p>
              </div>
              <div class="icon">
                <i class="fa fa-times-circle"></i>
              </div>
              <a href="#" class="small-box-footer" onclick="filterOrders('unpaid'); return false;">
                View Unpaid <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>

        <?php if (isset($user_permission['createOrder'])): ?>
          <a href="<?php echo base_url('orders/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Order</a>
          <br /> <br />
        <?php endif; ?>

        <!-- 🟢 SEARCH BOX - NOUVEAU -->
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="text" class="form-control" id="searchInput" placeholder="Search by customer name, phone or bill number...">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="clearSearch" title="Clear search">
                  <i class="fa fa-times"></i>
                </button>
              </span>
            </div>
          </div>
        </div>

        <!-- Filter Buttons -->
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-default active" id="filter-all" onclick="filterOrders('all')">
            <i class="fa fa-list"></i> All Orders
          </button>
          <button type="button" class="btn btn-success" id="filter-paid" onclick="filterOrders('paid')">
            <i class="fa fa-check"></i> Paid
          </button>
          <button type="button" class="btn btn-warning" id="filter-partial" onclick="filterOrders('partial')">
            <i class="fa fa-clock-o"></i> Partially Paid
          </button>
          <button type="button" class="btn btn-danger" id="filter-unpaid" onclick="filterOrders('unpaid')">
            <i class="fa fa-times"></i> Unpaid
          </button>
        </div>
        <br /><br />

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Orders</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Bill No</th>
                  <th>Customer</th>
                  <th>Phone</th>
                  <th>Date</th>
                  <th>Items</th>
                  <th>Total Amount</th>
                  <th>Paid Amount</th>
                  <th>Due Amount</th>
                  <th>Status</th>
                  <?php if (isset($user_permission['updateOrder']) || isset($user_permission['viewOrder']) || isset($user_permission['deleteOrder'])): ?>
                    <th>Action</th>
                  <?php endif; ?>
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
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #3c8dbc; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Order Details</h4>
      </div>
      <div class="modal-body" id="orderDetailsContent">
        <div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i>
          <p>Loading order details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #00a65a; color: white;">
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
          <i class="fa fa-money"></i> Add Payment Installment
        </h4>
      </div>
      <form id="addPaymentForm">
        <div class="modal-body">
          <input type="hidden" name="order_id" id="modal_order_id">

          <div class="alert alert-info">
            <strong><i class="fa fa-info-circle"></i> Outstanding Balance:</strong>
            <span id="modal_current_due" style="font-size: 20px; font-weight: bold;"></span>
          </div>

          <div class="form-group">
            <label for="modal_payment_amount">Payment Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-money"></i></span>
              <input type="number" class="form-control input-lg" id="modal_payment_amount" name="payment_amount"
                step="0.01" min="0.01" placeholder="Enter payment amount" required
                style="font-size: 18px; font-weight: bold;">
              <span class="input-group-addon"><strong>DZD</strong></span>
            </div>
            <small class="text-muted">Maximum: <span id="modal_max_amount"></span> DZD</small>
          </div>

          <div class="form-group">
            <label for="modal_payment_method">Payment Method <span class="text-danger">*</span></label>
            <select class="form-control" id="modal_payment_method" name="payment_method" required>
              <option value="">-- Select Payment Method --</option>
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cheque">Cheque</option>
              <option value="credit_card">Credit Card</option>
              <option value="mobile_payment">Mobile Payment</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="modal_payment_notes">Payment Notes (Optional)</label>
            <textarea class="form-control" id="modal_payment_notes" name="payment_notes" rows="3"
              placeholder="Add any notes about this payment (reference number, receipt number, etc.)"></textarea>
          </div>

          <div id="modal_payment_message"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-success btn-lg" id="modal_submit_payment_btn">
            <i class="fa fa-check-circle"></i> Record Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php if (isset($user_permission['deleteOrder'])): ?>

  <!-- remove modal -->
  <div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          <h4 class="modal-title">Remove Order</h4>
        </div>

        <form role="form" action="<?php echo base_url('orders/remove') ?>" method="post" id="removeForm">
          <div class="modal-body">
            <p>Do you really want to remove this order?</p>
            <p class="text-warning"><strong>Note:</strong> Stock will be restored when order is deleted.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Remove</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";
  var currentFilter = 'all';
  var searchTerm = ''; // 🟢 Variable pour stocker le terme de recherche

  $(document).ready(function() {
    $('.main_menu_orders').addClass('active');
    $('.manage_orders_nav').addClass('active');

    // 🟢 Initialize the datatable
    manageTable = $('#manageTable').DataTable({
      'ajax': {
        'url': base_url + 'orders/fetchOrdersData',
        'type': 'GET',
        'data': function(d) {
          d.status = currentFilter;
          d.search_term = searchTerm;
        }
      },
      'order': [
        [0, 'desc']
      ],
      'columnDefs': [{
        'orderable': false,
        'targets': [-1] // ✅ CORRIGÉ: -1 = dernière colonne (dynamique)
      }],
      'searching': false,
      'processing': true,
      'language': {
        'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
      }
    });


    // 🟢 Search input handler with debounce (attendre 500ms après la dernière frappe)
    var searchTimer;
    $('#searchInput').on('keyup', function() {
      clearTimeout(searchTimer);
      var value = $(this).val().trim();

      // Ajoute un indicateur visuel pendant la recherche
      if (value.length > 0) {
        $(this).css('border-color', '#3c8dbc');
      } else {
        $(this).css('border-color', '');
      }

      searchTimer = setTimeout(function() {
        searchTerm = value;
        manageTable.ajax.reload(null, false); // false = reste sur la page courante
      }, 500); // Délai de 500ms
    });

    // 🟢 Clear search button
    $('#clearSearch').on('click', function() {
      $('#searchInput').val('').css('border-color', '');
      searchTerm = '';
      manageTable.ajax.reload(null, false);
    });

    // 🟢 Permet aussi de clear avec Escape key
    $('#searchInput').on('keydown', function(e) {
      if (e.key === 'Escape') {
        $(this).val('').css('border-color', '');
        searchTerm = '';
        manageTable.ajax.reload(null, false);
      }
    });

    // Handle Add Payment Form Submission
    $('#addPaymentForm').on('submit', function(e) {
      e.preventDefault();

      var form = $(this);
      var submitBtn = $('#modal_submit_payment_btn');
      var messageDiv = $('#modal_payment_message');

      // Validate amount
      var paymentAmount = parseFloat($('#modal_payment_amount').val());
      var maxAmount = parseFloat($('#modal_order_id').data('max-amount'));

      if (paymentAmount > maxAmount) {
        messageDiv.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Payment amount cannot exceed the due amount!</div>');
        return false;
      }

      // Disable submit button
      submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing Payment...');
      messageDiv.html('');

      $.ajax({
        url: base_url + 'orders/addPayment',
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            messageDiv.html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + response.message + '</div>');

            // Reset form
            form[0].reset();

            // Reload the datatable
            manageTable.ajax.reload(null, false);

            // Close modal after 2 seconds
            setTimeout(function() {
              $('#addPaymentModal').modal('hide');
              messageDiv.html('');

              // Refresh order details if modal is open
              if ($('#orderDetailsModal').hasClass('in')) {
                var orderId = $('#modal_order_id').val();
                viewOrderDetails(orderId);
              }
            }, 2000);
          } else {
            messageDiv.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> ' + response.message + '</div>');
            submitBtn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Record Payment');
          }
        },
        error: function(xhr, status, error) {
          messageDiv.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error: Unable to process payment. Please try again.</div>');
          submitBtn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Record Payment');
        }
      });
    });
  });

  // 🟢 Filter orders by status
  function filterOrders(status) {
    currentFilter = status;

    // Update button states
    $('.btn-group button').removeClass('active');
    $('#filter-' + status).addClass('active');

    // Reload table with filter
    manageTable.ajax.reload(null, false);
  }

  // View order details
  function viewOrderDetails(id) {
    $('#orderDetailsModal').modal('show');
    $('#orderDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading order details...</p></div>');

    $.ajax({
      url: base_url + 'orders/getOrderDetails/' + id,
      type: 'GET',
      dataType: 'html',
      success: function(response) {
        $('#orderDetailsContent').html(response);
      },
      error: function() {
        $('#orderDetailsContent').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error loading order details</div>');
      }
    });
  }

  // Open Add Payment Modal
  function openAddPaymentModal(orderId, dueAmount) {
    // Close order details modal first
    $('#orderDetailsModal').modal('hide');

    // Set order details in payment modal
    $('#modal_order_id').val(orderId).data('max-amount', dueAmount);
    $('#modal_current_due').text(parseFloat(dueAmount).toFixed(2) + ' DZD');
    $('#modal_max_amount').text(parseFloat(dueAmount).toFixed(2));
    $('#modal_payment_amount').attr('max', dueAmount);

    // Reset form
    $('#addPaymentForm')[0].reset();
    $('#modal_payment_message').html('');
    $('#modal_submit_payment_btn').prop('disabled', false).html('<i class="fa fa-check-circle"></i> Record Payment');

    // Show payment modal
    $('#addPaymentModal').modal('show');
  }

  // Remove order
  function removeFunc(id) {
    if (id) {
      $('#removeForm').on('submit', function() {
        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();

        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: {
            order_id: id
          },
          dataType: 'json',
          success: function(response) {
            manageTable.ajax.reload(null, false);

            if (response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                '<strong><i class="fa fa-check"></i></strong> ' + response.messages +
                '</div>');

              // hide the modal
              $("#removeModal").modal('hide');

            } else {
              $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                '<strong><i class="fa fa-warning"></i></strong> ' + response.messages +
                '</div>');
            }
          }
        });

        return false;
      });
    }
  }
</script>

<style>
  /* Modal styling */
  .modal-header {
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }

  #addPaymentModal .modal-content {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  }

  #addPaymentModal .alert-info {
    background: #d9edf7;
    border-color: #bce8f1;
    padding: 15px;
    border-radius: 5px;
  }

  /* 🟢 Search input styling */
  #searchInput:focus {
    border-color: #3c8dbc;
    box-shadow: 0 0 5px rgba(60, 141, 188, 0.5);
  }

  #clearSearch {
    border-left: none;
  }

  #clearSearch:hover {
    background-color: #e74c3c;
    color: white;
    border-color: #e74c3c;
  }
</style>