<?php
// hook for get_core_option_group
function _hook_get_core_option_group($groups) {
    $groups ['base'] = '<i class="icon-cog"></i> 基本设置';
    $groups ['safe'] = '<i class="icon-fire"></i> 安全设置';
    $groups ['thumb'] = '<i class="icon-picture"></i> 图片与水印';
    $groups ['smtp'] = '<i class="icon-envelope"></i> 邮件设置';
    //$groups ['sitemap'] = '<i class="icon-globe"></i>网站地图';
    //$groups ['rss'] = '<i class="icon-heart"></i>RSS订阅';
    return $groups;
}