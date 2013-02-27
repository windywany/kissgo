<?php
/*
 * 验证码
 */
function do_admin_captcha_get($req, $res) {
    $type = $req->get ( 'type', 'gif' );
    header ( "Content-type: image/" . $type );
    $size = $req->get ( 'size', '50x20' );
    $size = explode ( 'x', $size );
    if (count ( $size ) == 1) {
        $width = intval ( $size [0] );
        $height = $width * 3 / 4;
    } else if (count ( $size ) >= 2) {
        $width = intval ( $size [0] );
        $height = intval ( $size [1] );
    } else {
        $width = 50;
        $height = 20;
    }
    srand ( ( double ) microtime () * 1000000 );
    $randval = randstr ( 5, "CHAR" );
    $_SESSION ['__CAPTCHA__'] = strtolower ( $randval );
    $_SESSION ['__CAPTCHA__TIMEOUT__'] = time () + 120;
    
    if ($type != "gif" && function_exists ( "imagecreatetruecolor" )) {
        $im = @imagecreatetruecolor ( $width, $height );
    } else {
        $im = @imagecreate ( $width, $height );
    }
    $r = array (225, 240, 250, 255 );
    $g = array (225, 240, 250, 255 );
    $b = array (225, 240, 250, 255 );
    $key = rand ( 0, 3 );
    $backColor = imagecolorallocate ( $im, 85, 190, 255 ); //$r[$key], $g[$key], $b[$key] );
    $borderColor = imagecolorallocate ( $im, 255, 255, 255 );
    $pointColor = imagecolorallocate ( $im, 255, 170, 255 );
    @imagefilledrectangle ( $im, 0, 0, $width - 1, $height - 1, $backColor );
    @imagerectangle ( $im, 0, 0, $width - 1, $height - 1, $borderColor );
    $stringColor = imagecolorallocate ( $im, 100, 55, 255 );
    $i = 0;
    for(; $i <= 100; ++ $i) {
        $pointX = rand ( 2, $width - 2 );
        $pointY = rand ( 2, $height - 2 );
        @imagesetpixel ( $im, $pointX, $pointY, $pointColor );
    }
    @imagestring ( $im, 4, 5, 1, $randval, $stringColor );
    $ImageFun = "Image" . $type;
    $ImageFun ( $im );
    @imagedestroy ( $im );
}
function randstr($len = 6, $format = "ALL") {
    switch ($format) {
        case "CHAR" :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            break;
        case "NUMBER" :
            $chars = "0123456789";
            break;
        case "ALL" :
        default :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    }
    $string = "";
    while ( strlen ( $string ) < $len ) {
        $string .= substr ( $chars, mt_rand () % strlen ( $chars ), 1 );
    }
    return $string;
}