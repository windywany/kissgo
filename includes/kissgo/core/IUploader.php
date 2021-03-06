<?php
/**
 * 
 * 文件上传器
 * @author LeoNing
 *
 */
interface IUploader {
    /**
	 * 上传文件 
	 * @param UploadTmpFile $file 使用plupload上传后的文件
	 * @return string 文件的URL,失败返回false
	 */
    public function save($file);
    /**
	 * 
	 * 文件扩展名是否是允许的文件类型
	 * @return boolean
	 */
    public function allowed($ext);
    /**
	 * 
	 * 允许的最大路径
	 * @return INT 单位为K
	 */
    public function getMaxSize();
    /**
	 * 
	 * 返回错误信息
	 */
    public function get_last_error();
    /**
     * generate thumbnails of the file
     * @param string $file
     * @param array $sizes
     * @return boolean 
     */
    public function thumbnail($file, $sizes);
    /**
     * marked the file with a cover
     * @param string $file
     * @param string $watermark
     * @return boolean
     */
    public function watermark($file, $watermark,$pos='br');
    /**
     * delete the file
     * @param string $file
     * @return boolean
     */
    public function delete($file);
}