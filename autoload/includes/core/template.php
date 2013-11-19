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
 * @param $name
 * @param $provider
 */
function register_cts_provider($name, $provider, $desc = '') {
    static $providers = false;
    if (! $providers) {
        $providers = KissGoSetting::getSetting ( 'cts_providers' );
    }
    $providers [$name] = array ($provider, $desc );
}
/**
 * generate a url to access module action
 * 
 * @param string $module
 * @param string $action
 * @param array $args
 */
function the_ctr_url($module, $action = '', $args = array()) {
    if ($args) {
        $args = '&=' . http_build_query ( $args );
    } else {
        $args = '';
    }
    if ($action) {
        $action = '/' . $action;
    }
    return MODULE_URL . '?do=' . $module . $action . $args;
}
/**
 * @param $name
 * @param $method
 * @param $args
 */
function get_data_from_cts_provider($name, $args) {
    $providers = KissGoSetting::getSetting ( 'cts_providers' );
    $data = null;
    if ($providers && isset ( $providers [$name] )) {
        $provider = $providers [$name];
        $provider = $provider [0];
        if (is_callable ( $provider )) {
            $data = call_user_func_array ( $provider, array ($args ) );
        } else if (is_array ( $provider )) {
            list ( $cb, $file ) = $provider;
            imports ( $file );
            if (is_callable ( $cb )) {
                $data = call_user_func_array ( $cb, array ($args ) );
            }
        }
    }
    if ($data instanceof CtsData) {
        return $data;
    } else {
        return new CtsData ();
    }
}
function get_theme_resource_uri($args) {
    if (isset ( $args [1] )) {
        $url = $args [1];
    } else {
        $url = get_theme ();
    }
    return THEME_URL . $url . '/' . $args [0];
}
function the_static_resource_uri($resource) {
    return ASSETS_URL . $resource;
}
function get_prefer_tpl($tpl, $node) {
    $tplx = THEME_PATH . THEME_DIR . DS . $tpl;
    if (is_file ( $tplx )) {
        return $tpl;
    }
    $pinfo = pathinfo ( $tpl, PATHINFO_FILENAME );
    $dirs = array (THEME_PATH . THEME_DIR . DS . 'default' . DS );
    $theme = get_theme ();
    if ($theme != 'default') {
        array_unshift ( $dirs, THEME_PATH . THEME_DIR . DS . $theme . DS );
    }
    $files = array ($pinfo . '-' . $node ['nid'] . '.tpl', $pinfo . '-' . $node ['node_type'] ['type'] . '-' . $node ['node_id'] . '.tpl', $pinfo . '-' . $node ['node_type'] ['type'] . '.tpl', $tpl );
    foreach ( $dirs as $dir ) {
        foreach ( $files as $f ) {
            if (file_exists ( $dir . $f )) {
                return $f;
            }
        }
    }
    log_warn ( 'The template file ' . $tpl . ' dose not exist.' );
    return '404.tpl';
}
/**
 * 
 * merge arguments
 * @param array $args the array to be merged
 * @param array $default the array to be merged with
 * @return array the merged arguments array
 */
function merge_args($args, $default) {
    $_args = array ();
    foreach ( $args as $key => $val ) {
        if (is_numeric ( $val ) || is_bool ( $val ) || ! empty ( $val )) {
            $_args [$key] = $val;
        }
    }
    foreach ( $default as $key => $val ) {
        if (! isset ( $_args [$key] )) {
            $_args [$key] = $val;
        }
    }
    return $_args;
}
/**
 * load the template view
 *
 * @param $tpl
 * @param array $data
 * @param array $headers
 * @return ThemeView
 */
function template($tpl, $data = array(), $headers = array('Content-Type'=>'text/html')) {
    $theme = get_theme ();
    $_tpls [] = THEME_DIR . '/' . $tpl;
    $_tpls [] = THEME_DIR . '/' . $theme . '/' . $tpl;
    $found = false;
    foreach ( $_tpls as $_tpl ) {
        if (is_file ( THEME_PATH . $_tpl )) {
            $tpl = $_tpl;
            $found = true;
            break;
        }
    }
    if (! $found) {
        $tpl = THEME_DIR . '/default/' . $tpl;
    }
    $data ['_current_template'] = $tpl;
    $data ['_current_theme_path'] = THEME_DIR . '/' . $theme;
    $data ['_theme_name'] = $theme;
    $data ['_theme_dir'] = THEME_DIR;
    $data ['_module_dir'] = MODULE_DIR;
    return new ThemeView ( $data, $tpl, $headers );
}
/**
 * the views in modules
 * 
 * @param string $tpl
 * @param array $data
 * @param array $headers
 */
function view($tpl, $data = array(), $headers = array('Content-Type'=>'text/html')) {
    return new SmartyView ( $data, $tpl, $headers );
}
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
    foreach ( $args as $key => $value ) {
        if (strpos ( $value, '_smarty_tpl->tpl_vars' ) !== false) {
            $args [$key] = trim ( $value, '\'"' );
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
    $a = array ();
    foreach ( $args as $k => $v ) {
        $v1 = trim ( $v );
        if (empty ( $v1 )) {
            continue;
        }
        $a [] = "'$k'=>$v";
    }
    return 'array(' . implode ( ',', $a ) . ')';
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
        $base = str_replace ( DS, '/', WEB_ROOT );
    }
    $tpl = str_replace ( DS, '/', dirname ( $compiler->template->source->filepath ) );
    $tpl = str_replace ( $base, '', $tpl );
    $url = BASE_URL . (! empty ( $tpl ) ? trailingslashit ( $tpl ) : '');
    return "'{$url}'." . $params [0];
}
/**
 * Smarty static modifier plugin
 *
 * <code>
 * {resource|static}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: static<br>
 * Purpose: 取静态资源的URL
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_static($params, $compiler) {
    return "ASSETS_URL." . $params [0];
}
function smarty_modifiercompiler_theme($params, $compiler) {
    $params = smarty_argstr ( $params );
    return "get_theme_resource_uri($params)";
}
function smarty_modifiercompiler_uploaded($params, $compiler) {
    return "UPLOAD_URL." . $params [0];
}
/**
 * Smarty url modifier plugin
 *
 * <code>
 * {<$page|url>|url:[args]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: url<br>
 * Purpose: 生成url,并添加或删除相应的参数
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_url($params, $compiler) {
    $page = array_shift ( $params );
    $type = 'node';
    if (! empty ( $params )) {
        $type = trim ( $params [0], '"\'' );
    }
    $output = "safe_url({$page},'{$type}')";
    return $output;
}

/**
 * Smarty fire modifier plugin
 *
 * <code>
 * {'hook'|fire:[args]}
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
    $args = isset ( $hook [1] ) ? $hook [1] : "''";
    $args1 = isset ( $hook [2] ) ? $hook [2] : "''";
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
    if (count ( $status ) < 2) {
        trigger_error ( 'error usage of status', E_USER_WARNING );
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
    if (count ( $sorth ) < 2) {
        trigger_error ( 'error usage of sorth', E_USER_WARNING );
        return "'error usage of sorth'";
    }
    $text = $sorth [0];
    $field = $sorth [1];
    if (isset ( $sorth [2] )) {
        $dir = $sorth [2];
    } else {
        $dir = "'d'";
    }
    if (isset ( $sorth [3] )) {
        $url = $sorth [3];
    } else {
        $url = "''";
    }
    $output = "sortheader($text,$field,$dir,$url)";
    return $output;
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
    if (count ( $value ) < 2) {
        trigger_error ( 'error usage of paging', E_USER_WARNING );
        return "'error usage of paging'";
    }
    $total = $value [0]; // 总数
    if (isset ( $value [1] )) {
        $limit = $value [1]; // 每页显示的条数
    } else {
        $limit = 10; // 默认显示10
    }
    if (isset ( $value [2] )) {
        $param = $value [2]; // 分类参数
    } else {
        $param = "'start'";
    }
    if (isset ( $value [3] )) {
        $pno = $value [3]; // 分类参数
    } else {
        $pno = 4;
    }
    $output = "paging($total,$limit,null,$param,$pno,null)";
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
    if (count ( $thumb ) < 2) {
        trigger_error ( 'error usage of thumb', E_USER_WARNING );
        return "'error usage of thumb'";
    }
    $url = $thumb [0];
    $w = intval ( $thumb [1] );
    $h = isset ( $thumb [2] ) ? intval ( $thumb [2] ) : $w;
    $output = "the_thumbnail_src($url,$w,$h)";
    return $output;
}
function smarty_modifiercompiler_random($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of random', E_USER_WARNING );
        return "'error usage of random'";
    }
    $output = "is_array({$ary[0]})?{$ary[0]}[array_rand({$ary[0]})]:''";
    return $output;
}
/**
 * Smarty ts modifier plugin
 *
 * <code>
 * {string|ts}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: ts<br>
 * Purpose: 翻译
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_ts($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of ts', E_USER_WARNING );
        return "''";
    }
    $string = array_shift ( $ary );
    if (! empty ( $ary )) {
        $args = smarty_argstr ( $ary );
        $output = "__({$string}, $args)";
    } else {
        $output = "__({$string})";
    }
    return $output;
}
/**
 * Smarty cfg modifier plugin
 *
 * <code>
 * {option|cfg:[group]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: cfg<br>
 * Purpose: 读取配置信息
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_cfg($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of cfg', E_USER_WARNING );
        return "''";
    }
    $option = array_shift ( $ary );
    $default = "''";
    if (isset ( $ary [0] )) {
        $default = $ary [0];
    }
    $output = "cfg($option, $default)";
    return $output;
}
/**
 * Smarty params modifier plugin
 *
 * <code>
 * {url|params:[args]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: params<br>
 * Purpose: 为URL添加或删除参数
 * @see build_page_url()
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_params($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of params', E_USER_WARNING );
        return "'error usage of params'";
    }
    $url = array_shift ( $ary );
    $args = empty ( $ary ) ? array () : smarty_argstr ( $ary );
    $output = "build_page_url($url,$args)";
    return $output;
}
function smarty_modifiercompiler_cm($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of cm', E_USER_WARNING );
        return "'error usage of cm'";
    }
    $args = empty ( $ary ) ? array () : smarty_argstr ( $ary );
    $output = "is_in_current_menu($args)";
    return $output;
}