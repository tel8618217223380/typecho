<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 数据库Mysql适配器
 *
 * @package Db
 */
class TypechoMysql implements TypechoDbAdapter
{
    /**
     * 数据库连接函数
     *
     * @param string $host 数据库服务器地址
     * @param string $port 数据库端口
     * @param string $user 数据库用户名
     * @param string $password 数据库密码
     * @param string $charset 数据库字符集
     * @throws TypechoDbException
     * @return resource
     */
    public function connect($host, $port, $db, $user, $password, $charset = NULL)
    {
        if($dbLink = @mysql_connect($host . ':' . $port, $user, $password))
        {
            if(@mysql_select_db($db, $dbLink))
            {
                if($charset)
                {
                    $this->query("SET NAMES '{$charset}'");
                }
                return $dbLink;
            }
        }

        throw new TypechoDbException(__TYPECHO_DEBUG__ ? 
        mysql_error() : _t('数据库连接错误'), __TYPECHO_EXCEPTION_503__);
    }
    
    /**
     * 执行数据库查询
     *
     * @param string $sql 查询字符串
     * @param boolean $op 查询读写开关
     * @throws TypechoDbException
     * @return resource
     */
    public function query($sql, $op = __TYPECHO_DB_READ__)
    {
        if($resource = @mysql_query($sql))
        {
            return $resource;
        }
        
        throw new TypechoDbException(__TYPECHO_DEBUG__ ? 
        mysql_error() : _t('数据库查询错误'), __TYPECHO_EXCEPTION_500__);
    }
    
    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询返回资源标识
     * @return array
     */
    public function fetch($resource)
    {
        return mysql_fetch_assoc($resource);
    }
    
    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quotes($string)
    {
        return str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string);
    }

    /**
     * 取出最后一次查询影响的行数
     *
     * @param resource $resource 查询返回资源标识
     * @return integer
     */
    public function affectedRows($resource)
    {
        return mysql_affected_rows($resource);
    }
    
    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询返回资源标识
     * @return integer
     */
    public function lastInsertId($resource)
    {
        return mysql_insert_id($resource);
    }
}
