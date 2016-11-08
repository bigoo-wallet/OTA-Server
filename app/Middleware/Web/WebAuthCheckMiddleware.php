<?php
/**
 * 查询司机列表页面
 * @author tytymnty@gmail.com
 * @since 2016-08-04 15:17:00
 */

namespace Growler\Middleware\Web;

use Growler\Libs\Session;

/**
 * 封装通用的页面数据
 */
class WebAuthCheckMiddleware
{
  public function __invoke($request, $response, $next)
  {
    // 当前登录用户
    $user = Session::get($_ENV['USER_SESSION_KEY']);

    if (empty($user)) {
      $uri = WEB_URL_ROOT . '/auth/signin';

      return $response = $response->withRedirect($uri, 403);
    }

    $response = $next($request, $response);
    return $response;
  }
}