<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义根目录 */
define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));

/** 定义库目录 */
define('__TYPECHO_LIB_DIR__', __TYPECHO_ROOT_DIR__ . '/includes');

/** 定义组件路径 */
define('__TYPECHO_WIDGET_DIR__', __TYPECHO_ROOT_DIR__ . '/widget');

/** 定义后台目录 */
define('__TYPECHO_ADMIN_DIR__', __TYPECHO_ROOT_DIR__ . '/admin');

/** 定义插件目录 */
define('__TYPECHO_PLUGIN_DIR__', __TYPECHO_ROOT_DIR__ . '/var/plugins');

/** 定义异常截获页面地址 */
define('__TYPECHO_EXCEPTION_FILE__', __TYPECHO_ROOT_DIR__ . '/admin/error.php');

/** 定义调试开关 **/
define('__TYPECHO_DEBUG__', true);

/** 定义mo文件 **/
define('__TYPECHO_I18N_LANGUAGE__', false);

/** 定义网页输出编码 **/
define('__TYPECHO_CHARSET__', 'UTF-8');

/** 定义gzip支持 **/
define('__TYPECHO_GZIP_ENABLE__', false);

/** 载入API支持 */
require_once 'includes/Typecho.php';

/** 载入配置支持 */
require_once 'includes/Config.php';

/** 载入国际化支持 */
require_once 'includes/I18n.php';

/** 载入组件支持 */
require_once 'includes/Widget.php';

/** 载入数据库支持 */
require_once 'includes/Db.php';

/** 载入路由支持 */
require_once 'includes/Route.php';

/** 载入请求处理支持 */
require_once 'includes/Request.php';

/** 载入插件支持 */
require_once 'includes/Plugin.php';

/** 定义数据库参数 */
TypechoConfig::set('Db', array(
    'host'          =>  'localhost',
    'port'          =>  '3306',
    'user'          =>  'root',
    'password'      =>  '',
    'database'      =>  'typecho',
    'prefix'        =>  'typecho_',
    'charset'       =>  'utf8',
    'adapter'       =>  'Mysql'
));

/** 定义路由参数 */
TypechoConfig::set('Route', array(
    'index'         =>  array('[/]?', 'index.php', NULL, '/', array('Archive')),
    'category'      =>  array('/category/([_0-9a-zA-Z-]+)[/]?', 'archive.php', array('slug'), '/category/%s/', array('Archive')),
    'tag'           =>  array('/tag/([^/]+)[/]?', 'archive.php', array('slug'), '/tag/%s/', array('Archive')),
    'index_page'    =>  array('/page/([0-9]+)[/]?', 'index.php', array('page'), '/page/%s/', array('Archive')),
    'category_page' =>  array('/category/([_0-9a-zA-Z-]+)/([0-9]+)[/]?', 'archive.php', array('slug', 'page'), '/category/%s/%s/', array('Archive')),
    'tag_page'      =>  array('/tag/([^/]+)/([0-9]+)[/]?', 'archive.php', array('slug', 'page'), '/tag/%s/%s/', array('Archive')),
    'post'          =>  array('/archives/([0-9]+)[/]?', 'post.php', array('cid'), '/archives/%s/', array('Archive')),
    'feed'          =>  array('/feed(.*)', array('Feed'), array('feed'), '/feed%s'),
    'do'            =>  array('/([_0-9a-zA-Z-]+)\.do', array('Do'), array('do'), '/%s.do'),
    'do_plugin'     =>  array('/([_0-9a-zA-Z-]+)/([_0-9a-zA-Z-]+)\.do', array('Do'), array('plugin', 'do'), '/%s/%s.do'),
    'job'           =>  array('/([_0-9a-zA-Z-]+)\.job', array('Job'), array('job'), '/%s.job'),
    'login'         =>  array('/login[/]?', 'admin/login.php'),
    'admin'         =>  array('/admin[/]?', 'admin/index.php'),
    'page'          =>  array('/([_0-9a-zA-Z-]+)[/]?', 'page.php', array('slug'), '/%s/', array('Archive')),
));
