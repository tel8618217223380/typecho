<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 插件处理类
 *
 * @category typecho
 * @package Plugin
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Plugin
{
    /**
     * 所有激活的插件
     * 
     * @access private
     * @var array
     */
    private static $_plugins = array();
    
    /**
     * 已经加载的文件
     * 
     * @access private
     * @var array
     */
    private static $_required = array();
    
    /**
     * 实例化的插件对象
     * 
     * @access private
     * @var array
     */
    private static $_instances;
    
    /**
     * 临时存储变量
     * 
     * @access private
     * @var array
     */
    private static $_tmp = array();
    
    /**
     * 唯一句柄
     * 
     * @access private
     * @var string
     */
    private $_handle;
    
    /**
     * 组件
     * 
     * @access private
     * @var string
     */
    private $_component;
    
    /**
     * 插件初始化
     * 
     * @access public
     * @param string $handle 插件
     * @return void
     */
    public function __construct($handle)
    {
        /** 初始化变量 */
        $this->_handle = $handle;
    }

    /**
     * 插件初始化
     * 
     * @access public
     * @param array $plugins 插件列表
     * @param mixed $callback 获取插件系统变量的代理函数
     * @return void
     */
    public static function init(array $plugins)
    {
        $plugins['activated'] = array_key_exists('activated', $plugins) ? $plugins['activated'] : array();
        $plugins['handles'] = array_key_exists('handles', $plugins) ? $plugins['handles'] : array();
        $plugins['files'] = array_key_exists('files', $plugins) ? $plugins['files'] : array();
        
        /** 初始化变量 */
        self::$_plugins = $plugins;
    }
    
    /**
     * 获取实例化插件对象
     * 
     * @access public
     * @return Typecho_Plugin
     */
    public static function factory($handle)
    {
        return isset(self::$_instances[$handle]) ? self::$_instances[$handle] :
        (self::$_instances[$handle] = new Typecho_Plugin($handle));
    }
    
    /**
     * 激活插件
     * 
     * @access public
     * @param string $pluginName 插件名称
     * @return void
     */
    public static function activate($pluginName)
    {
        self::$_plugins['activated'][$pluginName] = self::$_tmp;
        self::$_tmp = array();
    }
    
    /**
     * 禁用插件
     * 
     * @access public
     * @param string $pluginName 插件名称
     * @return void
     */
    public static function deactivate($pluginName)
    {
        /** 去掉所有相关文件 */
        foreach (self::$_plugins['activated'][$pluginName]['files'] as $handle => $files) {
            self::$_plugins['files'][$handle] = array_diff(self::$_plugins['files'][$handle], $files);
        }
        
        /** 去掉所有相关回调函数 */
        foreach (self::$_plugins['activated'][$pluginName]['handles'] as $handle => $handles) {
            self::$_plugins['handles'][$handle] = array_diff(self::$_plugins['handles'][$handle], $handles);
        }
        
        /** 禁用当前插件 */
        unset(self::$_plugins['activated'][$pluginName]);
    }
    
    /**
     * 导出当前插件设置
     * 
     * @access public
     * @return array
     */
    public static function export()
    {
        return self::$_plugins;
    }
    
    /**
     * 获取插件文件的头信息
     * 
     * @access public
     * @param string $pluginFile 插件文件路径
     * @return void
     */
    public static function parseInfo($pluginFile)
    {
        $tokens = token_get_all(file_get_contents($pluginFile));
        $isDoc = false;
        $isFunction = false;
        $isClass = false;
        $isInClass = false;
        $isInFunction = false;
        $isDefined = false;
        $current = NULL;
        
        /** 初始信息 */
        $info = array(
            'description' => '',
            'title'       => '',
            'author'      => '',
            'homepage'    => '',
            'version'     => '',
            'activate'    => false,
            'deactivate'  => false,
            'config'      => false
        );
        
        $map = array(
            'package'   =>  'title',
            'author'    =>  'author',
            'link'      =>  'homepage',
            'version'   =>  'version'
        );

        foreach ($tokens as $token) {
            /** 获取doc comment */
            if (!$isDoc && is_array($token) && T_DOC_COMMENT == $token[0]) {
            
                /** 分行读取 */
                $described = false;
                $lines = preg_split("(\r|\n)", $token[1]);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && '*' == $line[0]) {
                        $line = trim(substr($line, 1));
                        if (!$described && !empty($line) && '@' == $line[0]) {
                            $described = true;
                        }
                        
                        if (!$described && !empty($line)) {
                            $info['description'] .= $line . "\n";
                        } else if ($described && !empty($line) && '@' == $line[0]) {
                            $info['description'] = trim($info['description']);
                            $line = trim(substr($line, 1));
                            $args = explode(' ', $line);
                            $key = array_shift($args);
                            
                            if (isset($map[$key])) {
                                $info[$map[$key]] = trim(implode(' ', $args));
                            }
                        }
                    }
                }
                
                $isDoc = true;
            }
            
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_FUNCTION:
                        $isFunction = true;
                        break;
                    case T_IMPLEMENTS:
                        $isClass = true;
                        break;
                    case T_WHITESPACE:
                        break;
                    case T_STRING:
                        $string = strtolower($token[1]);
                        switch ($string) {
                            case 'typecho_plugin_interface':
                                $isInClass = true;
                                break;
                            case 'activate':
                            case 'deactivate':
                            case 'config':
                                if ($isFunction) {
                                    $current = $string;
                                }
                                break;
                            default:
                                if (!empty($current) && $isInFunction && $isInClass) {
                                    $info[$current] = true;
                                }
                                break;
                        }
                        break;
                    default:
                        if (!empty($current) && $isInFunction && $isInClass) {
                            $info[$current] = true;
                        }
                        break;
                }
            } else {
                $token = strtolower($token);
                switch ($token) {
                    case '{':
                        if ($isDefined) {
                            $isInFunction = true;
                        }
                        break;
                    case '(':
                        if ($isFunction && !$isDefined) {
                            $isDefined = true;
                        }
                        break;
                    case '}':
                    case ';':
                        $isDefined = false;
                        $isFunction = false;
                        $isInFunction = false;
                        $current = NULL;
                        break;
                    default:
                        if (!empty($current) && $isInFunction && $isInClass) {
                            $info[$current] = true;
                        }
                        break;
                }
            }
        }
        
        return $info;
    }
    
    /**
     * 需要预先包含的文件
     * 
     * @access public
     * @param string $file 文件名称(相对路径)
     * @return void
     */
    public function need($file)
    {
        $handle = $this->_handle . ':' . $this->_component;
        self::$_plugins['files'][$handle][] = $file;
        self::$_tmp['files'][$handle][] = $file;
    }
    
    /**
     * 设置回调函数
     * 
     * @access public
     * @param string $component 当前组件
     * @param mixed $value 回调函数
     * @return void
     */
    public function __set($component, $value)
    {
        $component = $this->_handle . ':' . $component;
        self::$_plugins['handles'][$component][] = $value;
        self::$_tmp['handles'][$component][] = $value;
    }
    
    /**
     * 通过魔术函数设置当前组件位置
     * 
     * @access public
     * @param string $component 当前组件
     * @return Typecho_Plugin
     */
    public function __get($component)
    {
        $this->_component = $component;
        return $this;
    }
    
    /**
     * 回调处理函数
     * 
     * @access public
     * @param string $component 当前组件
     * @param string $args 参数
     * @return mixed
     */
    public function __call($component, $args)
    {
        $component = $this->_handle . ':' . $component;
        $last = count($args);
        $args[$last] = $last > 0 ? $args[0] : false;
        
        if (isset(self::$_required[$component]) && isset(self::$_plugins['files'][$component])) {
            self::$_required[$component] = true;
            foreach (self::$_plugins['files'][$component] as $file) {
                require_once $file;
            }
        }
    
        if (isset(self::$_plugins['handles'][$component])) {
            $args[$last] = NULL;
            foreach (self::$_plugins['handles'][$component] as $callback) {
                $args[$last] = call_user_func_array($callback, $args);
            }
        }
        
        return $args[$last];
    }
}
