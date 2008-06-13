<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 配置管理 */
require_once 'Typecho/Config.php';

/** 载入api支持 */
require_once 'Typecho/API.php';

/** 载入request支持 */
require_once 'Typecho/Request.php';

/** 载入request支持 */
require_once 'Typecho/Widget.php';

/** 载入路由异常支持 */
require_once 'Typecho/Router/Exception.php';

/**
 * Typecho组件基类
 *
 * @package Route
 */
class Typecho_Router
{
    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public static $current;

    /**
     * 路径解析值列表
     *
     * @access private
     * @var array
     */
    private static $_parameters = array();

    /**
     * 解析路径
     * 
     * @access public
     * @param mixed $route 路由表
     * @param string $pathInfo 全路径
     * @param string $current 当前键值
     * @param array $matches 匹配值
     * @return array
     */
    public static function match($route, $pathInfo)
    {
        foreach($route as $key => $val)
        {
            if(preg_match('|^' . $val[0] . '$|', $pathInfo, $matches))
            {
                self::$current = $key;
                
                if(is_array($val[2]))
                {
                    unset($matches[0]);
                    self::$_parameters = array_combine($val[2], $matches);
                }
                
                return $val;
            }
        }
        
        return false;
    }

    /**
     * 路由指向函数,返回根据pathinfo和路由表配置的目的文件名
     *
     * @param string $path 目的文件所在目录
     * @return void
     * @throws Typecho_Route_Exception
     */
    public static function target()
    {
        /** 判断是否定义配置 */
        Typecho_Config::need('Router');
        
        /** 获取路由配置 */
        $route = Typecho_Config::get('Router');
        
        /** 获取PATHINFO */
        $pathInfo = Typecho_Request::getPathInfo();

        /** 遍历路由 */
        if(false !== ($val = self::match($route, $pathInfo)))
        {
            list($pattern, $widget, $values, $format) = $val;
            Typecho_API::factory($widget);
        }
        else
        {
            throw new Typecho_Router_Exception(_t('没有找到 %s', $pathInfo), Typecho_Exception::NOTFOUND);
        }
    }

    /**
     * 获取路径解析值
     *
     * @access public
     * @param string $key 路径键值
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getParameter($key, $default = NULL)
    {
        return empty(self::$_parameters[$key]) ? $default : self::$_parameters[$key];
    }

    /**
     * 路由反解析函数
     *
     * @param string $name 路由配置表名称
     * @param string $value 路由填充值
     * @param string $prefix 最终合成路径的前缀
     * @return string
     */
    public static function parse($name, array $value = NULL, $prefix = NULL)
    {
        $route = Typecho_Config::get('Route')->$name;

        if($value)
        {
            //交换数组键值
            $pattern = array();
            foreach($route[2] as $row)
            {
                $pattern[$row] = isset($value[$row]) ? $value[$row] : '{' . $row . '}';
            }

            return Typecho_API::pathToUrl(vsprintf($route[3], $pattern), $prefix);
        }
        else
        {
            return Typecho_API::pathToUrl($route[3], $prefix);
        }
    }
}
