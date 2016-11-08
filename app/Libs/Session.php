<?php
/**
 * Session
 * SESSION_MECHANISM: native内置session机制 / cookie使用cookie代替
 *
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler\Libs;

/**
 * Check Session status
 * @return bool
 */
function sessionStart()
{
  $isSessionStarted = false;
  if (php_sapi_name() !== 'cli') {
    if (version_compare(phpversion(), '5.4.0', '>=')) {
      $isSessionStarted === PHP_SESSION_ACTIVE ? TRUE : FALSE;
    }
  }

  if ($isSessionStarted === FALSE) {
    session_start();
  }
}

sessionStart();

class Session
{
  static $app = null;

  const RAND_STR_LENGTH = 8;

  private static function nativeSessionSet($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  private static function nativeSessionGet($key)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
  }

  private static function nativeSessionDelete($key)
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }

  private static function cookieSet($key, $value)
  {
    if (is_array($value)) {
      $value = json_encode($value);
    }
    $value = RC4::encrypt($_ENV['COOKIE_SECRET_KEY'], $value . Util::getRandStr(Session::RAND_STR_LENGTH));

    self::$app->setCookie($key, $value, 0, '/', $_ENV['COOKIE_DOMAIN']);
  }

  private static function cookieGet($key)
  {
    $value = self::$app->getCookie($key);
    $value = RC4::decrypt($_ENV['COOKIE_SECRET_KEY'], $value);
    $value = substr($value, 0, (strlen($value) - Session::RAND_STR_LENGTH));

    $result = json_decode($value, 1);

    if (!$result) {
      return $value;
    }

    return $result;
  }

  private static function cookieDelete($key)
  {
    self::$app->deleteCookie($key, '/', $_ENV['COOKIE_DOMAIN']);
  }

  public static function setApplication($app)
  {
    self::$app = $app;
  }

  /**
   * Set data
   * @param string $key
   * @param string | array $value
   * @return string
   */
  public static function set($key, $value)
  {
    switch ($_ENV['SESSION_MECHANISM']) {
      case 'native':
        self::nativeSessionSet($key, $value);
        break;

      case 'cookie':
        self::cookieSet($key, $value);
        break;

      default:
        break;
    }
  }

  /**
   * Get date
   * @param string $key
   */
  public static function get($key)
  {
    switch ($_ENV['SESSION_MECHANISM']) {
      case 'native':
        return self::nativeSessionGet($key);

      case 'cookie':
        return self::cookieGet($key);
        break;
      
      default:
        return null;
    }
  }

  /**
   * Delete session object
   * @param string $key
   */
  public static function delete($key)
  {
    switch ($_ENV['SESSION_MECHANISM']) {
      case 'native':
        return self::nativeSessionDelete($key);

      case 'cookie':
        return self::cookieDelete($key);
        break;

      default:
        return null;
    }
  }
}