# /includes/library/I18n/GetText.php #
  * 作者:Michael Wallner <mike@php.net>
  * 版权:2004-2005 Michael Wallner
  * 协议:BSD, revised
  * 版本:$Id: GetText.php 46 2008-03-10 13:59:36Z magike.net $

## 类:TypechoGetText ##
  * 描述 - `File Gettext`

### private static <sup>resource</sup> `$_handle` ###
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **24** 行
  * 说明: `当前mo文件句柄`

### private static <sup>array</sup> `$_files` ###
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **32** 行
  * 说明: `已经载入的文件列表`

### public static <sup>array</sup> `$strings` ###
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **40** 行
  * 说明: `翻译字符串列表`

### public static <sup>array</sup> `$meta` ###
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **48** 行
  * 说明: `mo文件头部信息`

### public static `init`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **55** 行
  * 说明: `initialize i18n`

### private static `_read`(<sup>int</sup> `$bytes`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$bytes    |int       |_N/A_     |_N/A_     |

  * 返回: **mixed**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **72** 行
  * 说明: `_read`

### private static `_readInt`(<sup>bool</sup> `$bigendian`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$bigendian|bool      |_N/A_     |_N/A_     |

  * 返回: **int**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **88** 行
  * 说明: `_readInt`

### private static `_readStr`(<sup>array</sup> `$params`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$params   |array     |_N/A_     |associative|

  * 返回: **string**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **101** 行
  * 说明: `_readStr                             of the string`

### private static `meta2array`(<sup>string</sup> `$meta`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$meta     |string    |_N/A_     |_N/A_     |

  * 返回: **array**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **115** 行
  * 说明: `meta2array`

### public static `load`(<sup>string</sup> `$file`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$file     |string    |_N/A_     |文件名 |

  * 返回: **boolean**
  * 位置: 文件 **/includes/library/I18n/GetText.php** 第 **135** 行
  * 说明: `载入mo文件`