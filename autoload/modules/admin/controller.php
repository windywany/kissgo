<?php
class AdminController extends Controller {
    public function index($abc, $def = 12) {
        $q = dbselect ( '*' )->from ( '{system_user}' );
        if (count ( $q )) {

        }
        return view ( 'index.tpl', array ('users' => $q ) );
    }
}