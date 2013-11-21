<?php
/**
 * Controller
 * 
 * @author guangfeng.ning
 *
 */
abstract class Controller {
    protected $viewPath;
    protected $request;
    protected $response;
    protected $method;
    public function __construct($viewPath, $res, $req, $method) {
        $this->viewPath = $viewPath;
        $this->request = $res;
        $this->response = $req;
        $this->method = $method;
    }
    public function preRun() {}
    public function postRun($view) {
        if ($view instanceof View) {
            $view->setRelatedPath ( $this->viewPath );
        }
        return $view;
    }
}