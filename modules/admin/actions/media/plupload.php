<?php
/**
 * upload file by chuck
 */

function do_admin_media_plupload_post($req, $res, $automove = false) {
    $I = assert_login ();
    $targetDir = TMP_PATH . "plupload";
    
    $cleanupTargetDir = true; // Remove old files
    $maxFileAge = 1080000; // Temp file age in seconds
    // 60 minutes execution time
    @set_time_limit ( 12000 );
    
    // Get parameters
    $chunk = irqst ( 'chunk', 0 );
    $chunks = irqst ( 'chunks', 0 );
    $fileName = rqst ( 'name', '' );
    
    // Clean the fileName for security reasons
    $fileName = preg_replace ( '/[^\w\._]+/', '_', $fileName );
    $filext = strtolower ( strrchr ( $fileName, '.' ) );
    $uploader = apply_filter ( 'get_uploader', new PlUploader () ); //得到文件上传器
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
    
    // Create target dir
    if (! file_exists ( $targetDir )) {
        @mkdir ( $targetDir, 0755, true );
    }
    // Remove old temp files
    if ($cleanupTargetDir && is_dir ( $targetDir ) && ($dir = opendir ( $targetDir ))) {
        while ( ($file = readdir ( $dir )) !== false ) {
            $tmpfilePath = $targetDir . DS . $file;
            // Remove temp file if it is older than the max age and is not the current file
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
    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (strpos ( $contentType, "multipart" ) !== false) {
        if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
            $out = fopen ( "{$filePath}.part", $chunk == 0 ? "wb" : "ab" );
            if ($out) {
                $in = fopen ( $_FILES ['file'] ['tmp_name'], "rb" );
                if ($in) {
                    do {
                        $buff = fread ( $in, 4096 );
                        if ($buff)
                            fwrite ( $out, $buff );
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
                    if ($buff)
                        fwrite ( $out, $buff );
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
            $ret = $file->save ( $uploader, $I ['uid'] );
            if ($ret !== false) {
                $ret ['id'] = $ret ['attachment_id'];
                unset ( $ret ['attachment_id'] );
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