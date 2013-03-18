<?php
/**
 * 图片工具
 * @author LeoNing
 *
 */
class ImageUtil {
    private $file;
    public function __construct($file) {
        if (file_exists ( $file ) && self::isImage ( $file )) {
            $this->file = $file;
        } else {
            $this->file = false;
        }
    }
    /**
	 * 生成缩略图
	 *
	 * @param array $size
	 * 尺寸集
	 */
    public function thumbnail($size = array(array(80,60))) {
        if ($this->file && ! empty ( $size )) {
            foreach ( $size as $s ) {
                if (is_array ( $s ) && isset ( $s [0] ) && isset ( $s [1] )) {
                    $width = intval ( $s [0] );
                    $height = intval ( $s [1] );
                    $tfile = get_thumbnail_filename ( $this->file, $width, $height );
                    if (file_exists ( $tfile )) {
                        continue;
                    }
                    $image = new image ( $this->file );
                    $fx = $image->attach ( new image_fx_resize ( $width, $height ) );
                    $tfile = get_thumbnail_filename ( $this->file, $width, $height );
                    $rst = $image->save ( $tfile );
                    if (! $rst) {
                        log_error ( '生成缩略图失败:' . $tfile );
                        return false;
                    }
                    $image->destroyImage ();
                }
            }
        }
        return true;
    }
    /**
	 * 添加水印
	 *
	 * @param string $mark
	 * 水印图片
	 * @param string $pos
	 * 位置
	 */
    public function watermark($mark, $pos) {}
    public static function deleteThumbnail($filename) {
        $pos = strrpos ( $filename, '.' );
        if ($pos === false) {
            return false;
        }
        $shortname = substr ( $filename, 0, $pos );
        $ext = substr ( $filename, $pos );
        
        $filep = $shortname . '-*' . $ext;
        dlog ( "file deleted pattern : " . $filep );
        $files = glob ( $filep );
        if ($files) {
            foreach ( $files as $f ) {
                dlog ( $f );
                @unlink ( $f );
            }
        }
    }
    public static function isImage($file) {
        $ext = strrchr ( $file, '.' );
        return in_array ( strtolower ( $ext ), array ('.jpeg', '.jpg', '.gif', '.png' ) );
    }
    /**
	 * 下载远程图片到本地
	 * 
	 * @param unknown_type $content
	 */
    public static function downloadRemotePic($imgUrls) {
        //忽略抓取时间限制
        set_time_limit ( 0 );
        //远程抓取图片配置
        $config = array ("fileType" => array (".gif", ".png", ".jpg", ".jpeg", ".bmp" ), "fileSize" => 30000 ); //文件大小限制，单位KB
        $tmpNames = array ();
        $savePath = trim ( (defined ( 'UPLOAD_DIR' ) && UPLOAD_DIR ? UPLOAD_DIR : 'uploads'), '/' ) . date ( '/Y/m/' );
        
        if (! file_exists ( WWW_ROOT . $savePath ) && ! mkdir ( WWW_ROOT . $savePath, 0777, true )) {
            return false;
        }
        
        foreach ( $imgUrls as $imgUrl ) {
            //http开头验证
            if (strpos ( $imgUrl, "http" ) !== 0) {
                continue;
            }
            //获取请求头
            $heads = get_headers ( $imgUrl, 1 );
            //死链检测
            if (! (stristr ( $heads [0], "200" ) && stristr ( $heads [0], "OK" ))) {
                continue;
            }
            
            //格式验证(扩展名验证和Content-Type验证)
            $fileType = strtolower ( strrchr ( $imgUrl, '.' ) );
            
            if (! in_array ( $fileType, $config ['fileType'] ) || stristr ( $heads ['Content-Type'], "image" )) {
                continue;
            }
            //打开输出缓冲区并获取远程图片
            ob_start ();
            $context = stream_context_create ( array ('http' => array ('follow_location' => false ) ) ); // don't follow redirects
            //请确保php.ini中的fopen wrappers已经激活
            readfile ( $imgUrl, false, $context );
            $img = ob_get_contents ();
            ob_end_clean ();
            //大小验证
            $uriSize = strlen ( $img ); //得到图片大小
            $allowSize = 1024 * $config ['fileSize'];
            if ($uriSize > $allowSize) {
                continue;
            }
            
            $tmpName = $savePath . rand ( 1, 10000 ) . time () . strrchr ( $imgUrl, '.' );
            try {
                $fp2 = @fopen ( WWW_ROOT . $tmpName, "a" );
                if (fwrite ( $fp2, $img )) {
                    fclose ( $fp2 );
                    $tmpNames [$imgUrl] = array ('url' => $tmpName, 'ext' => trim ( $fileType, '.' ), 'type' => 'image', 'mine_type' => $heads ['Content-Type'], 'name' => "", 'alt_text' => '' );
                }
            } catch ( Exception $e ) {}
        }
        return $tmpNames;
    }
}