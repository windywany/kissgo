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
    $models = array ('CoreUserTable', 'CoreRoleTable', 'CoreUserRoleTable', 'CorePreferenceTable', 'CoreAccessPolicyTable', 'CoreAttachmentTable', 'TagTable', 'NodeTagsTable', 'EnumTable', 'NodeTypeTable', 'NodeTemplateTable' );
    $models [] = 'NodeTable';
    $models [] = 'NodeCommentTable';
    $rtn = true;
    foreach ( $models as $model ) {
        if (! PdoDriver::createTable ( new $model () )) {
            $rtn = db_error ();
            break;
        }
    }
    
    if (! empty ( $rtn )) {
        $types = array (array ('type' => 'index', 'name' => '首页', 'creatable' => 0, 'template' => 'index.tpl', 'note' => '网站首页' ) );
        $types [] = array ('type' => 'plain', 'name' => '简单页面', 'template' => 'page.tpl', 'note' => '简单页面' );
        $nt = new NodeTypeTable ();
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
