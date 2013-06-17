<?php
/**
 * 
 * @author Leo
 *
 */
class ThumbPreferenceForm extends BootstrapForm {
    
    var $enable_watermark = array (FWT_LABEL => '启用水印', FWT_WIDGET => 'scheckbox', FWT_TIP => '启用水印后，每个上传的图片都加添加水印图片.' );
    var $watermark_pic = array (FWT_LABEL => '水印图片', FWT_WIDGET => 'file', FWT_TIP => '启用水印后，每个上传的图片都加添加水印图片.', 'pcls' => 'pull-left', 'ecls' => 'span4', FWT_VALIDATOR => array ('regexp(/.+\.(jpg|jpeg|png|bmp)$/i)' => '你只能上传jpg,png,bmp类型的图片,大小不超过10KB.' ) );
    var $watermark_pos = array (FWT_LABEL => '水印位置', FWT_WIDGET => 'radio', FWT_BIND => '@watermarkPos', FWT_INITIAL => 'br' );
    var $enable_thumb = array (FWT_LABEL => '启用缩略图', FWT_WIDGET => 'scheckbox', FWT_TIP => '启用缩略图后，系统将为每个上传的图片生成缩略图.' );
    var $thumb_sizes = array (FWT_LABEL => '缩略图大小', FWT_WIDGET => 'textarea', FWT_OPTIONS => array ('class' => 'span5' ), FWT_VALIDATOR => array ('required(enable_thumb:checked)' => '请填写至少一种缩略图规格.' ), FWT_TIP => '格式:宽x高,一行表示一种规格的缩略.' );
    
    public function watermarkPos($v, $d) {
        return array ('tl' => '左上', 'tm' => '上中', 'tr' => '右上', 'ml' => '左中', 'mm' => '中间', 'mr' => '右中', 'bl' => '左下', 'bm' => '下中', 'br' => '右下' );
    }
    public function saveWatermarkPic(&$watermark) {
        if (! isset ( $_FILES ['watermark_pic'] ) || empty($_FILES ['watermark_pic']['name'])) {
            return false;
        }
        $file = $_FILES ['watermark_pic'];
        if ($file ['error'] != 0) {
            return '上传文件失败.';
        }
        $ext = pathinfo ( $file ['name'], PATHINFO_EXTENSION );
        if (! preg_match ( '#^(jpg|jpeg|png|bmp)$#i', $ext )) {
            return '文件类型不正确.';
        }
        if ($file ['size'] > 10240) {
            return '文件太大啦.';
        }
        if (! is_uploaded_file ( $file ['tmp_name'] )) {
            return '非法上传';
        }
        if (! move_uploaded_file ( $file ['tmp_name'], APPDATA_PATH . 'watermark.' . $ext )) {
            return '移动文件失败.';
        }
        
        $watermark = 'watermark.' . $ext;
        return false;
    }
    protected function getDefaultWidgetOptions() {
        return array (FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
}