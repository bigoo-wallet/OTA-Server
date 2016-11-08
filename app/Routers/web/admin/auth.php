<?php
/**
 * 平台登录入口
 * @author tytymnty@gmail.com
 * @since 2016-08-03 11:14:07
 */

namespace Growler\Routers\Web\Admin;

use Growler\Libs\SMS;
use Growler\Libs\Session;
use Growler\Libs\Valid;
use Growler\Libs\RDB;
use Growler\Models\AdminModel;

/**
 * 用户登录页面
 */
$app->get('/web/admin/auth', function ($request, $response, $args) {

  $this->view->render($response, 'web/admin/auth/signin.php', $args);
  
});

/**
 * 登录认证逻辑
 */
$app->post('/web/admin/auth/signin', function ($request, $response, $args) {

  $rules = [
    [
      'key'        => 'username',
      'type'       => 'required',
      'error_code' => 'username_is_required'
    ],

    [
      'key'        => 'pwd',
      'type'       => 'required',
      'error_code' => 'pwd_is_required'
    ],
    [
      'key'        => 'pwd',
      'type'       => 'lengthMin',
      'value'      => 6,
      'error_code' => 'pwd_must_be_more_then_6_characters'
    ],
    [
      'key'        => 'pwd',
      'type'       => 'lengthMax',
      'value'      => 16,
      'error_code' => 'pwd_is_too_long'
    ]
  ];

  $params = $request->getParams();
  $result = Valid::validate($params, $rules);

  if ($result['code'] == 1) {
    echo json_encode($result);
    return;
  }

  $username = $request->getParam('username');
  $pwd = $request->getParam('pwd');

  $db = new RDB();

  $user = $db->get(AdminModel::$name,
    '*',

    [
      'username' => $username
    ]
  );

  if (empty($user)) {
    echo json_encode(Valid::addErrors($result, 'pwd', 'pwd_error'));
    return;
  }

  if ($user['pwd'] != md5($pwd)) {
    echo json_encode(Valid::addErrors($result, 'pwd', 'pwd_error'));
    return;
  }

  unset($user['pwd']);

  Session::set($_ENV['USER_SESSION_KEY'], $user);

  return json_encode([
    'code' => 0,
    'data' => [
      'href' => WEB_URL_ROOT . '/admin/ota'
    ]
  ]);

});

/**
 * 退出登录
 */
$app->get('/web/admin/auth/signout', function ($request, $response, $args) {
  Session::delete($_ENV['USER_SESSION_KEY']);
  Session::delete($_ENV['USER_FORGET_SESSION_KEY']);

  return $response->withRedirect('/web/admin/auth');
});