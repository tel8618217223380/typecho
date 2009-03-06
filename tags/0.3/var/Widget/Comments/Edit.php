<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 评论编辑组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Edit extends Widget_Abstract_Comments implements Widget_Interface_Do
{
    /**
     * 标记评论状态
     * 
     * @access private
     * @param integer $coid 评论主键
     * @param string $status 状态
     * @return boolean
     */
    private function mark($coid, $status)
    {
        $comment = $this->db->fetchRow($this->select()
        ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
        
        if ($comment && $this->commentIsWriteable()) {
            /** 不必更新的情况 */
            if ($status == $comment['status']) {
                return false;
            }
        
            /** 更新评论 */
            $this->db->query($this->db->update('table.comments')
            ->rows(array('status' => $status))->where('coid = ?', $coid));
        
            /** 更新相关内容的评论数 */
            if ('approved' == $comment['status'] && 'approved' != $status) {
                $this->db->query($this->db->update('table.contents')
                ->expression('commentsNum', 'commentsNum - 1')->where('cid = ?', $comment['cid']));
            } else if ('approved' != $comment['status'] && 'approved' == $status) {
                $this->db->query($this->db->update('table.contents')
                ->expression('commentsNum', 'commentsNum + 1')->where('cid = ?', $comment['cid']));
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 以数组形式获取coid
     * 
     * @access private
     * @return array
     */
    private function getCoidAsArray()
    {
        $coid = $this->request->coid;
        return $coid ? (is_array($coid) ? $coid : array($coid)) : array();
    }

    /**
     * 标记为待审核
     * 
     * @access public
     * @return void
     */
    public function waitingComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'waiting')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被标记为待审核') : _t('没有评论被标记为待审核'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 标记为垃圾
     * 
     * @access public
     * @return void
     */
    public function spamComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'spam')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被标记为垃圾') : _t('没有评论被标记为垃圾'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 标记为展现
     * 
     * @access public
     * @return void
     */
    public function approvedComment()
    {
        $comments = $this->getCoidAsArray();
        $updateRows = 0;
        
        foreach ($comments as $comment) {
            if ($this->mark($comment, 'approved')) {
                $updateRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($updateRows > 0 ? _t('评论已经被通过') : _t('没有评论被通过'), NULL,
        $updateRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 删除评论
     * 
     * @access public
     * @return void
     */
    public function deleteComment()
    {
        $comments = $this->getCoidAsArray();
        $deleteRows = 0;
        
        foreach ($comments as $coid) {
            $comment = $this->db->fetchRow($this->select()
            ->where('coid = ?', $coid)->limit(1), array($this, 'push'));
            
            if ($comment && $this->commentIsWriteable()) {
                /** 删除评论 */
                $this->db->query($this->db->delete('table.comments')->where('coid = ?', $coid));
            
                /** 更新相关内容的评论数 */
                if ('approved' == $comment['status']) {
                    $this->db->query($this->db->update('table.contents')
                    ->expression('commentsNum', 'commentsNum - 1')->where('cid = ?', $comment['cid']));
                } else if ('approved' != $comment['status']) {
                    $this->db->query($this->db->update('table.contents')
                    ->expression('commentsNum', 'commentsNum + 1')->where('cid = ?', $comment['cid']));
                }
                
                $deleteRows ++;
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($deleteRows > 0 ? _t('评论已经被删除') : _t('没有评论被删除'), NULL,
        $deleteRows > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('contributor');
        $this->onRequest('do', 'waiting')->waitingComment();
        $this->onRequest('do', 'spam')->spamComment();
        $this->onRequest('do', 'approved')->approvedComment();
        $this->onRequest('do', 'delete')->deleteComment();
        
        $response->redirect($this->options()->adminUrl);
    }
}