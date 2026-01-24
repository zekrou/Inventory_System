<!DOCTYPE html>
<html dir="<?= ($this->session->userdata('lang') ?? 'arabic') == 'arabic' ? 'rtl' : 'ltr'; ?>">
<head>
  <meta charset="utf-8">
  <title><?= $this->lang->line('login_title') ?? 'Log in' ?></title>
  <!-- Vos CSS AdminLTE... -->

  <!-- RTL CSS si arabe -->
  <?php if (($this->session->userdata('lang') ?? 'arabic') == 'arabic'): ?>
  <link rel="stylesheet" href="<?= base_url('assets/css/rtl.css') ?>">
  <?php endif; ?>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?= base_url('auth/login') ?>"><b><?= $this->lang->line('app_name') ?? 'Login' ?></b></a>
  </div>
  
  <div class="login-box-body">
    <p class="login-box-msg"><?= $this->lang->line('sign_in_msg') ?? 'Sign in to start your session' ?></p>

    <?= validation_errors() ?>
    <?php if(!empty($errors)) echo $errors; ?>

    <?= form_open('auth/login') ?>
      <div class="form-group has-feedback">
        <input type="email" class="form-control" name="email" placeholder="<?= $this->lang->line('email_ph') ?? 'Email' ?>">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="<?= $this->lang->line('password_ph') ?? 'Password' ?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="remember"> <?= $this->lang->line('remember_me') ?? 'Remember Me' ?>
            </label>
          </div>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">
            <?= $this->lang->line('btn_signin') ?? 'Sign In' ?>
          </button>
        </div>
      </div>
    <?= form_close() ?>
  </div>
</div>

<!-- Vos JS iCheck... -->
<!-- Switch langue TOP -->
<div style="position:fixed;top:10px;right:10px;z-index:9999;">
<select onchange="window.location='?lang='+this.value" style="padding:5px;">
<option value="english">EN</option>
<option value="arabic" selected>ع</option>
</select>
</div>

<script>
$(function () {
  $('input').iCheck({ checkboxClass: 'icheckbox_square-blue', radioClass: 'iradio_square-blue', increaseArea: '20%' });
});
</script>
</body>
</html>
