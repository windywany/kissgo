<?php
/*
 * Module ID: cn.usephp.core.gui 
 * Module Name: KissGO GUI 
 * Module URI: http://www.usephp.cn/modules/core/ui.html 
 * Description: KissGO管理界面。 
 * Author: Leo Ning 
 * Version: 1.0 
 * Author URI: http://www.usephp.cn/
 */
defined ( 'KISSGO' ) or exit ( 'No direct script access allowed' );

function on_install_module_cn_usephp_core_gui($rst) {
    imports ( 'admin/models/*' );
    $modelfiles = find_files ( MODULES_PATH . 'admin/models', '#.+Table\.php$#' );
    foreach ( $modelfiles as $mf ) {
        $p = pathinfo ( $mf, PATHINFO_FILENAME );
        $models [] = $p;
    }
    $rtn = true;
    foreach ( $models as $model ) {
        if (! PdoDialect::createTable ( new $model () )) {
            $rtn = db_error ();
            break;
        }
    }
    
    if ($rtn === true) {
        $types [] = array ('type' => 'plain', 'name' => '简单页面', 'template' => 'page.tpl', 'note' => '简单页面' );
        $types [] = array ('type' => 'index', 'name' => '首页', 'creatable' => 0, 'template' => 'index.tpl', 'note' => '网站首页, 用户不能直接创建.' );
        $types [] = array ('type' => 'catalog', 'name' => '目录页', 'creatable' => 0, 'template' => 'catalog.tpl', 'note' => '目录列表页, 栏目页, 分类页等等.' );
        $types [] = array ('type' => 'tag', 'name' => '标签页面', 'creatable' => 0, 'template' => 'tag.tpl', 'note' => '标签页面(如果有相应的类型页模板则优先使用)' );
        $nt = new KsgNodeTypeTable ();
        foreach ( $types as $type ) {
            if (! $nt->insert ( $type )) {
                $rtn = db_error ();
                break;
            }
        }
    }
    
    return empty ( $rtn ) ? '安装核心模块出错啦！' : $rtn;
}
bind ( 'on_install_module_cn.usephp.core.gui', 'on_install_module_cn_usephp_core_gui' );
