<?php
class UploadTmpFile {
	/**
	 * 附件描述
	 * @var string
	 */
	public $alt_text;
	/**
	 * 文件名
	 * @var string
	 */
	public $name;
	/**
	 * 无扩展名的文件名	 
	 * @var unknown_type
	 */
	public $basename;
	/**
	 * 大小 
	 * @var int
	 */
	public $size;
	/**
	 * 上传状态
	 * @var string
	 */
	public $status;
	/**
	 * 标题
	 * @var string
	 */
	public $title;
	/**
	 * 临时文件
	 * @var string
	 */
	public $tmpname;
	/**
	 * 扩展名
	 * @var string
	 */
	public $file_ext;
	/**
	 * mime类型
	 * @var string
	 */
	public $mimeType;
	/**
	 * 
	 * 创建一个新的上传文件实例
	 * @param int $index
	 * @param string $tmpdir
	 */
	public function __construct($i, $tmpdir, $keep = false) {
		$this->alt_text = rqst ( "uploader_{$i}_alt", '' );
		$this->name = rqst ( "uploader_{$i}_name", '' );
		$this->size = irqst ( "uploader_{$i}_size", 0 );
		$this->status = rqst ( "uploader_{$i}_status", '' );
		$this->title = rqst ( "uploader_{$i}_title", '' );
		$this->tmpname = rqst ( "uploader_{$i}_tmpname", '' );
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
	 * 
	 * 是否是完整的上传文件
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
	 * 
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
	 * 
	 * 获取所有附件的类型
	 * 
	 * @return array 附件类型列表(key=>value)
	 */
	public static function getAttachmentTypes() {
		static $types = null;
		if ($types == null) {
			$types = apply_filter ( 'get_attachment_types', array ('image' => '图片', 'zip' => '归档', 'media' => '多媒体', 'doc' => '办公', 'file' => '普通文件' ) );
		}
		return $types;
	}
	/**
	 * 根据文件扩展名得到一个文件的附件类型
	 * 
	 * @param string $ext 文件扩展名
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
	 * 
	 * 根据文件扩展 名获取文件的mime类型
	 * 
	 * @param string $ext 文件扩展名
	 * @return string 文件的mime名
	 */
	public static function getAttachmentMimeType($ext) {
		static $mimes = false;
		if (! $mimes) {
			$mimes = get_allowed_mime_types ();
		}
		foreach ( $mimes as $mime => $type ) {
			if (preg_match ( "/({$mime})/i", $mime )) {
				return $type;
			}
		}
		return 'text/plain';
	}
}