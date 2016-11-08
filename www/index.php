<?php
/**
 * 程序入口
 * 负责加载必要的环境
 * @author tytymnty@gmail.com
 * @since 2016-04-01 16:56:44
 */

namespace Growler;

date_default_timezone_set('PRC');
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
define('APP_ROOT', PROJECT_ROOT . '/app');
define('TEMP_ROOT', APP_ROOT . '/Templates/');
define('LANG_ROOT', PROJECT_ROOT . '/lang');

// require composer autoload
require_once PROJECT_ROOT . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\App;
use Slim\Views\PhpRenderer;
use I18N\Lang;

$dotenv = new Dotenv(PROJECT_ROOT);
$dotenv->load();

// Language util init
Lang::init($_ENV['ACCEPT_LANGUAGES'], $_ENV['DEFAULT_LANGUAGE'], LANG_ROOT);
Lang::setLang(Lang::getHTTPAcceptLangs());

$app = new App();

$container = $app->getContainer();
$container['view'] = new PhpRenderer(TEMP_ROOT);

// require controller
require_once PROJECT_ROOT . '/app/controller.php';

// // app run!
$app->run();