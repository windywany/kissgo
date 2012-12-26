<?php
class ThemeView extends View {
    /**
     * @var Smarty Smarty
     */
    private $__smarty;
    
    public function __construct($data = array(), $tpl = '', $headers = array('Content-Type'=>'text/html')) {
        if(!isset($headers['Content-Type'])){
            $headers['Content-Type'] = 'text/html';
        }
        parent::__construct ( $data, $tpl, $headers );
        $basedir = THEME_PATH;
        $tpl = $basedir . $this->tpl;
        if (is_file ( $tpl )) {
            $this->__smarty = new Smarty ();
            $this->__smarty->addPluginsDir ( KISSGO . 'vendors/smarty/user_plugins' );
            $this->__smarty->template_dir = $basedir; //模板目录
            $tpl = str_replace ( DS, '/', $this->tpl );
            $this->data ['_current_template_file'] = $tpl;
            $tpl = explode ( '/', $tpl );
            array_pop ( $tpl );
            $sub = implode ( DS, $tpl );
            $this->__smarty->compile_dir = TMP_PATH . 'themes_c' . DS . $sub; //模板编译目录
            $this->__smarty->cache_dir = TMP_PATH . 'themes_cache' . DS . $sub; //模板缓存目录
            $this->__smarty->_dir_perms = 0775;
            if (DEBUG) {
                $this->__smarty->force_compile = true;
            }
            $this->__smarty = apply_filter ( 'init_smarty_engine_for_theme', $this->__smarty );
        } else {
            trigger_error ( 'The view template ' . $tpl . ' is not found', E_USER_ERROR );
        }
    }
    
    /**
     * 绘制
     */
    public function render() {
        $data = $this->data;
        foreach ( $data as $n => $v ) {
            $this->__smarty->assign ( $n, $v ); //变量
        }
        return $this->__smarty->fetch ( $this->tpl );
    }
}