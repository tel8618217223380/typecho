<?php
/**
 * 按日期归档列表组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 按日期归档列表组件
 * 
 * @fixme 交给缓存
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Contents_Post_Date extends Typecho_Widget
{
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
        
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 设置参数默认值 */
        $this->parameter->setDefault('format=Y-m&type=month');
    
        $resource = $this->db->query($this->db->select('created')->from('table.contents')
        ->where('type = ?', 'post')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.created < ?', $this->options->gmtTime)
        ->order('table.contents.created', Typecho_Db::SORT_DESC));
        
        $result = array();
        while ($post = $this->db->fetchRow($resource)) {
            $date = gmdate($this->parameter->format, $post['created'] + $this->options->timezone);
            if (isset($result[$date])) {
                $result[$date]['count'] ++;
            } else {
                $result[$date]['year'] = gmdate('Y', $post['created']);
                $result[$date]['month'] = gmdate('m', $post['created']);
                $result[$date]['day'] = gmdate('d', $post['created']);
                $result[$date]['date'] = $date;
                $result[$date]['count'] = 1;
            }
        }
        
        foreach ($result as $row) {
            $row['permalink'] = Typecho_Router::url('archive_' . $this->parameter->type, $row, $this->widget('Widget_Options')->index);
            $this->push($row);
        }
    }
}
