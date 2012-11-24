<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.libs
 *
 * $Id$
 */
/**
 * Set HTTP status header.
 *
 * @since 1.0
 *
 * @param int $header HTTP status code
 *
 */
function status_header($header) {
    $text = get_status_header_desc($header);

    if (empty ($text)) {
        return;
    }
    $protocol = $_SERVER ["SERVER_PROTOCOL"];
    if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
        $protocol = 'HTTP/1.0';
    }
    $status_header = "$protocol $header $text";

    @header($status_header, true, $header);
}

/**
 * Retrieve the description for the HTTP status.
 *
 * @since 1.0
 *
 * @param int $code
 * HTTP status code.
 * @return string Empty string if not found, or description if found.
 */
function get_status_header_desc($code) {
    global $output_header_to_desc;

    $code = abs(intval($code));

    if (!isset ($output_header_to_desc)) {
        $output_header_to_desc = array(100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',

            200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 226 => 'IM Used',

            300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect',

            400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 426 => 'Upgrade Required',

            500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 510 => 'Not Extended');
    }

    if (isset ($output_header_to_desc [$code]))
        return $output_header_to_desc [$code];
    else
        return '';
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing slash if it exists already before adding a trailing
 * slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @uses untrailingslashit() Unslashes string if it was slashed already.
 *
 * @param string $string
 * What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit($string) {
    return untrailingslashit($string) . '/';
}

/**
 * Removes trailing slash if it exists.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 *
 * @param string $string
 * What to remove the trailing slash from.
 * @return string String without the trailing slash.
 */
function untrailingslashit($string) {
    return rtrim($string, '/\\');
}

/**
 * Test if a give filesystem path is absolute ('/foo/bar', 'c:\windows').
 *
 *
 * @param string $path
 * File path
 * @return bool True if path is absolute, false is not absolute.
 */
function path_is_absolute($path) {
    // this is definitive if true but fails if $path does not exist or contains
    // a symbolic link
    if (realpath($path) == $path)
        return true;

    if (strlen($path) == 0 || $path{0} == '.')
        return false;

    // windows allows absolute paths like this
    if (preg_match('#^[a-zA-Z]:\\\\#', $path))
        return true;

    // a path starting with / or \ is absolute; anything else is relative
    return ( bool )preg_match('#^[/\\\\]#', $path);
}

/**
 * Join two filesystem paths together (e.g.
 * 'give me $path relative to $base').
 *
 * If the $path is absolute, then it the full path is returned.
 *
 *
 * @param string $base
 * @param string $path
 * @return string The path with the base or absolute path.
 */
function path_join($base, $path) {
    if (path_is_absolute($path))
        return $path;

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/**
 * Sanitizes a filename replacing whitespace with dashes
 *
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trim period, dash and underscore from beginning
 * and end of filename.
 *
 * @since 2.1.0
 *
 * @param string $filename
 * The filename to be sanitized
 * @return string The sanitized filename
 */
function sanitize_file_name($filename) {
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);
    // Return if only one extension
    if (count($parts) <= 2)
        return $filename;

    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);

    $mimes = array('tmp', 'txt', 'jpg', 'gif', 'png', 'rar', 'zip', 'gzip', 'ppt');

    // Loop over any intermediate extensions. Munge them with a trailing
    // underscore if they are a 2 - 5 character
    // long alpha string not in the extension whitelist.
    foreach (( array )$parts as $part) {
        $filename .= '.' . $part;

        if (preg_match('/^[a-zA-Z]{2,5}\d?$/', $part)) {
            $allowed = false;
            foreach ($mimes as $ext_preg => $mime_match) {
                $ext_preg = '!(^' . $ext_preg . ')$!i';
                if (preg_match($ext_preg, $part)) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed)
                $filename .= '_';
        }
    }
    $filename .= '.' . $extension;

    return $filename;
}

/**
 * Get a filename that is sanitized and unique for the given directory.
 *
 * If the filename is not unique, then a number will be added to the filename
 * before the extension, and will continue adding numbers until the filename is
 * unique.
 *
 * The callback must accept two parameters, the first one is the directory and
 * the second is the filename. The callback must be a function.
 *
 * @param string $dir
 * @param string $filename
 * @param string $unique_filename_callback
 * Function name, must be a function.
 * @return string New filename, if given wasn't unique.
 */
function unique_filename($dir, $filename, $unique_filename_callback = null) {
    // sanitize the file name before we begin processing
    $filename = sanitize_file_name($filename);

    // separate the filename into a name and extension
    $info = pathinfo($filename);
    $ext = !empty ($info ['extension']) ? '.' . $info ['extension'] : '';
    $name = basename($filename, $ext);

    // edge case: if file is named '.ext', treat as an empty name
    if ($name === $ext)
        $name = '';

    // Increment the file number until we have a unique file to save in
    // $dir. Use $override['unique_filename_callback'] if supplied.
    if ($unique_filename_callback && is_callable($unique_filename_callback)) {
        $filename = $unique_filename_callback ($dir, $name);
    } else {
        $number = '';

        // change '.ext' to lower case
        if ($ext && strtolower($ext) != $ext) {
            $ext2 = strtolower($ext);
            $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);

            // check for both lower and upper case extension or image sub-sizes
            // may be overwritten
            while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
                $new_number = $number + 1;
                $filename = str_replace("$number$ext", "$new_number$ext", $filename);
                $filename2 = str_replace("$number$ext2", "$new_number$ext2", $filename2);
                $number = $new_number;
            }
            return $filename2;
        }

        while (file_exists($dir . "/$filename")) {
            if ('' == "$number$ext")
                $filename = $filename . ++$number . $ext;
            else
                $filename = str_replace("$number$ext", ++$number . $ext, $filename);
        }
    }

    return $filename;
}

/**
 * 查找文件
 *
 * @param string $dir
 * 起始目录
 * @param string $pattern
 * 合法的正则表达式,此表达式只用于文件名
 * @param array $excludes
 * 不包含的目录名
 * @param bool|int $recursive
 * 是否递归查找
 * @param int $stop
 * 递归查找层数
 * @return array 查找到的文件
 */
function find_files($dir = '.', $pattern = '', $excludes = array(), $recursive = 0, $stop = 0) {
    $files = array();
    $dir = trailingslashit($dir);
    if (is_dir($dir)) {
        $fhd = @opendir($dir);
        if ($fhd) {
            $excludes = is_array($excludes) ? $excludes : array();
            $_excludes = array_merge($excludes, array('.', '..'));
            while (($file = readdir($fhd)) !== false) {
                if ($recursive && is_dir($dir . $file) && !in_array($file, $_excludes)) {
                    if ($stop == 0 || $recursive <= $stop) {
                        $files = array_merge($files, find_files($dir . $file, $pattern, $excludes, $recursive + 1, $stop));
                    }
                }
                if (is_file($dir . $file) && @preg_match($pattern, $file)) {
                    $files [] = $dir . $file;
                }
            }
            @closedir($fhd);
        }
    }
    return $files;
}

/**
 * 取解析后的php文件内容
 *
 * 以{@link WEBROOT}为根目录查找$file,然后执行php文件并返回执行后的内容.
 *
 * @param string $file
 * @param array $vars
 * @return string
 */
function pfile_get_contents($file, $vars = array()) {
    $content = false;
    $file = trailingslashit(WEB_ROOT) . $file;
    if (is_readable($file)) {
        @extract($vars);
        @ob_start();
        include $file;
        $content = @ob_get_contents();
        @ob_end_clean();
    }
    return $content;
}

/**
 * 删除目录
 *
 * @param string $dir
 * @return bool
 */
function rmdirs($dir) {
    $hd = @opendir($dir);
    if ($hd) {
        while (($file = readdir($hd)) != false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($dir . DS . $file)) {
                rmdirs($dir . DS . $file);
            } else {
                @unlink($dir . DS . $file);
            }
        }
        closedir($hd);
        @rmdir($dir);
    }
    return true;
}

/**
 * 从SESSION中取值
 *
 * 如果未设置,则返回默认值 $default
 *
 * @param string $name
 * 值名
 * @param mixed $default
 * 默认值
 * @return mixed SESSION中的值
 */
function sess_get($name, $default = "") {
    if (isset($_SESSION[$name])) {
        return $_SESSION[$name];
    }
    return $default;
}

/**
 * 从SESSION中删除变量$name,并将该变量值返回
 *
 * @param string $name
 * @param string $default
 * @return mixed
 */
function sess_del($name, $default = '') {
    $value = sess_get($name, $default);
    if (isset($_SESSION[$name])) {
        $_SESSION[$name] = null;
        unset($_SESSION[$name]);
    }
    return $value;
}

/**
 * 安全ID
 *
 * @param string $ids
 * 以$sp分隔的id列表,只能是大与0的整形
 * @param string $sp
 * 分隔符
 * @param boolean $array
 * 是否返回数组
 * @return mixed
 */
function safe_ids($ids, $sp = ",", $array = false) {
    if (empty ($ids)) {
        return $array ? array() : null;
    }
    $_ids = explode($sp, $ids);
    $ids = array();
    foreach ($_ids as $id) {
        if (preg_match('/^[1-9]\d*$/', $id)) {
            $ids [] = $id;
        }
    }
    if ($array === false) {
        return empty ($ids) ? null : implode($sp, $ids);
    } else {
        return empty ($ids) ? array() : $ids;
    }
}

/**
 * 可读的size
 * @param int $size
 * @return string
 */
function readable_size($size) {
    if ($size < 1024) {
        return $size . 'B';
    } else if ($size < 1048576) {
        return number_format($size / 1024, 2) . 'K';
    } else if ($size < 1073741824) {
        return number_format($size / 1048576, 2) . 'M';
    } else {
        return number_format($size / 1073741824, 2) . 'G';
    }
}

/**
 * 来自ucenter的加密解密函数
 *
 * @param string $string 要解（加）密码字串
 * @param string $operation DECODE|ENCODE 解密|加密
 * @param string $key 密码
 * @param int $expiry 超时
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;

    $key = md5($key ? $key : SECURITY_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey [$i] = ord($cryptkey [$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * Include all of files in the $files or a single file if the $files is a string
 * @param array|string $files
 */
function includes($files) {
    if (!is_array($files)) {
        $files = array($files);
    }
    foreach ($files as $file) {
        if (preg_match('/^::/', $file)) { // 从核心库加载
            $file = str_replace('::', KISSGO, $file);
        } else {
            $file = APP_PATH . $file;
        }
        if (is_file($file)) {
            include_once $file;
        }
    }
}

/**
 * 加载应用程序中的文件
 * @internal param array|string $files
 */
function imports() {
    $args = func_get_args();
    if (empty($args)) return;
    foreach ($args as $files) {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            if (preg_match('/.+\*$/', $file)) {

                $_files = glob(MODULES_PATH . $file . '.php');
                foreach ($_files as $_file) {
                    if (is_file($_file)) {
                        include_once $_file;
                    }
                }
                $_files = glob(KISSGO . 'modules' . DS . $file . '.php');
                foreach ($_files as $_file) {
                    if (is_file($_file)) {
                        include_once $_file;
                    }
                }
            } else {
                $_file = MODULES_PATH . $file;
                if (is_file($_file)) {
                    include_once $_file;
                } else {
                    $_file = KISSGO . 'modules' . DS . $file;
                    if (is_file($_file)) {
                        include_once $_file;
                    }
                }
            }
        }
    }
}

/**
 * 合并$base与$arr
 *
 * @param mixed $base
 * @param array $arr
 * @return array 如果$base为空或$base不是一个array则直接返回$arr,反之返回array_merge($base,$arr)
 */
function array_merge2($base, $arr) {
    if (empty($base) || !is_array($base)) {
        return $arr;
    }
    return array_merge($base, $arr);
}

/**
 * 记录Log信息
 *
 * @param string $message 信息
 * @param array $trace_info 栈信息
 * @param int $level 调试级别
 */
function log_message($message, $trace_info, $level) {
    static $log_name = array(DEBUG_INFO => 'INFO', DEBUG_WARN => 'WARN', DEBUG_DEBUG => 'DEBUG', DEBUG_ERROR => 'ERROR');
    if ($level >= DEBUG) {
        $msg = date("Y-m-d H:i:s") . " {$log_name[$level]} [{$trace_info['line']}] {$trace_info['file']} - {$message}\n";
        @error_log($msg, 3, APPDATA_PATH . '/logs/kissgo.log');
    }
}

/**
 * 记录debug信息
 *
 * @param string $message
 */
function log_debug($message) {
    $trace = debug_backtrace();
    log_message($message, $trace[0], DEBUG_DEBUG);
}

/**
 * 记录info信息
 *
 * @param string $message
 */
function log_info($message) {
    $trace = debug_backtrace();
    log_message($message, $trace[0], DEBUG_INFO);
}

/**
 * 记录warn信息
 *
 * @param string $message
 */
function log_warn($message) {
    $trace = debug_backtrace();
    log_message($message, $trace[0], DEBUG_WARN);
}

/**
 * 记录error信息
 *
 * @param string $message
 */
function log_error($message) {
    $trace = debug_backtrace();
    log_message($message, $trace[0], DEBUG_ERROR);
}

/**
 *
 * @param string $uri
 * @return string
 */
function mpath($uri) {
    return untrailingslashit(apply_filter('get_module_path_from_path', $uri));
}

/**
 * 将模块路径，Action,参数等数据转换成url
 * @param string $module 模块路径
 * @param string $action Action
 * @param string|array $args 参数
 * @return string
 */
function murl($module, $action = '', $args = '') {
    $url = trailingslashit(apply_filter('get_module_url_from_path', $module));
    if (!empty($action)) {
        $url .= $action;
    }
    if (!empty($args)) {
        if (is_string($args)) {
            $url .= '?' . ltrim($args, '?&');
        } else if (is_array($args)) {
            $_args = array();
            foreach ($args as $key => $val) {
                $_args[] = $key . '=' . urlencode($val);
            }
            $url .= '?' . implode('&', $_args);
        }
    }
    if (!CLEAN_URL) {
        $url = 'index.php/' . $url;
    }
    return BASE_URL . $url;
}

/**
 * 根据数据构建查询参数
 * @param $args
 * @return string
 */
function build_query_args($args) {
    if (empty($args)) {
        return '';
    }
    $_args = array();
    foreach ($args as $name => $value) {
        $_args[] = $name . '=' . urlencode($value);
    }
    return implode('&', $_args);
}
// end of file functions.php