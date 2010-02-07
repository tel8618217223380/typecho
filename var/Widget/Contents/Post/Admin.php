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
    private $_countSql;

    /**
     * 所有文章个数
     *
     * @access private
     * @var integer
     */
    private $_total = false;

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
    private $_currentPage;

    /**
     * 当前文章的草稿
     *
     * @access protected
     * @return array
     */
    protected function ___hasDraft()
    {
        if ('draft' == $this->status) {
            return true;
        }

        if ($this->db->fetchRow($this->db->select('cid')
        ->from('table.contents')
        ->where('table.contents.parent = ? AND table.contents.type = ? AND table.contents.status = ?',
            $this->cid, $this->type, 'draft')
        ->limit(1))) {
            return true;
        }

        return false;
    }

    /**
     * 获取菜单标题
     *
     * @access public
     * @return string
     */
    public function getMenuTitle()
    {
        if (isset($this->request->uid)) {
            return _t('%s的文章', $this->db->fetchObject($this->db->select('screenName')->from('table.users')
                ->where('uid = ?', $this->request->filter('int')->uid))->screenName);
        }

        throw new Typecho_Widget_Exception(_t('用户不存在'), 404);
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault('pageSize=20');
        $this->_currentPage = $this->request->get('page', 1);

        /** 构建基础查询 */
        $select = $this->select()->where('table.contents.type = ?', 'post');

        /** 过滤分类 */
        if (NULL != ($category = $this->request->category)) {
            $select->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.relationships.mid = ?', $category);
        }

        /** 如果具有编辑以上权限,可以查看所有文章,反之只能查看自己的文章 */
        if (!$this->user->pass('editor', true)) {
            $select->where('table.contents.authorId = ?', $this->user->uid);
        } else if (isset($this->request->uid)) {
            $select->where('table.contents.authorId = ?', $this->request->filter('int')->uid);
        }

        /** 过滤状态 */
        $select->where('table.contents.status = ? OR table.contents.status = ? OR (table.contents.status = ? AND table.contents.parent = 0)',
            'publish', 'waiting', 'draft');

        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->filter('search')->keywords)) {
            $args = array();
            $keywordsList = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywordsList), 'table.contents.title LIKE ?'));

            foreach ($keywordsList as $keyword) {
                $args[] = '%' . $keyword . '%';
            }

            call_user_func_array(array($select, 'where'), $args);
        }

        /** 给计算数目对象赋值,克隆对象 */
        $this->_countSql = clone $select;

        /** 提交查询 */
        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->parameter->pageSize);

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
        $query = $this->request->makeUriByRequest('page={page}');

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render('&laquo;', '&raquo;');
    }
}
