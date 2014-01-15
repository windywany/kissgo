<?php

/**
 * comments controller
 * @author Leo
 *
 */
class NodesCommentController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $data = array ();
        return view ( 'comments.tpl', $data );
    }

    /**
     *
     * @param int $page
     * @param int $rp
     * @param string $sortname
     * @param string $sortorder asc|desc
     * @param string $status ''|'new'|'pass'|'spam'
     * @param string $sd start date
     * @param string $ed end date
     * @param int $nid
     * @param string $key
     */
    public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc', $status = '', $sd = '', $ed = '', $nid = '', $key = '') {
        $page = intval ( $page );
        $rp = intval ( $rp );
        $rp = $rp ? $rp : 15;
        $start = ($page ? $page - 1 : $page) * $rp;
        $where ['CMMT.deleted'] = 0;
        $nid = intval ( $nid );
        if ($nid) {
            $where ['CMMT.nid'] = $nid;
        }
        if (! in_array ( $status, array ('new', 'pass', 'spam', 'trush' ) )) {
            $status = 'new';
        }
        if ($status == 'trush') {
            $where ['CMMT.deleted'] = 1;
        } else {
            $where ['CMMT.status'] = $status;
        }
        if ($sd) {
            $where ['CMMT.create_time >='] = $sd . ' 00:00:00';
        }
        if ($ed) {
            $where ['CMMT.create_time <='] = $ed . ' 23:59:59';
        }
        if ($key) {
            $con = new Condition ();
            $val = '%' . $key . '%';
            $con ['CMMT.subject LIKE'] = $val;
            $con ['||CMMT.content LIKE'] = $val;
            $where [] = $con;
        }
        $comments = dbselect ( 'CMMT.*,ND.title,ND.url,U.display_name,CMMT1.id AS rid,CMMT1.author as rauthor' )->from ( '{comments} AS CMMT' )->where ( $where )->sort ( 'CMMT.' . $sortname, $sortorder )->limit ( $start, $rp );
        $comments->join ( '{nodes} AS ND', 'CMMT.nid = ND.id' );
        $comments->join ( '{users} AS U', 'U.id = CMMT.user_id' );
        $comments->join ( '{comments} AS CMMT1', 'CMMT.parent = CMMT1.id' );
        $total = $comments->count ( 'CMMT.id' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $comments )) {
            foreach ( $comments as $comment ) {
                // the order is very important
                $cell = array ();
                $cell [0] = $comment ['id'];
                $cell [1] = $comment ['author'];
                $cell [2] = $comment ['subject'];
                $cell [3] = $comment ['title'];
                $cell [4] = $comment ['status'];
                //extra fields
                $cell [5] = $comment ['display_name'];
                $cell [6] = $comment ['rid'];
                $cell [7] = $comment ['author_email'];
                $cell [8] = $comment ['author_url'];
                $cell [9] = $comment ['author_IP'];
                $cell [10] = $comment ['url'];
                $cell [11] = $comment ['create_time'];
                $cell [12] = $comment ['user_id'];
                $cell [13] = $comment ['rauthor'];
                $cell [14] = nl2br ( $comment ['content'] );
                $jsonData ['rows'] [] = array ('id' => $comment ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}