<?php
/**
 * kissgo framework that keep it simple and stupid, go go go ~~
 *
 * @author Leo Ning
 * @package kissgo
 * @time 2012/11/17 10:40
 * $Id$
 */
/**
 * 文件上传接口
 */
interface IFileUploader {
    /**
     * 上传文件
     * @param array $file 临时文件
     * @return string 文件的URL,失败返回false
     */
    public function upload($file);

    public function getFiles($start, $limit);

    public function deleteFile($file_uri);

    /**
     * get the last error message when uploading a file
     * @return string the error message
     */
    public function get_last_error();
}