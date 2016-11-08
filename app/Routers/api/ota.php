<?php
/**
 * 位置相关API
 * @author tytymnty@gmail.com
 * @since 2016-07-07 18:45:39
 */

namespace Growler\Routers\API;

use Growler\Libs\Valid;
use Growler\Libs\RDB;
use Growler\Libs\APIResponse;

use Growler\Models\OTAModel;
use Growler\Models\OTAPlatformModel;
use Growler\Models\OTAChannelModel;
use Growler\Models\OTAIdentificationModel;

use Growler\Services\OTAService;

/**
 * 请求更新 
 */
$app->post('/api/ota/update', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'platform',
      'type'       => 'required',
      'error_code' => 'platform_is_required'
    ],

    [
      'key'        => 'identification',
      'type'       => 'required',
      'error_code' => 'identification_is_required'
    ],

    [
      'key'        => 'version_code',
      'type'       => 'required',
      'error_code' => 'version_code_is_required'
    ],

    [
      'key'        => 'version_code',
      'type'       => 'numeric',
      'error_code' => 'version_code_must_be_integer'
    ],

  ];

  $params = $request->getParams();

  $result = Valid::apiValidate($params, $rules);

  if ($result['code'] == 1) {
    return APIResponse::build($request, 1, $result['data']);
  }

  $db = new RDB();

  $where = [
    'AND' => [
      OTAPlatformModel::$name . '.name'       => $params['platform'],
      OTAIdentificationModel::$name . '.name' => $params['identification'],
      'version_code[>]'                       => $params['version_code'],
      OTAModel::$name . '.status'             => '1'
    ]
  ];

  if (!empty($params['version_name'])) {
    $where['AND']['version_name'] = $params['version_name'];
  }

  if (!empty($params['channel'])) {
    $where['AND'][OTAChannelModel::$name . '.name'] = $params['channel']; 
  }
  $where['ORDER'] = ['ota_id DESC'];
  $ota = OTAService::getOTA($db, $where);

  $data['ota'] = new \stdClass();
  if (!empty($ota)) {
    $data['ota'] = [];
    $data['ota'] = [
      'platform'       => $ota['platform_name'],
      'identification' => $ota['identification_name'],
      'channel'        => $ota['channel_name'],
      'update_log'     => $ota['description'],
      'version_name'   => $ota['version_name'],
      'version_code'   => intval($ota['version_code']),
      'is_force'       => $ota['is_force'] == '1' ? true : false,
      'size'           => intval($ota['package_size']),
      'url'            => empty($ota['url']) ? '' : $_ENV['OTA_FILE_ACCESS_URL'] . '/' . $ota['url'],
      'md5'            => $ota['package_md5']
    ];
  }

  return APIResponse::build($request, 0, $data);

});