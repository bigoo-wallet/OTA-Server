<?php
/**
 * OTA管理页面
 * @author tytymnty@gmail.com
 * @since 2016-09-02 11:39:58
 */

namespace Growler\Routers\Web\Admin;

use Growler\Libs\Valid;
use Growler\Libs\RDB;
use Growler\Libs\Page;
use Growler\Libs\File;
use Growler\Models\OTAModel;
use Growler\Models\OTAPlatformModel;
use Growler\Models\OTAChannelModel;
use Growler\Models\OTAIdentificationModel;
use Growler\Services\OTAService;

/**
 * OTA列表
 */
$app->get('/web/admin/ota', function ($request, $response, $args) {

  $cp = $request->getParam('cp');

  $platform = $request->getParam('platform');
  $identification = $request->getParam('identification');
  $channel = $request->getParam('channel');

  $versionName = $request->getParam('version_name');
  $versionCode = $request->getParam('version_code');

  $where = [];
  if (!empty($platform)) {
    $where[OTAModel::$name . '.ota_platform_id'] = $platform;
  }

  if (!empty($identification)) {
    $where[OTAModel::$name . '.ota_identification_id'] = $identification;
  }

  if (!empty($channel)) {
    $where[OTAModel::$name . '.ota_channel_id'] = $channel;
  }

  if (!empty($versionName)) {
    $where['version_name[~]'] = $versionName;
  }

  if (!empty($versionCode)) {
    $where['version_code'] = $versionCode;
  }

  $where['status'] = '1';
  if (!empty($where)) {
    $where = [
      'AND' => $where
    ];
  }

  $db = new RDB();

  $count = $db->count(OTAModel::$name,
    [
      '[>]' . OTAPlatformModel::$name       => ['ota_platform_id' => 'ota_platform_id'],
      '[>]' . OTAChannelModel::$name        => ['ota_channel_id' => 'ota_channel_id'],
      '[>]' . OTAIdentificationModel::$name => ['ota_identification_id' => 'ota_identification_id']
    ],

    '*',

    $where
  );

  $page = new Page($cp, $count);
  $where['LIMIT'] = $page->getLimit();

  $where['ORDER'] = ['ota_id DESC'];
  $otas = $db->select(OTAModel::$name,
    [
      '[>]' . OTAPlatformModel::$name       => ['ota_platform_id' => 'ota_platform_id'],
      '[>]' . OTAChannelModel::$name        => ['ota_channel_id' => 'ota_channel_id'],
      '[>]' . OTAIdentificationModel::$name => ['ota_identification_id' => 'ota_identification_id']
    ],

    [
      OTAModel::$name . '.*',
      OTAPlatformModel::$name . '.name(platform_name)',
      OTAChannelModel::$name . '.name(channel_name)',
      OTAIdentificationModel::$name . '.name(identification_name)'
    ],

    $where
  );

  $platforms = $db->select(OTAPlatformModel::$name, '*');
  $channels = $db->select(OTAChannelModel::$name, '*');
  $identifications = $db->select(OTAIdentificationModel::$name, '*');
  
  $this->view->render($response, 'web/admin/ota/index.php', [
    'platforms'       => $platforms,
    'channels'        => $channels,
    'identifications' => $identifications,    
    'otas'            => $otas,
    'page'            => $page
  ]);
});

/**
 * 升级包详情页
 */
$app->get('/web/admin/ota/detail/{ota_id}', function ($request, $response, $args) {

  $db = new RDB();

  $where = [
  'ota_id' => $args['ota_id']
  ];

  $ota = OTAService::getOTA($db, $where);

  $this->view->render($response, 'web/admin/ota/detail.php', [
    'ota' => $ota
  ]);
});

/**
 * 添加升级包页面
 */
$app->get('/web/admin/ota/add', function ($request, $response, $args) {

  $db = new RDB();

  $platforms = $db->select(OTAPlatformModel::$name, '*');
  $channels = $db->select(OTAChannelModel::$name, '*');
  $identifications = $db->select(OTAIdentificationModel::$name, '*');

  $this->view->render($response, 'web/admin/ota/add.php', [
    'platforms'       => $platforms,
    'channels'        => $channels,
    'identifications' => $identifications
  ]);
});

/**
 * 添加升级包逻辑
 */
$app->post('/web/admin/ota/add', function ($request, $response, $args) {
  $rules = [
    [
      'key'        => 'platform',
      'type'       => 'required',
      'error_code' => 'platform_is_required'
    ],

    [
      'key'        => 'identification',
      'type'       => 'required',
      'error_code' => 'identification_id_is_required'
    ],

    [
      'key'        => 'version_name',
      'type'       => 'required',
      'error_code' => 'version_name_is_required'
    ],

    [
      'key'        => 'version_code',
      'type'       => 'required',
      'error_code' => 'version_code_is_required'
    ],

    [
      'key'        => 'version_code',
      'type'       => 'integer',
      'error_code' => 'version_code_must_be_integer'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $ota = $db->get(OTAModel::$name, '*', [
    'AND' => [
      'version_code'          => $params['version_code'],
      'ota_identification_id' => $params['identification'],
      'ota_platform_id'       => $params['platform'],
      'status'                => '1'
    ]
  ]);

  if (!empty($ota)) {
    echo json_encode(Valid::addErrors($result, 'version_code', 'version_code_exists'));
    return;
  }

  $platform = $db->get(OTAPlatformModel::$name, '*', [
    'ota_platform_id' => $params['platform']
  ]);

  $identification = $db->get(OTAIdentificationModel::$name, '*', [
    'ota_identification_id' => $params['identification']
  ]);

  $packageSize = 0;
  $packageMD5 = '';
  $packageURL = '';

  if (!empty($_FILES['file']['name'])) {
    $path = "{$platform['name']}/{$identification['name']}/{$params['version_code']}";
    $result = File::otaFileUpload('file', $path);

    if (!$result) {
      echo json_encode(Valid::addErrors($result, 'file', 'file_upload_error'));
      return;
    }
    $packageSize = $result['size'];
    $packageMD5 = $result['md5'];
    $packageURL = $result['subPath'];
  }

  $t = time();

  $db->insert(OTAModel::$name, [
    'ota_platform_id'       => $params['platform'],
    'ota_identification_id' => $params['identification'],
    'version_name'          => $params['version_name'],
    'version_code'          => $params['version_code'],
    'is_force'              => $params['is_force'],
    'description'           => $params['description'],
    'ota_channel_id'        => $params['channel'],
    'package_size'          => $packageSize,
    'package_md5'           => $packageMD5,
    'url'                   => $packageURL,
    'create_time'           => $t
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 删除升级包
 */
$app->get('/web/admin/ota/delete/{ota_id}', function ($request, $response, $args) {

  $otaId = $args['ota_id'];

  $db = new RDB();
  $db->beginTransaction();

  $db->update(OTAModel::$name, [
    'status' => '0'
  ],

  [
    'ota_id' => $otaId
  ]);

  $db->commit();
  
  $uri = WEB_URL_ROOT . '/admin/ota';
  return $response = $response->withRedirect($uri, 403);
});

/**
 * 升级包标识管理页面
 */
$app->get('/web/admin/ota/identification', function ($request, $response, $args) {

  $cp = $request->getParam('cp');

  $platformId = $request->getParam('platform');
  $where = [];

  if (!empty($platformId)) {
    $where['ota_platform_id'] = $platformId;
  }

  if (!empty($platformId)) {
    $where = [
      'AND' => $where
    ];
  }

  $db = new RDB();
  

  $count = $db->count(OTAIdentificationModel::$name,
    '*',
    $where
  );

  $page = new Page($cp, $count);

  $where['LIMIT'] = $page->getLimit();

  $identifications = $db->select(OTAIdentificationModel::$name,
    '*',
    $where
  );

  $platforms = $db->select(OTAPlatformModel::$name, '*');

  $this->view->render($response, 'web/admin/ota/identification.php', [
    'platforms'       => $platforms,
    'identifications' => $identifications,
    'page'            => $page
  ]);
});

/**
 * 添加升级包标识逻辑
 */
$app->post('/web/admin/ota/identification/add', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'platform',
      'type'       => 'required',
      'error_code' => 'platform_is_required'
    ],

    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'identification_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $identification = $db->get(OTAIdentificationModel::$name, '*', [
    'AND' => [
      'name'            => $params['name'],
      'ota_platform_id' => $params['platform']
    ]
  ]);

  if (!empty($identification)) {
    echo json_encode(Valid::addErrors($result, 'name', 'identification_exists'));
    return;
  }

  $db->insert(OTAIdentificationModel::$name, [
    'name'            => $params['name'],
    'ota_platform_id' => $params['platform'],
    'description'     => $params['description']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 修改升级包标识页面
 */
$app->get('/web/admin/ota/identification/modify/{identification_id}', function ($request, $response, $args) {

  $identificationId = $args['identification_id'];
  
  $db = new RDB();

  $identification = $db->get(OTAIdentificationModel::$name, '*', [
    'ota_identification_id' => $identificationId
  ]);

  $platforms = $db->select(OTAPlatformModel::$name, '*');

  $this->view->render($response, 'web/admin/ota/identification_modify.php', [
    'platforms'       => $platforms,
    'identification' => $identification
  ]);
});

/**
 * 修改升级包标识逻辑
 */
$app->post('/web/admin/ota/identification/modify', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'identification_id',
      'type'       => 'required',
      'error_code' => 'identification_id_is_required'
    ],

    [
      'key'        => 'platform',
      'type'       => 'required',
      'error_code' => 'platform_is_required'
    ],

    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'identification_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $identification = $db->get(OTAIdentificationModel::$name, '*', [
    'AND' => [
      'name'            => $params['name'],
      'ota_platform_id' => $params['platform']
    ]
  ]);

  if (!empty($identification)) {
    if ($identification['ota_identification_id'] != $params['identification_id']) {
      echo json_encode(Valid::addErrors($result, 'name', 'identification_exists'));
      return;
    }
  }

  $db->update(OTAIdentificationModel::$name, [
    'name'            => $params['name'],
    'ota_platform_id' => $params['platform'],
    'description'     => $params['description']
  ],

  [
    'ota_identification_id' => $params['identification_id']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 升级包渠道管理页面
 */
$app->get('/web/admin/ota/channel', function ($request, $response, $args) {

  $cp = $request->getParam('cp');

  $db = new RDB();
  
  $count = $db->count(OTAChannelModel::$name, '*');

  $page = new Page($cp, $count);

  $where = [];
  $where['LIMIT'] = $page->getLimit();

  $channels = $db->select(OTAChannelModel::$name,
    '*',
    $where
  );

  $this->view->render($response, 'web/admin/ota/channel.php', [
    'channels' => $channels,
    'page'     => $page
  ]);
});

/**
 * 添加升级包渠道逻辑
 */
$app->post('/web/admin/ota/channel/add', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'ota_channel_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $channel = $db->get(OTAChannelModel::$name, '*', [
    'AND' => [
      'name' => $params['name']
    ]
  ]);

  if (!empty($channel)) {
    echo json_encode(Valid::addErrors($result, 'name', 'ota_channel_exists'));
    return;
  }

  $db->insert(OTAChannelModel::$name, [
    'name'        => $params['name'],
    'description' => $params['description']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 修改升级包渠道页面
 */
$app->get('/web/admin/ota/channel/modify/{channel_id}', function ($request, $response, $args) {

  $channelId = $args['channel_id'];
  
  $db = new RDB();

  $channel = $db->get(OTAChannelModel::$name, '*', [
    'ota_channel_id' => $channelId
  ]);

  $this->view->render($response, 'web/admin/ota/channel_modify.php', [
    'channel' => $channel
  ]);
});

/**
 * 修改升级包标识逻辑
 */
$app->post('/web/admin/ota/channel/modify', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'channel_id',
      'type'       => 'required',
      'error_code' => 'ota_channel_id_is_required'
    ],

    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'ota_channel_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $channel = $db->get(OTAChannelModel::$name, '*', [
    'AND' => [
      'name' => $params['name']
    ]
  ]);

  if (!empty($channel)) {
    if ($channel['ota_channel_id'] != $params['channel_id']) {
      echo json_encode(Valid::addErrors($result, 'name', 'ota_channel_exists'));
      return;
    }
  }

  $db->update(OTAChannelModel::$name, [
    'name'            => $params['name'],
    'description'     => $params['description']
  ],

  [
    'ota_channel_id' => $params['channel_id']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 升级包平台管理页面
 */
$app->get('/web/admin/ota/platform', function ($request, $response, $args) {

  $cp = $request->getParam('cp');

  $db = new RDB();
  
  $count = $db->count(OTAPlatformModel::$name, '*');

  $page = new Page($cp, $count);

  $where = [];
  $where['LIMIT'] = $page->getLimit();

  $platforms = $db->select(OTAPlatformModel::$name,
    '*',
    $where
  );

  $this->view->render($response, 'web/admin/ota/platform.php', [
    'platforms' => $platforms,
    'page'      => $page
  ]);
});

/**
 * 添加升级包平台逻辑
 */
$app->post('/web/admin/ota/platform/add', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'ota_platform_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $platform = $db->get(OTAPlatFormModel::$name, '*', [
    'AND' => [
      'name' => $params['name']
    ]
  ]);

  if (!empty($platform)) {
    echo json_encode(Valid::addErrors($result, 'name', 'ota_platform_exists'));
    return;
  }

  $db->insert(OTAPlatFormModel::$name, [
    'name'        => $params['name'],
    'description' => $params['description']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});

/**
 * 修改升级包平台页面
 */
$app->get('/web/admin/ota/platform/modify/{platform_id}', function ($request, $response, $args) {

  $platformId = $args['platform_id'];
  
  $db = new RDB();

  $platform = $db->get(OTAPlatFormModel::$name, '*', [
    'ota_platform_id' => $platformId
  ]);

  $this->view->render($response, 'web/admin/ota/platform_modify.php', [
    'platform' => $platform
  ]);
});

/**
 * 修改升级包平台逻辑
 */
$app->post('/web/admin/ota/platform/modify', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'platform_id',
      'type'       => 'required',
      'error_code' => 'ota_platform_id_is_required'
    ],

    [
      'key'        => 'name',
      'type'       => 'required',
      'error_code' => 'ota_platform_name_is_required'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $db = new RDB();
  $db->beginTransaction();
  
  $platform = $db->get(OTAPlatFormModel::$name, '*', [
    'AND' => [
      'name' => $params['name']
    ]
  ]);

  if (!empty($platform)) {
    if ($platform['ota_platform_id'] != $params['platform_id']) {
      echo json_encode(Valid::addErrors($result, 'name', 'ota_platform_exists'));
      return;
    }
  }

  $db->update(OTAPlatFormModel::$name, [
    'name'            => $params['name'],
    'description'     => $params['description']
  ],

  [
    'ota_platform_id' => $params['platform_id']
  ]);

  $db->commit();

  return json_encode([
    'code' => 0,
    'data' => [
      'reload'       => true,
      'delaySuccess' => true
    ]
  ]);
});