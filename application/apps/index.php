<?php
/**
 * @param $request
 * @param Response $response
 * @return mixed
 */
function default_index_action($request, $response) {

    return $response->forward('aaaa.admin_index_action');
}