<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

$header = '<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/reset.source.css?v=' . $suffixVersion, $options->adminUrl) . '" /> 
<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/grid.source.css?v=' . $suffixVersion, $options->adminUrl) . '" /> 
<link rel="stylesheet" type="text/css" href="' . Typecho_Common::url('css/typecho.source.css?v=' . $suffixVersion, $options->adminUrl) . '" /> 
<script type="text/javascript" src="' . Typecho_Common::url('javascript/mootools-1.2.2-core-yc.js?v=' . $suffixVersion, $options->adminUrl) . '"></script> 
<script type="text/javascript" src="' . Typecho_Common::url('javascript/mootools-1.2.2.1-more.js?v=' . $suffixVersion, $options->adminUrl) . '"></script> 
<script type="text/javascript" src="' . Typecho_Common::url('javascript/typecho-ui.source.js?v=' . $suffixVersion, $options->adminUrl) . '"></script>';

/** 注册一个初始化插件 */
Typecho_Plugin::factory('admin/header.php')->header($header);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php $options->charset(); ?>" />
        <title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <?php echo $header; ?>
    </head>
    <body<?php if (isset($bodyClass)) {echo ' class="' . $bodyClass . '"';} ?>>
