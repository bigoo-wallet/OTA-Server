<?php
/**
 * Cookie
 * COOKIE_SECURITY_KEY = "newstart20150601"
 * COOKIE_DOMAIN = ".domain.com"
 *
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler\Libs;

class Cookie
{
  private static $instance;

  public static function setMockInstance($mock_instance) {
    self::$instance = $mock_instance;
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Cookie();
    }
    return self::$instance;
  }

  private function getSecurityKey($key)
  {
    return "{$_ENV['COOKIE_SECURITY_KEY']}-{$key}";
  }

  /**
  * Set cookie
  * @param string $key
  * @param string $value
  * @param int $timeout
  * @return string
  */
  public function set($key, $value, $timeout)
  {
    $securityKey = $this->getSecurityKey($key);
    $cookieContent = RC4::encrypt($securityKey, $value);
    setcookie($key, $cookieContent, $timeout, '/', $_ENV['COOKIE_DOMAIN']);
  }

  /**
  * Get cookie
  * @param string $key
  */
  public function get($key)
  {
    $securityKey = $this->getSecurityKey($key);
    return isset($_COOKIE[$key]) ? RC4::decrypt($securityKey, $_COOKIE[$key]) : '';
  }
}

