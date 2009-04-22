<?php
if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

/** 载入配置文件 */
if (!@include_once __DIR__ . '/../config.inc.php') {
    file_exists(__DIR__ . '/../install.php') ? header('Location: ../install.php') : print('Missing Config File');
    exit;
}

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/common.php')->begin();

Typecho_Widget::widget('Widget_User')->to($user);
Typecho_Widget::widget('Widget_Notice')->to($notice);
Typecho_Widget::widget('Widget_Menu')->to($menu);

/** 检测是否是第一次登录 */
$currentMenu = $menu->getCurrentMenu();
list($soft, $currentVersion) = explode(' ', $options->generator);
list($prefixVersion, $suffixVersion) = explode('/', $currentVersion);

if (!$user->logged && !Typecho_Request::getCookie('__typecho_first_run') && !empty($currentMenu)) {
    
    if ('/admin/welcome.php' != $currentMenu[2]) {
        Typecho_Response::redirect(Typecho_Common::url('welcome.php', $options->adminUrl));
    } else {
        Typecho_Response::setCookie('__typecho_first_run', 1);
    }
    
} else {

    /** 检测版本是否升级 */
    if ($user->pass('administrator', true) && !empty($currentMenu)) {
        $mustUpgrade = (!defined('Typecho_Common::VERSION') || version_compare(str_replace('/', '.', Typecho_Common::VERSION),
        str_replace('/', '.', $currentVersion), '>'));

        if ($mustUpgrade && '/admin/upgrade.php' != $currentMenu[2]) {
            Typecho_Response::redirect(Typecho_Common::url('upgrade.php', $options->adminUrl));
        } else if (!$mustUpgrade && '/admin/upgrade.php' == $currentMenu[2]) {
            Typecho_Response::redirect(Typecho_Common::url('index.php', $options->adminUrl));
        } else if (!$mustUpgrade && '/admin/welcome.php' == $currentMenu[2] && $user->logged) {
            Typecho_Response::redirect(Typecho_Common::url('index.php', $options->adminUrl));
        }
    }

}
