<?php
/**
 * 
 * @author Leo
 *
 */
class PlUploader implements IUploader {
    private $last_error = '';
    /**
	 * 默认文件上传器
	 *
	 * @param UploadTmpFile $title        	
	 * @return boolean
	 */
    public function save($file) {
        $path = (defined ( 'UPLOAD_DIR' ) && UPLOAD_DIR ? UPLOAD_DIR : 'uploads') . date ( '/Y/m/' );
        $destdir = WEB_ROOT . DS . STATIC_DIR . $path;
        $tmp_file = $file->tmpname;
        $fileinfo = stat ( $tmp_file );
        $maxSize = $this->getMaxSize ();
        if ($fileinfo [7] > $maxSize) {
            $this->last_error = '文件体积超出允许值[' . $maxSize . ']';
            return false;
        }
        if (! is_dir ( $destdir ) && ! mkdir ( $destdir, 0777, true )) { // 目的目录不存在，且创建也失败
            $this->last_error = '无法创建目录[' . $destdir . ']';
            return false;
        }
        
        $fileName = $path . $file->name;
        $destfile = $destdir . $file->name;
        $result = rename ( $tmp_file, $destfile );
        if ($result == false) {
            $this->last_error = '无法将文件[' . $tmp_file . ']重命名为[' . $destfile . ']';
            return false;
        }
        return array (str_replace ( DS, '/', $fileName ), $destfile );
    }
    /*
	 * (non-PHPdoc) @see IUploader::get_last_error()
	 */
    public function get_last_error() {
        return $this->last_error;
    }
    /*
	 * (non-PHPdoc) @see IUploader::getMaxSize()
	 */
    public function getMaxSize() {
        return 20971520; // 20M
    }
    /*
	 * (non-PHPdoc) @see IUploader::getTypes()
	 */
    public function allowed($ext) {
        static $types = false;
        if (! $types) {
            $types = array ('.jpg', '.gif', '.png', '.bmp', '.jpeg', '.zip', '.rar', '.7z', '.tar', '.gz', '.bz2', '.doc', '.docx', '.txt', '.ppt', '.pptx', '.xls', '.xlsx', '.pdf', '.mp3', '.avi', '.mp4', '.flv', '.swf' );
        }
        return in_array ( $ext, $types );
    }
}