<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Mysql.php 89 2008-03-31 00:10:57Z magike.net $
 */

/** 数据库适配器接口 */
require_once 'Typecho/Db/Adapter/Pdo.php';

/**
 * 数据库Pdo_Pgsql适配器
 *
 * @package Db
 */
class Typecho_Db_Adapter_Pdo_Pgsql extends Typecho_Db_Adapter_Pdo
{
    /**
     * 初始化数据库 
     * 
     * @param Typecho_Config $config 数据库配置
     * @access public
     * @return PDO
     */
    public function init(Typecho_Config $config)
    {
        $pdo = new PDO("pgsql:dbname={$config->database};host={$config->host};port={$config->port}", $config->user, $config->password);
        $pdo->exec("SET NAMES '{$config->charset}'");
        return $pdo;
    }
    
    /**
     * 对象引号过滤
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string)
    {
        return '"' . $string . '"';
    }

    /**
     * 合成查询语句
     *
     * @access public
     * @param array $sql 查询对象词法数组
     * @return string
     */
    public function parseSelect(array $sql)
    {
        if(!empty($sql['join']))
        {
            foreach($sql['join'] as $val)
            {
                list($table, $condition, $op) = $val;
                $sql['table'] = "{$sql['table']} {$op} JOIN {$table} ON {$condition}";
            }
        }
    
        $sql['limit'] = empty($sql['limit']) ? NULL : ' LIMIT ' . $sql['limit'];
        $sql['offset'] = empty($sql['offset']) ? NULL : ' OFFSET ' . $sql['offset'];

        return 'SELECT ' . $sql['fields'] . ' FROM ' . $sql['table'] .
        $sql['where'] . $sql['group'] . $sql['order'] . $sql['limit'] . $sql['offset'];
    }

    /**
     * 获取数据库版本
     * 
     * @access public
     * @return unknown
     */
    public function version()
    {
        $resource = $this->query('SELECT VERSION() AS version');
        $rows = $this->fetch($resource);
        return 'PostgreSQL ' . $rows['version'];
    }
}