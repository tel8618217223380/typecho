# /includes/library/Route.php #
  * 作者:qining
  * 版权:Copyright (c) 2008 Typecho team (http://www.typecho.org)
  * 协议:GNU General Public License 2.0
  * 版本:$Id: Route.php 57 2008-03-20 08:06:57Z magike.net $

## 类:TypechoRoute ##
  * 描述 - `Typecho组件基类`
  * 包 - `Route`

### public static `target`(<sup>string</sup> `$path`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$path     |string    |_N/A_     |目的文件所在目录|

  * 返回: **string**
  * 抛出异常: **TypechoRouteException**
  * 位置: 文件 **/includes/library/Route.php** 第 **28** 行
  * 说明: 路由指向函数,返回根据pathinfo和路由表配置的目的文件名

### public static `handle`(<sup>string</sup> `$path`, <sup>string</sup> `$get`, <sup>string</sup> `$default`, <sup>array</sup> `$deny`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$path     |string    |_N/A_     |目的文件所在目录|
|$get      |string    |'mod'     |获取目的文件的GET值|
|$default  |string    |NULL      |当目的不存在时默认的文件|
|$deny     |array     |array     |禁止访问的handle|

  * 返回: **string**
  * 位置: 文件 **/includes/library/Route.php** 第 **62** 行
  * 说明: 路由指向函数,返回根据GET配置的目的文件名

### public static `parse`(<sup>string</sup> `$name`, <sup>string</sup> `$value`, <sup>string</sup> `$prefix`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |路由配置表名称|
|$value    |string    |NULL      |路由填充值|
|$prefix   |string    |NULL      |最终合成路径的前缀|

  * 返回: **string**
  * 位置: 文件 **/includes/library/Route.php** 第 **89** 行
  * 说明: 路由反解析函数