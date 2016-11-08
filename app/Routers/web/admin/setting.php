<?php
/**
 * 设置管理页面
 * @author tytymnty@gmail.com
 * @since 2016-09-28 17:30:36
 */

namespace Growler\Routers\Web\Admin;

use Growler\Libs\Valid;
use Growler\Libs\RDB;
use Growler\Libs\Session;
use Growler\Models\AdminModel;

/**
 * 修改密码
 */
$app->get('/web/admin/setting/rpwd', function ($request, $response, $args) {
  
  $this->view->render($response, 'web/admin/setting/rpwd.php');
});


/**
 * 修改密码
 */
$app->post('/web/admin/setting/rpwd', function ($request, $response, $args) {
  
  $rules = [
    [
      'key'        => 'opwd',
      'type'       => 'required',
      'error_code' => 'opwd_is_required'
    ],
    [
      'key'        => 'opwd',
      'type'       => 'lengthMin',
      'value'      => 6,
      'error_code' => 'opwd_must_be_more_then_6_characters'
    ],
    [
      'key'        => 'opwd',
      'type'       => 'lengthMax',
      'value'      => 16,
      'error_code' => 'opwd_is_too_long'
    ],

    [
      'key'        => 'npwd',
      'type'       => 'required',
      'error_code' => 'npwd_is_required'
    ],
    [
      'key'        => 'npwd',
      'type'       => 'lengthMin',
      'value'      => 6,
      'error_code' => 'npwd_must_be_more_then_6_characters'
    ],
    [
      'key'        => 'npwd',
      'type'       => 'lengthMax',
      'value'      => 16,
      'error_code' => 'npwd_is_too_long'
    ],


    [
      'key'        => 'rpwd',
      'type'       => 'required',
      'error_code' => 'rpwd_is_required'
    ],

    [
      'key'        => 'rpwd',
      'type'       => 'equals',
      'value'      => 'npwd',
      'error_code' => 'rpwd_error'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $admin = Session::get($_ENV['USER_SESSION_KEY']);
  $db = new RDB();
  $db->beginTransaction();
  
  $tmp = $db->get(AdminModel::$name, '*', [
    'admin_id' => $admin['admin_id']
  ]); 

  if ($tmp['pwd'] != md5($params['opwd'])) {
    echo json_encode(Valid::addErrors($result, 'opwd', 'opwd_error'));
    return;
  }

  $db->update(AdminModel::$name, [
    'pwd' => md5($params['npwd'])
  ],

  [
    'admin_id' => $admin['admin_id']
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