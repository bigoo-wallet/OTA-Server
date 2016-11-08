<?php
/**
 * 参数验证
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler\Libs;

use I18N\Lang;
use Valitron\Validator;

class Valid
{
  /**
   * parameters validate
   *
   * @param array $params
   * @param array $rules inspection rules
   */
  public static function validate($params, $rules)
  {
    if (empty($rules)) {
      return array(
        'code' => 0,
        'data' => array()
      );
    }
    
    $v = new Validator($params);
    
    foreach ($rules as $key => $rule) {
      if (empty($rule['key'])) {
        throw new \InvalidArgumentException('each rule must be have the key attribute');
      }
      
      if (empty($rule['type'])) {
        throw new \InvalidArgumentException('each rule must be have the type attribute');
      }
      
      $errorMsg = isset($rule['error_code']) ? Lang::getWord($rule['error_code']) : '';
      
      $value = isset($rule['value']) ? $rule['value'] : null;
      
      $msg = [
        'err_no'  => $rule['error_code'],
        'err_msg' => $errorMsg
      ];

      $v->rule($rule['type'], $rule['key'], $value)->message(json_encode($msg));
    }
    
    if ($v->validate()) {
      return array(
        'code' => 0,
        'data' => $v->data()
      );
    }
    
    $errors = $v->errors();
    foreach ($errors as $key => $err) {
      foreach ($err as $i => $emsg) {
        $emsg = json_decode($emsg, 1);
        $errors[$key][$i] = $emsg;
      }
    }

    return [
      'code' => 1,
      'data' => [
        'errors' => $errors
      ]
    ];
  }

  /**
   * API验证
   *
   * @param array $params
   * @param array $rules inspection rules
   */
  public static function apiValidate($params, $rules)
  {
    $resp = self::validate($params, $rules);

    // has some error
    if ($resp['code'] === 1 && !empty($resp['data']['errors'])) {
      $keys = array_keys($resp['data']['errors']);

      return [
        'code' => 1,
        'data'  => $resp['data']['errors'][$keys[0]][0]
      ];
    }

    return $resp;
  }

  /**
   * 添加错误信息
   *
   * @param array $result 原有错误信息
   * @param array $rules inspection rules
   */ 
  public static function addErrors($result, $key, $errorCode) 
  {
    if (empty($result)) {
      $result = [
        'code' => 1,
        'data' => []
      ];
    } else if ($result['code'] === 0) {
      $result = [
        'code' => 1,
        'data' => []
      ];
    }
    if (empty($result['errors'])) {
      $result['data']['errors'] = [];
    }
    if (empty($result['errors'][$key])) {
      $result['data']['errors'][$key] = [];
    }

    array_push($result['data']['errors'][$key], [
      'err_no'  => $errorCode,
      'err_msg' => Lang::getWord($errorCode)
    ]);

    return $result;
  }

  /**
   * 返回错误信息的HTML文档
   */
  public static function errorHTMLPage($errors) 
  { 
    $errorMSG = '';
    
    foreach ($errors['data']['errors'] as $k => $err) {
      $errorMSG .= '<p>' . $err[0]['err_msg'] . '</p>';
    }
    $html = <<<EOS
    <!DOCTYPE html>
      <html id="finance">
        <head>
          <meta charset="utf-8">
        </head>
        <body>
          {$errorMSG}
        </body>
      </html>
EOS;
    return $html;
  }
}