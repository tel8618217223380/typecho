<?php
/**
 * API方法,Typecho命名空间
 *
 * @category typecho
 * @package Common
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * Typecho公用方法
 *
 * @category typecho
 * @package Common
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Common
{
    /** 默认不解析的标签列表 */
    const LOCKED_HTML_TAG = 'code|script';
    
    /**
     * 缓存的包含路径
     * 
     * @access private
     * @var array
     */
    private static $_cachedIncludePath = false;

    /**
     * 锁定的代码块
     *
     * @access private
     * @var array
     */
    private static $_lockedBlocks = array();
    
    /**
     * 默认的初始化配置
     * 
     * @access public
     * @var array
     */
    public static $config = array(
        'autoLoad'      =>  true,
        'exception'     =>  false,
        'gpc'           =>  true,
        'timezone'      =>  'UTC',
        'gzip'          =>  false,
        'charset'       =>  'UTF-8',
        'session'       =>  false,
        'contentType'   =>  'text/html'
    );

    /**
     * 锁定标签回调函数
     * 
     * @access public
     * @param array $matches 匹配的值
     * @return string
     */
    public static function __lockHTML(array $matches)
    {
        $guid = uniqid(time());
        self::$_lockedBlocks[$guid] = $matches[0];
        return $guid;
    }
    
    /**
     * 程序初始化方法
     * 
     * @access public
     * @param array $config 配置信息
     * @return void
     */
    public static function init(array $config = NULL)
    {
        self::$config = empty($config) ? self::$config : array_merge(self::$config, $config);
        
        /** 输出logo */
        if (isset($_GET['464D-E63E-9D08-97E2-16DD-6A37-BDEC-6021'])) {
            header('content-Type: image/gif', true);
            die(base64_decode('R0lGODlhXQAVANUAAP////Pz8+bm5tnZ2c3NzcDAwLOzs5mZmY2NjeR+ANp6A9l5A4CAgM51BsNwCsNwCbhrDXNzc61nEKxmEKFiE6JiE2ZmZpddFpZdFoxZGYtYGoFUHYBUHVlZWXZPIHVPIGtLI2pKI01NTV9GJlRBKVRBKkBAQEk8LT44MD03MDMzMwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAHAP8ALAAAAABdABUAAAb/QJVwSCwaj8ikUgjxLJ9Gj+JELBSg2OwyodFCSYkKkUDwms3cs5E0GlYSVCFZTX+m60IpSnhKXIZzeEonI21HKIVxQlyIhkaEjkVOQg2TKhIKKggHAgIHCEMmByZEERFCHaAmEQcHp0ckDgmzCxlEKG+zCRAkiw4LtCBEIw26CbZFEIYXD0MeCSQBANMAAUMWABZjZSoHAAzS1AGvQyAJDRwjIRQJDnsoshSFGAsLVLPKIbKGz+jq7A6KjBCjAsweFSgSbJDD7Vq2bUK8ARjwKsIAAORQLHBHREKaCQlKEDmhIYWKdkNSLKCg4sTGg3nuDGm2yJGCPyoCOdQGiJu3/wGkQg0IEJQDtCEjIJwr0QdDEpkqIEBQgQGOkQkLilSIAwFZ1Kk5GwrBxpNhxIdFsDFg0oDPmwUchIxIEKkIVKlRAxrZQJdIBkNdh+ANmxat2W4AkAA4wOQBigyzMJgUEqIvkrtTB0uyLOQvE6+DdY41TBhxUCImFgupqmCXIhInSkC120Xw1Kow3SSYLGTr55kSDu+EiJhxEW8d+CRYEAmMUwgLehERdrI2k6mygxMBw5IIzZOOjgkpIECIieQmAlxBBcDntLVDGABYL8QoBGEoNtSjUqKeBxQneFCMSJgJocEuwpyQgQIN8KbCQEIYJFcC1EXQHiflqSDfAAcYIJ+NexdxeECIpwnxATC6QKBICbLo0kAIi1j31RAcoIiPg1Et890FCcAUgQAACACfChZVY8AA7nVz0URDFpFCCBp8oAgRJWigAYxDSDlMJCl8oAEHIkUxRCVDKABWFoF4I8iaUOgRkyVIdFCWCgKspyabeCLBxphtLVEAAKoYgNadeRZ6BF9wImHCku8NQaihkAqRXxasICACEalEqqkRQQAAOw=='));
        }
        
        if (isset(self::$config['autoLoad']) && self::$config['autoLoad']) {
            /** 设置自动载入函数 */
            function __autoLoad($className)
            {
                /**
                 * 自动载入函数并不判断此类的文件是否存在, 我们认为当你显式的调用它时, 你已经确认它存在了
                 * 如果真的无法被加载, 那么系统将出现一个严重错误(Fetal Error)
                 * 如果你需要判断一个类能否被加载, 请使用 Typecho_Common::isAvailableClass 方法
                 */
                require_once str_replace('_', '/', $className) . '.php';
            }
        }
        
        if (isset(self::$config['gpc']) && self::$config['gpc']) {
            /** 兼容php6 */
            if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
                $_GET = self::stripslashesDeep($_GET);
                $_POST = self::stripslashesDeep($_POST);
                $_COOKIE = self::stripslashesDeep($_COOKIE);

                reset($_GET);
                reset($_POST);
                reset($_COOKIE);
            }
        }
        
        if (isset(self::$config['timezone'])) {
            if (!ini_get("date.timezone") && function_exists("date_default_timezone_set")) {
                @date_default_timezone_set($timezone);
            }
        }
        
        if (isset(self::$config['session']) && self::$config['session']) {
            session_start();
        }
        
        if (isset(self::$config['gzip'])) {
            //开始监视输出区
            //~ fix issue 39
            if (self::$config['gzip'] && !empty($_SERVER['HTTP_ACCEPT_ENCODING'])
               && false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        }
        
        if (isset(self::$config['charset']) || isset(self::$config['contentType'])) {
            /** Typecho_Response */
            require_once 'Typecho/Response.php';
        }
        
        if (isset(self::$config['exception'])) {
            /** 设置异常截获函数 */
            set_exception_handler(array('Typecho_Common', 'exceptionHandle'));
        }
        
        if (isset(self::$config['contentType'])) {
            Typecho_Response::setContentType(self::$config['contentType']);
        }
    }
    
    /**
     * 异常截获函数
     * 
     * @access public
     * @param Exception $exception 截获的异常
     * @return void
     */
    public static function exceptionHandle(Exception $exception)
    {
        if (!self::$config['exception']) {
            echo nl2br($exception->__toString());
        } else {
            $handleClass = self::$config['exception'];
            new $handleClass($exception);
        }
        
        exit;
    }
    
    /**
     * 判断类是否能被加载
     * 此函数会遍历所有的include目录, 所以会有一定的性能消耗, 但是不会很大
     * 可是我们依然建议你在必须检测一个类能否被加载时使用它, 它通常表现为以下两种情况
     * 1. 当需要被加载的类不存在时, 系统不会停止运行 (如果你不判断, 系统会因抛出严重错误而停止)
     * 2. 你需要知道哪些类无法被加载, 以提示使用者
     * 除了以上情况, 你无需关注那些类无法被加载, 因为当它们不存在时系统会自动停止并报错
     * 
     * @access public
     * @param string $className 类名
     * @return boolean
     */
    public static function isAvailableClass($className)
    {
        /** 获取所有include目录 */
        $dirs = false === self::$_cachedIncludePath ? explode(PATH_SEPARATOR, get_include_path())
        : self::$_cachedIncludePath;
        $file = str_replace('_', '/', $className) . '.php';

        foreach ($dirs as $dir) {
            if (!empty($dir)) {
                if (is_file($dir . '/' . $file)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * 递归去掉数组反斜线
     *
     * @access public
     * @param mixed $value
     * @return mixed
     */
    public static function stripslashesDeep($value)
    {
        return is_array($value) ? array_map(array('Typecho_Common', 'stripslashesDeep'), $value) : stripslashes($value);
    }

    /**
     * 抽取多维数组的某个元素,组成一个新数组,使这个数组变成一个扁平数组
     * 使用方法:
     * <code>
     * <?php
     * $fruit = array(array('apple' => 2, 'banana' => 3), array('apple' => 10, 'banana' => 12));
     * $banana = Typecho_Common::arrayFlatten($fruit, 'banana');
     * print_r($banana);
     * //outputs: array(0 => 3, 1 => 12);
     * ?>
     * </code>
     *
     * @access public
     * @param array $value 被处理的数组
     * @param string $key 需要抽取的键值
     * @return array
     */
    public static function arrayFlatten(array $value, $key)
    {
        $result = array();

        if ($value) {
            foreach ($value as $inval) {
                if (is_array($inval) && isset($inval[$key])) {
                    $result[] = $inval[$key];
                } else {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * 自闭合html修复函数
     * 使用方法:
     * <code>
     * $input = '这是一段被截断的html文本<a href="#"';
     * echo Typecho_Common::fixHtml($input);
     * //output: 这是一段被截断的html文本
     * </code>
     *
     * @access public
     * @param string $string 需要修复处理的字符串
     * @return string
     */
    public static function fixHtml($string)
    {
        //关闭自闭合标签
        $startPos = strrpos($string, "<");
        $trimString = substr($string, $startPos);

        if (false === strpos($trimString, ">")) {
            $string = substr($string, 0, $startPos);
        }

        //非自闭合html标签列表
        preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
        preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

        if (!empty($startTags[1]) && is_array($startTags[1])) {
            krsort($startTags[1]);
            $closeTagsIsArray = is_array($closeTags[1]);
            foreach ($startTags[1] as $key => $tag) {
                $attrLength = strlen($startTags[2][$key]);
                if ($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1])) {
                    continue;
                }
                if (!empty($closeTags[1]) && $closeTagsIsArray) {
                    if (false !== ($index = array_search($tag, $closeTags[1]))) {
                        unset($closeTags[1][$index]);
                        continue;
                    }
                }
                $string .= "</{$tag}>";
            }
        }

        return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string);
    }

    /**
     * 去掉字符串中的html标签
     * 使用方法:
     * <code>
     * $input = '<a href="http://test/test.php" title="example">hello</a>';
     * $output = Typecho_Common::stripTags($input, <a href="">);
     * echo $output;
     * //display: '<a href="http://test/test.php">hello</a>'
     * </code>
     *
     * @access public
     * @param string $string 需要处理的字符串
     * @param string $allowableTags 需要忽略的html标签
     * @return string
     */
    public static function stripTags($string, $allowableTags = NULL)
    {
        if (!empty($allowableTags) && preg_match_all("/\<([a-z]+)([^>]*)\>/is", $allowableTags, $tags)) {
            $normalizeTags = '<' . implode('><', $tags[1]) . '>';
            $string = strip_tags($string, $normalizeTags);
            $attributes = array_map('trim', $tags[2]);
            
            $allowableAttributes = array();
            foreach ($attributes as $key => $val) {
                $allowableAttributes[$tags[1][$key]] = array();
                if (preg_match_all("/([a-z]+)\s*\=/is", $val, $vals)) {
                    foreach ($vals[1] as $attribute) {
                        $allowableAttributes[$tags[1][$key]][] = $attribute;
                    }
                }
            }
            
            foreach ($tags[1] as $key => $val) {
                $match = "/\<{$val}(\s*[a-z]+\s*\=\s*[\"'][^\"']*[\"'])*\s*\>/is";
                
                if (preg_match_all($match, $string, $out)) {
                    foreach ($out[0] as $startTag) {
                        if (preg_match_all("/([a-z]+)\s*\=\s*[\"'][^\"']*[\"']/is", $startTag, $attributesMatch)) {
                            $replace = $startTag;
                            foreach ($attributesMatch[1] as $attribute) {
                                if (!in_array($attribute, $allowableAttributes[$val])) {
                                    $startTag = preg_replace("/\s*{$attribute}\s*=\s*[\"'][^\"']*[\"']/is", '', $startTag);
                                }
                            }
                            
                            $string = str_replace($replace, $startTag, $string);
                        }
                    }
                }
            }
            
            return $string;
        } else {
            return strip_tags($string);
        }
    }

    /**
     * 过滤用于搜索的字符串
     * 
     * @access public
     * @param string $query 搜索字符串
     * @return string
     */
    public static function filterSearchQuery($query)
    {
        return str_replace(array('%', '?', '*', '/'), '', $query);
    }
    
    /**
     * 处理XSS跨站攻击的过滤函数
     * 
     * @author kallahar@kallahar.com
     * @link http://kallahar.com/smallprojects/php_xss_filter_function.php
     * @access public
     * @param string $val 需要处理的字符串
     * @return string
     */
    public static function removeXSS($val)
    { 
       // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 
       // this prevents some character re-spacing such as <java\0script> 
       // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs 
       $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val); 
        
       // straight replacements, the user should never need these since they're normal characters 
       // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29> 
       $search = 'abcdefghijklmnopqrstuvwxyz'; 
       $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
       $search .= '1234567890!@#$%^&*()'; 
       $search .= '~`";:?+/={}[]-_|\'\\'; 
       
       for ($i = 0; $i < strlen($search); $i++) {
          // ;? matches the ;, which is optional 
          // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 
        
          // &#x0040 @ search for the hex values 
          $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 
          // &#00064 @ 0{0,7} matches '0' zero to seven times 
          $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
       } 
        
       // now the only remaining whitespace attacks are \t, \n, and \r 
       $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'); 
       $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'); 
       $ra = array_merge($ra1, $ra2); 
        
       $found = true; // keep replacing as long as the previous round replaced something 
       while ($found == true) {
          $val_before = $val; 
          for ($i = 0; $i < sizeof($ra); $i++) {
             $pattern = '/'; 
             for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                   $pattern .= '('; 
                   $pattern .= '(&#[xX]0{0,8}([9ab]);)'; 
                   $pattern .= '|'; 
                   $pattern .= '|(&#0{0,8}([9|10|13]);)'; 
                   $pattern .= ')*'; 
                }
                $pattern .= $ra[$i][$j]; 
             }
             $pattern .= '/i'; 
             $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 
             $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 
             
             if ($val_before == $val) {
                // no replacements were made, so exit the loop 
                $found = false; 
             } 
          } 
       }
       
       return $val; 
    }

    /**
     * 宽字符串截字函数
     *
     * @access public
     * @param string $str 需要截取的字符串
     * @param integer $start 开始截取的位置
     * @param integer $length 需要截取的长度
     * @param string $trim 截取后的截断标示符
     * @return string
     */
    public static function subStr($str, $start, $length, $trim = "...")
    {
        if (function_exists('mb_get_info')) {
            $iLength = mb_strlen($str, self::$config['charset']);
            $str = mb_substr($str, $start, $length, self::$config['charset']);
            return ($length < $iLength - $start) ? $str . $trim : $str;
        } else {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            $str = join("", array_slice($info[0], $start, $length));
            return ($length < (sizeof($info[0]) - $start)) ? $str . $trim : $str;
        }
    }

    /**
     * 获取宽字符串长度函数
     *
     * @access public
     * @param string $str 需要获取长度的字符串
     * @return integer
     */
    public static function strLen($str)
    {
        if (function_exists('mb_get_info')) {
            return mb_strlen($str, self::$config['charset']);
        } else {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            return sizeof($info[0]);
        }
    }

    /**
     * 生成缩略名
     *
     * @access public
     * @param string $str 需要生成缩略名的字符串
     * @param string $default 默认的缩略名
     * @return string
     */
    public static function slugName($str, $default = NULL)
    {
        $str = str_replace(array("'", ":", "\\", "/"), "", $str);
        $str = str_replace(array("+", ",", " ", ".", "?", "=", "&", "!", "<", ">", "(", ")", "[", "]", "{", "}"), "-", $str);

        //cut string
        //from end
        $length = strlen($str);
        $i = $length;
        $cutOff = 0;

        while ($i > 0) {
            $i--;
            if ('-' == $str[$i]) {
                $cutOff ++;
            } else {
                break;
            }
        }

        if ($cutOff) {
            $str = substr($str, 0, - $cutOff);
        }

        //from start
        $length = strlen($str);
        $i = 0;
        $cutOff = 0;

        while ($i < $length) {
            if ('-' == $str[$i]) {
                $cutOff ++;
            } else {
                break;
            }
            $i++;
        }

        if ($cutOff) {
            $str = substr($str, $cutOff);
        }

        return empty($str) ? (empty($default) ? time() : $default) : $str;
    }
    
    /**
     * 文本分段函数
     *
     * @param string $string
     * @return string
     */
    public static function cutParagraph($string)
    {
        /** 锁定自闭合标签 */
        $string = trim($string);
        $string = preg_replace_callback("/\<(" . self::LOCKED_HTML_TAG . ")[^\>]*\/\>/is", array('Typecho_Common', '__lockHTML'), $string);
        
        /** 锁定开标签 */
        $string = preg_replace_callback("/\<(" . self::LOCKED_HTML_TAG . ")[^\>]*\>.*\<\/\w+\>/is", array('Typecho_Common', '__lockHTML'), $string);

        /** 区分段落 */
        $string = preg_replace("/(\r\n|\n|\r)/", "\n", $string);
        $string = '<p>' . preg_replace("/\n{2,}/", "</p><p>", $string) . '</p>';
        $string = str_replace("\n", '<br />', $string);
        
        /** 去掉不需要的 */
        $string = preg_replace("/\<p\>\s*\<h([1-6])\>(.*)\<\/h\\1\>\s*\<\/p\>/is", "\n<h\\1>\\2</h\\1>\n", $string);
        $string = preg_replace("/\<p\>\s*\<(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>(.*)\<\/\\1\>\s*\<\/p\>/is", "\n<\\1>\\2</\\1>\n", $string);
        $string = preg_replace("/\<\/(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>\s*\<br\s?\/?\>\s*\<(div|blockquote|pre|table|tr|th|td|li|ol|ul)\>/is",
        "</\\1>\n<\\2>", $string);

        return str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $string);
    }
    
    /**
     * 生成随机字符串
     * 
     * @access public
     * @param integer $length 字符串长度
     * @return string
     */
    public static function randString($length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= chr(rand(32, 126));
        }
        return $result;
    }
    
    /**
     * 对字符串进行hash加密
     * 
     * @access public
     * @param string $string 需要hash的字符串
     * @param string $salt 扰码
     * @return string
     */
    public static function hash($string, $salt = NULL)
    {
        /** 生成随机字符串 */
        $salt = empty($salt) ? self::randString(9) : $salt;
        $length = strlen($string);
        $hash = '';
        $last = ord($string[$length - 1]);
        $pos = 0;
        
        /** 判断扰码长度 */
        if (strlen($salt) != 9) {
            /** 如果不是9直接返回 */
            return;
        }
        
        while ($pos < $length) {
            $asc = ord($string[$pos]);
            $last = ($last * ord($salt[($last % $asc) % 9]) + $asc) % 95 + 32;
            $hash .= chr($last);
            $pos ++;
        }

        return '$T$' . $salt . md5($hash);
    }
    
    /**
     * 判断hash值是否相等
     * 
     * @access public
     * @param string $from 源字符串
     * @param string $to 目标字符串
     * @return boolean
     */
    public static function hashValidate($from, $to)
    {
        if ('$T$' == substr($to, 0, 3)) {
            $salt = substr($to, 3, 9);
            return self::hash($from, $salt) == $to;
        } else {
            return md5($from) == $to;
        }
    }
    
    /**
     * 将路径转化为链接
     * 
     * @access public
     * @param string $path 路径
     * @param string $prefix 前缀
     * @return string
     */
    public static function url($path, $prefix)
    {
        $path = (0 === strpos($path, './')) ? substr($path, 2) : $path;
        return rtrim($prefix, '/') . '/' . str_replace('//', '/', ltrim($path, '/'));
    }
}