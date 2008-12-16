<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="content-type" content="text/html; charset=<?php $this->options->charset(); ?>" />
<title><?php
    /** 自定义标题 */
    switch ($this->options->archiveType)
    {
        /** 如果是分类 */
        case 'category':
            $this->options->archiveTitle('分类"%s" &raquo; ');
            break;
        /** 如果是标签 */
        case 'tag':
            $this->options->archiveTitle('标签"%s" &raquo; ');
            break;
        /** 如果是日期归档 */
        case 'date':
            $this->options->archiveTitle('按日期归档 %s &raquo; ');
            break;
        /** 如果是搜索 */
        case 'search':
            $this->options->archiveTitle('搜索关键字"%s" &raquo; ');
            break;
        /** 如果是文章或独立页面 */
        case 'post':
        case 'page':
            $this->options->archiveTitle('%s &raquo; ');
            break;
        default:
            $this->options->archiveTitle();
            break;
    }
?><?php $this->options->title(); ?></title>

<!-- 使用url函数转换相关路径 -->
<link rel="stylesheet" type="text/css" media="all" href="<?php $this->options->themeUrl('style.css'); ?>" />

<!-- 通过自有函数输出HTML头部信息 -->
<?php $this->header(); ?>
</head>

<body>
<div class="container_16 clearfix">
    <div id="header" class="grid_16">
		<ul class="clearfix" id="nav_menu">
			<li><a href="<?php $this->options->siteUrl(); ?>">Home</a></li>
				<?php $this->widget('Widget_Contents_Page_List')
                ->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
		</ul>

		<div id="logo">
	        <h1><a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a></h1>
			<span><?php $this->options->description() ?></span>
		</div>

		<div id="search">
            <form method="post" action="">
            	<div><input type="text" name="s" class="text" size="32" /> <input type="submit" class="submit" value="Search" /></div>
            </form>
		</div>
    </div><!-- end #header -->