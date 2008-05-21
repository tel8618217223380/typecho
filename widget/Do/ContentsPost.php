<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: ContentsPost.php 199 2008-05-20 16:21:00Z magike.net $
 */

/** 载入提交基类支持 **/
require_once 'DoPost.php';

/**
 * 内容处理类
 *
 * @package Widget
 */
abstract class ContentsPostWidget extends DoPostWidget
{
    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param integer $cid 内容主键
     * @param string $type 内容类型
     * @return array
     */
    public function permalink($cid, $type)
    {
        $select = $this->db->sql()
        ->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.users.`screenName` AS `author`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', TypechoDb::LEFT_JOIN)
        ->where('table.contents.`type` = ?', $type)
        ->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', Typecho::widget('Options')->gmtTime)
        ->group('table.contents.`cid`')
        ->limit(1);
        
        $value = $this->db->fetchRow($select);
    
        /** 生成分类 */
        $categories = $this->db->fetchAll($this->db->sql()
        ->select('table.metas')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $value['cid'])
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')
        ->order('sort', 'ASC'));
        
        $value['category'] = implode('+', Typecho::arrayFlatten($categories, 'slug'));
    
        //生成日期
        $value['year'] = date('Y', $value['created'] + Typecho::widget('Options')->timezone);
        $value['month'] = date('n', $value['created'] + Typecho::widget('Options')->timezone);
        $value['day'] = date('j', $value['created'] + Typecho::widget('Options')->timezone);

        //生成静态链接
        return isset(TypechoConfig::get('Route')->$type) ? TypechoRoute::parse($type, $value, Typecho::widget('Options')->index) : '#';
    }

    /**
     * 设置内容标签
     * 
     * @access protected
     * @param integer $cid
     * @param string $tags
     * @return string
     */
    protected function setTags($cid, $tags)
    {
        $tags = str_replace(array(' ', '，', ' '), ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));

        /** 取出已有tag */
        $existTags = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有tag */
        if($existTags)
        {
            foreach($existTags as $tag)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $tag));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
            }
        }
        
        /** 取出插入tag */
        $insertTags = $this->getTags($tags);
        
        /** 插入tag */
        if($insertTags)
        {
            foreach($insertTags as $tag)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $tag,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
            }
        }
    }
    
    /**
     * 根据tag获取ID
     * 
     * @access protected
     * @param array $tags
     * @return array
     */
    protected function getTags(array $tags)
    {
        $result = array();
        foreach($tags as $tag)
        {
            if(empty($tag))
            {
                continue;
            }
        
            $row = $this->db->fetchRow($this->db->sql()->select('table.metas', '`mid`')
            ->where('`name` = ?', $tag)->limit(1));
            
            if($row)
            {
                $result[] = $row['mid'];
            }
            else
            {
                $result[] = 
                $this->db->query($this->db->sql()->insert('table.metas')
                ->rows(array(
                    'name'  =>  $tag,
                    'slug'  =>  urlencode($tag),
                    'type'  =>  'tag',
                    'count' =>  0,
                    'sort'  =>  0,
                )));
            }
        }
        
        return $result;
    }

    /**
     * 设置分类
     * 
     * @access protected
     * @param integer $cid
     * @param array $categories
     * @return integer
     */
    protected function setCategories($cid, array $categories)
    {
        $categories = array_unique(array_map('trim', $categories));

        /** 取出已有category */
        $existCategories = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有category */
        if($existCategories)
        {
            foreach($existCategories as $category)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $category));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
            }
        }
        
        /** 插入category */
        if($categories)
        {
            foreach($categories as $category)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
            }
        }
    }
    
    /**
     * 插入内容
     * 
     * @access protected
     * @param array $content 内容数组
     * @return integer
     */
    protected function insertContent(array $content)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'uri'           =>  empty($content['uri']) ? NULL : $content['uri'],
            'created'       =>  empty($content['created']) ? Typecho::widget('Options')->gmtTime : $content['created'],
            'modified'      =>  Typecho::widget('Options')->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'author'        =>  Typecho::widget('Access')->uid,
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'commentsNum'   =>  0,
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 'enable' : 'disable',
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 'enable' : 'disable',
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 'enable' : 'disable',
        );
        
        /** 首先插入部分数据 */
        $insertId = $this->db->query($this->db->sql()->insert('table.contents')->rows($insertStruct));
        
        /** 更新缩略名 */
        $slug = Typecho::slugName(empty($content['slug']) ? NULL : $content['slug'], $insertId);
        $this->db->query($this->db->sql()->update('table.contents')
        ->rows(array('slug' => $slug))
        ->where('`cid` = ?', $insertId));

        /** 更新关联数据 */
        if('post' == $insertStruct['type'] || 'draft' == $insertStruct['type'])
        {
            /** 插入分类 */
            $this->setCategories($insertId, !empty($content['category']) && is_array($content['category']) ? 
            $content['category'] : array());
            
            /** 插入标签 */
            $tags = $this->setTags($insertId, empty($content['tags']) ? NULL : $content['tags']);
        }

        return $insertId;
    }
    
    /**
     * 更新内容
     * 
     * @access protected
     * @param array $content 内容数组
     * @param integer $cid 内容主键
     * @return boolean
     */
    protected function updateContent(array $content, $cid)
    {
        /** 首先验证写入权限 */
        if(false === $this->postIsWriteable($cid))
        {
            return false;
        }
    
        /** 构建更新结构 */
        $updateStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'uri'           =>  empty($content['uri']) ? NULL : $content['uri'],
            'modified'      =>  Typecho::widget('Options')->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 'enable' : 'disable',
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 'enable' : 'disable',
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 'enable' : 'disable',
        );
        
        if(!empty($content['created']))
        {
            $updateStruct['created'] = $content['created'];
        }
        
        /** 首先插入部分数据 */
        $updateRows = $this->db->query($this->db->sql()->update('table.contents')->rows($updateStruct)
        ->where('`cid` = ?', $cid));
        
        /** 如果数据不存在 */
        if($updateRows < 1)
        {
            return false;
        }
        
        /** 更新缩略名 */
        $slug = Typecho::slugName(empty($content['slug']) ? NULL : $content['slug'], $cid);
        $this->db->query($this->db->sql()->update('table.contents')
        ->rows(array('slug' => $slug))
        ->where('`cid` = ?', $cid));
        
        /** 更新关联数据 */
        if('post' == $content['type'] || 'draft' == $content['type'])
        {
            /** 插入分类 */
            $this->setCategories($cid, !empty($content['category']) && is_array($content['category']) ? 
            $content['category'] : array());
            
            /** 插入标签 */
            $tags = $this->setTags($cid, empty($content['tags']) ? NULL : $content['tags']);
        }

        return true;
    }
    
    /**
     * 删除内容
     * 
     * @access protected
     * @param integer $cid 内容主键
     * @return boolean
     */
    protected function deleteContent($cid)
    {
        /** 首先验证写入权限 */
        if(false === ($post = $this->postIsWriteable($cid)))
        {
            return false;
        }

        $deleteRows = $this->db->query($this->db->sql()->delete('table.contents')
        ->where('`cid` = ?', $cid));
        
        /** 如果数据不存在 */
        if($deleteRows < 1)
        {
            return false;
        }
        
        /** 更新关联数据 */
        if('post' == $post['type'] || 'draft' == $post['type'])
        {
            /** 删除分类 */
            $this->setCategories($cid, array());
            
            /** 删除标签 */
            $this->setTags($cid, NULL);
            
            /** 删除评论 */
            $this->db->query($this->db->sql()->delete('table.comments')
            ->where('`cid` = ?', $cid));
        }
        
        return true;
    }

    /**
     * 检测当前用户是否具备修改权限
     * 
     * @access protected
     * @param integer $userId 文章的作者id
     * @return boolean
     */
    protected function haveContentPermission($userId)
    {
        if(!Typecho::widget('Access')->pass('editor', true))
        {
            if($userId != Typecho::widget('Access')->uid)
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * 内容是否可以被修改
     * 
     * @access protected
     * @param integer $cid 内容主键
     * @return mixed
     */
    protected function postIsWriteable($cid)
    {
        $post = $this->db->fetchRow($this->db->sql()->select('table.contents')
        ->where('`cid` = ?', $cid)->limit(1));
        
        if($post && $this->haveContentPermission($post['author']))
        {
            return $post;
        }
        
        return false;
    }
}
