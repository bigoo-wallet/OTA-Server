<?php 
  $title = '升级包详情';
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
<!-- Main content -->
<section class="content">
  <div class="databox">

    <div class="row">
      <div class="col-md-8">
        <!-- 基本信息 -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">基本信息</h3>
            <div class="box-tools pull-right">
              <a href="<?=WEB_URL_ROOT?>/admin/ota/delete/<?=$ota['ota_id']?>" class="btn btn-danger btn-confirm" title="sure" confirm-msg="您确定要删除升级包吗？">删除</a>
            </div>
          </div>
          <div class="box-body">
            <ul class="products-list product-list-in-box">
              <li class="item">
                <b>平台</b> <a class="pull-right"><?=$ota['platform_name']?></a>
              </li>
              <li class="item">
                <b>渠道</b> <a class="pull-right"><?=$ota['channel_name']?></a>
              </li>
              <li class="item">
                <b>标识</b> <a class="pull-right"><?=$ota['identification_name']?></a>
              </li>
              <li class="item">
                <b>版本名称</b> <a class="pull-right"><?=$ota['version_name']?></a>
              </li>
              <li class="item">
                <b>版本号</b> <a class="pull-right"><?=$ota['version_code']?></a>
              </li>
              <li class="item">
                <b>文件大小</b> <a class="pull-right"><?=$ota['package_size']?> Byte</a>
              </li>
              <li class="item">
                <b>MD5</b> <a class="pull-right"><?=$ota['package_md5']?> Byte</a>
              </li>
              <li class="item">
                <b>下载地址</b> <a class="pull-right"><?=$_ENV['OTA_FILE_ACCESS_URL']?>/<?=$ota['url']?></a>
              </li>
              <li class="item">
                <b>强制升级</b> <a class="pull-right"><?=$ota['is_force'] == '1' ? '是' : '否'?></a>
              </li>
              <li class="item">
                <b>版本描述</b> <a class="pull-right"><?=$ota['description']?></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php require TEMP_ROOT. '/web/footer.php'; ?>