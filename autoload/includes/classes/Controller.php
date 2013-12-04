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
    protected $http_method;
    public function __construct($viewPath, $res, $req, $method, $http_method) {
        $this->viewPath = $viewPath;
        $this->request = $res;
        $this->response = $req;
        $this->method = $method;
        $this->http_method = $http_method;
    }
    public function preRun() {}
    public function postRun($view) {
        if ($view instanceof View) {
            $view->setRelatedPath ( $this->viewPath );
        }
        return $view;
    }
}