<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Purchases</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchases</li>
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
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <!-- Purchase Statistics -->
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- Total Purchases -->
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo isset($purchase_stats['total_purchases']) ? $purchase_stats['total_purchases'] : 0; ?></h3>
                <p>Total Purchases</p>
              </div>
              <div class="icon">
                <i class="fa fa-shopping-cart"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <!-- Pending Purchases -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3><?php echo isset($purchase_stats['pending_purchases']) ? $purchase_stats['pending_purchases'] : 0; ?></h3>
                <p>Pending</p>
              </div>
              <div class="icon">
                <i class="fa fa-clock-o"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <!-- Received Purchases -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo isset($purchase_stats['received_purchases']) ? $purchase_stats['received_purchases'] : 0; ?></h3>
                <p>Received</p>
              </div>
              <div class="icon">
                <i class="fa fa-check"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-xs-6">
            <!-- Total Spent -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3><?php echo number_format(isset($purchase_stats['total_spent']) ? $purchase_stats['total_spent'] : 0, 2); ?> DZD</h3>
                <p>Total Spent</p>
              </div>
              <div class="icon">
                <i class="fa fa-money"></i>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($user_permission['createPurchase'])): ?>
          <a href="<?php echo base_url('purchases/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Purchase</a>
          <br /> <br />
        <?php endif; ?>

        <!-- 🟢 SEARCH BOX -->
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="text" class="form-control" id="searchInput" placeholder="Search by supplier name, phone or purchase number...">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="clearSearch" title="Clear search">
                  <i class="fa fa-times"></i>
                </button>
              </span>
            </div>
          </div>
        </div>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Purchases</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Purchase No</th>
                  <th>Supplier</th>
                  <th>Phone</th> <!-- 🟢 Colonne téléphone ajoutée -->
                  <th>Date</th>
                  <th>Total Amount</th>
                  <th>Status</th>
                  <th style="width: 15%;">Actions</th>
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

<!-- Remove Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Supprimer l'achat</h4>
      </div>
      <form id="removeForm" action="<?php echo base_url('purchases/remove') ?>" method="post">
        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer cet achat?</p>
          <p class="text-warning">
            <strong>Note:</strong> Si l'achat est déjà reçu, une confirmation supplémentaire sera demandée.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </div>

        <!-- ✅ OBLIGATOIRE : name="purchase_id" -->
        <input type="hidden" name="purchase_id" id="remove_id" value="">
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";
  var searchTerm = '';

  $(document).ready(function() {
    // Add active class to menu
    $('.main_menu_purchases').addClass('active');
    $('.manage_purchases_nav').addClass('active');

    // Initialize the DataTable
    manageTable = $('#manageTable').DataTable({
      'ajax': {
        'url': base_url + 'purchases/fetchPurchasesData',
        'type': 'GET',
        'data': function(d) {
          d.search_term = searchTerm;
        },
        'error': function(xhr, error, code) {
          console.log('Ajax Error:', xhr.status, xhr.responseText);
          $('#messages').html(
            '<div class="alert alert-danger">' +
            '<i class="fa fa-exclamation-triangle"></i> Error loading purchases.' +
            '</div>'
          );
        }
      },
      'order': [
        [0, 'desc']
      ],
      'columnDefs': [{
        'orderable': false,
        'targets': [6]
      }],
      'searching': false,
      'processing': true,
      'language': {
        'processing': '<i class="fa fa-spinner fa-spin fa-3x"></i><span class="sr-only">Loading...</span>'
      }
    });

    // Search input handler
    var searchTimer;
    $('#searchInput').on('keyup', function() {
      clearTimeout(searchTimer);
      var value = $(this).val().trim();

      if (value.length > 0) {
        $(this).css('border-color', '#3c8dbc');
      } else {
        $(this).css('border-color', '');
      }

      searchTimer = setTimeout(function() {
        searchTerm = value;
        manageTable.ajax.reload(null, false);
      }, 500);
    });

    // Clear search button
    $('#clearSearch').on('click', function() {
      $('#searchInput').val('').css('border-color', '');
      searchTerm = '';
      manageTable.ajax.reload(null, false);
    });

    // Clear avec Escape key
    $('#searchInput').on('keydown', function(e) {
      if (e.key === 'Escape') {
        $(this).val('').css('border-color', '');
        searchTerm = '';
        manageTable.ajax.reload(null, false);
      }
    });

    // ✅ DÉPLACER LE HANDLER ICI (dans document.ready)
    $('#removeForm').on('submit', function(e) {
      e.preventDefault();
      var form = $(this);
      var purchase_id = $('#remove_id').val();

      // ✅ DEBUG
      console.log('Purchase ID à supprimer:', purchase_id);

      if (!purchase_id) {
        alert('Erreur: ID achat manquant!');
        return false;
      }

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: {
          purchase_id: purchase_id
        },
        dataType: 'json',
        success: function(response) {
          console.log('Réponse serveur:', response); // ✅ DEBUG

          if (response.success === true) {
            // Suppression réussie
            $("#removeModal").modal('hide');
            $("#messages").html(
              '<div class="alert alert-success alert-dismissible" role="alert">' +
              '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
              '<strong><i class="fa fa-check"></i></strong> ' + response.messages +
              '</div>'
            );
            manageTable.ajax.reload(null, false);

          } else if (response.type === 'is_received') {
            // Achat déjà reçu - demander confirmation
            $("#removeModal").modal('hide');

            if (confirm("⚠️ ATTENTION!\n\n" + response.messages + "\n\nCette action supprimera l'achat et restaurera le stock.\n\nContinuer?")) {
              // Force delete
              $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                  purchase_id: purchase_id,
                  force_delete: 'yes'
                },
                dataType: 'json',
                success: function(resp) {
                  if (resp.success === true) {
                    $("#messages").html(
                      '<div class="alert alert-success alert-dismissible" role="alert">' +
                      '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                      '<strong><i class="fa fa-check"></i></strong> ' + resp.messages +
                      '</div>'
                    );
                    manageTable.ajax.reload(null, false);
                  } else {
                    $("#messages").html(
                      '<div class="alert alert-danger alert-dismissible" role="alert">' +
                      '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                      '<strong><i class="fa fa-times"></i></strong> ' + resp.messages +
                      '</div>'
                    );
                  }
                },
                error: function(xhr, status, error) {
                  console.log('Erreur AJAX:', xhr.responseText); // ✅ DEBUG
                  $("#messages").html(
                    '<div class="alert alert-danger alert-dismissible" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
                    '<strong><i class="fa fa-exclamation-triangle"></i></strong> Erreur: ' + error +
                    '</div>'
                  );
                }
              });
            }
          } else {
            // Autre erreur
            $("#removeModal").modal('hide');
            $("#messages").html(
              '<div class="alert alert-warning alert-dismissible" role="alert">' +
              '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
              '<strong><i class="fa fa-warning"></i></strong> ' + response.messages +
              '</div>'
            );
          }
        },
        error: function(xhr, status, error) {
          console.log('Erreur AJAX:', xhr.responseText); // ✅ DEBUG
          $("#removeModal").modal('hide');
          $("#messages").html(
            '<div class="alert alert-danger alert-dismissible" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>' +
            '<strong><i class="fa fa-exclamation-triangle"></i></strong> Erreur: ' + error +
            '</div>'
          );
        }
      });

      return false;
    });
  });

  // ✅ Fonction pour ouvrir le modal de suppression
  function removeFunc(id) {
    if (id) {
      console.log('Ouverture du modal pour ID:', id); // ✅ DEBUG
      $('#remove_id').val(id);
      $('#removeModal').modal('show');
    }
  }

  // ✅ Fonction receive purchase
  function receivePurchase(id) {
    if (confirm("Confirmer la réception de cet achat?\n\nLe stock sera mis à jour.")) {
      window.location.href = base_url + 'purchases/receive/' + id;
    }
  }

  // ✅ Fonction cancel purchase
  function cancelPurchase(id) {
    if (confirm("Annuler cet achat?")) {
      window.location.href = base_url + 'purchases/cancel/' + id;
    }
  }
</script>



<style>
  /* Search input styling */
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

  /* 🟢 Style pour la colonne # */
  #manageTable tbody td:first-child {
    text-align: center;
    font-weight: bold;
    color: #666;
    background-color: #f9f9f9;
  }

  #manageTable thead th:first-child {
    text-align: center;
    background-color: #f4f4f4;
  }

  /* Small box hover effect */
  .small-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }

  /* Status badge styling */
  .label {
    font-size: 11px;
    padding: 5px 10px;
  }

  /* DataTable styling improvements */
  #manageTable_wrapper .dataTables_processing {
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid #3c8dbc;
    border-radius: 5px;
    padding: 20px;
  }

  /* Button group spacing */
  .btn-sm {
    margin-right: 3px;
  }

  /* Alert animations */
  .alert {
    animation: slideDown 0.3s ease-out;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>