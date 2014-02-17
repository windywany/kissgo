<?php

/**
 *
 * Theme manager
 * @author guangfeng.ning
 *
 */
class ThemeManager {

    /**
     * get system themes
     *
     * @return array thems and their templates
     */
    public static function getThemes() {
        static $themes = false;
        if (! $themes) {
            $theme_dir = THEME_PATH . THEME_DIR;
            $hd = opendir ( $theme_dir );
            if ($hd) {
                while ( ($dir = readdir ( $hd )) !== false ) {
                    if ($dir == '.' || $dir == '..') {
                        continue;
                    }
                    if (is_dir ( $theme_dir . DS . $dir )) {
                        $themes [$dir] = self::getThemeTemplates ( $dir );
                    }
                }
                closedir ( $hd );
            }
        }
        return $themes;
    }

    public static function getThemeTemplates($theme) {
        static $types = false;
        if (! $types) {
            $ctm = ContentTypeManager::getInstance ();
            $types = $ctm->getTypes ();
        }
        $templates = array_merge ( array (), $types );
        $tpls = dbselect ( 'id,template,type' )->from ( '{themetemplates}' )->where ( array ('theme' => $theme ) );
        if (count ( $tpls )) {
            $deleting = array ();
            foreach ( $tpls as $tpl ) {
                $type = $tpl ['type'];
                if (isset ( $templates [$type] )) {
                    if ($tpl ['template']) {
                        $templates [$type] [1] = $tpl ['template'];
                    }
                } else {
                    $deleting [] = $tpl ['id'];
                }
            }
            if (! empty ( $deleting )) {
                $rst = dbdelete ()->from ( '{themetemplates}' )->where ( array ('id IN' => $deleting ) );
            }
        }
        return $templates;
    }
}