<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 后台评论输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Admin extends Widget_Abstract_Comments
{
    /**
     * 用于过滤的条件
     * 
     * @access private
     * @var array
     */
    private $_filterQuery = array();
    
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 分页大小
     * 
     * @access private
     * @var integer
     */
    private $_pageSize;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 构造函数
     * 
     * @access public
     * @param integer $pageSize 每页内容数
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        
        $select = $this->select();
        $this->_pageSize = empty($pageSize) ? 20 : $pageSize;
        $this->_currentPage = Typecho_Request::getParameter('page', 1);
    
        /** 过滤标题 */
        if(NULL != ($keywords = Typecho_Request::getParameter('keywords')))
        {
            $select->where('table.comments.`text` LIKE ?', '%' . Typecho_API::filterSearchQuery($keywords) . '%');
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        if(in_array(Typecho_Request::getParameter('status'), array('approved', 'waiting', 'spam')))
        {
            $select->where('table.comments.`status` = ?', Typecho_Request::getParameter('status'));
        }
    
        $this->_countSql = clone $select;
        
        $select->group('table.comments.`coid`')
        ->order('table.comments.`created`', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...')
    {
        $query = Typecho_API::pathToUrl('comment-list.php?' . http_build_query($this->_filterQuery) . '&page={page}', $this->options->adminUrl);

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->size($this->_countSql), $this->_currentPage, $this->_pageSize, $query);
        $nav->render($prev, $next, $splitPage, $splitWord);
    }
}
