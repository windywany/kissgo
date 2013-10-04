<?php
function do_admin_media_images($req, $res) {
    $I = assert_login ();
    $action = $req ['action'];
    $data = '';
    if ($action == 'get') {
        $start = irqst ( 'pid', 1 );
        $start = empty ( $start ) ? 1 : $start;
        $title = rqst ( 'title' );
        $ext = rqst ( 'type' );
        $where ['type'] = 'image';
        if (! empty ( $ext )) {
            $where ['ext'] = $ext;
        }
        if (! empty ( $title )) {
            $where ['name LIKE'] = "%$title%";
        }
        $month = rqst ( 'month' );
        $year = irqst ( 'year', 0 );
        if (! empty ( $month ) && empty ( $year )) { // 月不为空
            $year = date ( 'Y-' );
            $time1 = strtotime ( $year . $month . '-01' );
            $time2 = strtotime ( '+1 months -1 seconds', $time1 );
            $where ['create_time BETWEEN'] = array ($time1, $time2 );
        } elseif (! empty ( $year ) && empty ( $month )) {
            $time1 = strtotime ( $year . '-01-01' );
            $time2 = strtotime ( '+1 years -1 seconds', $time1 );
            $where ['create_time BETWEEN'] = array ($time1, $time2 );
        } elseif (! empty ( $month ) && ! empty ( $year )) {
            $time1 = strtotime ( $year . '-' . $month . '-01' );
            $time2 = strtotime ( '+1 months -1 seconds', $time1 );
            $where ['create_time BETWEEN'] = array ($time1, $time2 );
        }
        
        $atM = new MediaTable ();
        
        $imgs = $atM->query ( 'url' )->where ( $where )->limit ( $start, 20 )->sort ( 'create_time', 'd' );
        
        $total = count ( $imgs );
        if ($total > 0) {
            $files = array ();
            foreach ( $imgs as $img ) {
                $files [] = $img ['url'];
            }
            $data = ceil ( $total / 20 ) . 'ue_page_ue' . implode ( 'ue_separate_ue', $files );
        }
    }
    return $data;
}