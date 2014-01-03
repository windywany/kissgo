<?php
defined ( 'WEB_ROOT' ) or exit ( 'No direct script access allowed' );
I18n::append ( __FILE__ );

/**
 * register the default content type
 *
 * @param ContentTypeManager $typeManager
 *
 */
function register_media_content_type($typeManager) {
    $typeManager->register ( 'attachment', '附件', 'attach.tpl', '附件(图片,文件).', false );
    return $typeManager;
}
bind ( 'register_content_type', 'register_media_content_type' );