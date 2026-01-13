  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.0.0
    </div>
    <strong>System Administration &copy; 2026 <a href="#">Inventory System</a>.</strong> All rights reserved.
  </footer>

</div>
<!-- ./wrapper -->

<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?php echo base_url('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/dist/js/adminlte.min.js') ?>"></script>

<script>
$(document).ready(function() {
  $('.data-table').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false
  });
});
</script>

</body>
</html>
