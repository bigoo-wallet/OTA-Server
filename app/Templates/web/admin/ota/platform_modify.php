<?php 
  $title = '平台渠道';
  $pageId = 'ota';
  $menus = TEMP_ROOT . '/web/admin/left-menus.php';
  require TEMP_ROOT. '/web/header.php'; 
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><?=$title?></h1>
  <ol class="breadcrumb">
    <li><a href="<?=WEB_URL_ROOT?>/admin/ota/platform">返回渠道管理</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="databox">

    <div class="row">
      <div class="col-md-4">
        <!-- 基本信息 -->
        <div class="box box-primary">
          <form action="<?=WEB_URL_ROOT?>/admin/ota/platform/modify" method="post">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$title?></h3>
            </div>
            <div class="box-body box-profile">

              <div class="form-group">
                <label>平台名称</label>
                <input type="text" name="name" class="form-control" value="<?=$platform['name']?>">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>描述</label>
                <textarea name="description" class="form-control"><?=$platform['description']?></textarea>
                <div class="help-block">&nbsp;</div>
              </div>

            </div>
            <div class="box-footer">
              <input name="platform_id" value="<?=$platform['ota_platform_id']?>" type="hidden">
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
</section>

<?php require TEMP_ROOT. '/web/footer.php'; ?>