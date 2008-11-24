<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入配置支持 */
require_once 'config.inc.php';

/** 定义路由参数 */
Typecho_Router::setRoutes(Typecho_Widget::widget('Widget_Options')->routingTable);

/** 初始化插件 */
Typecho_Plugin::init(Typecho_Widget::widget('Widget_Options')->plugins,
array(Typecho_Widget::widget('Widget_Options'), 'getPluginOption'));

/** 注册一个初始化插件 */
Typecho_Plugin::factory('index.php')->begin();

/** 开始路由分发 */
Typecho_Router::dispatch();

/** 注册一个结束插件 */
Typecho_Plugin::factory('index.php')->end();
