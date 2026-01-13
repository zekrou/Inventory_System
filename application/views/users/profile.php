<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      User
      <small>Profile</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Profile</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">User Profile</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table class="table table-bordered table-condensed table-hovered">
              <tr>
                <th style="width: 200px;">Username</th>
                <td><?php echo $user_data['username']; ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?php echo $user_data['email']; ?></td>
              </tr>
              <tr>
                <th>First Name</th>
                <td><?php echo isset($user_info['firstname']) ? $user_info['firstname'] : 'N/A'; ?></td>
              </tr>
              <tr>
                <th>Last Name</th>
                <td><?php echo isset($user_info['lastname']) ? $user_info['lastname'] : 'N/A'; ?></td>
              </tr>
              <tr>
                <th>Gender</th>
                <td><?php echo isset($user_info['gender']) && $user_info['gender'] ? $user_info['gender'] : 'N/A'; ?></td>
              </tr>
              <tr>
                <th>Phone</th>
                <td><?php echo isset($user_info['phone']) && $user_info['phone'] ? $user_info['phone'] : 'N/A'; ?></td>
              </tr>
              <tr>
                <th>Group</th>
                <td><span class="label label-info"><?php echo isset($user_group['group_name']) ? $user_group['group_name'] : 'Administrator'; ?></span></td>
              </tr>
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
