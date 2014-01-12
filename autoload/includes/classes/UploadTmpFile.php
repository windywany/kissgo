<?php

/**
 *
 * @author Leo
 *
 */
class UploadTmpFile {
    /**
     * 附件描述
     *
     * @var string
     */
    public $alt_text;
    /**
     * 文件名
     *
     * @var string
     */
    public $name;
    public $orName;
    /**
     * 无扩展名的文件名
     *
     * @var unknown_type
     */
    public $basename;
    /**
     * 大小
     *
     * @var int
     */
    public $size;
    /**
     * 上传状态
     *
     * @var string
     */
    public $status;
    /**
     * 标题
     *
     * @var string
     */
    public $title;
    /**
     * 临时文件
     *
     * @var string
     */
    public $tmpname;
    /**
     * 扩展名
     *
     * @var string
     */
    public $file_ext;
    /**
     * mime类型
     *
     * @var string
     */
    public $mimeType;
    public $errors = array ();

    /**
     * 创建一个新的上传文件实例
     *
     * @param int $index
     * @param string $tmpdir
     */
    public function __construct($i, $tmpdir, $keep = false, $base64 = false) {
        if (is_array ( $i )) {
            $this->status = 'done';
            $this->tmpname = $i ['tmpname'];
            $this->size = $i ['filesize'];
            $this->name = $i ['name'];
            $this->title = $i ['title'];
            $this->alt_text = $i ['alt'];
        } else {
            $this->alt_text = rqst ( "uploader_{$i}_alt", '' );
            $this->name = rqst ( "uploader_{$i}_name", '' );
            $this->size = irqst ( "uploader_{$i}_size", 0 );
            $this->status = rqst ( "uploader_{$i}_status", '' );
            $this->title = rqst ( "uploader_{$i}_title", '' );
            $this->tmpname = rqst ( "uploader_{$i}_tmpname", '' );
        }
        $this->orName = $this->name;
        if ($tmpdir === false) {
            if ($base64 == true) {
                $name = time () . rand ( 1, 10000 ) . '.png';
                $content = rqst ( $this->name );
                $img = base64_decode ( $content );
                $this->size = strlen ( $img );
                $this->name = $name;
                if (file_put_contents ( TMP_PATH . 'plupload/' . $name, $img )) {
                    $tmpdir = TMP_PATH . 'plupload';
                    $this->tmpname = $name;
                }
            } else {
                $ext = strtolower ( strrchr ( $this->name, '.' ) );
                $name = time () . rand ( 1, 10000 ) . $ext;
                if (move_uploaded_file ( $this->tmpname, TMP_PATH . 'plupload/' . $name )) {
                    $tmpdir = TMP_PATH . 'plupload';
                    $this->tmpname = $name;
                }
            }
        }
        if ($keep) {
            $this->tmpname = $tmpdir . $this->tmpname;
            if ($this->name && $this->tmpname) {
                $ext1 = strrchr ( $this->name, '.' );
                $ext2 = strrchr ( $this->tmpname, '.' );
                if ($ext1 && $ext1 != $ext2) {
                    $this->tmpname = false;
                } else {
                    $this->file_ext = strtolower ( $ext1 );
                }
            }
        } else {
            if (is_dir ( $tmpdir ) && $this->tmpname) {
                $this->tmpname = $tmpdir . DS . $this->tmpname;
                if (! is_readable ( $this->tmpname )) {
                    $this->tmpname = false;
                }
            } else {
                $this->tmpname = false;
            }
            if ($this->name && $this->tmpname) {
                $ext1 = strrchr ( $this->name, '.' );
                $ext2 = strrchr ( $this->tmpname, '.' );
                if ($ext1 && $ext1 != $ext2) {
                    $this->tmpname = false;
                } else {
                    $this->file_ext = strtolower ( $ext1 );
                    if ($keep) {
                        $this->basename = $this->name;
                    } else {
                        $this->basename = rand ( 1, 99999 ) . time ();
                        $this->name = $this->basename . $this->file_ext;
                    }
                }
            }
        }
        if ($this->file_ext) {
            $this->mimeType = self::getAttachmentMimeType ( $this->file_ext );
        }
    }

    /**
     * 是否是完整的上传文件
     *
     * @return boolean
     */
    public function isuploaded() {
        return $this->status == 'done' && $this->tmpname && $this->name;
    }

    public function getType() {
        return self::getAttachmentType ( $this->file_ext );
    }

    public function getMimeType() {
        return self::getAttachmentMimeType ( $this->file_ext );
    }

    /**
     * 删除使用plupload机制上传的文件
     *
     * @return boolean
     */
    public function delete() {
        if ($this->tmpname && file_exists ( $this->tmpname )) {
            return @unlink ( $this->tmpname );
        } else if ($this->tmpname && file_exists ( APP_PATH . $this->tmpname )) {
            return @unlink ( APP_PATH . $this->tmpname );
        }
        return true;
    }

    /**
     * 获取所有附件的类型
     *
     * @return array 附件类型列表(key=>value)
     */
    public static function getAttachmentTypes() {
        static $types = null;
        if ($types == null) {
            $types = apply_filter ( 'get_attachment_types', array ('image' => '图片', 'zip' => '归档文件', 'media' => '多媒体', 'doc' => '办公文件', 'file' => '普通文件' ) );
        }
        return $types;
    }

    /**
     * 根据文件扩展名得到一个文件的附件类型
     *
     * @param string $ext
     *            文件扩展名
     * @return string 附件类型
     */
    public static function getAttachmentType($ext) {
        static $ext2type = null;
        if ($ext2type == null) {
            $ext2type = apply_filter ( 'get_attachment_ext2type', array ('gif|bmp|tiff|png|jpg|jpeg|jpe|tif' => 'image', 'zip|rar|7z|tar|gz|bz2' => 'zip', 'doc|docx|txt|ppt|pptx|xls|xlsx|pdf' => 'doc', 'mp3|avi|mp4|flv|swf' => 'media' ) );
        }
        foreach ( $ext2type as $pt => $type ) {
            if (preg_match ( "/({$pt})/i", $ext )) {
                return $type;
            }
        }
        return 'file';
    }

    /**
     * 根据文件扩展 名获取文件的mime类型
     *
     * @param string $ext
     *            文件扩展名
     * @return string 文件的mime名
     */
    public static function getAttachmentMimeType($ext) {
        static $mimes = false;
        if (! $mimes) {
            $mimes = get_allowed_mime_types ();
        }
        foreach ( $mimes as $mime => $type ) {
            if (preg_match ( "/({$mime})/i", $ext )) {
                return $type;
            }
        }
        return 'text/plain';
    }

    public function errorInfo() {
        return empty ( $this->errors ) ? '' : $this->errors [0];
    }

    /**
     * save uploaded file
     *
     * @param IUploader $uploader
     * @param Passport $uid
     * @return boolean
     */
    public function save($uploader, $user) {
        $rst = false;
        if ($this->isuploaded () && $uploader->allowed ( $this->file_ext )) {
            $rst = $uploader->save ( $this );
        }

        if ($rst !== false) {
            $data ['uid'] = $user ['uid'];
            $data ['gid'] = $user ['gid'];
            $data ['create_uid'] = $user ['uid'];
            $data ['create_time'] = date ( 'Y-m-d H:i:s' );
            $data ['update_uid'] = $user ['uid'];
            $data ['update_time'] = $data ['create_time'];
            $data ['content_type'] = 'attachment';
            $data ['content_id'] = '0';
            $data ['mime_type'] = $this->mimeType;
            $data ['path'] = $rst [2];
            $data ['filename'] = $rst [3];
            $data ['url'] = $rst [0];
            $data ['name'] = $this->title;
            $data ['title'] = $this->alt_text;
            $data ['target'] = '_self';
            $data ['commentable'] = 1;
            $ret = dbinsert ( $data )->inito ( '{nodes}' );
            if (count ( $ret ) > 0) {
                // 生成缩略图与添加水印
                if (in_array ( $this->file_ext, array ('.jpg', '.gif', '.jpeg', '.png', '.bmp' ) )) {
                    if (cfg ( 'enable_watermark@thumb', false ) && cfg ( 'watermark_pic@thumb' )) {
                        $watermark = APPDATA_PATH . cfg ( 'watermark_pic@thumb' );
                        if (file_exists ( $watermark )) {
                            $uploader->watermark ( $rst [0], $watermark, cfg ( 'watermark_pos@thumb', 'br' ) );
                        }
                    }
                    if (cfg ( 'enable_thumb@thumb', false ) && cfg ( 'thumb_sizes@thumb' )) {
                        $sizestr = cfg ( 'thumb_sizes@thumb' );
                        if (preg_match_all ( '#(\d+?)[x,X](\d+)#', $sizestr, $ss, PREG_SET_ORDER )) {
                            foreach ( $ss as $m ) {
                                $sizes [] = array ($m [1], $m [2] );
                            }
                            $uploader->thumbnail ( $rst [0], $sizes );
                        }
                    }
                }
                count ( dbinsert ( array ('nid' => $ret [0], 'meta_key' => 'attach_type', 'meta_value' => self::getAttachmentType ( $this->file_ext ) ) )->inito ( '{nodemeta}' ) );
                return $ret [0];
            } else {
                $this->errors [] = "文件上传失败: " . $ret->lastError ();
                $uploader->delete ( $rst [1] );
            }
        } else {
            log_error ( "文件上传失败: " . $uploader->get_last_error () );
            $this->errors [] = "文件上传失败: " . $uploader->get_last_error ();
        }
        $this->delete ();
        return false;
    }
}