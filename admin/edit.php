<?php 
require_once 'common.php';
Typecho_API::factory('Widget_Contents_Post_Edit')->to($post);
require_once 'header.php';
require_once 'menu.php';

if($post->cid)
{
    $created = $post->created + $options->timezone;
}
else
{
    $created = $options->gmtTime + $options->timezone;
}
?>

	<div id="main" class="clearfix">
	<form method="post" action="<?php $options->index('/Contents/Post/Edit.do'); ?>" id="post" name="post">
        
        <div id="sidebar">
			<h3><?php _e('发布'); ?></h3>
			<div id="publishing">
				<p><label><?php _e('发布日期'); ?></label><input type="text" class="text" readonly="readonly" id="date" name="date" value="<?php echo date('Y-m-d', $created);?>" /></p>
				<p><label><?php _e('发布时间'); ?></label><input type="text" class="text" readonly="readonly" id="time" name="time" value="<?php echo date('g:i A', $created);?>" /></p>
			</div>

			<h3><?php _e('分类'); ?></h3>
            <?php if($access->pass('editor', true)): ?>
			<p><input type="text" class="text" style="color: #666; width: 155px; margin-right: 15px;" value="<?php _e('增加新分类'); ?>" onclick="value='';id='category';" />
            <input type="button" class="button" value="<?php _e('增加'); ?>" onclick="ajaxInsertCategory('undefined' == typeof($('#category').val()) ? '' : $('#category').val());" /></p>
			<?php endif; ?>
            <ul id="cat_list">
            <?php Typecho_API::factory('Widget_Metas_Category_List')->to($category);
            $categories = ($categories = Typecho_API::arrayFlatten(empty($post->categories) ? array() : $post->categories, 'mid')) ? $categories : ($post->cid ? array() : array($options->defaultCategory)); ?>
            <?php if($category->have()): ?>
            <?php while($category->get()): ?>
                <li><label for="category-<?php $category->mid(); ?>"><input type="checkbox" name="category[]" value="<?php $category->mid(); ?>" <?php if(in_array($category->mid, $categories)){echo 'checked="true"';} ?> id="category-<?php $category->mid(); ?>" /> <?php $category->name(); ?></label></li>
            <?php endwhile; ?>
            <?php else: ?>
                <li><?php _e('没有任何分类'); ?></li>
            <?php endif; ?>
			</ul>
			<hr class="space">

			<h3><?php _e('评论,引用和聚合'); ?></h3>
			<div id="allow_status">
                <ul id="permission_list">
                <li><input type="checkbox" id="allowComment" value="1" name="allowComment" <?php if($post->allow('comment') || (!$post->cid && $options->defaultAllowComment)): ?>checked="checked"<?php endif; ?> /><label for="allowComment"><?php _e('允许访问者对此文评论'); ?></label></li>
				<li><input type="checkbox" id="allowPing" value="1" name="allowPing" <?php if($post->allow('ping') || (!$post->cid && $options->defaultAllowPing)): ?>checked="checked"<?php endif; ?> /><label for="allowPing"><?php _e('允许其它网站向此文发送广播'); ?></label></li>
                <li><input type="checkbox" id="allowFeed" value="1" name="allowFeed" <?php if($post->allow('feed') || (!$post->cid && $options->defaultAllowFeed)): ?>checked="checked"<?php endif; ?> /><label for="allowFeed"><?php _e('允许在聚合中出现此文'); ?></label></li>
                </ul>
            </div>

			<h3><?php _e('密码保护'); ?></h3>
			<p><input type="text" class="text" name="password" id="password" style="width: 225px;" value="<?php $post->password(); ?>" /></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><?php _e('为这篇日志分配一个密码,访问者需要输入密码才能阅读到日志内容。'); ?></p>

			<h3><?php _e('引用通告'); ?></h3>
			<p><textarea id="trackback" name="trackback" rows="5" cols="" style="width: 225px;"></textarea></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><?php _e('每行一个引用地址,用回车隔开'); ?></p>
		</div><!-- end #sidebar -->
        
		<div id="content">
            <h2><?php $menu->title(); ?></h2>
            
			<?php require_once 'notice.php'; ?>
            
			<h4><?php _e('标题'); ?></h4>
			<p><input id="title" class="text" type="text" name="title" onfocus="this.select();" value="<?php $post->title(); ?>" /></p>
			<h4><?php _e('内容'); ?></h4>
			<p><textarea id="text" name="text" style="height:300px" cols="40"><?php $post->text(); ?></textarea></p>
			<p style="text-align: right;" class="submit">
                <input type="submit" class="button" onclick="$('input[@name=draft]').val(1);" value="<?php _e('保存为草稿'); ?>" /> 
                <input type="submit" class="button" onclick="$('input[@name=draft]').val(1);$('input[@name=continue]').val(1);" value="<?php _e('保存并继续编辑'); ?>" />
                <input type="submit" class="submit" value="<?php _e('发布'); ?>" />
                <input type="hidden" name="do" value="<?php echo ($post->cid ? 'update' : 'insert'); ?>" />
                <input type="hidden" name="cid" value="<?php $post->cid(); ?>" />
                <input type="hidden" name="draft" value="0" />
                <input type="hidden" name="continue" value="0" />
            </p>
			<h4><?php _e('标签'); ?></h4>
            <?php Typecho_API::factory('Widget_Query', 'from=table.metas&type=tag&order=count&sort=DESC&limit=8')->to($tags); ?>
			<p><input id="tags" type="text" class="text" name="tags" value="<?php if($post->cid) $post->tags(',', false); ?>" />
            <span id="tag_list">
            <?php while($tags->get()): ?>
                <a href="#" class="select"><?php $tags->name(); ?></a> 
            <?php endwhile; ?>
            </span>
            </p>
			
            <h4><?php _e('缩略名(用于自定义链接)'); ?></h4>
			<p><input id="slug" type="text" class="text" name="slug" value="<?php $post->slug(); ?>" /></p>
		</div><!-- end #content -->

	</form>
	</div><!-- end #main -->
<script type="text/javascript" src="<?php $options->adminUrl('/js/jquery-ui-personalized-1.5.1.min.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('/js/jquery.clockpick.1.2.3.pack.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('/js/tiny_mce/tiny_mce.js'); ?>"></script>
<script type="text/javascript" src="<?php $options->adminUrl('/javascript.php'); ?>"></script>

<?php require_once 'footer.php'; ?>