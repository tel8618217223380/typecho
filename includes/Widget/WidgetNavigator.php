<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容分页类
 * 定义的css类
 * a.next:下一页链接
 * a.prev:上一页链接
 * span.foucs:当前页
 * 关于"页面链接模板"的说明,为了便于生成分页链接,我们观察到页面链接都是如下形式
 * http://xxx.xxx.com/test?page=%d
 * 其中%d所标识的就是分页类需要填充的数据,这就是我们的页面链接模板.从原理上来说,页面链接模板的填充数据应该用诸如%d这样的格式化标识符来标识.但是考虑到与
 * 路由器所定义的格式化字符串的兼容性,我们用一个不常用的数字来标识填充位置,这个数字就是-65536,因此上面的链接变为
 * http://xxx.xxx.com/test?page=-65536
 * 通过分页类的解析以及替换,它可能会变成
 * http://xxx.xxx.com/test?page=1
 *
 * @package Widget
 */
class TypechoWidgetNavigator
{
    /**
     * 记录总数
     *
     * @access private
     * @var integer
     */
    private $_total;

    /**
     * 页面总数
     *
     * @access private
     * @var integer
     */
    private $_totalPage;

    /**
     * 当前页面
     *
     * @access private
     * @var integer
     */
    private $_currentPage;

    /**
     * 每页内容数
     *
     * @access private
     * @var integer
     */
    private $_pageSize;

    /**
     * 页面链接模板
     *
     * @access private
     * @var string
     */
    private $_pageTemplate;

    /**
     * 构造函数,初始化页面基本信息
     *
     * @access public
     * @param integer $total 记录总数
     * @param integer $page 当前页面
     * @param integer $pageSize 每页记录数
     * @param string $pageTemplate 页面链接模板
     * @return void
     * @throws TypechoWidgetException
     */
    public function __construct($total, $currentPage, $pageSize, $pageTemplate)
    {
        $this->_total = $total;
        $this->_totalPage = ceil($total / $pageSize);
        $this->_currentPage = $currentPage;
        $this->_pageSize = $pageSize;
        $this->_pageTemplate = $pageTemplate;

        //如果页面超出范围,则抛出404异常
        if(($currentPage > $this->_totalPage && 0 < $this->_totalPage) || 1 > $currentPage)
        {
            throw new TypechoWidgetException(_t('没有找到'), 404);
        }
    }

    /**
     * 输出经典样式的分页
     *
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @return void
     */
    public function makeClassicNavigator($prevWord = 'PREV', $nextWord = 'NEXT')
    {
        //输出下一页
        if($this->_currentPage < $this->_totalPage)
        {
            echo '<a class="next" href="' . str_replace('{page}', $this->_currentPage + 1, $this->_pageTemplate) . '">'
            . $nextWord . '</a>';
        }

        //输出上一页
        if($this->_currentPage > 1)
        {
            echo '<a class="prev" href="' . str_replace('{page}', $this->_currentPage - 1, $this->_pageTemplate) . '">'
            .$prevWord . '</a>';
        }
    }

    /**
     * 输出盒装样式分页栏
     *
     * @access public
     * @param string $prevWord 上一页文字
     * @param string $nextWord 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return unknown
     */
    public function makeBoxNavigator($prevWord = 'PREV', $nextWord = 'NEXT', $splitPage = 3, $splitWord = '...')
    {
        $from = max(1, $this->_currentPage - $splitPage);
        $to = min($this->_totalPage, $this->_currentPage + $splitPage);

        //输出上一页
        if($this->_currentPage > 1)
        {
            echo '<a class="prev" href="' . str_replace('{page}', $this->_currentPage - 1, $this->_pageTemplate) . '">'
            .$prevWord . '</a>';
        }

        //输出第一页
        if($from > 1)
        {
            echo '<a href="' . str_replace('{page}', 1, $this->_pageTemplate) . '">1</a>';
            //输出省略号
            echo '<span>' . $splitWord . '</span>';
        }

        //输出中间页
        for($i = $from; $i <= $to; $i ++)
        {
            if($i != $this->_currentPage)
            {
                echo '<a href="' . str_replace('{page}', $i, $this->_pageTemplate) . '">'
                . $i . '</a>';
            }
            else
            {
                //当前页
                echo '<span class="current">' . $i . '</span>';
            }
        }

        //输出最后页
        if($to < $this->_totalPage)
        {
            echo '<span>' . $splitWord . '</span>';
            echo '<a href="' . str_replace('{page}', $this->_totalPage, $this->_pageTemplate) . '">'
            . $this->_totalPage . '</a>';
        }

        //输出下一页
        if($this->_currentPage < $this->_totalPage)
        {
            echo '<a class="next" href="' . str_replace('{page}', $this->_currentPage + 1, $this->_pageTemplate) . '">'
            . $nextWord . '</a>';
        }
    }

    /**
     * 执行分页输出,代理函数
     *
     * @access public
     * @param string $class
     * @return void
     */
    public function make($class)
    {
        $args = func_get_args();
        array_shift($args);
        $method = 'make' . $class . 'Navigator';

        if(method_exists($this, $method))
        {
            call_user_func_array(array(&$this, $method), $args);
        }
    }
}
