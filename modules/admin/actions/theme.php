<?php
assert_login ();
function do_admin_theme_get($req, $res) {
    $data ['_CUR_URL'] = murl ( 'admin', 'theme' );
    $data ['current_theme'] = get_theme ();
    $theme_dir = THEME_PATH . THEME_DIR;
    $hd = opendir ( $theme_dir );
    $themes = array ();
    $ntM = new KsgNodeTemplateTable ();
    if ($hd) {
        while ( ($dir = readdir ( $hd )) !== false ) {
            if ($dir == '.' || $dir == '..') {
                continue;
            }
            if (is_dir ( $theme_dir . DS . $dir )) {
                $themes [$dir] = $ntM->getTemplates ( $dir );
            }
        }
        closedir ( $hd );
    }
    $data ['themes'] = $themes;
    $data ['theme_count'] = count ( $themes );
    return view ( 'admin/views/node/theme.tpl', $data );
}
function do_admin_theme_post($req, $res) {
    $data ['success'] = true;
    $op = $req ['op'];
    
    switch ($op) {
        case 'reset' :
            $nt = new KsgNodeTemplateTable ();
            $rst = $nt->remove ( array ('theme' => $req ['theme'] ) );
            if ($rst === false) {
                $data ['success'] = false;
                $data ['msg'] = db_error ();
            }
            break;
        case 'use' :
            $settings = KissGoSetting::getSetting ();
            $settings ['THEME'] = $req ['theme'];
            $rst = save_setting_to_file ( APPDATA_PATH . 'settings.php' );
            if (! $rst) {
                $data ['success'] = false;
                $data ['msg'] = '无法保存配置文件.';
            }
            break;
        case 'set' :
            $form = new SetTplForm ();
            $set = $form->validate ();
            if ($set) {
                $nt = new KsgNodeTemplateTable ();
                $where = $set;
                unset ( $where ['template'] );
                if ($nt->exist ( $where )) {
                    $rst = $nt->update ( array ('template' => $set ['template'] ), $where );
                } else {
                    $rst = $nt->insert ( $set );
                }
                if ($rst === false) {
                    $data ['success'] = false;
                    $data ['msg'] = db_error ();
                }
                break;
            }
            $data ['success'] = false;
            $data ['msg'] = $form->getError ( "\n" );
            break;
        default :
            break;
    }
    return new JsonView ( $data );
}