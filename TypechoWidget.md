# /includes/library/Widget.php #
  * 作者:qining
  * 版权:Copyright (c) 2008 Typecho team (http://www.typecho.org)
  * 协议:GNU General Public License 2.0
  * 版本:$Id: Widget.php 57 2008-03-20 08:06:57Z magike.net $

## 函数:`widget`(`string $widget, mixed $param`) ##
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$widget   |string    |_N/A_     |组件名称|
|$param    |mixed     |_N/A_     |参数    |

  * 返回: **TypechoWidget**
  * 位置: 文件 **/includes/library/Widget.php** 第 **30** 行
  * 说明: Typecho组件调用


## 函数:`_cookie`(`string $name`) ##
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |cookie名称|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **52** 行
  * 说明: 直接输出cookie信息


## 类:TypechoWidget ##
  * 描述 - `Typecho组件基类`
  * 包 - `Widget`

### private static <sup>array</sup> `$_registry` ###
  * 位置: 文件 **/includes/library/Widget.php** 第 **70** 行
  * 说明: 保存所有实例化的widget对象

### protected <sup>array</sup> `$_stack` ###
  * 位置: 文件 **/includes/library/Widget.php** 第 **78** 行
  * 说明: 内部数据堆栈

### protected <sup>array</sup> `$_row` ###
  * 位置: 文件 **/includes/library/Widget.php** 第 **86** 行
  * 说明: 数据堆栈每一行

### public `__construct`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **94** 行
  * 说明: 构造函数,将实例化对象放入全局堆栈

### public `to`(`string $variable`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$variable |string    |_N/A_     |变量名 |

  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **105** 行
  * 说明: 将类本身赋值

### public `registry`(`string $name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |对象名 |

  * 返回: **TypechoWidget**
  * 位置: 文件 **/includes/library/Widget.php** 第 **120** 行
  * 说明: 获取静态缓存中的实例化对象

### public `parse`(`string $format`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$format   |string    |_N/A_     |数据格式|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **131** 行
  * 说明: 格式化解析堆栈内的所有数据

### public `push`(`array $value`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$value    |array     |_N/A_     |每一行的值|

  * 返回: **array**
  * 位置: 文件 **/includes/library/Widget.php** 第 **153** 行
  * 说明: 将每一行的值压入堆栈

### public `have`() ###
  * 返回: **boolean**
  * 位置: 文件 **/includes/library/Widget.php** 第 **170** 行
  * 说明: 返回堆栈是否为空

### public `get`() ###
  * 返回: **array**
  * 位置: 文件 **/includes/library/Widget.php** 第 **180** 行
  * 说明: 返回堆栈每一行的值

### public `set`(`string $name, mixed $name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |值对应的键值|
|$name     |mixed     |_N/A_     |相应的值|

  * 返回: **array**
  * 位置: 文件 **/includes/library/Widget.php** 第 **194** 行
  * 说明: 设定堆栈每一行的值

### public `__call`(`string $name, array $args`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |函数名 |
|$args     |array     |_N/A_     |函数参数|

  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **207** 行
  * 说明: 魔术函数,用于挂接其它函数

### public `__get`(`string $name`) ###
**参数列表**
|**名称**|**类型**|**默认**|**说明**|
|:---------|:---------|:---------|:---------|
|$name     |string    |_N/A_     |变量名 |

  * 返回: **mixed**
  * 位置: 文件 **/includes/library/Widget.php** 第 **227** 行
  * 说明: 魔术函数,用于获取内部变量

### public `render`() ###
  * 返回: **void**
  * 位置: 文件 **/includes/library/Widget.php** 第 **237** 行
  * 说明: 必须实现的执行函数