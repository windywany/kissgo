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
function on_install_module_cn_usephp_core_gui($rst) {
    imports ( 'admin/models/*' );
    $rtn = true;
    do {
        if (! PdoDriver::createTable ( new CoreUserTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new CoreRoleTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new CoreUserRoleTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new CorePreferenceTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new CoreAccessPolicyTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new CoreAttachmentTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new TagTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new NodeTagsTable () )) {
            $rtn = db_error ();
            break;
        }
        if (! PdoDriver::createTable ( new EnumTable () )) {
            $rtn = db_error ();
            break;
        }
    } while ( 0 );
    return empty ( $rtn ) ? '创建数据表时出错啦！' : $rtn;
}
bind ( 'on_install_module_cn.usephp.core.gui', 'on_install_module_cn_usephp_core_gui' );
