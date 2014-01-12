<?php

/**
 * 多媒体
 * @author Leo
 *
 */
class MediaController extends Controller {
    private $user;

    public function preRun() {
        $this->user = whoami ();
        if (! $this->user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $data = array ();
        $data ['types'] = array_merge ( array ('' => '全部' ), UploadTmpFile::getAttachmentTypes () );
        return view ( 'media.tpl', $data );
    }

    public function upload($uploader_count = 0) {
        $data ['success'] = false;
        $tmpdir = TMP_PATH . "plupload";
        $errors = array ();
        if ($uploader_count > 0) {
            @set_time_limit ( 0 );
            $uploader = apply_filter ( 'get_uploader', new LocalFileUploader () ); // 得到文件上传器
            for($i = 0; $i < $uploader_count; $i ++) {
                $tmpfile = new UploadTmpFile ( $i, $tmpdir );
                $rst = $tmpfile->save ( $uploader, $this->user );
                if (! $rst) {
                    $errors += $tmpfile->errors;
                }
            }
        }
        if (count ( $errors ) > 0) {
            $data ['msg'] = '<p>' . implode ( '</p><p>', $errors ) . '</p>';
        } else {
            $data ['success'] = true;
        }
        return new JsonView ( $data );
    }

    public function plupload($name = '', $chunk = 0, $chunks = 0, $automove = false) {
        $targetDir = TMP_PATH . "plupload";
        // Create target dir
        if (! file_exists ( $targetDir )) {
            @mkdir ( $targetDir, 0755, true );
        }
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 1080000; // Temp file age in seconds
        @set_time_limit ( 0 );
        // Get parameters
        $fileName = $name;
        // Clean the fileName for security reasons
        $fileName = preg_replace ( '/[^\w\._]+/', '_', $fileName );
        $filext = strtolower ( strrchr ( $fileName, '.' ) );
        $uploader = apply_filter ( 'get_uploader', new LocalFileUploader () ); // 得到文件上传器
        if (! $uploader->allowed ( $filext )) {
            @header ( '错误的文件类型.', true, 500 );
            die ( '{"jsonrpc" : "2.0", "error" : {"code": 200, "message": "错误的文件类型."}, "id" : "id"}' );
        }
        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists ( $targetDir . DS . $fileName )) {
            $ext = strrpos ( $fileName, '.' );
            $fileName_a = substr ( $fileName, 0, $ext );
            $fileName_b = substr ( $fileName, $ext );

            $count = 1;
            while ( file_exists ( $targetDir . DS . $fileName_a . '_' . $count . $fileName_b ) ) {
                $count ++;
            }
            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        $filePath = $targetDir . DS . $fileName;

        // Remove old temp files
        if ($cleanupTargetDir && is_dir ( $targetDir ) && ($dir = opendir ( $targetDir ))) {
            while ( ($file = readdir ( $dir )) !== false ) {
                $tmpfilePath = $targetDir . DS . $file;
                if (preg_match ( '/\.part$/', $file ) && (filemtime ( $tmpfilePath ) < time () - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                    @unlink ( $tmpfilePath );
                }
            }
            closedir ( $dir );
        } else {
            @header ( 'Failed to open temp directory.', true, 500 );
            die ( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}' );
        }

        // Look for the content type header
        if (isset ( $_SERVER ["HTTP_CONTENT_TYPE"] )) {
            $contentType = $_SERVER ["HTTP_CONTENT_TYPE"];
        }
        if (isset ( $_SERVER ["CONTENT_TYPE"] )) {
            $contentType = $_SERVER ["CONTENT_TYPE"];
        }
        // Handle non multipart uploads older WebKit versions didn't support
        // multipart in HTML5
        if (strpos ( $contentType, "multipart" ) !== false) {
            if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
                $out = fopen ( "{$filePath}.part", $chunk == 0 ? "wb" : "ab" );
                if ($out) {
                    $in = fopen ( $_FILES ['file'] ['tmp_name'], "rb" );
                    if ($in) {
                        do {
                            $buff = fread ( $in, 4096 );
                            if ($buff) {
                                fwrite ( $out, $buff );
                            }
                        } while ( $buff );
                    } else {
                        @header ( 'Failed to open input stream.', true, 500 );
                        die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
                    }
                    fclose ( $in );
                    fclose ( $out );
                    @unlink ( $_FILES ['file'] ['tmp_name'] );
                } else {
                    @header ( 'Failed to open output stream.', true, 500 );
                    die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
                }
            } else {
                @header ( 'Failed to move uploaded file.', true, 500 );
                die ( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}' );
            }
        } else {
            $out = fopen ( "{$filePath}.part", $chunk == 0 ? "wb" : "ab" );
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen ( "php://input", "rb" );
                if ($in) {
                    do {
                        $buff = fread ( $in, 4096 );
                        if ($buff) {
                            fwrite ( $out, $buff );
                        }
                    } while ( $buff );
                } else {
                    @header ( 'Failed to open input stream.', true, 500 );
                    die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
                }
                fclose ( $in );
                fclose ( $out );
            } else {
                @header ( 'Failed to open output stream.', true, 500 );
                die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
            }
        }
        // Check if file has been uploaded
        if (! $chunks || $chunk == $chunks - 1) {
            rename ( "{$filePath}.part", $filePath );
            if ($automove) {
                $title = rqst ( 'filename', '' );
                $tmpfile = array ('tmpname' => $fileName, 'name' => rqst ( 'name' ), 'filesize' => rqst ( 'filesize', 0 ), 'title' => $title, 'alt' => $title );
                $file = new UploadTmpFile ( $tmpfile, $targetDir );
                $ret = $file->save ( $uploader, $this->user );
                if ($ret !== false) {
                    $ret ['id'] = $ret ['fid'];
                    unset ( $ret ['fid'] );
                    $ret ['t1'] = the_thumbnail_src ( $ret ['url'], 80, 60 );
                    $ret ['t2'] = the_thumbnail_src ( $ret ['url'], 260, 180 );
                    $ret = json_encode ( $ret );
                    die ( '{"jsonrpc" : "2.0", "result" : ' . $ret . ', "id" : "id"}' );
                } else {
                    @header ( 'failed to save file information.', true, 500 );
                    die ( '{"jsonrpc" : "2.0", "error" : {"code": 109, "message":"Failed to move file."}, "id" : "id"}' );
                }
            }
        }
        die ( '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}' );
    }

    /**
     * user data - JSON
     *
     * @param int $page
     * @param int $rp
     */
    public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc', $type = '', $sd = '', $ed = '', $filename = '') {
        $types = UploadTmpFile::getAttachmentTypes ();
        $page = intval ( $page );
        $rp = intval ( $rp );
        $rp = $rp ? $rp : 15;
        $start = ($page ? $page - 1 : $page) * $rp;
        $where ['content_type'] = 'attachment';
        $where ['deleted'] = 0;
        if ($sd) {
            $where ['create_time >='] = $sd . ' 00:00:00';
        }
        if ($ed) {
            $where ['create_time <='] = $ed . ' 23:59:59';
        }

        if ($type) {
            $where ['@'] = dbselect ( 'meta_id' )->from ( '{nodemeta}' )->where ( array ('nid' => imv ( 'ND.id' ), 'meta_key' => 'attach_type', 'meta_value' => $type ) );
        }
        if ($filename) {
            $con = new Condition ();
            $val = '%' . $filename . '%';
            $con ['filename LIKE'] = $val;
            $con ['||ND.name LIKE'] = $val;
            $con ['||title LIKE'] = $val;
            $where [] = $con;
        }
        $nodes = dbselect ( 'ND.*', 'U.display_name', 'G.name AS group_name' )->from ( '{nodes} AS ND' )->where ( $where )->limit ( $start, $rp )->sort ( $sortname, $sortorder );
        $nodes->field ( dbselect ( 'meta_value' )->from ( '{nodemeta}' )->where ( array ('nid' => imv ( 'ND.id' ), 'meta_key' => 'attach_type' ) ), 'attach_type' );

        $nodes->join ( '{users} AS U', 'ND.uid = U.id', Query::LEFT );
        $nodes->join ( '{groups} AS G', 'ND.gid = G.gid', Query::LEFT );

        $total = $nodes->count ( 'ND.id' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $nodes )) {
            foreach ( $nodes as $node ) {
                // the order is very important
                $cell = array ();
                $cell [] = $node ['id'];
                if(empty($node ['attach_type'])){
                    $node ['attach_type'] = 'file';
                }
                if ($node ['attach_type'] != 'image') {
                    $cell [] = MISC_DIR . '/images/icons/' . $node ['attach_type'] . '.png';
                } else {
                    $cell [] = $node ['url'];
                }
                $cell [] = $node ['name'];
                $cell [] = $types [$node ['attach_type']];
                $cell [] = $node ['display_name'];
                $cell [] = $node ['group_name'];
                $cell [] = $node ['create_time'];
                $jsonData ['rows'] [] = array ('id' => $node ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}