<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <ul class="typecho-option-tabs">
                    <li<?php if(!Typecho_Request::isSetParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php'); ?>"><?php _e('所有'); ?></a></li>
                    <li<?php if('published' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php?status=published'); ?>"><?php _e('已发布'); ?></a></li>
                    <li<?php if('draft' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php?status=draft'); ?>"><?php _e('草稿'); ?></a></li>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作：<a href="#">全选</a>，<a href="#">反选</a>，<a href="#">删除选中项</a></p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />            
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo Typecho_Request::getParameter('status'); ?>" name="status" />
                    <?php endif; ?>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="450"/>
                        <col width="100"/>
                        <col width="125"/>
                        <col width="150"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th><?php _e('发布日期'); ?></th>
                            <th><?php _e('评论'); ?></th>
                            <th><?php _e('状态'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Page_Admin')->to($pages); ?>
                    	<?php if($pages->have()): ?>
                        <?php while($pages->next()): ?>
                        <tr<?php $pages->alt(' class="even"', ''); ?>>
                            <td><input type="checkbox" value="<?php $pages->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $options->adminUrl('write-page.php?cid=' . $pages->cid); ?>"><?php $pages->title(); ?></a></td>
                            <td><?php $pages->author(); ?></td>
                            <td><?php $pages->dateWord(); ?></td>
                            <td><?php $pages->commentsNum(_t('没有评论'), _t('仅有一条评论'), _t('%d条评论')); ?></td>
                            <td><?php if('page' == $pages->type):
                        _e('<a href="%s">已发布</a>', $pages->permalink);
                        else:
                        _e('草稿');
                        endif;?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><?php _e('没有任何页面'); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<?php include 'copyright.php'; ?>
