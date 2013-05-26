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
 * 数据提供器可以提供的结果
 */
class CtsData implements Iterator {
    private $data = array ();
    private $pos = 0;
    private $total = 0;
    private $countTotal = 0;
    private $per = 10;
    public function __construct($data, $countTotal = 0, $per = 0) {
        $this->data = $data;
        if (is_array ( $data ) || $data instanceof ResultCursor) {
            $this->total = count ( $data );
        }
        $this->countTotal = $countTotal;
        $this->per = $per <= 0 ? 1 : intval ( $per );
    }
    
    /**
     * 取用于ctv标签的数据
     *
     * @return mixed
     */
    public function assign() {
        if (is_array ( $this->data ) || $this->data instanceof ResultCursor) {
            return empty ( $this->data ) ? array () : $this->data [0];
        } else {
            return $this->data;
        }
    }
    public function current() {
        if (is_array ( $this->data ) || $this->data instanceof ResultCursor) {
            return $this->data [$this->pos];
        }
        return null;
    }
    public function next() {
        $this->pos ++;
    }
    public function key() {
        return $this->pos;
    }
    public function valid() {
        return $this->pos < $this->total;
    }
    public function rewind() {
        $this->pos = 0;
    }
    
    /**
     * 绘制分页
     * @param string $render
     * @param array $options
     * @return array
     */
    public final function onPagingRender($render, $options) {
        global $_current_page;
        $_current_page = $_current_page == null ? 1 : $_current_page;
        $url = explode ( '.', Request::getUri () );
        $ext = array_pop ( $url );
        $paging = array ('prefix' => implode ( '.', $url ) . '_', 'current' => $_current_page, 'total' => $this->countTotal, 'limit' => $this->per, 'ext' => '.' . $ext );
        $paging_data = apply_filter ( 'on_render_paging_by_' . $render, array (), $paging, $options );
        if (empty ( $paging_data )) {
            $paging_data = $this->getPageInfo ( $paging, $options );
        } else if (is_array ( $paging_data )) {
            $paging_data = array_merge2 ( array ('total' => ceil ( $this->countTotal / $this->per ), 'ctotal' => $this->countTotal, 'first' => '#', 'prev' => '#', 'next' => '#', 'last' => '#' ), $paging_data );
        }
        return $paging_data;
    }
    
    /**
     * 取分页
     * @param $paging
     */
    private function getPageInfo($paging, $args) {
        $_c_url = Request::getUri ();
        $url = safe_url ( $paging ['prefix'] );
        $cur = $paging ['current'];
        $total = $paging ['total'];
        $per = $paging ['limit'];
        $ext = $paging ['ext'];
        $tp = ceil ( $total / $per ); // 一共有多少页
        $pager = array ();
        if ($tp < 2) {
            return $pager;
        }
        $pager ['total'] = $tp;
        $pager ['ctotal'] = $total;
        if ($cur == 1) { // 当前在第一页
            $pager ['first'] = '#';
            $pager ['prev'] = '#';
        } else {
            $pager ['first'] = $_c_url;
            $pager ['prev'] = $cur == 2 ? $_c_url : $url . ($cur - 1) . $ext;
        }
        // 向前后各多少页
        $pp = isset ( $args ['pp'] ) ? intval ( $args ['pp'] ) : 10;
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
                $pager [$i] = '#';
            } else if ($i == 1) {
                $pager [$i] = BASE_URL . $_c_url;
            } else {
                $pager [$i] = $url . $i . $ext;
            }
        }
        if ($cur == $tp) {
            $pager ['next'] = '#';
            $pager ['last'] = '#';
        } else {
            $pager ['next'] = $url . ($cur + 1) . $ext;
            $pager ['last'] = $url . $tp . $ext;
        }
        return $pager;
    }
}

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
        }
    }
    if ($data instanceof CtsData) {
        return $data;
    } else {
        return new CtsData ( array () );
    }
}
function get_theme_resource_uri($args) {
    if (isset ( $args [1] )) {
        $url = $args [1];
    } else {
        $url = get_theme();
    }
    return BASE_URL . THEME_DIR . '/' . $url . '/' . $args [0];
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
    $theme = get_theme();
    $_tpl = THEME_DIR . '/' . $theme . '/' . $tpl;
    if (is_file ( THEME_PATH . $_tpl )) {
        $tpl = $_tpl;
    } else {
        $tpl = THEME_DIR . '/defaults/' . $tpl;
    }
    $data ['ksg_current_template'] = $tpl;
    $data ['ksg_current_theme'] = THEME_DIR . '/' . $theme;
    $data ['ksg_theme_name'] = $theme;
    $data ['ksg_theme_dir'] = THEME_DIR;
    $data ['ksg_module'] = MODULE_DIR;
    $data ['ksg_admincp_url'] = murl ( 'admin' );
    return new ThemeView ( $data, $tpl, $headers );
}
/**
 * load the template for module view
 *
 * @param $tpl
 * @param array $data
 * @param array $headers
 * @return SmartyView
 */
function view($tpl, $data = array(), $headers = array('Content-Type'=>'text/html')) {
    $theme = get_theme();
    $data ['ksg_current_template'] = $tpl;
    $data ['ksg_current_theme'] = THEME_DIR . '/' . $theme;
    $data ['ksg_theme_name'] = $theme;
    $data ['ksg_theme_dir'] = THEME_DIR;
    $data ['ksg_module'] = MODULE_DIR;
    $admincp_layout = THEME_PATH . $theme . DS . 'admin/layout.tpl';
    if (is_file ( $admincp_layout )) {
        $data ['ksg_admincp_layout'] = THEME_DIR . '/' . $theme . '/admin/layout.tpl';
    } else {
        $data ['ksg_admincp_layout'] = THEME_DIR . '/defaults/admin/layout.tpl';
    }
    $data ['ksg_admincp_url'] = murl ( 'admin' );
    return new SmartyView ( $data, MODULE_DIR . '/' . $tpl, $headers );
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
    $tpl = defined ( 'STATIC_DIR' ) ? STATIC_DIR : 'static';
    $url = (! empty ( $tpl ) ? trailingslashit ( $tpl ) : '');
    return "BASE_URL.'{$url}'." . $params [0];
}
function smarty_modifiercompiler_theme($params, $compiler) {    
    $params = smarty_argstr ( $params );
    return "get_theme_resource_uri($params)";
}
function smarty_modifiercompiler_img($params, $compiler) {
    $tpl = defined ( 'STATIC_DIR' ) ? STATIC_DIR : 'static';
    $url = (! empty ( $tpl ) ? trailingslashit ( $tpl ) : '');
    return "BASE_URL.'{$url}'." . $params [0];
}
function smarty_modifiercompiler_uploaded($params, $compiler) {    
    return "BASE_URL." . $params [0];
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
    $args = empty ( $params ) ? array () : smarty_argstr ( $params );
    if (! empty ( $args )) {
        $output = "build_page_url(safe_url({$page}),$args)";
    } else {
        $output = "safe_url({$page})";
    }
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
 * Smarty mrul modifier plugin
 *
 * <code>
 * {module|thumb:[action]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: murl<br>
 * Purpose: 生成模块的访问路径
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_murl($value, $compiler) {
    if (count ( $value ) < 1) {
        trigger_error ( 'error usage of murl', E_USER_WARNING );
        return "#";
    }
    $module = $value [0]; // url
    $action = "''";
    if (isset ( $value [1] )) {
        $action = $value [1]; // 每页显示的条数
    }
    $output = "murl($module,$action)";
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
    if (! empty ( $ary )) {
        $name = $ary [0];
        if (isset ( $ary [1] )) {
            $default = $ary [1];
        }
    } else {
        $name = "'default'";
    }
    $output = "KissGoSetting::getSetting($name)->get($option, $default)";
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
/**
 * Smarty form modifier plugin
 *
 * <code>
 * {$form|form:[component[:widget]]}
 * </code>
 *
 *
 * Type: modifier<br>
 * Name: form<br>
 * Purpose: 输出表单
 *
 * @param Smarty $compiler
 * @return string with compiled code
 */
function smarty_modifiercompiler_form($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of form', E_USER_WARNING );
        return "''";
    }
    $form = $ary [0];
    if (isset ( $ary [2] )) {
        $name = trim ( $ary [2], "'\"" );
        $component = trim ( $ary [1], "'\"" );
        return "{$form}->render('$name',$component)";
    } else if (isset ( $ary [1] )) {
        $name = trim ( $ary [1], "'\"" );
        return "{$form}->render('$name',null)";
    }
    return "{$form}->render()";
}
function smarty_modifiercompiler_render($ary, $compiler) {
    if (count ( $ary ) < 1) {
        trigger_error ( 'error usage of render', E_USER_WARNING );
        return "''";
    }
    $render = $ary [0];
    return "{$render}->render()";
}