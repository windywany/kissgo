<?php
/**
 * Smarty视图
 *
 * 通过Smarty模板引擎绘制视图。
 *
 * @author Leo Ning <leo.ning@like18.com> 2010-11-14 12:25
 * @version 1.0
 * @since 1.0
 * @copyright 2008-2011 LIKE18 INC.
 * @package view
 */
class SmartyView extends View {
    /**
     * @var Smarty Smarty
     */
    private $__smarty;

    public function __construct($data = array(), $tpl = '', $headers = array()) {
        parent::__construct($data, $tpl, $headers);
        $basedir = TEMPLATE_PATH;
        $tpl = $basedir . $this->tpl;
        if (is_file($tpl)) {
            $this->__smarty = new Smarty ();
            $this->__smarty->addPluginsDir(KISSGO . 'vendors/smarty/user_plugins');
            $this->__smarty->template_dir = $basedir; //模板目录
            $tpl = str_replace(DS, '/', $this->tpl);
            $tpl = explode('/', $tpl);
            array_pop($tpl);
            $sub = implode(DS, $tpl);
            $this->__smarty->compile_dir = TMP_PATH . 'tpls_c' . DS . $sub; //模板编译目录
            $this->__smarty->cache_dir = TMP_PATH . 'tpls_cache' . DS . $sub; //模板缓存目录
            $this->__smarty->_dir_perms = 0775;
            if (DEBUG) {
                $this->__smarty->force_compile = true;
            }
            $this->__smarty = apply_filter('init_smarty_engine', $this->__smarty);
        } else {
            trigger_error('The view template ' . $tpl . ' is not found', E_USER_ERROR);
        }
    }


    /**
     * 绘制
     */
    public function render() {
        $data = $this->data;
        foreach ($data as $n => $v) {
            $this->__smarty->assign($n, $v); //变量
        }
        return $this->__smarty->fetch($this->tpl);
    }

    public function setHeader() {
        @header('Content-Type: text/html');
    }
}