<?php
/**
 * 程序总控制器
 * 负责调用Router
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler;

use Growler\Libs\Session;
use Growler\Libs\RDB;
use Growler\Libs\Util;
use Growler\Libs\SimpleLogger;

$app->add(function ($request, $response, $next) use ($app) {

  $path = $request->getUri()->getPath();
  $urlItems = explode('/', $path);

  $startTime = Util::microtime_float();
  SimpleLogger::debug("== path: $path START ==");

  try {
    if (!empty($urlItems[1])) {
        $filename = $urlItems[1];
    } else {
      throw new \Exception('Please set router name');
    }

    $method = $urlItems[count($urlItems) -1];

    $this->view->addAttribute('request', $request);
    $this->view->addAttribute('currRouter', $filename);
    $this->view->addAttribute('currMethod', $method);
    
    $params = $request->getParams();
    $paramsStr =json_encode($params);
    SimpleLogger::debug("route: {$filename}");
    SimpleLogger::debug("params: {$paramsStr}");

    $routerPath = __DIR__ . "/Routers/{$filename}.php";

    if (!file_exists($routerPath)) {
      throw new \Exception('router_error: route not found');
    }
    require $routerPath;

    $response = $next($request, $response);

    $body = $response->getBody();
    // 只输出JSON数据
    if (preg_match('/\{([\s\S].*)\}/', $body)) {
      SimpleLogger::debug("body: $body");
    }

  } catch (\Exception $e) {

    $errMsg = $e->getMessage();
    SimpleLogger::debug("request error: $errMsg");

    $error = json_decode($errMsg, 1);
    if (!empty($error)) {
      if ($error['type'] == 'auth_error') {
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
          return $response->withJson(['state' => '302', 'location' => $error['url']]);
        }else {
          return $response->withRedirect($error['url']);
        }

      } else {
        $response->getBody()->write($errMsg);  
      }

    } else {
      $response->getBody()->write($errMsg);
    }
  }

  $endTime = Util::microtime_float();
  $t = $endTime - $startTime;
  SimpleLogger::debug("== path: $path END ({$t}) ==");
  return $response;
});