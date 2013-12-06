<?php
/**
 * 
 * nodes controller
 * @author guangfeng.ning
 *
 */
class NodesController extends Controller {
    public function index($key = '') {
        $data ['key'] = $key;
        return view ( 'index.tpl', $data );
    }
    public function data($start = 0, $limit = 20) {

    }
}