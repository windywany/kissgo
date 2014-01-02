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
    $typeManager->register ( 'image', '图片', 'image.tpl', '图片.', false );
    $typeManager->register ( 'attach', '附件', 'attach.tpl', '图片.', false );
    return $typeManager;
}
bind ( 'register_content_type', 'register_media_content_type' );