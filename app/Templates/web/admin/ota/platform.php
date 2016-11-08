<?php 
  $title = '平台管理';
  $pageId = 'ota';
  $menus = TEMP_ROOT . '/web/admin/left-menus.php';
  require TEMP_ROOT. '/web/header.php'; 
?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?=$title?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?=WEB_URL_ROOT?>/admin/ota/add">返回添加升级包</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="databox">

    <div class="row">
      <div class="col-md-4">
        <!-- 基本信息 -->
        <div class="box box-primary">
          <form action="<?=WEB_URL_ROOT?>/admin/ota/platform/add" method="post">
            <div class="box-header with-border">
              <h3 class="box-title">添加平台</h3>
            </div>
            <div class="box-body box-profile">

              <div class="form-group">
                <label>平台名称</label>
                <input type="text" name="name" class="form-control">
                <div class="help-block">&nbsp;</div>
              </div>

              <div class="form-group">
                <label>描述</label>
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

      <div class="col-md-8">
        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">平台列表</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center">名称</th>
                  <th class="text-center">描述</th>
                  <th class="text-center">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($platforms as $platform) {
                ?>
                <tr>
                  <td class="text-center"><?=$platform['name']?></td>
                  <td class="text-center"><?=$platform['description']?></td>
                  <td class="text-center">
                    <a href="<?=WEB_URL_ROOT?>/admin/ota/platform/modify/<?=$platform['ota_platform_id']?>" class="btn btn-primary">修改</a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>

            <?php
              require TEMP_ROOT. '/web/page.php';
            ?>
          </div>
        </div>
      </div>    
    </div>

  </div>
</section>

<?php require TEMP_ROOT. '/web/footer.php'; ?>