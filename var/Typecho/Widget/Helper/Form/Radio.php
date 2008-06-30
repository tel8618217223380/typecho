<?php
/**
 * 单选框帮手
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Widget_Helper_Form_Abstract */
require_once 'Typecho/Widget/Helper/Form/Abstract.php';

/**
 * 单选框帮手类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Widget_Helper_Form_Radio extends Typecho_Widget_Helper_Form_Abstract
{
    /**
     * 重载构造函数
     * 
     * @access public
     * @param string $inputName 表单名称
     * @param array $options 选择项
     * @param string $value 表单默认值
     * @param string $label 表单标题
     * @param string $description 表单描述
     * @return void
     */
    public function __construct($inputName, array $options = NULL, $value = NULL, $label = NULL, $description = NULL)
    {
        /** 设置表单输入项标签 */
        $this->setTagName('tr');
        $this->name = $inputName;
        
        /** 设置左边 */
        $this->leftTd = new Typecho_Widget_Helper_Layout('td');
        $this->label = new Typecho_Widget_Helper_Layout('label');
        $this->label->setAttribute('for', $inputName)->appendTo($this->leftTd);
        $this->addItem($this->leftTd);
        
        /** 设置右边 */
        $this->rightTd = new Typecho_Widget_Helper_Layout('td');
        $this->addItem($this->rightTd);
        $this->label($label);
        
        if(!empty($options))
        {
            foreach($options as $optionValue => $optionLabel)
            {
                $this->addRadio($optionValue, $optionLabel);
            }
        }
        
        /** 如果有错误提示 */
        $notice = Typecho_Request::getCookie('form_message');
        if(!empty($notice[$inputName]))
        {
            $detail = new Typecho_Widget_Helper_Layout('span');
            $detail->setAttribute('class', 'detail')->html($notice[$inputName])->appendTo($this->rightTd);
        }
        
        if(!empty($description))
        {
            $this->description($description);
        }
        
        if(NULL !== $value)
        {
            $this->value($value);
        }
    }
    
    /**
     * 增加选择项
     * 
     * @access public
     * @param string $value 选择值
     * @param string $label 选择项标题
     * @return Typecho_Widget_Helper_Form_Select
     */
    public function addRadio($value, $label)
    {
        $this->input[$value] = new Typecho_Widget_Helper_Layout('input');
        $this->input[$value]->setAttribute('name', $this->name)
        ->setAttribute('type', 'radio')
        ->setAttribute('value', $value)
        ->setAttribute('id', $this->name . '-' . $value)
        ->appendTo($this->rightTd);
        
        $labelItem = new Typecho_Widget_Helper_Layout('label');
        $labelItem->setAttribute('for', $this->name . '-' . $value)
        ->html($label . '&nbsp;')->appendTo($this->rightTd);
        return $this;
    }
    
    /**
     * 设置表单项默认值
     * 
     * @access public
     * @param string $value 表单项默认值
     * @return Typecho_Widget_Helper_Form_Select
     */
    public function value($value)
    {
        if(isset($this->input[$value]))
        {
            $this->input[$value]->setAttribute('checked', 'true');
        }
        return $this;
    }
}
