<?php
/**
 * 后台管理系统
 * @author tytymnty@gmail.com
 * @since 2016-08-03 10:49:11
 */

namespace Growler\Routers\Web;

use Growler\Libs\Session;

define('SYSTEM_TYPE', 'admin');

$user = Session::get($_ENV['USER_SESSION_KEY']);

if (empty($user) && $urlItems[3] != 'auth') {
  $error = [
    'type' => 'auth_error',
    'url'  => WEB_URL_ROOT . '/' . SYSTEM_TYPE . '/auth'
  ];
  throw new \Exception(json_encode($error));
} 

$file = __DIR__ . '/admin/' . $urlItems[3] . '.php';
if (file_exists($file)) {
  require_once $file;
}
