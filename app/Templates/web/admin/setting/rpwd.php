<?php 
  $title = '修改密码';
  $pageId = 'setting';
  $menus = TEMP_ROOT . '/web/admin/left-menus.php';
  require TEMP_ROOT. '/web/header.php'; 
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?=$title?>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="databox">

    <div class="row">
      <div class="col-md-4">
        <!-- 基本信息 -->
        <div class="box box-primary">

          <form action="<?=WEB_URL_ROOT?>/admin/setting/rpwd" method="post">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$title?></h3>
            </div>
            <div class="box-body box-profile">

              <div class="form-group">
                <label>原密码</label>
                <input type="password" name="opwd" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>新密码</label>
                <input type="password" name="npwd" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>确认新密码</label>
                <input type="password" name="rpwd" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>
            </div>

            <div class="box-footer">
              <button type="button" class="btn btn-submit btn-primary btn-block btn-flat">
                确定
                <span class="label label-success hide">操作成功</span>
              </button>
            </div>
          </form>
        </div>

        </div>
      </div>
    </div>

  </div>
</section>

<?php require TEMP_ROOT. '/web/footer.php'; ?>