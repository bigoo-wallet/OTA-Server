<?php
/**
 * Common Log Module.
 * HOWTO:
 * Step 1: Define the logger settings
 * LOG_FILE_PATH = '/tmp/growler-server.log'
 * LOG_BACKUP_COUNT = 10
 * # DEBUG     100
 * # INFO      200
 * # NOTICE    250
 * # WARNING   300
 * # ERROR     400
 * # CRITICAL  500
 * # ALERT     550
 * # EMERGENCY 600
 * LOG_LEVEL = 100
 * 
 * Step 2: Coding
 * use Growler\Libs;
 * Libs\SimpleLogger::info('your message');
 *
 * @author tytymnty@163.com
 * @since 2015-12-31 16:29:16
 */

namespace Growler\Libs;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class SimpleLogger
{
  private static $logger = null;

  public static function getLogger()
  {
    if (self::$logger == null) {
      self::$logger = new Logger('Growler');

      self::$logger->pushHandler(new RotatingFileHandler(
        $_ENV['LOG_FILE_PATH'], $_ENV['LOG_BACKUP_COUNT'], $_ENV['LOG_LEVEL'])
      );
    }

    return self::$logger;
  }

  public static function debug($msg)
  {
    self::getLogger()->debug($msg);
  }

  public static function info($msg)
  {
    self::getLogger()->info($msg);
  }

  public static function notice($msg)
  {
    self::getLogger()->notice($msg);
  }

  public static function warning($msg)
  {
    self::getLogger()->warning($msg);
  }

  public static function error($msg)
  {
    self::getLogger()->error($msg);
  }

  public static function crittcal($msg)
  {
    self::getLogger()->crittcal($msg);
  }

  public static function emergency($msg)
  {
    self::getLogger()->emergency($msg);
  }
}