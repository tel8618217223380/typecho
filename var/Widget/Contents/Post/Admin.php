<?php
/**
 * 文章管理列表
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 文章管理列表组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Post_Admin extends Widget_Abstract_Contents
{
    /**
     * 用于计算数值的语句对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $countSql;
    
    /**
     * 用于过滤的条件
     * 
     * @access private
     * @var array
     */
    private $_filterQuery = array();
    
    /**
     * 分页大小
     * 
     * @access private
     * @var integer
     */
    private $pageSize;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $currentPage;

    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->parameter->setDefault('pageSize=20');
        $this->currentPage = $this->request->getParameter('page', 1);

        /** 构建基础查询 */
        $select = $this->select();

        /** 过滤分类 */
        if(NULL != ($category = $this->request->category))
        {
            $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.relationships.mid = ?', $category);
            
            $this->_filterQuery['category'] = $category;
        }

        /** 获取状态过滤条件 */
        if(NULL != ($status = $this->request->status))
        {
            $this->_filterQuery['status'] = $status;
        }
        
        /** 如果具有编辑以上权限,可以查看所有文章,反之只能查看自己的文章 */
        if(!$this->user->pass('editor', true))
        {
            $select->where('table.contents.author = ?', $this->user->uid);
        }
        
        /** 过滤状态 */
        switch($status)
        {
            case 'draft':
                $select->where('table.contents.type = ?', 'draft');
                break;
            case 'published':
                $select->where('table.contents.type = ?', 'post');
                break;
            default:
                $select->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'draft');
                break;
        }
        
        /** 过滤标题 */
        if(NULL != ($keywords = $this->request->keywords))
        {
            $args = array();
            $keywordsList = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywordsList), 'table.contents.title LIKE ?'));
            
            foreach($keywordsList as $keyword)
            {
                $args[] = '%' . Typecho_Common::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($select, 'where'), $args);
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        /** 给计算数目对象赋值,克隆对象 */
        $this->countSql = clone $select;
        
        /** 提交查询 */
        $select->group('table.contents.cid')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->page($this->currentPage, $this->parameter->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = Typecho_Common::pathToUrl('manage-posts.php?' . http_build_query($this->_filterQuery) . '&page={page}',
        $this->options->adminUrl);
        
        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->count($this->countSql), $this->currentPage, $this->parameter->pageSize, $query);
        $nav->render(_t('&laquo;'), _t('&raquo;'));
    }
}
