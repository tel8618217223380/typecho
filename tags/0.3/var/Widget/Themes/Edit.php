<?php
/**
 * 编辑风格
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑风格组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Themes_Edit extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 更换外观
     * 
     * @access public
     * @param string $theme 外观名称
     * @return void
     */
    public function changeTheme($theme)
    {
        $theme = trim($theme, './');
        if (is_dir(__TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' . $theme)) {
            $this->update(array('value' => $theme), $this->db->sql()->where('name = ?', 'theme'));
            $this->widget('Widget_Notice')->set(_t("外观已经改变"), NULL, 'success');
            $this->response->goBack();
        } else {
            throw new Typecho_Widget_Exception(_t('您选择的风格不存在'), 404);
        }
    }
    
    /**
     * 编辑外观文件
     * 
     * @access public
     * @param string $theme 外观名称
     * @param string $file 文件名
     * @return void
     */
    public function editThemeFile($theme, $file)
    {
        $path = __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' . trim($theme, './') . '/' . trim($file, './');
        
        if (is_file($path) && is_writeable($path)) {
            file_put_contents($path, $this->request->content);
            $this->widget('Widget_Notice')->set(_t("文件 %s 的更改已经保存", $file), NULL, 'success');
            $this->response->goBack();
        } else {
            throw new Typecho_Widget_Exception(_t('您编辑的文件不存在'), 404);
        }
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 需要管理员权限 */
        $this->user->pass('administrator');
        $this->onRequest('change')->changeTheme($this->request->change);
        
        $this->onRequest('edit')->onRequest('theme')
        ->editThemeFile($this->request->theme, $this->request->edit);
        
        changeTheme($this->request->change);
        $this->response->redirect($this->options->adminUrl);
    }
}