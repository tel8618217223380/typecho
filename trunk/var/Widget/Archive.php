<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Posts.php 200 2008-05-21 06:33:20Z magike.net $
 */

/**
 * 内容的文章基类
 * 定义的css类
 * p.more:阅读全文链接所属段落
 *
 * TODO 增加feed支持
 * @package Widget
 */
class Widget_Archive extends Widget_Abstract_Contents
{
    /**
     * 调用的风格文件
     * 
     * @access private
     * @var string
     */
    private $_themeFile;
    
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 所有文章个数
     * 
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 生成分页的内容
     * 
     * @access private
     * @var array
     */
    private $_pageRow;
    
    /**
     * 聚合器对象
     * 
     * @access private
     * @var Typecho_Feed_Writer
     */
    private $_feed;
    
    /**
     * RSS 2.0聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedUrl;
    
    /**
     * RSS 1.0聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedRssUrl;
    
    /**
     * ATOM 聚合地址
     * 
     * @access private
     * @var string
     */
    private $_feedAtomUrl;
    
    /**
     * 本页关键字
     * 
     * @access private
     * @var string
     */
    private $_keywords;
    
    /**
     * 本页描述
     * 
     * @access private
     * @var string
     */
    private $_description;
    
    /**
     * 聚合类型
     * 
     * @access private
     * @var string
     */
    private $_feedType;
    
    /**
     * 归档标题
     * 
     * @access private
     * @var array
     */
    private $_archiveTitle = array();
    
    /**
     * 归档类型
     * 
     * @access private
     * @var string
     */
    private $_archiveType = 'index';
    
    /**
     * 归档缩略名
     * 
     * @access private
     * @var string
     */
    private $_archiveSlug;
    
    /**
     * 构造函数
     * 
     * @access public
     * @param mixed $params 传递的参数
     * @return void
     */
    public function __construct($params = NULL)
    {
        parent::__construct($params);

        /** 处理feed模式 **/
        if ('feed' == Typecho_Router::$current) {
        
            /** 判断聚合类型 */
            switch (true) {
                case 0 === strpos($this->request->feed, '/rss/') || '/rss' == $this->request->feed:
                    /** 如果是RSS1标准 */
                    $this->request->feed = substr($this->request->feed, 4);
                    $this->_feedType = Typecho_Feed::RSS1;
                    break;
                case 0 === strpos($this->request->feed, '/atom/') || '/atom' == $this->request->feed:
                    /** 如果是ATOM标准 */
                    $this->request->feed = substr($this->request->feed, 5);
                    $this->_feedType = Typecho_Feed::ATOM1;
                    break;
                default:
                    $this->_feedType = Typecho_Feed::RSS2;
                    break;
            }
        
            if (!Typecho_Router::match($this->request->feed) || 'feed' == Typecho_Router::$current) {
                if (0 === strpos($this->request->feed, '/comments/') || '/comments' == $this->request->feed) {
                    /** 专为feed使用的hack */
                    Typecho_Router::$current = 'comments';
                } else {
                    throw new Typecho_Widget_Exception(_t('聚合页不存在'), 404);
                }
            }
            
            /** 初始化聚合器 */
            $this->_feed = Typecho_Feed::generator($this->_feedType);
            
            /** 默认输出10则文章 **/
            $this->parameter->pageSize = 10;
        }
    }

    /**
     * 重载select 
     * 
     * @access public
     * @return void
     */
    public function select()
    {
        if ('feed' == Typecho_Router::$current) {
            // 对feed输出加入限制条件
            return parent::select()->where('table.contents.allowFeed = ?', 1)
            ->where('table.contents.password IS NULL');
        } else {
            return parent::select();
        }
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 处理搜索结果跳转 */
        if (isset($this->request->s)) {
            $filterKeywords = Typecho_Common::filterSearchQuery($this->request->s);
            
            /** 跳转到搜索页 */
            if (NULL != $filterKeywords) {
                $this->response->redirect(Typecho_Router::url('search', 
                array('keywords' => urlencode($filterKeywords)), $this->options->index));
            }
        }
    
        /** 初始化分页变量 */
        $this->parameter->setDefault(array('pageSize' => $this->options->pageSize));
        $this->_currentPage = isset($this->request->page) ? $this->request->page : 1;
        $hasPushed = false;

        /** 定时发布功能 */
        $select = $this->select()->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $this->options->gmtTime);
        
        /** 初始化其它变量 */
        $this->_feedUrl = $this->options->feedUrl;
        $this->_feedRssUrl = $this->options->feedRssUrl;
        $this->_feedAtomUrl = $this->options->feedAtomUrl;
        $this->_keywords = $this->options->keywords;
        $this->_description = $this->options->description;

        switch (Typecho_Router::$current) {
            /** 单篇内容 */
            case 'page':
            case 'post':
                
                /** 如果是单篇文章或独立页面 */
                if (isset($this->request->cid)) {
                    $select->where('table.contents.cid = ?', $this->request->cid);
                }
                
                /** 匹配缩略名 */
                if (isset($this->request->slug)) {
                    $select->where('table.contents.slug = ?', $this->request->slug);
                }
                
                /** 匹配时间 */
                if (isset($this->request->year)) {
                    $year = $this->request->year;
                    
                    $fromMonth = 1;
                    $toMonth = 12;
                    
                    if (isset($this->request->month)) {
                        $fromMonth = $this->request->month;
                        $toMonth = $fromMonth;
                        
                        $fromDay = 1;
                        $toDay = idate('t', mktime(0, 0, 0, $toMonth, 1, $year));
                        
                        if (isset($this->request->day)) {
                            $fromDay = $this->request->day;
                            $toDay = $fromDay;
                        }
                    }
                    
                    /** 获取起始GMT时间的unix时间戳 */
                    $from = mktime(0, 0, 0, $fromMonth, $fromDay, $year) - idate('z');
                    $to = mktime(23, 59, 59, $toMonth, $toDay, $year) - idate('z');
                    $select->where('table.contents.created > ? AND table.contents.created < ?', $from, $to);
                }

                /** 保存密码至cookie */
                if ($this->request->isPost() && isset($this->request->protectPassword)) {
                    $this->response->setCookie('protectPassword', $this->request->protectPassword, 0, $this->options->siteUrl);
                }
                
                /** 匹配类型 */
                $select->where('table.contents.type = ?', Typecho_Router::$current)->limit(1);
                $this->db->fetchRow($select, array($this, 'push'));
                
                if (!$this->have() || (isset($this->request->category) && $this->category != $this->request->category)) {
                    /** 对没有索引情况下的判断 */
                    throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
                }

                /** 设置关键词 */
                $this->_keywords = implode(',', Typecho_Common::arrayFlatten($this->tags, 'name'));
                
                /** 设置描述 */
                $this->_description = $this->excerpt;
                
                /** 设置模板 */
                if ($this->template) {
                    /** 应用自定义模板 */
                    $this->_themeFile = 'custom/' . $this->template;
                }
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->_feedUrl = $this->feedUrl;
                
                /** RSS 1.0 */
                $this->_feedRssUrl = $this->feedRssUrl;
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = $this->feedAtomUrl;
                
                /** 设置标题 */
                $this->_archiveTitle[] = $this->title;
                
                /** 设置归档类型 */
                $this->_archiveType = Typecho_Router::$current;
                
                /** 设置归档缩略名 */
                $this->_archiveSlug = 'post' == Typecho_Router::$current ? $this->cid : $this->slug;
                
                /** 设置403头 */
                if ($this->hidden) {
                    $this->response->setStatus(403);
                }

                $hasPushed = true;
                break;
                
            /** 分类归档 */
            case 'category':
            case 'category_page':
                /** 如果是分类 */
                $category = $this->db->fetchRow($this->db->select()
                ->from('table.metas')
                ->where('type = ?', 'category')
                ->where('slug = ?', $this->request->slug)->limit(1),
                array($this->widget('Widget_Abstract_Metas'), 'filter'));
                
                if (!$category) {
                    throw new Typecho_Widget_Exception(_t('分类不存在'), 404);
                }
            
                /** fix sql92 by 70 */
                $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                ->where('table.relationships.mid = ?', $category['mid']);
                
                /** 设置分页 */
                $this->_pageRow = $category;
                
                /** 设置关键词 */
                $this->_keywords = $category['name'];
                
                /** 设置描述 */
                $this->_description = $category['description'];
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->_feedUrl = $category['feedUrl'];
                
                /** RSS 1.0 */
                $this->_feedRssUrl = $category['feedRssUrl'];
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = $category['feedAtomUrl'];
                
                /** 设置标题 */
                $this->_archiveTitle[] = $category['name'];
                
                /** 设置归档类型 */
                $this->_archiveType = 'category';
                
                /** 设置归档缩略名 */
                $this->_archiveSlug = $category['slug'];
                break;

            /** 标签归档 */
            case 'tag':
            case 'tag_page':

                /** 如果是标签 */
                $tag = $this->db->fetchRow($this->db->select()->from('table.metas')
                ->where('type = ?', 'tag')
                ->where('slug = ?', $this->request->slug)->limit(1),
                array($this->widget('Widget_Abstract_Metas'), 'filter'));
                
                if (!$tag) {
                    throw new Typecho_Widget_Exception(_t('标签%s不存在', $this->request->slug), 404);
                }
            
                /** fix sql92 by 70 */
                $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                ->where('table.relationships.mid = ?', $tag['mid']);
                
                /** 设置分页 */
                $this->_pageRow = $tag;
                
                /** 设置关键词 */
                $this->_keywords = $tag['name'];
                
                /** 设置描述 */
                $this->_description = $tag['description'];
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->_feedUrl = $tag['feedUrl'];
                
                /** RSS 1.0 */
                $this->_feedRssUrl = $tag['feedRssUrl'];
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = $tag['feedAtomUrl'];
                
                /** 设置标题 */
                $this->_archiveTitle[] = $tag['name'];
                
                /** 设置归档类型 */
                $this->_archiveType = 'tag';
                
                /** 设置归档缩略名 */
                $this->_archiveSlug = $tag['slug'];
                break;

            /** 日期归档 */
            case 'archive_year':
            case 'archive_month':
            case 'archive_day':
            case 'archive_year_page':
            case 'archive_month_page':
            case 'archive_day_page':

                /** 如果是按日期归档 */
                $year = $this->request->year;
                $month = $this->request->month;
                $day = $this->request->day;
                
                if (!empty($year) && !empty($month) && !empty($day)) {
                
                    /** 如果按日归档 */
                    $from = mktime(0, 0, 0, $month, $day, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, $day, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->_archiveTitle[] = $year;
                    $this->_archiveTitle[] = $month;
                    $this->_archiveTitle[] = $day;
                } else if (!empty($year) && !empty($month)) {
                
                    /** 如果按月归档 */
                    $from = mktime(0, 0, 0, $month, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, $month, idate('t', $from), $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->_archiveTitle[] = $year;
                    $this->_archiveTitle[] = $month;
                } else if (!empty($year)) {
                
                    /** 如果按年归档 */
                    $from = mktime(0, 0, 0, 1, 1, $year) - $this->options->timezone;
                    $to = mktime(23, 59, 59, 12, 31, $year) - $this->options->timezone;
                    
                    /** 设置标题 */
                    $this->_archiveTitle[] = $year;
                }
                
                $select->where('table.contents.created >= ?', $from)
                ->where('table.contents.created <= ?', $to);
                
                /** 设置归档类型 */
                $this->_archiveType = 'date';
                
                /** 设置头部feed */
                $value = array('year' => $year, 'month' => $month, 'day' => $day);
                
                /** 设置分页 */
                $this->_pageRow = $value;
                
                /** 获取当前路由,过滤掉翻页情况 */
                $currentRoute = str_replace('_page', '', Typecho_Router::$current);
                
                /** RSS 2.0 */
                $this->_feedUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedUrl);
                
                /** RSS 1.0 */
                $this->_feedRssUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedRssUrl);
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = Typecho_Router::url($currentRoute, $value, $this->options->feedAtomUrl);
                break;

            /** 搜索归档 */
            case 'search':
            case 'search_page':
    
                /** 增加自定义搜索引擎接口 */
                //~ fix issue 40
                $this->plugin()->trigger($hasPushed)->search($this->request->keywords, $this);
    
                $keywords = Typecho_Common::filterSearchQuery($this->request->keywords);
                $searchQuery = '%' . $keywords . '%';
                
                /** 搜索无法进入隐私项保护归档 */
                $select->where('table.contents.password IS NULL')
                ->where('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery);
                
                /** 设置关键词 */
                $this->_keywords = $keywords;
                
                /** 设置分页 */
                $this->_pageRow = array('keywords' => $keywords);
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->_feedUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedUrl);
                
                /** RSS 1.0 */
                $this->_feedRssUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
                
                /** ATOM 1.0 */
                $this->_feedAtomUrl = Typecho_Router::url('search', array('keywords' => $keywords), $this->options->feedAtomUrl);
                
                /** 设置标题 */
                $this->_archiveTitle[] = $keywords;
                
                /** 设置归档类型 */
                $this->_archiveType = 'search';
                break;

            default:
                break;
        }
        
        /** 如果已经提前压入则直接返回 */
        if ($hasPushed) {
            return;
        }
        
        /** 仅输出文章 */
        $select->where('table.contents.type = ?', 'post');
        $this->_countSql = clone $select;

        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @param string $prev 上一页文字
     * @param string $next 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...')
    {
        $this->plugin()->trigger($hasNav)->pageNav($prev, $next, $splitPage, $splitWord);
        
        if (!$hasNav) {
            $query = Typecho_Router::url(Typecho_Router::$current . 
            (false === strpos(Typecho_Router::$current, '_page') ? '_page' : NULL),
            $this->_pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
            $this->_currentPage, $this->parameter->pageSize, $query);
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }
    
    /**
     * 获取评论归档对象
     * 
     * @access public
     * @param string $type 评论类型
     * @param boolean $desc 是否倒序输出
     * @return Widget_Abstract_Comments
     */
    public function comments($type = NULL, $desc = false)
    {
        $type = strtolower($type);
        $parameter = array('cid' => $this->hidden ? 0 : $this->cid, 'desc' => $desc, 'parentContent' => $this->row);
        
        switch ($type) {
            case 'comment':
                return $this->widget('Widget_Comments_Archive_Comment', $parameter);
            case 'trackback':
                return $this->widget('Widget_Comments_Archive_Trackback', $parameter);
            case 'pingback':
                return $this->widget('Widget_Comments_Archive_Pingback', $parameter);
            default:
                return $this->widget('Widget_Comments_Archive', $parameter);
        }
    }
    
    /**
     * 显示下一个内容的标题链接
     * 
     * @access public
     * @param string $format 格式
     * @param string $default 如果没有下一篇,显示的默认文字
     * @return void
     */
    public function theNext($format = '%s', $default = NULL)
    {
        $content = $this->db->fetchRow($this->select()->where('table.contents.created > ? AND table.contents.created < ?',
        $this->created, $this->options->gmtTime)
        ->where('table.contents.type = ?', $this->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1));
        
        if ($content) {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        } else {
            echo $default;
        }
    }
    
    /**
     * 显示上一个内容的标题链接
     * 
     * @access public
     * @param string $format 格式
     * @param string $default 如果没有上一篇,显示的默认文字
     * @return void
     */
    public function thePrev($format = '%s', $default = NULL)
    {
        $content = $this->db->fetchRow($this->select()->where('table.contents.created < ?', $this->created)
        ->where('table.contents.type = ?', $this->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1));
        
        if ($content) {
            $content = $this->filter($content);
            $link = '<a href="' . $content['permalink'] . '" title="' . $content['title'] . '">' . $content['title'] . '</a>';
            printf($format, $link);
        } else {
            echo $default;
        }
    }
    
    /**
     * 获取关联内容组件
     * 
     * @access public
     * @param integer $limit 输出数量
     * @param string $type 关联类型
     * @return Typecho_Widget
     */
    public function related($limit = 5, $type = NULL)
    {
        $type = strtolower($type);
        
        switch ($type) {
            case 'author':
                /** 如果访问权限被设置为禁止,则tag会被置为空 */
                return $this->widget('Widget_Contents_Related_Author', 
                array('cid' => $this->cid, 'type' => $this->type, 'author' => $this->author->uid, 'limit' => $limit));
            default:
                /** 如果访问权限被设置为禁止,则tag会被置为空 */
                return $this->widget('Widget_Contents_Related', 
                array('cid' => $this->cid, 'type' => $this->type, 'tags' => $this->tags, 'limit' => $limit));
        }
    }
    
    /**
     * 输出头部元数据
     * 
     * @access public
     * @return void
     */
    public function header()
    {
        $header = new Typecho_Widget_Helper_Layout_Header();
        $header->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'description', 'content' => $this->_description)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'keywords', 'content' => $this->_keywords)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'generator', 'content' => $this->options->generator)))
        ->addItem(new Typecho_Widget_Helper_Layout('meta', array('name' => 'template', 'content' => $this->options->theme)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'pingback', 'href' => $this->options->xmlRpcUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'EditURI', 'type' => 'application/rsd+xml', 'title' => 'RSD', 'href' => $this->options->xmlRpcUrl . '?rsd')))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'wlwmanifest', 'type' => 'application/wlwmanifest+xml',
        'href' => Typecho_Common::url('wlwmanifest.xml', $this->options->adminUrl))))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0', 'href' => $this->_feedUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 1.0', 'href' => $this->_feedRssUrl)))
        ->addItem(new Typecho_Widget_Helper_Layout('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'ATOM 1.0', 'href' => $this->_feedAtomUrl)));
        
        /** 插件支持 */
        $this->plugin()->header($header);
        
        /** 输出header */
        $header->render();
    }
    
    /**
     * 输出cookie记忆别名
     * 
     * @access public
     * @param string $cookieName 已经记忆的cookie名称
     * @return string
     */
    public function remember($cookieName)
    {
        echo $this->request->getCookie('__typecho_remember_' . $cookieName);
    }
    
    /**
     * 输出归档标题
     * 
     * @access public
     * @param string $split
     * @return void
     */
    public function archiveTitle($split = ' &raquo; ')
    {
        if ($this->_archiveTitle) {
            echo $split . implode($split, $this->_archiveTitle);
        }
    }
    
    /**
     * 判断归档类型和名称
     * 
     * @access public
     * @param string $archiveType 归档类型
     * @param string $archiveSlug 归档名称
     * @return boolean
     */
    public function is($archiveType, $archiveSlug = NULL)
    {        
        return ($archiveType == $this->_archiveType) && (empty($archiveSlug) ? true : $archiveSlug == $this->_archiveSlug);
    }
    
    /**
     * 设置主题文件
     * 
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function setTheme($fileName)
    {
        $this->_themeFile = $fileName;
    }
    
    /**
     * 获取主题文件
     * 
     * @access public
     * @param string $fileName 主题文件
     * @return void
     */
    public function get($fileName)
    {
        require_once __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' . $fileName;
    }
    
    /**
     * 输出视图
     * 
     * @access public
     * @return void
     */
    public function render()
    {    
        /** 添加Pingback */
        $this->response->setHeader('X-Pingback', $this->options->xmlRpcUrl);
        $themeDir = __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/';
        $validated = false;

        /** 个性化模板系统 */
        if (empty($this->_themeFile) && !empty($this->_archiveType)) {
        
            //~ 首先找具体路径, 比如 category/default.php
            if (!empty($this->_archiveSlug)) {
                $themeFile = $this->_archiveType . '/' . $this->_archiveSlug . '.php';
                if (is_file($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }

            //~ 然后找归档类型路径, 比如 category.php
            if (!$validated) {
                $themeFile = $this->_archiveType . '.php';
                if (is_file($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }
            
            //~ 最后找归档路径, 比如 archive.php
            if (!$validated && 'index' != $this->_archiveType) {
                $themeFile = 'archive.php';
                if (is_file($themeDir . $themeFile)) {
                    $this->_themeFile = $themeFile;
                    $validated = true;
                }
            }
        }
        
        /** 文件不存在 */
        if (!$validated && !is_file($themeDir . $this->_themeFile)) {
            throw new Typecho_Widget_Exception(_t('请求的地址不存在'), 404);
        }
    
        /** 输出模板 */
        require_once $themeDir . $this->_themeFile;
        
        /** 挂接插件 */
        $this->plugin()->render($this);
    }

    /**
     * 输出feed
     * 
     * @access public
     * @return void
     */
    public function feed()
    {
        $this->_feed->setCharset($this->options->charset);
        $this->_feed->setTitle($this->options->title . ($this->_archiveTitle ? ' - ' . implode(' - ', $this->_archiveTitle) : NULL));
        $this->_feed->setSubTitle($this->_description);

        if (Typecho_Feed::RSS2 == $this->_feedType) {
            $this->_feed->setChannelElement('language', _t('zh-cn'));
            $this->_feed->setLink($this->_feedUrl);
        }
        
        if (Typecho_Feed::RSS1 == $this->_feedType) {
            /** 如果是RSS1标准 */
            $this->_feed->setChannelAbout($this->_feedRssUrl);
            $this->_feed->setLink($this->_feedRssUrl);
        }
        
        if (Typecho_Feed::ATOM1 == $this->_feedType) {
            /** 如果是ATOM标准 */
            $this->_feed->setLink($this->_feedAtomUrl);
        }

        if (Typecho_Feed::RSS1 == $this->_feedType || Typecho_Feed::RSS2 == $this->_feedType) {
            $this->_feed->setDescription($this->_description);
        }

        if (Typecho_Feed::RSS2 == $this->_feedType || Typecho_Feed::ATOM1 == $this->_feedType) {
            $this->_feed->setChannelElement(Typecho_Feed::RSS2 == $this->_feedType ? 'pubDate' : 'updated',
            date(Typecho_Feed::dateFormat($this->_feedType), 
            $this->options->gmtTime + $this->options->timezone));
        }
        
        /** 插件接口 */
        $this->plugin()->feed($this->_feed, $this);
        
        /** 添加聚合频道 */
        switch (Typecho_Router::$current) {
            case 'post':
            case 'page':
            case 'comments':
                if ('comments' == Typecho_Router::$current) {
                    $comments = $this->widget('Widget_Comments_Recent', 'pageSize=10');
                } else {
                    $comments = $this->comments(NULL, true);
                }
                
                while ($comments->next()) {
                    $item = $this->_feed->createNewItem();
                    $item->setTitle($comments->author);
                    $item->setLink($comments->permalink);
                    $item->setDate($comments->date + $this->options->timezone);
                    $item->setDescription(Typecho_Common::cutParagraph($comments->content));

                    if (Typecho_Feed::RSS2 == $this->_feedType) {
                        $item->addElement('guid', $comments->permalink);
                        $item->addElement('content:encoded', Typecho_Common::subStr(Typecho_Common::stripTags($comments->content), 0, 100, '...'));
                        $item->addElement('author', $comments->author);
                        $item->addElement('dc:creator', $comments->author);
                    }
                    
                    $this->plugin()->commentFeedItem($item, $this->_feedType, $this);
                    $this->_feed->addItem($item);
                }
                break;
                
            case 'category':
            case 'category_page':
            case 'tag':
            case 'tag_page':
            case 'archive_year':
            case 'archive_month':
            case 'archive_day':
            case 'archive_year_page':
            case 'archive_month_page':
            case 'archive_day_page':
            case 'search':
            case 'search_page':
            default:
                while ($this->next()) {
                    $item = $this->_feed->createNewItem();
                    $item->setTitle($this->title);
                    $item->setLink($this->permalink);
                    $item->setDate($this->created + $this->options->timezone);
                    
                    /** RSS全文输出开关支持 */
                    if ($this->options->feedFullText) {
                        $item->setDescription($this->text);
                    } else {
                        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
                        $contents = explode('<!--more-->', $content);
                        
                        list($abstract) = $contents;
                        $item->setDescription(Typecho_Common::fixHtml($abstract) . (count($contents) > 1 ? '<p><a href="'
                        . $this->permalink . '">' . _t('阅读更多...') . '</a></p>' : NULL));
                    }
                    
                    $item->setCategory($this->categories);
                    
                    if (Typecho_Feed::RSS2 == $this->_feedType) {
                        $item->addElement('guid', $this->permalink);
                        $item->addElement('slash:comments', $this->commentsNum);
                        $item->addElement('comments', $this->permalink . '#comments');
                        $item->addElement('content:encoded', $this->excerpt);
                        $item->addElement('author', $this->author->screenName);
                        $item->addElement('dc:creator', $this->author->screenName);
                        $item->addElement('wfw:commentRss', $this->feedUrl);
                    }
                    
                    $this->plugin()->feedItem($item, $this->_feedType, $this);
                    $this->_feed->addItem($item);
                }
                break;
        }
        
        $this->_feed->generateFeed();
    }
}
