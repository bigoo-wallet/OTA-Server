<?php
/**
 * 数据库连接抽象
 * 底层使用 medoo 实现。数据库连接对象只在发生真实请求时才创建。
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler\Libs;
use Growler\Libs\SimpleLogger;

class RDB
{
  private static $config = null;
  private static $database = null;

  public function __construct()
  {
    if (empty(self::$config)) {
      // 数据库配置
      self::$config = [
        'database_type' => $_ENV['DB_TYPE'],
        'database_name' => $_ENV['DB_NAME'],
        'server'        => $_ENV['DB_HOST'],
        'username'      => $_ENV['DB_USERNAME'],
        'password'      => $_ENV['DB_PASSWORD'],
        'charset'       => $_ENV['DB_CHARSET'],
        'prefix'        => $_ENV['DB_PREFIX'],
        'logger'        => SimpleLogger::getLogger(),
        'debug_mode'   => intval($_ENV['DB_DEBUG_MODE']) ? true : false
      ];
    }
  } 

  private static function dbinit() 
  {
    self::$database = new SuperMedoo(self::$config);
  }

  public function __call($method, $arguments) 
  {
    if (empty(self::$database)) {
      self::dbinit();
    }
    
    if (!method_exists(self::$database, $method)) {
      throw new \Exception('method not found');
    }

    return call_user_func_array(array(self::$database, $method), $arguments);
  }
}