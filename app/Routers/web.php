<?php
/**
 * 管理平台相关逻辑
 * @author tytymnty@gmail.com
 * @since 2016-08-03 10:49:16
 */

namespace Growler\Routers\Web;

use Growler\Libs\Session;

define('WEB_URL_ROOT', '/web');

$user = Session::get($_ENV['USER_SESSION_KEY']);
if (!empty($user)) {
  $this->view->addAttribute('userSession', $user);
}

if (!empty($urlItems[2])) {
  $file = __DIR__ . '/web/' . $urlItems[2] . '.php';
  if (file_exists($file)) {
    require_once $file;
  }
}