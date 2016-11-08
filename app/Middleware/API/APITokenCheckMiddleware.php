<?php
/**
 * 判断api token是否正确
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler\Middleware\API;

use Growler\Libs\APIResponse;
use Growler\Libs\RDB;
use Growler\Models\UserModel;

class APITokenCheckMiddleware
{
  public function __invoke($request, $response, $next)
  {
    $userId = $request->getParam('user_id');
    $token = $request->getParam('token');

    if (empty($userId) || empty($token)) {
      $resp = APIResponse::buildError($request, 'user_token_error');
      $response->getBody()->write($resp);

    } else {
      $db = new RDB();
      $user = $db->get(UserModel::$name, [
        '*'
      ],

      [
        'and' => [
          'user_id' => $userId,
          'token'   => $token
        ]
      ]);

      if (empty($user)) {
        $resp = APIResponse::buildError($request, 'user_token_error');
        $response->getBody()->write($resp);

      } else {
        $request = $request->withAttribute('user', $user);
        $response = $next($request, $response);
      }
    }
    return $response;
  }
}