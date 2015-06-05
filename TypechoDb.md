# /includes/library/Db.php #
  * 作者:qining
  * 版权:Copyright (c) 2008 Typecho team (http://www.typecho.org)
  * 协议:GNU General Public License 2.0
  * 版本:$Id: Db.php 54 2008-03-19 07:08:58Z magike.net $

## 类:TypechoDb ##
  * 描述 - `包含获取数据支持方法的类.必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,__TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__`
  * 包 - `Db`

### private <sup>TypechoDbAdapter</sup> `$_adapter` ###
  * 位置: 文件 **/includes/library/Db.php** 第 **48** 行
  * 说明: 数据库适配器

### private <sup>TypechoDbQuery</sup> `$_query` ###
  * 位置: 文件 **/includes/library/Db.php** 第 **54** 行
  * 说明: sql词法构建器

### private static <sup>TypechoDb</sup> `$_instance` ###
  * 位置: 文件 **/includes/library/Db.php** 第 **60** 行
  * 说明: 实例化的数据库对象

### public `__construct`(`string $adapter`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$adapter  |string    |_N/A_     |数据库适配器名称|

  * 返回: **void**
  * 抛出异常: **TypechoDbException**
  * 位置: 文件 **/includes/library/Db.php** 第 **69** 行
  * 说明: 数据库类构造函数

### public `sql`() ###
  * 返回: **TypechoDbQuery**
  * 位置: 文件 **/includes/library/Db.php** 第 **101** 行
  * 说明: 获取SQL词法构建器实例化对象

### public static `get`() ###
  * 返回: **TypechoDb**
  * 位置: 文件 **/includes/library/Db.php** 第 **118** 行
  * 说明: 获取数据库实例化对象用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次

### public `query`(`mixed $query, boolean $op`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$query    |mixed     |_N/A_     |查询语句或者查询对象|
|$op       |boolean   |TYPECHO\_DB\_READ|数据库读写状态|

  * 返回: **mixed**
  * 位置: 文件 **/includes/library/Db.php** 第 **136** 行
  * 说明: 执行查询语句

### public `fetchAll`(`mixed $query, array $filter`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$query    |mixed     |_N/A_     |查询对象|
|$filter   |array     |NULL      |行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中|

  * 返回: **array**
  * 位置: 文件 **/includes/library/Db.php** 第 **170** 行
  * 说明: 一次取出所有行

### public `fetchRow`(`mixed $query, array $filter`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$query    |mixed     |_N/A_     |查询对象|
|$filter   |array     |NULL      |行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中|

  * 返回: **array**
  * 位置: 文件 **/includes/library/Db.php** 第 **194** 行
  * 说明: 一次取出一行