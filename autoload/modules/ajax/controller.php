<?php

/**
 *
 *
 * @author guangfeng.ning
 *
 */
class AjaxController extends Controller {

    public function index($callback) {
        return apply_filter ( 'ajax_' . $callback, '' );
    }

    public function post_post($callback) {
        return apply_filter ( 'ajax_post_' . $callback, '' );
    }

    public function get_get($callback) {
        return apply_filter ( 'ajax_get_' . $callback, '' );
    }

    /**
     *
     * ajax validate
     * @param string $_fm_
     * @param string $_cb_
     * @param string $_fd_
     */
    public function validate($_fm_, $_cb_, $_fd_) {
        if (class_exists ( $_fm_ )) {
            $_form = new $_fm_ ();
            if (is_callable ( array ($_form, $_cb_ ) )) {
                $rq = Request::getInstance ( true );
                $value = $rq->get ( $_fd_, '' );
                $rst = call_user_func_array ( array ($_form, $_cb_ ), array ($value, $_form->toArray () ) );
                if ($rst === true) {
                    return 'true';
                } else {
                    return $rst;
                }
            } else {
                return __ ( 'Remove Validate Error!' );
            }
        } else {
            return __ ( 'Remove Validate Error!' );
        }
    }
}