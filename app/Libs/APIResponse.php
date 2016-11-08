<?php
/**
 * API 返回值处理
 * @author tytymnty@gmail.com
 * @since 2016年05月19日11:07:48
 */

namespace Growler\Libs;
use I18N\Lang;

class APIResponse
{
  /**
   * 打包返回值
   * @param object $request
   * @param int $state
   * @param array $data
   * @return string json string
   */
  public static function build($request, $state, $data = null)
  {
    $path = $request->getUri()->getPath();
    $method = preg_replace('/\/api\//', '', $path);

    if (empty($data)) {
      $data = new \stdClass();
    }
    $resp = [
      'code'  => $state,
      'method' => $method,
      'data'   => $data
    ];

    return json_encode($resp, JSON_UNESCAPED_UNICODE);
  }

  /**
   * 打包错误返回值
   * @param object $request
   * @param string $errorCode
   * @return string json string
   */
  public static function buildError($request, $errorCode)
  {
    $data = [
      'err_no'  => $errorCode,
      'err_msg' => Lang::getWord($errorCode)
    ];
    return self::build($request, 1, $data);
  }
}

