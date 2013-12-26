<?php

/**
 *
 * user's group controller
 * @author guangfeng.ning
 *
 */
class AdminUsergroupController extends Controller {

    public function preRun() {
        $user = whoami ();
        if (! $user->isLogin ()) {
            Response::redirect ( ADMINCP_URL );
        }
    }

    public function index() {
        $data = array ();
        $form = new UsergroupForm ();
        $data ['validateRule'] = $form->rules ();
        $view = view ( 'groups.tpl', $data );
        return $view;
    }

    public function add() {
        $data ['action'] = __ ( '@admin:Add New User Group' );
        $form = new UsergroupForm ();
        $data ['validateRule'] = $form->rules ();
        $view = view ( 'group_form.tpl', $data );
        return $view;
    }

    public function edit($gid = 0) {
        $data ['action'] = __ ( '@admin:Edit User Group' );
        $group = dbselect ( '*' )->from ( '{groups}' )->where ( array ('gid' => intval ( $gid ) ) );
        if (count ( $group )) {
            $group = $group [0];
        } else {
            $group = array ();
        }
        $form = new UsergroupForm ( $group );
        $data ['validateRule'] = $form->rules ();
        $data ['group'] = $group;
        $view = view ( 'group_form.tpl', $data );
        return $view;
    }

    public function save($gid = 0) {
        $data = array ('success' => false );
        $form = new UsergroupForm ();
        if ($form->valid ()) {
            $gid = $form->save ( $gid );
            if ($gid) {
                $data ['success'] = true;
                $data ['id'] = $gid;
            } else {
                $data ['msg'] = '无法保存用户组信息.';
            }
        } else {
            $formerr = $form->getErrors ();
            $data ['formerr'] = $formerr;
        }
        return new JsonView ( $data );
    }

    /**
     *
     * groups data
     * @param int $page
     * @param int $rp
     * @param string $sortname
     * @param string $sortorder
     */
    public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc') {
        $page = intval ( $page );
        $rp = intval ( $rp );
        $rp = $rp ? $rp : 15;
        $start = ($page ? $page - 1 : $page) * $rp;
        $where = Condition::where ( array ('name', 'like' ) );
        $groups = dbselect ( '*' )->from ( '{groups}' )->where ( $where )->limit ( $start, $rp )->sort ( $sortname, $sortorder );
        $total = $groups->count ( 'gid' );
        $jsonData = array ('page' => $page, 'total' => $total, 'rows' => array (), 'rp' => $rp );
        if ($total > 0 && count ( $groups )) {
            foreach ( $groups as $g ) {
                $cell = array ();
                $cell [] = $g ['gid'];
                $cell [] = $g ['name'];
                $cell [] = $g ['note'];
                $jsonData ['rows'] [] = array ('id' => $g ['gid'], 'cell' => $cell );
            }
        }
        return new JsonView ( $jsonData );
    }
}