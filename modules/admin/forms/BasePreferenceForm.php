<?php
/**
 * 基本设置表单
 * @author Leo
 *
 */
class BasePreferenceForm extends BootstrapForm {
    var $site_name = array (FWT_LABEL => '网站名称', FWT_TIP => '你网站的名称，可以在模板中使用cfg读取.' );
    var $site_url = array (FWT_LABEL => '网站URL', FWT_TIP => '必须以http://或https://开始的合法URL.', FWT_VALIDATOR => array ('url' => '请输入合法的URL.' ) );
    var $gzip = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用GZIP压缩', FWT_NO_APPLY => true, FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_TIP => '启用GZIP压缩以节省网络流利与加快传输速度.' );
    var $clean_url = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用伪静态', FWT_NO_APPLY => true, FWT_TIP => '需要服务器支持.', FWT_TIP_SHOW => FWT_TIP_SHOW_S, FWT_INITIAL_FUN => '@isSupportedCleanURL' );
    var $i18n = array (FWT_WIDGET => 'scheckbox', FWT_LABEL => '启用多语言支持', FWT_NO_APPLY => true );
    var $timezone = array (FWT_WIDGET => 'select', FWT_LABEL => '选择时区', FWT_TIP => '选择系统将使用的时区.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_INITIAL => 'Asia/Shanghai', FWT_BIND => '@getTimezones' );
    var $date_format = array (FWT_WIDGET => 'select', FWT_LABEL => '日期格式', FWT_TIP => '系统将以此格式显示日期.', FWT_OPTIONS => array ('class' => 'span2' ), FWT_INITIAL => 'Y-m-d', FWT_BIND => '@getDateFormats' );
    var $debug = array (FWT_WIDGET => 'select', FWT_LABEL => '调试级别', FWT_TIP => '控制系统日志记录级别.', FWT_OPTIONS => array ('class' => 'span3' ), FWT_INITIAL => '4', FWT_BIND => '@getDebugLevels' );
    var $link_types = array (FWT_LABEL => '友情链接分类', FWT_TIP => '多个分类使用","号分隔' );
    var $site_keywords = array (FWT_LABEL => '网站默认关键词', FWT_WIDGET => 'textarea', FWT_TIP => '如果你未给你的页面指定关键词，系统将以此做为页面的关键词.' );
    var $site_desc = array (FWT_LABEL => '网站默认描述', FWT_WIDGET => 'textarea', FWT_TIP => '如果你未给你的页面指定描述，系统将以此做为页面的描述.' );
    var $site_copyright = array (FWT_LABEL => '网站版权信息', FWT_WIDGET => 'textarea', FWT_TIP => '可以在模板中引用.' );
    var $site_beian = array (FWT_LABEL => '网站备案号', FWT_TIP => '如果你的网站在中国，你知道的...' );
    // 设置默认
    protected function getDefaultWidgetOptions() {
        return array (FWT_OPTIONS => array ('class' => 'span5' ), FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
    public function getTimezones($value, $data) {
        $timezones = array ('Asia/Shanghai' => 'Asia/Shanghai' );
        return $timezones;
    }
    public function getDateFormats($value, $data) {
        $formats = array ('Y-m-d' => '年-月-日', 'm/d/y' => '月/日/年' );
        return $formats;
    }
    public function getDebugLevels() {
        $levels = array (DEBUG_DEBUG => '调试(记录所有日志)', DEBUG_WARN => '警告(记录除调试之外的日志)', DEBUG_INFO => '信息(记录信息与错误日志)', DEBUG_ERROR => '错误(仅记录错误日志)' );
        return $levels;
    }
    public function isSupportedCleanURL() {
        $headers = get_headers ( detect_app_base_url ( true ) . 'install.test.clean.url' );
        if (preg_match ( '#.*OK$#', $headers [0] ))
            return 1;
        return 0;
    }
    public function init_clean_url(&$widget, &$value) {
        if (! $value) {
            $widget [FWT_OPTIONS] ['disabled'] = 'disabled';
            $widget [FWT_TIP] = '您的服务器不支持伪静态.';
        } else {
            $widget [FWT_TIP] = '您的服务器支持伪静态,建议开启.';
        }
    }
}