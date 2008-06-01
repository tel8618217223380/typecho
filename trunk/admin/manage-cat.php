<?php 
require_once 'common.php';
Typecho::widget('Metas.EditCategory')->to($category);
require_once 'header.php';
require_once 'menu.php';
?>

	<div id="main">
		<h2><?php Typecho::widget('Menu')->title(); ?></h2>
		<div id="page">
        <?php require_once 'notice.php'; ?>
        
		<form method="post" id="category" name="category" action="<?php Typecho::widget('Options')->index('DoCategory.do'); ?>">
			<div class="table_nav">
                <input type="button" onclick="window.location = '<?php Typecho::widget('Options')->adminUrl('/manage-cat.php'); ?>#edit'" value="<?php _e('增加分类'); ?>" />
				<input type="button" onclick="$('#category input[@name=do]').val('delete');category.submit();" value="<?php _e('删除'); ?>" />
				<select name="merge" style="width: 160px;">
                    <?php Typecho::widget('Query', 'from=table.metas&type=category&order=sort&sort=ASC')
                    ->parse('<option value="{mid}">{name}</option>'); ?>
				</select>
				<input type="button" onclick="$('#category input[@name=do]').val('merge');category.submit();" value="<?php _e('合并'); ?>" />
                <input type="hidden" name="do" value="delete" />
			</div>

			<table class="latest">
				<tr>
					<th width="1%"><input type="checkbox" id="" /></th>
					<th width="20%"><?php _e('分类名称'); ?></th>
					<th width="50%"><?php _e('分类描述'); ?></th>
					<th width="10%"><?php _e('文章'); ?></th>
					<th width="19%"><?php _e('分类缩略名'); ?></th>
				</tr>
                <?php Typecho::widget('Categories')->to($categories); ?>
                <?php if($categories->have()): ?>
                <?php while($categories->get()): ?>
                <tr>
					<td><input type="checkbox" name="mid[]" value="<?php $categories->mid(); ?>" /></td>
					<td><a href="<?php Typecho::widget('Options')->adminUrl('/manage-cat.php?mid=' . $categories->mid); ?>#edit">
                    <?php $categories->name(); ?></a>
                    <?php if(Typecho::widget('Options')->defaultCategory == $categories->mid): ?> <sup><strong><?php _e('默认分类'); ?></strong></sup>
                    <?php else: ?> <sub><a href="<?php Typecho::widget('Options')->index('DoCategory.do'); ?>?do=default&mid=<?php $categories->mid(); ?>"><?php _e('设为默认'); ?></a></sub><?php endif; ?>
                    </td>
					<td><?php $categories->description(); ?></td>
					<td><a href="<?php $categories->permalink(); ?>">
                    <?php $categories->count(); ?></a></td>
					<td><?php $categories->slug(); ?></td>
				</tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5"><?php _e('没有任何分类,请在下方添加'); ?></td>
                </tr>
                <?php endif; ?>
			</table>
        </form>    
        
        <form method="post" action="<?php Typecho::widget('Options')->index('DoCategory.do'); ?>">
			<h4 id="edit"><?php if('update' == $category->do){ _e('编辑分类'); }else{ _e('增加分类'); } ?></h4>
			<table class="setting">
				<tr><th width="20%"></th><th width="80%"></th></tr>
				<tr>
					<td><label for="name"><?php _e('分类名称'); ?>*</label></td>
					<td><input type="text" name="name" id="name" style="width: 60%;" value="<?php $category->name(); ?>" />
                    <?php Typecho::widget('Notice')->display('name', '<span class="detail">%s</span>'); ?></td>
				</tr>
				<tr>
					<td><label for="slug"><?php _e('分类缩略名'); ?>*</label></td>
					<td><input type="text" name="slug" id="slug" style="width: 60%;" value="<?php $category->slug(); ?>" />
                    <?php Typecho::widget('Notice')->display('slug', '<span class="detail">%s</span>'); ?>
                    <small><?php _e('分类缩略名用于创建友好的链接形式,请使用字母,数字,下划线和横杠.'); ?></small></td>
				</tr>
				<tr>
					<td><label for="description"><?php _e('分类描述'); ?></label></td>
					<td><textarea name="description" id="description" rows="5" style="width: 80%;"><?php $category->description(); ?></textarea>
                    <small><?php _e('此文字用于描述分类,在有的主题中它会被显示.'); ?></small></td>
				</tr>
				<tr>
					<td><input type="hidden" name="do" value="<?php $category->do(); ?>" />
                    <input type="hidden" name="mid" value="<?php $category->mid(); ?>" /></td>
					<td><input type="submit" value="<?php if('update' == $category->do){ _e('编辑分类'); }else{ _e('增加分类'); } ?>" /></td>
				</tr>
			</table>
		</form>
		</div><!-- end #page -->
	</div><!-- end #main -->
	
<?php require_once 'footer.php'; ?>
