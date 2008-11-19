<?php
/**
 * 登录动作
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 登录组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Login extends Widget_Abstract_Users implements Widget_Interface_Do
{
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 如果已经登录 */
        if ($this->user->hasLogin()) {
            /** 直接返回 */
            $this->response->redirect($this->options->index);
        }
        
        /** 初始化验证类 */
        $validator = new Typecho_Validate();
        $validator->addRule('name', 'required', _t('请输入用户名'));
        $validator->addRule('password', 'required', _t('请输入密码'));
        
        /** 截获验证异常 */
        try {
            $validator->run($this->request->from('name', 'password'));
        }
         catch (Typecho_Validate_Exception $e) {
            /** 设置提示信息 */
            $this->widget('Widget_Notice')->set($e->getMessages());
            $this->response->goBack();
        }
        
        /** 开始验证用户 **/
        $user = $this->db->fetchRow($this->select()
        ->where('name = ?', $this->request->name)
        ->limit(1));
        
        /** 比对密码 */
        if ($user && $user['password'] == md5($this->request->password)) {
            $this->user->login($user['uid'], $user['password'], sha1(Typecho_Common::randString(20)),
            1 == $this->request->remember ? $this->options->gmtTime + $this->options->timezone + 30*24*3600 : 0);
        } else {
            $this->widget('Widget_Notice')->set(_t('无法找到匹配的用户'), NULL, 'error');
            $this->response->redirect($this->options->loginUrl . ((NULL === $this->request->referer) ? 
            NULL : '?referer=' . urlencode($this->request->referer)));
        }
        
        /** 跳转验证后地址 */
        if (NULL != $this->request->referer) {
            $this->response->redirect($this->request->referer);
        } else {
            $this->response->redirect($this->options->adminUrl);
        }
    }
}
