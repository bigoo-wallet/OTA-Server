<?php 
  $title = '添加升级包';
  $pageId = 'ota';
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
      <div class="col-md-6">
        <!-- 基本信息 -->
        <div class="box box-primary">

          <form action="<?=WEB_URL_ROOT?>/admin/ota/add" method="post" enctype="multipart/form-data">
            <div class="box-header with-border">
              <h3 class="box-title"><?=$title?></h3>
            </div>
            <div class="box-body box-profile">
              
              <div class="form-group has-feedback">
                <label>平台</label>
                [<a href="<?=WEB_URL_ROOT?>/admin/ota/platform">管理</a>]
                <select name="platform" type="text" class="form-control">
                  <option value="">请选择</option>
                  <?php 
                    foreach ($platforms as $platform) {
                  ?>
                    <option value="<?=$platform['ota_platform_id']?>"><?=$platform['name']?></option>
                  <?php } ?>
                </select>
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group has-feedback">
                <label>
                  <span>渠道</span>
                  [<a href="<?=WEB_URL_ROOT?>/admin/ota/channel">管理</a>]
                </label>
                <select name="channel" type="text" class="form-control">
                  <option value="">请选择</option>
                  <?php 
                    foreach ($channels as $channel) {
                  ?>
                    <option value="<?=$channel['ota_channel_id']?>"><?=$channel['name']?></option>
                  <?php } ?>
                </select>
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group has-feedback">
                <label>
                  <span>标识</span>
                  [<a href="<?=WEB_URL_ROOT?>/admin/ota/identification">管理</a>]
                </label>
                <select name="identification" type="text" class="form-control">
                  <option value="">请选择</option>
                  <?php 
                    foreach ($identifications as $identification) {
                  ?>
                    <option value="<?=$identification['ota_identification_id']?>"><?=$identification['name']?></option>
                  <?php } ?>
                </select>
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>版本名称</label>
                <input type="text" name="version_name" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>版本号</label>
                <input type="number" name="version_code" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>强制升级</label>
                <select name="is_force" type="text" class="form-control">
                  <option value="0">否</option>
                  <option value="1">是</option>
                </select>
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>文件上传</label>
                <input type="file" name="file" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>版本描述</label>
                <textarea name="description" class="form-control"></textarea>
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