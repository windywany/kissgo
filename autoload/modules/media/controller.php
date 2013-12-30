<?php

/**
 * 多媒体
 * @author Leo
 *
 */
class MediaController extends Controller {

    public function index() {
        $data = array ();
        return view ( 'media.tpl', $data );
    }

    public function upload() {
        return 'nihao';
    }
    /**
     * user data - JSON
     *
     * @param int $page
     * @param int $rp
     */
    public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc') {
        $page = intval ( $page );
        $rp = intval ( $rp );
        $rp = $rp ? $rp : 15;
        $start = ($page ? $page - 1 : $page) * $rp;
        $where = Condition::where ( array ('display_name', 'like' ), 'status', array ('username', 'like' ), array ('email', 'like' ), 'G.gid' );
        $users = dbselect ( 'U.*', 'G.name AS groupname' )->from ( '{users} AS U' )->join ( '{groups} AS G', 'U.gid = G.gid' )->where ( $where )->limit ( $start, $rp )->sort ( $sortname, $sortorder );
        $total = $users->count ( 'U.id' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $users )) {
            foreach ( $users as $u ) {
                // the order is very important
                $cell = array ();
                $cell [] = $u ['id'];
                $cell [] = $u ['username'];
                $cell [] = $u ['display_name'];
                $cell [] = $u ['groupname'];
                $cell [] = $u ['email'];
                $cell [] = $u ['status'] ? '' : __ ( '@admin:blocked' );
                $cell [] = $u ['last_ip'];
                $cell [] = $u ['last_time'];
                $jsonData ['rows'] [] = array ('id' => $u ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}