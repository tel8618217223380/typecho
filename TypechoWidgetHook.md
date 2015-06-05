# /includes/library/Widget/WidgetHook.php #
  * 作者:qining
  * 版权:Copyright (c) 2008 Typecho team (http://www.typecho.org)
  * 协议:GNU General Public License 2.0
  * 版本:$Id$

## 类:TypechoWidgetHook ##
  * 描述 - `Typecho Blog Platform`

### private static <sup>array</sup> `$_hooks` ###
  * 位置: 文件 **/includes/library/Widget/WidgetHook.php** 第 **24** 行
  * 说明: 当前所有钩子

### public static `register`(<sup>string</sup> `$hookName`, <sup>string</sup> `$functionName`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$hookName |string    |_N/A_     |组件名称|
|$functionName|string    |_N/A_     |钩子函数名称|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget/WidgetHook.php** 第 **34** 行
  * 说明: 注册钩子

### public static `call`(<sup>string</sup> `$hookName`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$hookName |string    |_N/A_     |组件名称|

  * 返回: **array**
  * 位置: 文件 **/includes/library/Widget/WidgetHook.php** 第 **51** 行
  * 说明: 运行钩子

### public static `name`(<sup>string</sup> `$fileName`, <sup>string</sup> `$component`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$fileName |string    |_N/A_     |钩子文件名称|
|$component|string    |NULL      |钩子部件名称|

  * 返回: **string**
  * 位置: 文件 **/includes/library/Widget/WidgetHook.php** 第 **76** 行
  * 说明: 返回标准化钩子名称