<?php 
require_once 'common.php';
Typecho::widget('Menu')->setCurrentParent('/admin/edit.php');
Typecho::widget('Menu')->setCurrentChild('/admin/edit.php');
require_once 'header.php';
require_once 'menu.php';
Typecho::widget('contents.EditPost')->to($post);
?>

	<div id="main" class="clearfix">
	<form method="post" action="<?php Typecho::widget('Options')->index('DoEditPost.do'); ?>" id="post" name="post">
        
        <div id="sidebar">
			<h3><?php _e('发布'); ?></h3>
			<div id="publishing">
				<p><label><?php _e('发布日期'); ?></label><input type="text" name="test" value="<?php echo date('Y-m-d');?>" /></p>
				<p><label><?php _e('发布时间'); ?></label><input type="text" name="test" value="<?php echo date('H:i:s');?>" /></p>
			</div>

			<h3><?php _e('页面顺序'); ?></h3>
			<p><input type="text" id="" style="width: 240px;" value="0" /></p>

			<h3><?php _e('分类'); ?></h3>
			<p><input type="text" id="" style="color: #666; width: 155px; margin-right: 15px;" value="Add New Category" onclick="value=''" /><input type="button" value="<?php _e('增加'); ?>" onclick="" /></p>
			<ul id="cat_list">
            <?php Typecho::widget('Metas', 'category')->to($category); $categories = $post->categories(); ?>
            <?php if($category->have()): ?>
            <?php while($category->get()): ?>
                <li><span class="right"><a href="#">&times;</a></span><label for="category-<?php $category->mid(); ?>"><input type="checkbox" name="category[]" value="<?php $category->mid(); ?>" <?php if(in_array($category->mid, $categories)){echo 'checked="true"';} ?> id="category-<?php $category->mid(); ?>" /> <?php $category->name(); ?></label></li>
            <?php endwhile; ?>
            <?php else: ?>
                <li><?php _e('没有任何分类'); ?></li>
            <?php endif; ?>
			</ul>
			<hr class="space">

			<h3><?php _e('评论,引用和聚合'); ?></h3>
			<div id="allow_status">
				<p><input type="checkbox" id="allowComment" name="allowComment" checked="checked"/><label for="allowComment"><?php _e('允许评论'); ?></label><br />
				<input type="checkbox" id="allowPing" name="allowPing" checked="checked"/><label for="allowPing"><?php _e('允许引用'); ?></label><br />
                <input type="checkbox" id="allowFeed" name="allowFeed" checked="checked"/><label for="allowFeed"><?php _e('允许聚合'); ?></label></p>
			</div>

			<h3><?php _e('密码保护'); ?></h3>
			<p><input type="text" name="pasword" id="pasword" style="width: 225px;" /></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><small>Setting a password will require people who visit your blog to enter the above password to view this post and its comments.</small></p>

			<h3><?php _e('引用通告'); ?></h3>
			<p><textarea id="trackback" name="trackback" rows="5" cols="" style="width: 225px;"></textarea></p>
			<p style="margin: -1em 1.5em 1.5em 0;"><small>Separate multiple Urls with spaces</small></p>
		</div><!-- end #sidebar -->
        
		<div id="content">
            <h2><?php _e('撰写新文章'); ?></h2>
			<div id="msg" class="notice">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div>
			<!-- <div id="msg" class="success">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div>
			<div id="msg" class="error">A saved version of this entry was auto-saved 6 days ago. <a href="#">Recover auto-saved content</a></div> -->
			<h4><?php _e('标题'); ?></h4>
			<p><input id="title" type="text" name="title" value="<?php $post->title(); ?>" /></p>
			<h4><?php _e('内容'); ?></h4>
			<p><textarea id="text" name="text" rows="15" cols="40"><?php $post->text(); ?></textarea></p>
			<p style="text-align: right;">
                <input type="button" onclick="$('input[@name=draft]').val(1);post.submit();" value="<?php _e('保存'); ?>" /> 
                <input type="button" onclick="$('input[@name=continue]').val(1);post.submit();" value="<?php _e('保存并继续编辑'); ?>" /> 
                <input type="submit" value="<?php _e('发布'); ?>" />
                <input type="hidden" name="do" value="insert" />
                <input type="hidden" name="draft" value="0" />
                <input type="hidden" name="continue" value="0" />
            </p>
			<h4><?php _e('标签'); ?></h4>
			<p><input id="tags" type="text" name="tags" value="" /><span id="tag_list"><a href="#" class="select">design</a> <a href="#">program</a> <a href="#">wordpress</a> </span></p>
			<h4><?php _e('缩略名'); ?></h4>
			<p><input id="slug" type="text" name="slug" value="" /></p>
		</div><!-- end #content -->

	</form>
	</div><!-- end #main -->
<script type="text/javascript" src="<?php Typecho::widget('Options')->adminUrl('/js/tiny_mce/tiny_mce.js'); ?>"></script>
<script type="text/javascript">
tinyMCE.init({
mode : "exact",
elements : "text",
theme : "advanced",
skin : "o2k7",
plugins : "safari,inlinepopups",
theme_advanced_buttons1 : "bold,italic,underline,strikethrough, separator, forecolor,backcolor,fontselect,fontsizeselect",
theme_advanced_buttons2_add_before: "cut,copy,pastetext,separator",
theme_advanced_buttons2 : "undo,redo,separator,hr,link,unlink,image,flash,separator",
theme_advanced_buttons2_add :"code,emotions,charmap",
theme_advanced_buttons3 : "",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
relative_urls : false,
remove_script_host : false
});
</script>

<?php require_once 'footer.php'; ?>
