# Typecho PHP 编码规范 #

如果您已经决定向Typecho贡献代码，请详细阅读以下规范，并严格遵守。这样在保证您代码可读性的同时还可以大大减少我们的工作量。

## 约定 ##

### 文件编码 ###
请调整您的编辑器文件编码为\*UTF-8**，并\*关闭UTF-8 BOM\*的功能。请不要使用windows自带的记事本编辑项目文件。**

### 缩进 ###
详细的代码缩进会在后面提到，这里需要注意的是，Typecho项目中的代码缩进使用的是\*4个空格(space)**，而不是制表符(tab)，请务必调整。**

### UNIX编码规范 ###
如果你正在编写一个php文件，那么根据UNIX的C语言编码规范，必须留出最后一个空行。比如
```
<?php
//this is a test file
echo 'hello';
<---这行留空
```
而且，如果此文件为纯php文件(没有嵌套HTML)，请不要用**?>**符号结尾，保持最后一行留空即可。

## 命名 ##

### 文件命名 ###
文件名与类名保持一致，区分大小写。在某些情况下文件名可以去掉类名的前缀或后缀，比如所有的库类命名都要求加上Typecho的前缀，那么这个类的文件则去掉前缀，详见这里[TypechoLibraryFileNaming](TypechoLibraryFileNaming.md)。

### 类命名 ###
使用骆驼法则，首字母大写。
```
class TypechoDb
{
```

### 函数(方法,接口)命名 ###
使用骆驼法则，首字母小写。
```
public function fetchRows(TypechoDbQuery $query, array $filter = NULL)
```

### 变量命名 ###
使用骆驼法则，首字母小写。
```
protected $callbackFunctions;
```

如为私有变量，请在变量名前方加上下划线。
```
private $_adapter;
```

### 常量命名 ###
所有字母大写，前后加上双下划线，单词之间用下划线分割，如果是Typecho的内部常量，则需要加上TYPECHO前缀。
```
define('__TYPECHO_DB_ADAPTER__', 'Mysql');
```

## 注释 ##

注释是开源项目的重点，请务必重视。

### 头部注释 ###
头部注释主要用来阐述此文件的版权，协议，作者，版本。对于Typecho核心开发组，请按照下列形式书写(你可以把它设置为代码模板)。
```
<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */
```
其中author为作者的名称，请自己命名。version定义为$Id$是为了匹配svn的关键字，设置此文件的svn:keywords属性为id，每次提交以后,$Id$就会被替换为具体的版本信息，比如:$Id: Db.php 14 2008-02-23 13:07:16Z magike.net $。

### 引用文件和定义常量注释 ###
文件的引用和常量的定义一般都放置在文件的开头部分。对于单行注释，请参考c99标准。
```
/** 定义数据库适配器 **/
define('__TYPECHO_DB_ADAPTER__', 'Mysql');

/** 数据库异常 **/
require_once 'Db/DbException.php';
```
多行注释，使用如下形式
```
/**
 * 定义数据库查询读写状态
 * true表示读状态
 * false表示写状态
 *
 */
define('__TYPECHO_DB_READ__', true);
define('__TYPECHO_DB_WRITE__', false);
```

### 类(接口)注释 ###
一个类(接口)在声明的时候必须声明其作用，如果是类库文件，则必须声明其包所属。此注释参考phpdoc规范。
```
/**
 * 包含获取数据支持方法的类
 * 必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,
 * __TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__
 *
 * @package Db
 */
class TypechoDb
{
```

### 函数(方法,接口)注释 ###
函数(方法,接口)的声明注释参考phpdoc规范。注意，如果是无返回函数，必须指明@return void，请尽量在函数参数表中使用已知类型。如果函数中抛出异常则必须指明@throws <异常类型>。
```
/**
 * 一次取出所有行
 * 
 * @param TypechoDbQuery $query 查询对象
 * @param array $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
 * @return array
 */
public function fetchRows(TypechoDbQuery $query, array $filter = NULL)
{

/**
 * 数据库类构造函数
 * 
 * @param string $adapter 数据库适配器名称
 * @return void
 * @throws TypechoDbException
 */
public function __construct($adapter = __TYPECHO_DB_ADAPTER__)
{
```

### 程序行间注释 ###
行间注释采用双斜线注释法
```
//实例化适配器对象
$this->_adapter = new $adapter();
```

## 大括号放置 ##
**所有的大括号都要换行**。
```
class TypechoDb
{
    public function __construct($adapter = __TYPECHO_DB_ADAPTER__)
    {
        if(!defined($const = '__TYPECHO_DB_HOST__') || 
        !defined($const = '__TYPECHO_DB_PORT__') || 
        !defined($const = '__TYPECHO_DB_NAME__') || 
        !defined($const = '__TYPECHO_DB_USER__') || 
        !defined($const = '__TYPECHO_DB_PASS__') ||
        !defined($const = '__TYPECHO_DB_CHAR__'))
        {
        }
        else
        {
        }
```

## 逗号放置 ##
函数中用逗号来分隔参数，所有的参数与前面的逗号之间要空格(第一个参数除外)。
```
public function connect($host, $port, $db, $user, $password, $charset = NULL)
```

## 空格使用 ##
除了参数之间要使用空格外，所有操作符之间都要使用空格，包括字符连接符(.)。
```
$host . ':' . $port
```

## 代码布局 ##
### 类布局 ###
类的内部方法排序为construct,private,protected,public,destruct。属性的排序为private,protected,public。

### 空行使用 ###
使用空行可以分割代码的不同区块，做建议，具体请各位自己把握。请不要使用每行一个空行编码风格。