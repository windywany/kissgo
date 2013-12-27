<?php
defined ( 'WEB_ROOT' ) or exit ( 'No direct script access allowed' );
I18n::append ( __FILE__ );

/**
 * register the default content type
 *
 * @param ContentTypeManager $typeManager
 */
function register_default_content_type($typeManager) {
    $typeManager->register ( 'index', '首页', 'index.tpl', '网站首页.',false );
    return $typeManager;
}
bind ( 'register_content_type', 'register_default_content_type' );
