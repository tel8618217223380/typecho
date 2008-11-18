<?php if(!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if($notice->have() && in_array($notice->noticeType, array('success', 'notice', 'error'))): ?>
<div class="typecho-page-title message <?php $notice->noticeType(); ?> popup">
<ul>
    <?php $notice->lists(); ?>
</ul>
</div>
<?php endif; ?>
<div class="container typecho-page-title">
    <div class="column-24 start-01">
        <h2><?php echo $menu->title; ?></h2>
        <p><a href="<?php $options->siteUrl(); ?>"><?php _e('查看我的站点'); ?></a></p>
    </div>
</div>
