<?php

/**
 * comments controller
 * @author Leo
 *
 */
class NodesCommentController extends Controller {
    private $user;

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
        $this->user = $user;
    }

    public function index() {
        $data = array ();
        return view ( 'comments.tpl', $data );
    }

    public function edit($id) {
        $data = array ();
        $cmt = dbselect ( '*' )->from ( '{comments}' )->where ( array ('id' => $id ) );
        $data ['cmt'] = $cmt [0];
        return view ( 'comment_edit.tpl', $data );
    }

    public function save($id, $author = '', $url = '', $mail = '', $subject = '', $status = '', $content = '', $apid = 0) {
        $data ['success'] = false;
        if (! empty ( $id )) {
            $cmt ['author'] = $author;
            $cmt ['author_url'] = $url;
            $cmt ['subject'] = $subject;
            $cmt ['content'] = $content;
            $cmt ['author_email'] = $mail;
            $cmt ['status'] = in_array ( $status, array ('new', 'pass', 'spam' ) ) ? $status : 'spam';
            if ($cmt ['status'] == 'pass' && empty ( $apid )) {
                $cmt ['approved_time'] = date ( 'Y-m-d H:i:s' );
                $cmt ['approved_uid'] = $this->user ['uid'];
            }
            $rst = dbupdate ( '{comments}' )->set ( $cmt )->where ( array ('id' => $id ) );
            $data ['success'] = $rst->execute ();
            if (! $data ['success']) {
                $data ['msg'] = $rst->error ();
            }
        } else {
            $data ['msg'] = '错误的评论编号';
        }
        return new JsonView ( $data );
    }

    public function reply($id, $nid, $subject = '', $content = '') {
        $data ['success'] = true;
        $cmt ['parent'] = intval ( $id );
        $cmt ['nid'] = $nid;
        $cmt ['user_id'] = $this->user ['uid'];
        $cmt ['create_uid'] = $this->user ['uid'];
        $cmt ['status'] = 'pass';
        $cmt ['subject'] = $subject;
        $cmt ['content'] = $content;
        $cmt ['create_time'] = date ( 'Y-m-d H:i:s' );
        $cmt ['author'] = $this->user ['name'];
        $cmt ['author_email'] = $this->user ['mail'];
        $cmt ['author_IP'] = $_SERVER ['REMOTE_ADDR'];

        $rst = dbinsert ( $cmt )->inito ( '{comments}' );
        if (count ( $rst ) > 0) {
            count ( dbupdate ( '{comments}' )->set ( array ('status' => 'pass' ) )->where ( array ('id' => $id ) ) );
        } else {
            $data ['success'] = false;
            $data ['msg'] = $rst->error ();
        }
        return new JsonView ( $data );
    }

    public function approve($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('status' => 'pass', 'approved_uid' => $this->user ['uid'], 'approved_time' => date ( 'Y-m-d H:i:s' ) ) )->where ( array ('id IN' => $ids, 'status' => 'new' ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function revoke($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('status' => 'new' ) )->where ( array ('id IN' => $ids, 'status' => 'pass' ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function spam($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('status' => 'spam' ) )->where ( array ('id IN' => $ids ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function unspam($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('status' => 'new' ) )->where ( array ('id IN' => $ids, 'status' => 'spam' ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function trash($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('deleted' => 1 ) )->where ( array ('id IN' => $ids ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function restore($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbupdate ( '{comments}' )->set ( array ('deleted' => 0 ) )->where ( array ('id IN' => $ids ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
    }

    public function delete($ids) {
        $ids = safe_ids ( $ids, ',', true );
        $rst = dbdelete ()->from ( '{comments}' )->where ( array ('id IN' => $ids, 'deleted' => 0 ) );
        $data ['success'] = $rst->execute ();
        if (! $data ['success']) {
            $data ['msg'] = $rst->lastError ();
        }
        return new JsonView ( $data );
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
                $cell [15] = $comment ['nid'];
                $jsonData ['rows'] [] = array ('id' => $comment ['id'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}