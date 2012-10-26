<?php
/**
 * 解析smarty参数.
 *
 * 将参数中 '" 去除比,如 '1' 转换为1.
 *
 * @param array $args
 * 参数数组
 * @return array 解析后的参数
 */
function smarty_parse_args($args) {
    foreach ($args as $key => $value) {
        if (strpos($value, '_smarty_tpl->tpl_vars') !== false) {
            $args [$key] = trim($value, '\'"');
        }
    }
    return $args;
}

/**
 *
 *
 * 将smarty传过来的参数转换为可eval的字符串
 *
 * @param array $args
 * @return string
 */
function smarty_argstr($args) {
    $a = array();
    foreach ($args as $k => $v) {
        $v1 = trim($v);
        if (empty ($v1)) {
            continue;
        }
        $a [] = "'$k'=>$v";
    }
    return 'array(' . implode(',', $a) . ')';
}

/**
 *
 * 输入安全URL
 * @param $page
 * @internal param string $url
 * @return string
 */
function the_safe_url($page) {
    global $_PAGE;
    static $domain = false, $proto = false, $port = '';
    if (!$domain) {
        $domain = preg_match('#^https?://#i', BASE_URL) ? preg_replace('#^https?://#i', '', trim(BASE_URL, '/')) : $_SERVER ['HTTP_HOST'];
        $domain = strstr($domain, ".");
        $proto = isset ($_SERVER ['HTTPS']) ? 'https://' : 'http://';
        $port = intval($_SERVER ['SERVER_PORT']) == 80 ? '' : ':' . $_SERVER ['SERVER_PORT'];
    }
    if (is_string($page)) {
        $url = $page;
        $page = $_PAGE;
    } else {
        $url = $page ['url'];
    }
    if (preg_match('/index\.html?$/i', $url)) {
        $url = preg_replace('/index\.html?$/i', '', $url);
    }
    if (preg_match('#^(http|ftp)s?://#i', $url)) {
        return $url;
    } else {
        $url = ltrim($url, '/');
        if (isset ($page ['bind']) && !empty ($page ['bind'])) { //绑定了二级域名
            if (!empty ($page ['domain_home']) || !empty ($page ['home'])) { //是二级域名的首页啦，要清空url
                $url = '';
            }
            return $proto . $page ['bind'] . $domain . $port . '/' . $url;
        } else {
            return BASE_URL . $url;
        }
    }
}

/**
 * Smarty here modifier plugin
 *
 * <code>
 * {'images/logo.png'|here}
 * </code>
 * 以上代表输出模板所在目录下的images/logo.png
 *
 * Type: modifier<br>
 * Name: here<br>
 * Purpose: 输出模板所在目录下资源的URL
 *
 * @staticvar string WEBROOT的LINUX表示.
 * @param array $params
 * 参数
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_here($params, $compiler) {
    static $base = null;
    if ($base == null) {
        $base = str_replace(DS, '/', WEB_ROOT);
    }
    $tpl = str_replace(DS, '/', dirname($compiler->template->source->filepath));
    $tpl = str_replace($base, '', $tpl);
    $url = BASE_URL . (!empty ($tpl) ? trailingslashit($tpl) : '');
    return "'{$url}'." . $params [0];
}

function smarty_modifiercompiler_static($params, $compiler) {
    $tpl = defined('STATIC_DIR') ? STATIC_DIR : 'static';
    $url = (!empty ($tpl) ? trailingslashit($tpl) : '');
    return "BASE_URL.'{$url}'." . $params [0];
}

/**
 *
 * 输出URL
 * @param array $params
 * @param mixed $compiler
 */
function smarty_modifiercompiler_url($params, $compiler) {
    $page = array_shift($params);
    $args = empty ($params) ? array() : smarty_argstr($params);
    if (!empty ($args)) {
        $output = "build_page_url(the_safe_url({$page}),$args)";
    } else {
        $output = "the_safe_url({$page})";
    }
    return $output;
}

/**
 * Smarty fire modifier plugin
 *
 * <code>
 * {'hook'|fire}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: fire<br>
 * Purpose: 调用系统触发器
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_fire($hook, $compiler) {
    $filter = $hook [0];
    $args = isset ($hook [1]) ? $hook [1] : "''";
    $args1 = isset ($hook [2]) ? $hook [2] : "''";
    return "apply_filter({$filter},'',{$args},{$args1})";
}

/**
 * Smarty checked modifier plugin
 *
 * <code>
 * {'0'|checked:$value}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: checked<br>
 * Purpose: 根据值输出checked="checked"
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_checked($value, $compiler) {
    return "((is_array($value[1]) && in_array($value[0],$value[1]) ) || $value[0] == $value[1])?'checked = \"checked\"' : ''";
}

/**
 * Smarty paging modifier plugin
 *
 * <code>
 * {url|paging:total:limit:paging_arg:num_per_page}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: paging<br>
 * Purpose: 输出分页
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_paging($value, $compiler) {
    if (count($value) < 2) {
        trigger_error('error usage of paging', E_USER_WARNING);
        return "'error usage of paging'";
    }
    $url = $value [0]; // url
    $total = $value [1]; // 总数
    if (isset ($value [2])) {
        $limit = $value [2]; // 每页显示的条数
    } else {
        $limit = 10; // 默认显示10
    }
    if (isset ($value [3])) {
        $param = $value [3]; // 分类参数
    } else {
        $param = "'pid'";
    }
    if (isset ($value [4])) {
        $pno = $value [4]; // 分类参数
    } else {
        $pno = 4;
    }
    $output = "paging($total,$limit,null,$param,$pno,$url)";
    return $output;
}

/**
 * Smarty status modifier plugin
 *
 * <code>
 * {value|status:list}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: status<br>
 * Purpose: 将值做为LIST中的KEY输出LIST对应的值
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_status($status, $compiler) {
    if (count($status) < 2) {
        trigger_error('error usage of status', E_USER_WARNING);
        return "'error usage of status'";
    }
    $key = "$status[0]";
    $status_str = "$status[1]";
    $output = "$status_str" . "[$key]";
    return $output;
}

/**
 * Smarty sorth modifier plugin
 *
 * <code>
 * {text|sorth:field:[a|d]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: sorth<br>
 * Purpose: 生成排序头
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_sorth($sorth, $compiler) {
    if (count($sorth) < 3) {
        trigger_error('error usage of sorth', E_USER_WARNING);
        return "'error usage of sorth'";
    }
    $text = $sorth [0];
    $field = $sorth [1];
    $url = $sorth [2];
    if (isset ($sorth [3])) {
        $dir = $sorth [3];
    } else {
        $dir = "'d'";
    }
    $output = "sortheader($text,$url,$field,$dir)";
    return $output;
}

/**
 * Smarty thumb modifier plugin
 *
 * <code>
 * {original|thumb:[x|y]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: sorth<br>
 * Purpose: 生成排序头
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_thumb($thumb, $compiler) {
    if (count($thumb) < 2) {
        trigger_error('error usage of thumb', E_USER_WARNING);
        return "'error usage of thumb'";
    }
    $url = $thumb [0];
    $w = intval($thumb [1]);
    $h = isset ($thumb [2]) ? intval($thumb [2]) : $w;
    $output = "the_thumbnail_src($url,$w,$h)";
    return $output;
}

function smarty_modifiercompiler_random($ary, $compiler) {
    if (count($ary) < 1) {
        trigger_error('error usage of random', E_USER_WARNING);
        return "'error usage of random'";
    }
    $output = "is_array({$ary[0]})?{$ary[0]}[array_rand({$ary[0]})]:''";
    return $output;
}

function smarty_modifiercompiler_ts($ary, $compiler) {
    if (count($ary) < 1) {
        trigger_error('error usage of ts', E_USER_WARNING);
        return "''";
    }
    $string = array_shift($ary);
    if (!empty($ary)) {
        $args = smarty_argstr($ary);
        $output = "__({$string},$args)";
    } else {
        $output = "__({$string})";
    }
    return $output;
}

function smarty_modifiercompiler_params($ary, $compiler) {
    if (count($ary) < 1) {
        trigger_error('error usage of params', E_USER_WARNING);
        return "'error usage of params'";
    }
    $url = array_shift($ary);
    $args = empty ($ary) ? array() : smarty_argstr($ary);
    $output = "build_page_url($url,$args)";
    return $output;
}