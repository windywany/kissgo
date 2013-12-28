<?php

/**
 *
 *
 * @author guangfeng.ning
 *
 */
class AdminThemeController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $data = array ();
        $data ['current_theme'] = get_theme ();
        $data ['themes'] = ThemeManager::getThemes ();
        $data ['totalTheme'] = count ( $data ['themes'] );
        return view ( 'theme.tpl', $data );
    }

    public function settpl($theme, $type, $tpl) {
        $data = array ('success' => true );
        $set = array ('theme' => $theme, 'type' => $type );
        $rst = dbselect ()->from ( '{theme_templates}' )->where ( $set );
        if ($rst->count ( 'id' ) > 0) {
            $rst = dbupdate ( '{theme_templates}' )->set ( array ('template' => $tpl ) )->where ( $set );
        } else {
            $set ['template'] = $tpl;
            $rst = dbinsert ( $set )->inito ( '{theme_templates}' );
        }
        if (count ( $rst ) === false) {
            $data ['success'] = false;
            $data ['msg'] = '不能修改数据库';
        }
        return new JsonView ( $data );
    }

    public function usetheme($theme) {
        $data = array ('success' => true );
        $settings = KissGoSetting::getSetting ();
        $settings ['THEME'] = $theme;
        $rst = save_setting_to_file ( APPDATA_PATH . 'settings.php' );
        if (! $rst) {
            $data ['success'] = false;
            $data ['msg'] = '无法保存配置文件.';
        }
        return new JsonView ( $data );
    }

    public function reset($theme) {
        $data = array ('success' => true );
        if ($theme) {
            count ( dbupdate('{theme_templates}')->set(array('template'=>''))->where ( array ('theme' => $theme ) ) );
        }
        return new JsonView ( $data );
    }

    public function templates($id = '', $theme = false) {
        if (! $theme) {
            $theme = THEME;
        }
        $path = THEME_PATH . THEME_DIR . DS . $theme . $id;
        $hd = opendir ( $path );
        $dirs = array ();
        $files = array ();
        if ($hd) {
            while ( ($f = readdir ( $hd )) != false ) {
                if (is_dir ( $path . DS . $f ) && $f != '.' && $f != '..' && $f != 'admin') {
                    $dirs [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => true );
                }
                if (is_file ( $path . DS . $f ) && preg_match ( '/.+\.tpl$/', $f )) {
                    $files [$f] = array ('id' => $id . '/' . $f, 'name' => $f, 'isParent' => false );
                }
            }
            closedir ( $hd );
            ksort ( $dirs );
            ksort ( $files );
            $dirs = $dirs + $files;
            $dirs = array_values ( $dirs );
        }
        return new JsonView ( $dirs );
    }
}