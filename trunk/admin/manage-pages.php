<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>
<div class="main">
    <div class="body body-950">
        <?php include 'page-title.php'; ?>
        <div class="container typecho-page-main">
            <div class="column-24 start-01 typecho-list">
                <ul class="typecho-option-tabs">
                    <li<?php if(!Typecho_Request::isSetParameter('status') || 'publish' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php'); ?>"><?php _e('已发布'); ?></a></li>
                    <li<?php if('draft' == Typecho_Request::getParameter('status')): ?> class="current"<?php endif; ?>><a href="<?php $options->adminUrl('manage-pages.php?status=draft'); ?>"><?php _e('草稿'); ?>
                    <?php if($stat->draftPagesNum > 0): ?> 
                        <span class="balloon"><?php $stat->draftPagesNum(); ?></span>
                    <?php endif; ?>
                    </a></li>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate"><?php _e('操作'); ?>: 
                        <span class="operate-button typecho-table-select-all"><?php _e('全选'); ?></span>, 
                        <span class="operate-button typecho-table-select-none"><?php _e('不选'); ?></span>&nbsp;&nbsp;&nbsp;
                        <?php _e('选中项'); ?>: 
                        <span rel="delete" class="operate-button typecho-table-select-submit"><?php _e('删除'); ?></span>
                    </p>
                    <p class="search">
                    <input type="text" value="<?php _e('请输入关键字'); ?>" onclick="value='';name='keywords';" />            
                    <?php if(Typecho_Request::isSetParameter('status')): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars(Typecho_Request::getParameter('status')); ?>" name="status" />
                    <?php endif; ?>
                    
                    <button type="submit"><?php _e('筛选'); ?></button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_pages" class="operate-form" action="<?php $options->index('Contents/Page/Edit.do'); ?>">
                <table class="typecho-list-table draggable">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="425"/>
                        <col width="150"/>
                        <col width="150"/>
                        <col width="200"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th><?php _e('标题'); ?></th>
                            <th><?php _e('缩略名'); ?></th>
                            <th><?php _e('作者'); ?></th>
                            <th class="typecho-radius-topright"><?php _e('发布日期'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php Typecho_Widget::widget('Widget_Contents_Page_Admin')->to($pages); ?>
                    	<?php if($pages->have()): ?>
                        <?php while($pages->next()): ?>
                        <tr<?php $pages->alt(' class="even"', ''); ?> id="<?php $pages->theId(); ?>">
                            <td><input type="checkbox" value="<?php $pages->cid(); ?>" name="cid[]"/></td>
                            <td><a href="<?php $pages->permalink(); ?>" class="balloon-button right <?php
                            switch (true) {
                                case 0 == $pages->commentsNum:
                                    echo 'size-0';
                                    break;
                                case 0 < $pages->commentsNum && $pages->commentsNum < 10:
                                    echo 'size-1';
                                    break;
                                case 10 <= $pages->commentsNum && $pages->commentsNum < 20:
                                    echo 'size-2';
                                    break;
                                case 20 <= $pages->commentsNum && $pages->commentsNum < 50:
                                    echo 'size-3';
                                    break;
                                case 50 <= $pages->commentsNum && $pages->commentsNum < 100:
                                    echo 'size-4';
                                    break;
                                case 100 <= $pages->commentsNum:
                                    echo 'size-5';
                                    break;
                            }
                            ?>"><?php $pages->commentsNum(); ?></a></td>
                            <td><a href="<?php $options->adminUrl('write-page.php?cid=' . $pages->cid); ?>"><?php $pages->title(); ?></a></td>
                            <td><?php $pages->slug(); ?></td>
                            <td><?php $pages->author(); ?></td>
                            <td><?php $pages->dateWord(); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title"><?php _e('没有任何页面'); ?></h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            
            </div>
        </div>
    </div>
</div>
<?php include 'common-js.php'; ?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            typechoTable.dragStop = function (item, result) {
                var _r = new Request.JSON({
                    url: '<?php $options->index('Contents/Page/Edit.do'); ?>'
                }).send(result + '&do=sort');
            };
        });
    })();
</script>
<?php include 'copyright.php'; ?>