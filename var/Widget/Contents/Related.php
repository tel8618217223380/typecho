<?php
/**
 * 相关内容
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 相关内容组件(根据标签关联)
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Related extends Widget_Abstract_Contents
{
    /**
     * 构造函数,初始化数据
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->parameter->setDefault('limit=5');
    
        if ($this->parameter->tags) {
            $tagsGroup = implode(',', Typecho_API::arrayFlatten($this->parameter->tags, 'mid'));
            $this->db->fetchAll($this->select()
            ->selectAlso(array('COUNT(table.contents.cid)' => 'contentsNum'))
            ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.relationships.mid in (' . $tagsGroup . ')')
            ->where('table.contents.cid <> ?', $this->parameter->cid)
            ->where('table.contents.password IS NULL')
            ->where('table.contents.created < ?', $this->options->gmtTime)
            ->where('table.contents.type = ?', $this->parameter->type)
            ->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->group('table.contents.cid')->limit($this->parameter->limit), array($this, 'push'));
        }
    }
}
