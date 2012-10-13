<?php
/**
 * @param $request
 * @param Response $response
 * @return mixed
 */
function do_default_index($request, $response) {
    return $response->forward('index', 'admin');
}