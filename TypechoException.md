# /includes/library/Exception.php #
  * 作者:qining
  * 版权:Copyright (c) 2008 Typecho team (http://www.typecho.org)
  * 协议:GNU General Public License 2.0
  * 版本:$Id: Exception.php 58 2008-03-21 15:47:20Z magike.net $

## 类:TypechoException ##
  * 继承自 - `Exception`
  * 描述 - `Typecho异常基类主要重载异常打印函数`

### public `__construct`(<sup>mixed</sup> `$message`, <sup>integer</sup> `$code`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$message  |mixed     |_N/A_     |异常消息|
|$code     |integer   |0         |异常代码|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Exception.php** 第 **34** 行
  * 说明: `异常基类构造函数,重载以增加$code的默认参数`

### public static `parse`(<sup>string</sup> `$exceptionString`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$exceptionString|string    |_N/A_     |异常字符串|

  * 返回: **string**
  * 位置: 文件 **/includes/library/Exception.php** 第 **46** 行
  * 说明: `解析异常字符串`


## 函数:`__toString`() ##
  * 返回: **void**
  * 位置: 文件 **/includes/library/Exception.php** 第 **89** 行
  * 说明: 打印异常错误


## 函数:`exceptionHandler`(<sup>string</sup> `$exception`) ##
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$exception|string    |_N/A_     |          |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Exception.php** 第 **120** 行
  * 说明: 异常截获函数


## 函数:`errorHandler`(<sup>integer</sup> `$errno`, <sup>string</sup> `$errstr`, <sup>string</sup> `$errfile`, <sup>integer</sup> `$errline`) ##
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$errno    |integer   |_N/A_     |错误代码|
|$errstr   |string    |NULL      |错误描述|
|$errfile  |string    |NULL      |错误文件|
|$errline  |integer   |NULL      |错误代码行|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Exception.php** 第 **184** 行
  * 说明: 错误截获函数