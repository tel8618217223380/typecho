<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Widget.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 异常基类 */
require_once 'Exception.php';

/** 载入异常支持 */
require_once 'Widget/WidgetException.php';

/** 载入导航页支持 */
require_once 'Widget/WidgetNavigator.php';

/** 载入api支持 */
require_once 'Typecho.php';

/**
 * Typecho组件基类
 *
 * @package Widget
 */
abstract class TypechoWidget
{
    /**
     * 内部数据堆栈
     *
     * @access protected
     * @var array
     */
    protected $_stack = array();

    /**
     * 数据堆栈每一行
     *
     * @access protected
     * @var array
     */
    protected $_row = array();

    /**
     * 将类本身赋值
     *
     * @param string $variable 变量名
     * @return void
     */
    public function to(&$variable)
    {
        if(empty($variable) ||
        ($variable instanceof TypechoWidget && !$variable->have()))
        {
            $variable = $this;
        }
    }

    /**
     * 格式化解析堆栈内的所有数据
     *
     * @param string $format 数据格式
     * @return void
     */
    public function parse($format)
    {
        $_rowsKey = array();

        //将数据格式化
        foreach($this->_row as $key => $val)
        {
            $_rowsKey[] = '{' . $key . '}';
        }

        foreach($this->_stack as $val)
        {
            echo str_replace($_rowsKey, $val, $format);
        }
        
        reset($this->_row);
        reset($this->_stack);
    }

    /**
     * 将每一行的值压入堆栈
     *
     * @param array $value 每一行的值
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        if(empty($this->_row))
        {
            $this->_row = $value;
        }

        $this->_stack[] = $value;
        return $value;
    }
    
    /**
     * 根据余数输出
     * 
     * @access public
     * @param string $param 需要输出的值
     * @return void
     */
    public function alt()
    {
        $args = func_get_args();
        echo $args[key($this->_stack) % func_num_args()];
    }
    
    /**
     * 如果是第一个则输出字符串
     * 
     * @access public
     * @param string $string
     * @return void
     */
    public function first($string)
    {
        echo (0 === key($this->_stack)) ? $string : NULL;
    }

    /**
     * 返回堆栈是否为空
     *
     * @return boolean
     */
    public function have()
    {
        return !empty($this->_stack);
    }

    /**
     * 返回堆栈每一行的值
     *
     * @return array
     */
    public function get()
    {
        $this->_row = current($this->_stack);
        next($this->_stack);
        return $this->_row;
    }
    
    /**
     * 重置堆栈
     *
     * @return void
     */
    public function reset()
    {
        reset($this->_stack);
    }

    /**
     * 魔术函数,用于挂接其它函数
     *
     * @access public
     * @param string $name 函数名
     * @param array $args 函数参数
     * @return void
     */
    public function __call($name, $args)
    {
        echo isset($this->_row[$name]) ? $this->_row[$name] : NULL;
    }

    /**
     * 魔术函数,用于获取内部变量
     *
     * @access public
     * @param string $name 变量名
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_row[$name]) ? $this->_row[$name] : NULL;
    }
    
    /**
     * 设定堆栈每一行的值
     *
     * @param string $name 值对应的键值
     * @param mixed $value 相应的值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_row[$name] = $value;
    }
    
    /**
     * 验证堆栈值是否存在
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_row[$name]);
    }

    /**
     * 必须实现的执行函数
     *
     * @return void
     */
    public function render()
    {
        throw new TypechoWidgetException(_t('render方法必须在继承子类中实现'), TypechoException::RUNTIME);
    }
}
