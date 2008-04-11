<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义当前目录 */
if(!defined('__DIR__'))
{
    define('__DIR__', dirname(__FILE__));
}

/** 定义根目录 */
define('__TYPECHO_ROOT_DIR__', __DIR__ . '/..');

/** 定义库目录 */
define('__TYPECHO_LIB_DIR__', __TYPECHO_ROOT_DIR__ . '/includes/library');

/** 定义后台目录 */
define('__TYPECHO_ADMIN_DIR__', __TYPECHO_ROOT_DIR__ . '/admin');

/** 定义插件目录 */
define('__TYPECHO_PLUGIN_DIR__', __TYPECHO_ROOT_DIR__ . '/var/plugins');

/** 定义异常截获页面地址 */
define('__TYPECHO_EXCEPTION_DIR__', __TYPECHO_ROOT_DIR__ . '/var/error');

/** 载入函数库支持 */
require 'functions.php';

/** 载入异常支持 */
require 'library/Exception.php';

/** 载入国际化支持 */
require 'library/I18n.php';

/** 载入组件支持 */
require 'library/Widget.php';

/** 载入数据库支持 */
require 'library/Db.php';

/** 载入路由支持 */
require 'library/Route.php';

/** 载入请求处理支持 */
require 'library/Request.php';

//初始化会话
session_start();

//关闭魔术引号功能
if(get_magic_quotes_gpc())
{
    $_GET = typechoStripslashesDeep($_GET);
    $_POST = typechoStripslashesDeep($_POST);
    $_COOKIE = typechoStripslashesDeep($_COOKIE);

    reset($_GET);
    reset($_POST);
    reset($_COOKIE);
}

//开始监视输出区
if(__TYPECHO_GZIP_ENABLE__ 
   && empty($_SERVER['HTTP_ACCEPT_ENCODING']) 
   && false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
{
    ob_start("ob_gzhandler");
}
else
{
    ob_start();
}

//设置默认时区
if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
{
    @date_default_timezone_set('UTC');
}
