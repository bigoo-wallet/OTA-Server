<?php 
  $title = '管理员登录';
  $pageId = 'signin';
  require TEMP_ROOT. '/web/header.php'; 
?>
<div class="login-box">
  <div class="login-logo">
    <a href="<?=$_ENV['HOMEPAGE']?>"><b><?=$_ENV['SITENAME']?></b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">系统管理平台</p>

    <form action="<?=WEB_URL_ROOT?>/admin/auth/signin" method="post">
      <div class="form-group has-feedback">
        <input name="username" type="text" class="form-control" placeholder="登录名">
        <span class="fa fa-mobile form-control-feedback"></span>
        <div class="help-block">&nbsp;</div>
      </div>
      <div class="form-group has-feedback">
        <input name="pwd" type="password" class="form-control" placeholder="密码">
        <span class="fa fa-lock form-control-feedback"></span>
        <div class="help-block">&nbsp;</div>
      </div>
      <div class="row text-center">
        <button type="button" class="btn btn-submit btn-primary btn-flat">&nbsp;&nbsp;登录&nbsp;&nbsp;</button>
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<?php require TEMP_ROOT . '/web/footer.php'; ?>