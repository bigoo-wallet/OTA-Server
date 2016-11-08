<?php 
  $title = 'OTA管理';
  $pageId = 'ota';
  $menus = TEMP_ROOT . '/web/admin/left-menus.php';
  require TEMP_ROOT. '/web/header.php'; 

  $queryPlatform = $request->getParam('platform');
  $queryChannel = $request->getParam('channel');
  $queryIdentification = $request->getParam('identification');
  $queryVersionName = $request->getParam('version_name');
  $queryVersionCode = $request->getParam('version_code');
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
      <div class="col-md-12">
        <div class="box box-warning collapsed-box">
          <div class="box-header with-border">
            <h3 class="box-title">条件筛选</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
          </div>
          <div class="box-body">
            <form class="container-fluid" action="?" method="get">
              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>所属平台</label>
                    <select name="platform" type="text" class="form-control">
                      <option value="">请选择</option>
                      <?php 
                        foreach ($platforms as $platform) {
                      ?>
                        <option value="<?=$platform['ota_platform_id']?>" <?=$queryPlatform == $platform['ota_platform_id'] ? 'selected' : ''?>><?=$platform['name']?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-xs-3">
                  <div class="form-group">
                    <label>渠道</label>
                    <select name="channel" type="text" class="form-control">
                      <option value="">请选择</option>
                      <?php 
                        foreach ($channels as $channel) {
                      ?>
                        <option value="<?=$channel['ota_channel_id']?>" <?=$queryChannel == $channel['ota_channel_id'] ? 'selected' : ''?>><?=$channel['name']?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-xs-3">
                  <div class="form-group">
                    <label>标识</label>
                    <select name="identification" type="text" class="form-control">
                      <option value="">请选择</option>
                      <?php 
                        foreach ($identifications as $identification) {
                      ?>
                        <option value="<?=$identification['ota_identification_id']?>" <?=$queryIdentification == $identification['ota_identification_id'] ? 'selected' : ''?>><?=$identification['name']?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-xs-3">
                  <div class="form-group">
                    <label>版本名</label>
                    <input name="version_name" type="text" class="form-control" value="<?=$queryVersionName?>">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label>版本号</label>
                    <input name="version_code" type="text" class="form-control" value="<?=$queryVersionCode?>">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6">
                  <button type="submit" class="btn btn-primary">查询</button>
                  <a href="?" class="btn btn-default">清除条件</a>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">升级包列表</h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center">平台</th>
                  <th class="text-center">渠道</th>
                  <th class="text-center">标识</th>
                  <th class="text-center">版本名称</th>
                  <th class="text-center">版本号</th>
                  <th class="text-center">强制升级</th>
                  <th class="text-center">操作</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($otas as $ota) {
                ?>
                <tr>
                  <td class="text-center"><?=$ota['platform_name']?></td>
                  <td class="text-center"><?=$ota['channel_name']?></td>
                  <td class="text-center"><?=$ota['identification_name']?></td>
                  <td class="text-center"><?=$ota['version_name']?></td>
                  <td class="text-center"><?=$ota['version_code']?></td>
                  <td class="text-center"><?=$ota['is_force'] == '1' ? '是' : '否'?></td>
                  <td class="text-center">
                    <a href="<?=WEB_URL_ROOT?>/admin/ota/detail/<?=$ota['ota_id']?>" class="btn btn-primary">详情</a>
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