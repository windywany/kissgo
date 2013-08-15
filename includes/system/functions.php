<?php
/*
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo.libs
 *
 * $Id$
 */
/**
 *
 * @param string $name preference group and name
 * @param mixed $default
 */
function cfg($name, $default = '') {
    static $cfgs = false;
    if (! $cfgs) {
        $cpt = new KsgPreferenceTable ();
        $data = $cpt->query ();
        $cfgs = $data->walk ( array ($cpt, 'map' ) );
        $settings = KissGoSetting::getSetting ();
        $data = $settings->toArray ();
        foreach ( $data as $key => $value ) {
            $cfgs [$key . '@core'] = $value;
        }
    }
    
    if (strpos ( $name, '@', 1 ) === false) {
        $name .= '@core';
    }
    if (isset ( $cfgs [$name] )) {
        $default = $cfgs [$name];
    }
    return $default;
}
/**
 * 显示下个页面上的提示
 * @param string $message
 * @param string $type
 */
function show_page_tip($message, $type = 'success') {
    if ($message) {
        $_SESSION ['_ksg_page_tip_info'] = $message;
        $_SESSION ['_ksg_page_tip_info_cls'] = 'alert-' . $type;
    }
}
/**
 * 
 * covert the charset of filename to UTF-8
 * @param string $filename
 * @return string
 */
function thefilename($filename) {
    $encode = mb_detect_encoding ( $filename, "UTF-8,GBK,GB2312,BIG5,ISO-8859-1" );
    if ($encode != 'UTF-8') {
        $filename = mb_convert_encoding ( $filename, "UTF-8", $encode );
    }
    return $filename;
}
/**
 * 
 * reimplements is_subclass_of function
 * @param mixed $object
 * @param string $class_name
 * @return boolean
 */
function is_subclass_of2($object, $class_name) {
    if (version_compare ( '5.3.7', phpversion (), '>' )) {
        if (is_subclass_of ( $object, $class_name )) {
            return true;
        } else {
            $intefaces = class_implements ( $object );
            return $intefaces && isset ( $intefaces [$class_name] );
        }
    } else {
        return is_subclass_of ( $object, $class_name );
    }
}
/**
 * 发送邮件
 *
 * @param array $to
 * 接收人
 * @param string $subject
 * 主题
 * @param string $content
 * 正文
 * @param array $attachments
 * 附件
 * @param
 * string 正文类型
 * @return boolean true发送成功,如果失败false
 */
function sendmail($to, $subject, $message, $attachments = array(), $type = '') {
    global $__mailer;
    if ($__mailer == null) {
        $__mailer = new KsgMailer ();
    }
    if (! empty ( $type )) {
        $__mailer->setMessageType ( $type );
    }
    return $__mailer->send ( $to, $subject, $message, $attachments );
}
/**
 * Set HTTP status header.
 *
 * @since 1.0
 *
 * @param int $header HTTP status code
 *
 */
function status_header($header) {
    $text = get_status_header_desc ( $header );
    
    if (empty ( $text )) {
        return;
    }
    $protocol = $_SERVER ["SERVER_PROTOCOL"];
    if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
        $protocol = 'HTTP/1.0';
    }
    
    $status_header = "$protocol $header $text";
    
    @header ( $status_header, true, $header );
    if (php_sapi_name () == 'cgi-fcgi') {
        @header ( "Status: $header $text" );
    }
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
    
    $code = abs ( intval ( $code ) );
    
    if (! isset ( $output_header_to_desc )) {
        $output_header_to_desc = array (100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing', 

        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-Status', 226 => 'IM Used', 

        300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Reserved', 307 => 'Temporary Redirect', 

        400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 
                422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 426 => 'Upgrade Required', 

                500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 510 => 'Not Extended' );
    }
    
    if (isset ( $output_header_to_desc [$code] ))
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
    return untrailingslashit ( $string ) . '/';
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
    return rtrim ( $string, '/\\' );
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
    if (realpath ( $path ) == $path)
        return true;
    
    if (strlen ( $path ) == 0 || $path {0} == '.')
        return false;
    
     // windows allows absolute paths like this
    if (preg_match ( '#^[a-zA-Z]:\\\\#', $path ))
        return true;
    
     // a path starting with / or \ is absolute; anything else is relative
    return ( bool ) preg_match ( '#^[/\\\\]#', $path );
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
    if (path_is_absolute ( $path ))
        return $path;
    
    return rtrim ( $base, '/' ) . '/' . ltrim ( $path, '/' );
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
    $special_chars = array ("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr ( 0 ) );
    $filename = str_replace ( $special_chars, '', $filename );
    $filename = preg_replace ( '/[\s-]+/', '-', $filename );
    $filename = trim ( $filename, '.-_' );
    // Split the filename into a base and extension[s]
    $parts = explode ( '.', $filename );
    // Return if only one extension
    if (count ( $parts ) <= 2)
        return $filename;
    
     // Process multiple extensions
    $filename = array_shift ( $parts );
    $extension = array_pop ( $parts );
    
    $mimes = array ('tmp', 'txt', 'jpg', 'gif', 'png', 'rar', 'zip', 'gzip', 'ppt' );
    
    // Loop over any intermediate extensions. Munge them with a trailing
    // underscore if they are a 2 - 5 character
    // long alpha string not in the extension whitelist.
    foreach ( ( array ) $parts as $part ) {
        $filename .= '.' . $part;
        
        if (preg_match ( '/^[a-zA-Z]{2,5}\d?$/', $part )) {
            $allowed = false;
            foreach ( $mimes as $ext_preg => $mime_match ) {
                $ext_preg = '!(^' . $ext_preg . ')$!i';
                if (preg_match ( $ext_preg, $part )) {
                    $allowed = true;
                    break;
                }
            }
            if (! $allowed)
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
    $filename = sanitize_file_name ( $filename );
    
    // separate the filename into a name and extension
    $info = pathinfo ( $filename );
    $ext = ! empty ( $info ['extension'] ) ? '.' . $info ['extension'] : '';
    $name = basename ( $filename, $ext );
    
    // edge case: if file is named '.ext', treat as an empty name
    if ($name === $ext)
        $name = '';
    
     // Increment the file number until we have a unique file to save in
    // $dir. Use $override['unique_filename_callback'] if supplied.
    if ($unique_filename_callback && is_callable ( $unique_filename_callback )) {
        $filename = $unique_filename_callback ( $dir, $name );
    } else {
        $number = '';
        
        // change '.ext' to lower case
        if ($ext && strtolower ( $ext ) != $ext) {
            $ext2 = strtolower ( $ext );
            $filename2 = preg_replace ( '|' . preg_quote ( $ext ) . '$|', $ext2, $filename );
            
            // check for both lower and upper case extension or image sub-sizes
            // may be overwritten
            while ( file_exists ( $dir . "/$filename" ) || file_exists ( $dir . "/$filename2" ) ) {
                $new_number = $number + 1;
                $filename = str_replace ( "$number$ext", "$new_number$ext", $filename );
                $filename2 = str_replace ( "$number$ext2", "$new_number$ext2", $filename2 );
                $number = $new_number;
            }
            return $filename2;
        }
        
        while ( file_exists ( $dir . "/$filename" ) ) {
            if ('' == "$number$ext")
                $filename = $filename . ++ $number . $ext;
            else
                $filename = str_replace ( "$number$ext", ++ $number . $ext, $filename );
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
    $files = array ();
    $dir = trailingslashit ( $dir );
    if (is_dir ( $dir )) {
        $fhd = @opendir ( $dir );
        if ($fhd) {
            $excludes = is_array ( $excludes ) ? $excludes : array ();
            $_excludes = array_merge ( $excludes, array ('.', '..' ) );
            while ( ($file = readdir ( $fhd )) !== false ) {
                if ($recursive && is_dir ( $dir . $file ) && ! in_array ( $file, $_excludes )) {
                    if ($stop == 0 || $recursive <= $stop) {
                        $files = array_merge ( $files, find_files ( $dir . $file, $pattern, $excludes, $recursive + 1, $stop ) );
                    }
                }
                if (is_file ( $dir . $file ) && @preg_match ( $pattern, $file )) {
                    $files [] = $dir . $file;
                }
            }
            @closedir ( $fhd );
        }
    }
    return $files;
}
/**
 * 删除目录
 *
 * @param string $dir
 * @return bool
 */
function rmdirs($dir) {
    $hd = @opendir ( $dir );
    if ($hd) {
        while ( ($file = readdir ( $hd )) != false ) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir ( $dir . DS . $file )) {
                rmdirs ( $dir . DS . $file );
            } else {
                @unlink ( $dir . DS . $file );
            }
        }
        closedir ( $hd );
        @rmdir ( $dir );
    }
    return true;
}
/**
 * Retrieve list of allowed mime types and file extensions.
 *
 *
 * @return array Array of mime types keyed by the file extension regex
 * corresponding to those types.
 */
function get_allowed_mime_types() {
    static $mimes = false;
    if (! $mimes) {
        // Accepted MIME types are set here as PCRE unless provided.
        $mimes = apply_filter ( 'upload_mimes', array ('jpg|jpeg|jpe' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png', 'bmp' => 'image/bmp', 'tif|tiff' => 'image/tiff', 'ico' => 'image/x-icon', 'asf|asx|wax|wmv|wmx' => 'video/asf', 'avi' => 'video/avi', 'divx' => 'video/divx', 'flv' => 'video/x-flv', 'mov|qt' => 'video/quicktime', 'mpeg|mpg|mpe' => 'video/mpeg', 'txt|asc|c|cc|h' => 'text/plain', 'csv' => 'text/csv', 'tsv' => 'text/tab-separated-values', 
                'ics' => 'text/calendar', 'rtx' => 'text/richtext', 'css' => 'text/css', 'htm|html' => 'text/html', 'mp3|m4a|m4b' => 'audio/mpeg', 'mp4|m4v' => 'video/mp4', 'ra|ram' => 'audio/x-realaudio', 'wav' => 'audio/wav', 'ogg|oga' => 'audio/ogg', 'ogv' => 'video/ogg', 'mid|midi' => 'audio/midi', 'wma' => 'audio/wma', 'mka' => 'audio/x-matroska', 'mkv' => 'video/x-matroska', 'rtf' => 'application/rtf', 'js' => 'application/javascript', 'pdf' => 'application/pdf', 
                'doc|docx' => 'application/msword', 'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm' => 'application/vnd.ms-powerpoint', 'wri' => 'application/vnd.ms-write', 'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm' => 'application/vnd.ms-excel', 'mdb' => 'application/vnd.ms-access', 'mpp' => 'application/vnd.ms-project', 'docm|dotm' => 'application/vnd.ms-word', 'pptx|sldx|ppsx|potx' => 'application/vnd.openxmlformats-officedocument.presentationml', 
                'xlsx|xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml', 'docx|dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml', 'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote', 'swf' => 'application/x-shockwave-flash', 'class' => 'application/java', 'tar' => 'application/x-tar', 'zip' => 'application/zip', 'gz|gzip' => 'application/x-gzip', 'rar' => 'application/rar', '7z' => 'application/x-7z-compressed', 'exe' => 'application/x-msdownload',  // openoffice
                // formats
                'odt' => 'application/vnd.oasis.opendocument.text', 'odp' => 'application/vnd.oasis.opendocument.presentation', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet', 'odg' => 'application/vnd.oasis.opendocument.graphics', 'odc' => 'application/vnd.oasis.opendocument.chart', 'odb' => 'application/vnd.oasis.opendocument.database', 'odf' => 'application/vnd.oasis.opendocument.formula' ) );
    }
    return $mimes;
}
/**
 * 根据宽高生成缩略图文件名
 *
 * @param string $filename
 * 原始文件名
 * @param int $w
 * @param int $h
 * @return string
 */
function get_thumbnail_filename($filename, $w, $h) {
    $pos = strrpos ( $filename, '.' );
    if ($pos === false) {
        return false;
    }
    $shortname = substr ( $filename, 0, $pos );
    $ext = substr ( $filename, $pos );
    return $shortname . "-{$w}x{$h}{$ext}";
}
function the_thumbnail_src($src, $w, $h) {
    $thumbfile = get_thumbnail_filename ( $src, $w, $h );
    //TODO check whether the file exists or not, then decide which url will be return
    if (file_exists ( WEB_ROOT . $thumbfile )) {
        return BASE_URL . $thumbfile;
    } else {
        return BASE_URL . $src;
    }
}
/**
 *
 * 只保留URL中部分参数
 *
 * @param string $url
 * @param array $include
 * 要保留的参数
 * @return string
 */
function keepargs($url, $include = array()) {
    $urls = explode ( '?', $url );
    if (count ( $urls ) < 2) {
        return $url;
    }
    $kargs = array ();
    foreach ( $include as $arg ) {
        if (preg_match ( '/' . $arg . '=([^&]+)/', $urls [1], $m )) {
            $kargs [] = $m [0];
        }
    }
    if (! empty ( $kargs )) {
        $urls [1] = implode ( '&', $kargs );
        return implode ( '?', $urls );
    } else {
        return $urls [0];
    }
}
/**
 *
 * 去除URL中的参数
 *
 * @param
 * string url
 * @param
 * array 要去除的参数
 * @return string
 */
function unkeepargs($url, $exclude = array()) {
    $regex = array ();
    $rpm = array ();
    if (is_string ( $exclude )) {
        $exclude = array ($exclude );
    }
    foreach ( $exclude as $ex ) {
        $regex [] = '/&?' . $ex . '=[^&]*/';
        $rpm [] = '';
    }
    return preg_replace ( $regex, $rpm, $url );
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
    if (isset ( $_SESSION [$name] )) {
        return $_SESSION [$name];
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
    $value = sess_get ( $name, $default );
    if (isset ( $_SESSION [$name] )) {
        $_SESSION [$name] = null;
        unset ( $_SESSION [$name] );
    }
    return $value;
}
function rqst($name, $default = '', $xss_clean = false) {
    global $__rqst;
    return $__rqst->get ( $name, $default, $xss_clean );
}
function irqst($name, $default = 0) {
    return intval ( rqst ( $name, $default, true ) );
}
function frqst($name, $default = 0) {
    return floatval ( rqst ( $name, $default, true ) );
}
function sortinfo($field = '', $dir = 'd') {
    $info = array ();
    $info ['field'] = rqst ( '_sf', $field );
    $info ['dir'] = rqst ( '_sd', $dir );
    return $info;
}
function sortargs($sortinfo) {
    return '_sf=' . $sortinfo ['field'] . '&_sd=' . $sortinfo ['dir'];
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
    if (empty ( $ids )) {
        return $array ? array () : null;
    }
    $_ids = explode ( $sp, $ids );
    $ids = array ();
    foreach ( $_ids as $id ) {
        if (preg_match ( '/^[1-9]\d*$/', $id )) {
            $ids [] = $id;
        }
    }
    if ($array === false) {
        return empty ( $ids ) ? null : implode ( $sp, $ids );
    } else {
        return empty ( $ids ) ? array () : $ids;
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
        return number_format ( $size / 1024, 2 ) . 'K';
    } else if ($size < 1073741824) {
        return number_format ( $size / 1048576, 2 ) . 'M';
    } else {
        return number_format ( $size / 1073741824, 2 ) . 'G';
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
    
    $key = md5 ( $key ? $key : SECURITY_KEY );
    $keya = md5 ( substr ( $key, 0, 16 ) );
    $keyb = md5 ( substr ( $key, 16, 16 ) );
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
    
    $cryptkey = $keya . md5 ( $keya . $keyc );
    $key_length = strlen ( $cryptkey );
    
    $string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
    $string_length = strlen ( $string );
    
    $result = '';
    $box = range ( 0, 255 );
    
    $rndkey = array ();
    for($i = 0; $i <= 255; $i ++) {
        $rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
    }
    
    for($j = $i = 0; $i < 256; $i ++) {
        $j = ($j + $box [$i] + $rndkey [$i]) % 256;
        $tmp = $box [$i];
        $box [$i] = $box [$j];
        $box [$j] = $tmp;
    }
    
    for($a = $j = $i = 0; $i < $string_length; $i ++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box [$a]) % 256;
        $tmp = $box [$a];
        $box [$a] = $box [$j];
        $box [$j] = $tmp;
        $result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
    }
    
    if ($operation == 'DECODE') {
        if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
            return substr ( $result, 26 );
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
    }
}

/**
 * Include all of files in the $files or a single file if the $files is a string
 * @param array|string $files
 */
function includes($files) {
    if (! is_array ( $files )) {
        $files = array ($files );
    }
    foreach ( $files as $file ) {
        $file = APP_PATH . $file;
        if (is_file ( $file )) {
            include_once $file;
        }
    }
}
/**
 * 加载已经安装模块中的文件
 * @internal param array|string $files
 */
function imports() {
    global $_kissgo_processing_installation;
    $args = func_get_args ();
    if (empty ( $args )) {
        return;
    }
    foreach ( $args as $files ) {
        if (! is_array ( $files )) {
            $files = array ($files );
        }
        foreach ( $files as $file ) {
            if ($_kissgo_processing_installation != true && ! is_module_file ( $file )) {
                continue;
            }
            if (preg_match ( '/.+\*$/', $file )) {
                $_files = glob ( MODULES_PATH . $file . '.php' );
                foreach ( $_files as $_file ) {
                    if (is_file ( $_file )) {
                        include_once $_file;
                    }
                }
            } else {
                $_file = MODULES_PATH . $file;
                if (is_file ( $_file )) {
                    include_once $_file;
                }
            }
        }
    }
}
/**
 * 
 * 是否是模块中的文件
 * @param unknown_type $file
 */
function is_module_file($file) {
    global $_ksg_installed_modules;
    static $results = array ();
    if (isset ( $results [$file] )) {
        return $results [$file];
    }
    if (! empty ( $file ) && ! empty ( $_ksg_installed_modules )) {
        $module = explode ( "/", $file );
        $results [$file] = in_array ( $module [0], $_ksg_installed_modules );
        return $results [$file];
    }
    return false;
}
/**
 * 合并$base与$arr
 *
 * @param mixed $base
 * @param array $arr
 * @return array 如果$base为空或$base不是一个array则直接返回$arr,反之返回array_merge($base,$arr)
 */
function array_merge2($base, $arr) {
    if (empty ( $base ) || ! is_array ( $base )) {
        return $arr;
    }
    return array_merge ( $base, $arr );
}

/**
 *
 * 输入安全URL
 * @param string $url
 * @return string
 */
function safe_url($node, $type = 'node') {
    global $_CURRENT_NODE;
    static $domain = false, $protocol = false, $port = '';
    if (! $domain) {
        $domain = preg_match ( '#^https?://#i', BASE_URL ) ? preg_replace ( '#^https?://#i', '', trim ( BASE_URL, '/' ) ) : $_SERVER ['HTTP_HOST'];
        $domain = strstr ( $domain, "." );
        $protocol = isset ( $_SERVER ['HTTPS'] ) ? 'https://' : 'http://';
        $port = intval ( $_SERVER ['SERVER_PORT'] ) == 80 ? '' : ':' . $_SERVER ['SERVER_PORT'];
    }
    if (is_string ( $node )) {
        $url = $node;
        $node = $_CURRENT_NODE;
    } else if (isset ( $node ['url'] )) {
        $url = $node ['url'];
    }
    
    if (empty ( $url ) && is_array ( $node )) {
        $url = apply_filter ( "get_the_{$type}_url", false, $node );
        if (empty ( $url )) {
            return '#';
        }
    }
    if (preg_match ( '/index\.html?$/i', $url )) {
        $url = preg_replace ( '/index\.html?$/i', '', $url );
    }
    
    if (preg_match ( '#^(http|ftp)s?://#i', $url )) {
        return $url;
    } else {
        $url = ltrim ( $url, '/' );
        if (isset ( $node ['bind'] ) && ! empty ( $node ['bind'] )) { //绑定了二级域名
            if (! empty ( $node ['domain_home'] ) || ! empty ( $node ['home'] )) { //是二级域名的首页啦，要清空url
                $url = '';
            }
            return $protocol . $node ['bind'] . $domain . $port . '/' . $url;
        } else {
            return BASE_URL . $url;
        }
    }
}
/**
 * 记录debug信息
 *
 * @param string $message
 */
function log_debug($message) {
    $trace = debug_backtrace ();
    log_message ( $message, $trace, DEBUG_DEBUG, $trace );
}

/**
 * 记录info信息
 *
 * @param string $message
 */
function log_info($message) {
    $trace = debug_backtrace ();
    log_message ( $message, $trace, DEBUG_INFO, $trace );
}

/**
 * 记录warn信息
 *
 * @param string $message
 */
function log_warn($message) {
    $trace = debug_backtrace ();
    log_message ( $message, $trace, DEBUG_WARN, $trace );
}

/**
 * 记录error信息
 *
 * @param string $message
 */
function log_error($message) {
    $trace = debug_backtrace ();
    log_message ( $message, $trace, DEBUG_ERROR, $trace );
}
/**
 * 将模块路径，Action,参数等数据转换成url
 * @param string $module 模块路径
 * @param string $action Action
 * @param string|array $args 参数
 * @return string
 */
function murl($module, $action = '', $args = null) {
    static $em = false, $_urls = array ();
    $_args = '';
    if (is_string ( $args )) {
        $_args = ltrim ( $args, '?&' );
    } else if (is_array ( $args )) {
        $_args = array ();
        foreach ( $args as $key => $val ) {
            $_args [] = $key . '=' . urlencode ( $val );
        }
        $_args = implode ( '&', $_args );
    }
    $md5 = md5 ( $module . $action . $_args );
    if (isset ( $_urls [$md5] )) {
        return $_urls [$md5];
    }
    
    if (! $em) {
        $em = ExtensionManager::getInstance ();
    }
    $url = trailingslashit ( $em->getAlias ( $module ) );
    if (! empty ( $action )) {
        $url .= $action;
    }
    if (! empty ( $_args )) {
        $url .= '?' . $_args;
    }
    if (! defined ( 'CLEAN_URL' ) || CLEAN_URL == false) {
        $url = 'index.php/' . $url;
    }
    $url = BASE_URL . $url;
    $_urls [$md5] = $url;
    return $url;
}
/**
 * 
 * return the clean url
 * @param string $url
 * @return string
 */
function clean_url($url = '') {
    if (! defined ( 'CLEAN_URL' ) || CLEAN_URL == false) {
        $url = 'index.php/' . $url;
    }
    return BASE_URL . $url;
}
/**
 * 表格排序
 *
 * @param string $text
 * 表头文字
 * @param string $url
 * url,无参数的URL
 * @param string $filed
 * 排序字段
 * @param string $sort
 * 默认排序
 * @param string $url 
 * @return string
 */
function sortheader($text, $filed, $sort = 'd', $url = '') {
    static $_url = false;
    if (! $_url) {
        $_url = Request::getUri ();
    }
    if (empty ( $url )) {
        $url = $_url;
    }
    $stext = '';
    if (preg_match ( '/_sf=' . $filed . '/', $_SERVER ['QUERY_STRING'] )) {
        if (preg_match ( '/_sd=([ad])/', $_SERVER ['QUERY_STRING'], $_sort )) {
            $sort = $_sort [1] == 'a' ? 'd' : 'a';
            $stext = $_sort [1] == 'a' ? '<i class="asc"></i>' : '<i class="desc"></i>';
        }
    }
    ! empty ( $stext ) or $stext = '<i class="sdir"></i>';
    $qs = preg_replace ( array ('/[&\?]?_sf=[^&]*/', '/[&\?]?_sd=[^&]*/' ), array ('', '' ), $url );
    $ss = '_sf=' . $filed . '&_sd=' . $sort;
    $qs .= (strpos ( $qs, '?' ) === false ? '?' : '&') . $ss;
    return sprintf ( '<div class="sortheader"><a href="%s">%s</a>%s</div>', $qs, $text, $stext );
}
/**
 * 分页
 *
 *
 * @param int $total
 * 记录总数
 * @param int $limit
 * 每页记录数,默认为15
 * @param int $cur
 * 当前是第几页,默认从URL获取
 * @param string $param
 * 分页参数,默认为start
 * @param int $pp
 * 每页显示几条页数导航,默认为10
 * @param string $url
 * 分页链接,不需要添加参数
 * @return string 分页HTML片断
 */
function paging($total, $limit = 15, $cur = null, $param = 'start', $pp = 10, $url = null) {
    $req = Request::getInstance ();
    $cur != null or $cur = $req->get ( $param, 1 );
    if (empty ( $url )) {
        $url = Request::getUri ();
    }
    $limit = empty ( $limit ) ? 10 : $limit;
    $total = intval ( $total );
    $tp = ceil ( $total / $limit ); // 一共有多少页
    $qs = preg_replace ( '/[&\?]?' . $param . '=\d*/', '', $url );
    $qs .= (strpos ( $qs, '?' ) === false ? '?' : '&') . $param . '=';
    $url = $qs;
    $pager [] = '<ul>';
    $pager [] = sprintf ( '<li><a>共%s条记录,每页%d条记录,', $total, $limit );
    $_cp = $cur * $limit;
    $_cp = $_cp > $total ? $total : $_cp;
    if ($_cp > 0) {
        $pager [] = sprintf ( '第%s~%s条记录</a></li>', ($cur - 1) * $limit + 1, $_cp );
    } else {
        $pager [] = '</a></li>';
    }
    if ($tp > 1) {
        if ($cur == 1) { // 当前在第一页
            $pager [] = '<li><a>首</a></li><li><a>上</a></li>';
        } else {
            $pager [] = sprintf ( '<li><a title="第一页" href="%s">首</a></li><li><a title="上一页" href="%s">上</a></li>', $url . '1', $url . ($cur - 1) );
        }
        // 向前后各多少页
        $sp = $pp % 2 == 0 ? $pp / 2 : ($pp - 1) / 2;
        if ($cur <= $sp) {
            $start = 1;
            $end = $pp;
            $end = $end > $tp ? $tp : $end;
        } else {
            $start = $cur - $sp;
            $end = $cur + $sp;
            if ($pp % 2 == 0) {
                $end -= 1;
            }
            if ($end >= $tp) {
                $start -= ($end - $tp);
                $start > 0 or $start = 1;
                $end = $tp;
            }
        }
        for($i = $start; $i <= $end; $i ++) {
            if ($i == $cur) {
                $pager [] = sprintf ( '<li class="active"><a>%d</a></li>', $i );
            } else {
                $pager [] = sprintf ( '<li><a href="%s" title="第%d页">%d</a></li>', $url . $i, $i, $i );
            }
        }
        if ($cur == $tp) {
            $pager [] = '<li><a>下</a></li><li><a>尾</a></li>';
        } else {
            $pager [] = sprintf ( '<li><a title="下一页" href="%s">下</a></li><li><a title="最后一页" href="%s">尾</a></li>', $url . ($cur + 1), $url . $tp );
        }
    }
    $pager [] = '</ul>';
    return implode ( "", $pager );
}
/**
 * 生成带参数的页面url
 */
function build_page_url($url, $args) {
    static $params = null;
    if (is_null ( $params )) {
        parse_str ( $_SERVER ['QUERY_STRING'], $params );
        unset ( $params ['_url'] );
    }
    $url = explode ( '?', $url );
    $url = $url [0];
    $pargs = $params;
    if (! empty ( $args )) {
        $argnames = array_shift ( $args );
        $argnames = explode ( ',', $argnames );
        $i = 0;
        foreach ( $argnames as $n ) {
            if (preg_match ( '#^\-([a-z_][a-z\d_-]*)$#', $n, $m )) {
                unset ( $pargs [$m [1]] );
            } else {
                $pargs [$n] = $args [$i ++];
            }
        }
    }
    if (! empty ( $pargs ) && ! preg_match ( '/.*#$/', $url )) {
        if (strpos ( $url, '?' ) === false) {
            return $url . '?' . http_build_query ( $pargs );
        } else {
            return $url . '&' . http_build_query ( $pargs );
        }
    } else {
        return $url;
    }
}
/**
 * 
 * 生成html标签属性
 * @param array $properties
 * @return string
 */
function html_tag_properties($properties) {
    if (empty ( $properties )) {
        return '';
    }
    $tmp_ary = array ();
    foreach ( $properties as $name => $val ) {
        $name = trim ( $name );
        $tmp_ary [] = $name . '="' . $val . '"';
    }
    return ' ' . implode ( ' ', $tmp_ary ) . ' ';
}
/**
 * 
 * 合并二个数组，并将对应值相加
 * @param array $ary1
 * @param array $ary2
 * @param string $sep 相加时的分隔符
 * @return array 合并后的数组
 */
function merge_add($ary1, $ary2, $sep = ' ') {
    foreach ( $ary2 as $key => $val ) {
        if (isset ( $ary1 [$key] )) {
            if (is_array ( $ary1 [$key] ) && is_array ( $val )) {
                $ary1 [$key] = merge_add ( $ary1 [$key], $val );
            } else if (is_array ( $ary1 [$key] ) && ! is_array ( $val )) {
                $ary1 [$key] [] = $val;
            } else if (! is_array ( $ary1 [$key] ) && is_array ( $val )) {
                $val [] = $ary1 [$key];
                $ary1 [$key] = $val;
            } else {
                $ary1 [$key] = $ary1 [$key] . $sep . $val;
            }
        } else {
            $ary1 [$key] = $val;
        }
    }
    return $ary1;
}
/**
 * 显示消息提示页面
 *
 * @param string $type
 * 消息类型 
 * @param string $message
 * 消息内容
 * @param string $redirect
 * 跳转到URL
 * @param int $timeout
 * 跳转时间,当$redirect为空时，些值无效
 */
function show_message($type, $message, $redirect = '') {
    static $titles = array ('error' => '出错啦!', 'warning' => '警告!', 'info' => '提示!' );
    $msg ['type'] = $type;
    $msg ['message'] = $message;
    $msg ['title'] = $titles [$type];
    if (! Request::isAjaxRequest ()) { //html 
        if (Request::isGet ()) {
            $redirect = $redirect ? $redirect : Request::getUri ();
        } else {
            $redirect = $redirect ? $redirect : $_SERVER ['HTTP_REFERER'];
        }
        $msg ['redirect'] = $redirect;
        $view = view ( 'admin/layout/error.tpl', $msg );
    } else { //ajax        
        @header ( 'X-AJAX-MESSAGE: ' . $type );
        status_header ( 500 );
        $view = new JsonView ( $msg );
    }
    echo $view->render ();
    Response::getInstance ()->close ();
}
/**
 * 
 * @param string $message
 * @param string $redirect
 */
function show_error_message($message, $redirect = '') {
    show_message ( 'error', $message, $redirect );
}
/**
 *
 * @param string $message
 * @param string $redirect
 */
function show_warning_message($message, $redirect = '') {
    show_message ( 'warning', $message, $redirect );
}
/**
 *
 * @param string $message
 * @param string $redirect
 */
function show_info_message($message, $redirect = '') {
    show_message ( 'info', $message, $redirect );
}
/**
 * 将配置保存到文件
 * 
 * @param string $file
 * @param string $setting
 */
function save_setting_to_file($filename, $setting = 'default') {
    $settings = KissGoSetting::getSetting ( $setting );
    $file = "<?php\n//generated by kissgo,don't edit this file manually!\ndefined('KISSGO') or exit('No direct script access allowed');\n";
    $file .= "\$settings = KissGoSetting::getSetting();\n\n";
    $settings = $settings->toArray ();
    foreach ( $settings as $key => $value ) {
        if (is_array ( $value )) {
            $file .= "\$settings['$key'] = " . var_export ( $value, true ) . ";\n\n";
        } else if (is_numeric ( $value )) {
            $value = empty ( $value ) ? '0' : $value;
            $file .= "\$settings['$key'] = $value;\n\n";
        } else if (is_bool ( $value )) {
            $value = empty ( $value ) ? 'false' : 'true';
            $file .= "\$settings['$key'] = $value;\n\n";
        } else if (is_null ( $value )) {
            $file .= "\$settings['$key'] = null;\n\n";
        } else {
            $file .= "\$settings['$key'] = '{$value}';\n\n";
        }
    }
    $file .= "// end of $filename\n?>";
    $rst = @file_put_contents ( $filename, $file );
    if ($rst !== false) {
        return true;
    }
    return '无法写入配置文件 [' . $filename . '] 请检查目录是否有可写权限.';
}
/**
 * 原封不动引用
 * @param string $reference
 */
function imtv($value) {
    return new DbImmutableV ( $value );
}
function imtf($field, $alias = null) {
    return new DbImmutableF ( $field, $alias );
}
/**
 * generates a condition array that can be used by DbSqlHelper::where
 * @param array $keys fields
 * example:
 * <code>
 * array(
 * 'field_name'=>array(
 * 'like'=>array(
 * 'name'=>'the name of form',
 * 'default'=>'default value',
 * 'suffix'=>'append string to the value',
 * 'prefix'=>'preppend string to the value'
 * )
 * ),
 * 'field_name1'=>array(
 * '>'=>array('name'....),
 * '<'=>array('name'....)
 * ),
 * 'field_name2'=>array(
 * array('name'=>....)
 * )
 * )
 * </code>
 * @param array $data
 * @return array generated condition array
 */
function where($keys, &$data = null) {
    static $req = false;
    if (! $req) {
        $req = Request::getInstance ( true );
    }
    $data = is_array ( $data ) ? $data : array ();
    $where = array ();
    foreach ( $keys as $key => $def ) {
        if (! is_array ( $def )) {
            $key = $filed = $def;
            $def = array ($def );
        } else if (isset ( $def ['name'] )) {
            $filed = $def ['name'];
            unset ( $def ['name'] );
        } else {
            $filed = $key;
        }
        foreach ( $def as $op => $extra ) {
            $extra = is_array ( $extra ) ? $extra : array ($extra );
            if (! is_numeric ( $op )) {
                switch ($op) {
                    case 'like' :
                        if (! isset ( $extra ['suffix'] )) {
                            $extra ['suffix'] = '%';
                        }
                        if (! isset ( $extra ['prefix'] )) {
                            $extra ['prefix'] = '%';
                        }
                        break;
                    case 'llike' :
                        if (! isset ( $extra ['suffix'] )) {
                            $extra ['suffix'] = '%';
                        }
                        $op = 'like';
                        break;
                    case 'rlike' :
                        if (! isset ( $extra ['prefix'] )) {
                            $extra ['prefix'] = '%';
                        }
                        $op = 'like';
                        break;
                }
                $key = $filed . ' ' . $op;
            }
            $default = null;
            $suffix = null;
            $prefix = null;
            if (is_array ( $extra )) {
                if (! isset ( $extra ['name'] )) {
                    $name = $filed;
                } else {
                    $name = $extra ['name'];
                }
                if (isset ( $extra ['default'] )) {
                    $default = $extra ['default'];
                }
                if (isset ( $extra ['suffix'] )) {
                    $suffix = $extra ['suffix'];
                }
                if (isset ( $extra ['prefix'] )) {
                    $prefix = $extra ['prefix'];
                }
            } else {
                $name = $extra;
            }
            $value = null;
            if (! isset ( $req [$name] ) && $default != null) {
                $value = $req [$name];
            } else if (isset ( $req [$name] )) {
                $value = $req [$name];
            }
            
            if (empty ( $value ) && $value != '0') {
                continue;
            }
            
            $data [$name] = $value;
            if ($prefix != null) {
                $value = $prefix . $value;
            }
            if ($suffix != null) {
                $value .= $suffix;
            }
            
            $where [$key] = $value;
        }
    }
    return $where;
}
function randstr($len = 6, $format = "ALL") {
    switch ($format) {
        case "CHAR" :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            break;
        case "NUMBER" :
            $chars = "0123456789";
            break;
        case "ALL" :
        default :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_+=-";
    }
    $string = "";
    while ( strlen ( $string ) < $len ) {
        $string .= substr ( $chars, mt_rand () % strlen ( $chars ), 1 );
    }
    return $string;
}
function get_theme() {
    return apply_filter ( 'get_current_theme', THEME );
}
// end of file functions.php