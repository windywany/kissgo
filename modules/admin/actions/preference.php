<?php
/*
 * 选项
 */
assert_login ();
bind ( 'get_core_option_group', '_hook_get_core_option_group', 0 );
function do_admin_preference_get($req, $res, $group = 'base') {
    
    $data = array ();
    $optM = new KsgPreferenceTable ();
    
    $data ['__opt_groups'] = apply_filter ( 'get_core_option_group', array () );
    
    $data ['__options'] = apply_filter ( 'get_preference_' . $group, array () );
    
    if (empty ( $data ['__options'] )) {
        $opts = $optM->query ()->where ( array ('group' => $group ) );
        $data ['__options'] = count ( $opts ) ? $opts->toArray ( 'name', 'value' ) : array ();
    }
    $data ['_g'] = $group;
    
    $data ['__option_tpl'] = apply_filter ( "get_{$group}_option_tpl", false, $data ['__options'] );
    if ($data ['__option_tpl'] instanceof BaseForm) {
        $data ['__option_form'] = $data ['__option_tpl'];
        unset ( $data ['__option_tpl'] );
    }
    $data ['_CUR_URL'] = murl ( 'admin', 'preference' );
    
    return view ( 'admin/views/preference.tpl', $data );
}
//保存
function do_admin_preference_post($req, $res, $group = 'base') {
    apply_filter ( 'get_core_option_group', array () );
    if (has_hook ( 'on_save_preference_' . $group )) {
        $rest = apply_filter ( 'on_save_preference_' . $group, true );
        if ($rest !== true) {
            show_page_tip ( $rest, 'error' );
        } else {
            show_page_tip ( '<strong>恭喜!</strong>设置成功.' );
        }
    } else {
        $data = apply_filter ( 'get_preference_for_save_' . $group, array () );
        if (! is_array ( $data )) {
            show_page_tip ( '<strong>糟啦!</strong>' . $data, 'error' );
        } else {
            $optM = new KsgPreferenceTable ();
            foreach ( $data as $name => $val ) {
                $conf = array ('group' => $group, 'name' => $name );
                if ($optM->exist ( $conf )) {
                    $optM->update ( array ('value' => $val ), $conf );
                } else {
                    $conf ['value'] = is_null ( $val ) ? '' : $val;
                    $optM->insert ( $conf );
                }
            }
            show_page_tip ( '<strong>恭喜!</strong>设置成功.' );
        }
    }
    Response::redirect ( murl ( 'admin', 'preference/' . $group ) );
}
// 基本配置
function _hook_for_get_option_base($data) {
    $settings = KissGoSetting::getSetting ();
    $data = $settings->toArray ();
    $data ['debug'] = $data ['DEBUG'];
    $data ['clean_url'] = $data ['CLEAN_URL'];
    $data ['i18n'] = $data ['I18N_ENABLED'];
    $data ['gzip'] = $data ['GZIP_ENABLED'];
    $data ['timezone'] = $data ['TIMEZONE'];
    $data ['site_url'] = $data ['BASE_URL'];
    return $data;
}
// 保存基本配置
function _hook_for_save_preference_base($rst) {
    $form = new BasePreferenceForm ();
    if ($form->validate ()) {
        $settings = KissGoSetting::getSetting ();
        $data = $form->getCleanData ();
        $settings ['DEBUG'] = $data ['debug'];
        $settings ['CLEAN_URL'] = $data ['clean_url'] ? true : false;
        $settings ['I18N_ENABLED'] = $data ['i18n'] ? true : false;
        $settings ['GZIP_ENABLED'] = $data ['gzip'] ? true : false;
        $settings ['TIMEZONE'] = $data ['timezone'];
        $settings ['BASE_URL'] = $data ['site_url'];
        unset ( $data ['debug'], $data ['clean_url'], $data ['i18n'], $data ['gzip'], $data ['timezone'], $data ['site_url'] );
        foreach ( $data as $key => $val ) {
            $settings [$key] = $val;
        }
        return save_setting_to_file ( APPDATA_PATH . 'settings.php' );
    }
    return $form->getError ( "<br/>" );
}
// 设置组
function _hook_get_core_option_group($groups) {
    $groups ['base'] = '<i class="icon-cog"></i> 基本设置';
    bind ( 'get_preference_base', '_hook_for_get_option_base' );
    bind ( 'get_base_option_tpl', '_hook_for_base_option_tpl', 10, 2 );
    bind ( 'on_save_preference_base', '_hook_for_save_preference_base' );
    
    //$groups ['safe'] = '<i class="icon-fire"></i> 安全设置'; 
    

    $groups ['thumb'] = '<i class="icon-picture"></i> 图片与水印';
    bind ( 'get_thumb_option_tpl', '_hook_for_thumb_option_tpl', 10, 2 );
    bind ( 'get_preference_for_save_thumb', '_hook_for_get_preference_thumb' );
    
    $groups ['smtp'] = '<i class="icon-envelope"></i> 邮件设置';
    bind ( 'get_smtp_option_tpl', '_hook_for_smtp_option_tpl', 10, 2 );
    bind ( 'get_preference_for_save_smtp', '_hook_for_get_preference_smtp' );
    return $groups;
}
// 创建基本设置模板
function _hook_for_base_option_tpl($tpl, $data) {
    return new BasePreferenceForm ( $data );
}
// 邮件模板
function _hook_for_smtp_option_tpl($tpl, $data) {
    $form = new SmtpPerferenceForm ( $data );
    if (! ($data ['smtp_host'] && $data ['smtp_user'])) {
        $form->removeWidget ( 'smtp_test_email' );
    } else {
        $form->setTestEmail ();
    }
    return $form;
}
// 为邮件设置获取数据
function _hook_for_get_preference_smtp($data) {
    $form = new SmtpPerferenceForm ();
    if ($form->validate ()) {
        $data = $form->getCleanData ();
        unset ( $data ['smtp_test_email'] );
        return $data;
    }
    return $form->getError ( "<br/>" );
}

// 图片与水印设置
function _hook_for_thumb_option_tpl($tpl, $data) {
    return new ThumbPreferenceForm ( $data );
}
// 为图片与水印设置获取数据
function _hook_for_get_preference_thumb($data) {
    $form = new ThumbPreferenceForm ();
    if ($form->validate ()) {
        $data = $form->getCleanData ();
        $watermark = '';
        $rst = $form->saveWatermarkPic ( $watermark );
        if ($watermark) {
            $data ['watermark_pic'] = $watermark;
        } else if ($rst === false) {
            unset ( $data ['watermark_pic'] );
        } else {
            return $rst;
        }
        return $data;
    }
    return $form->getError ( "<br/>" );
}