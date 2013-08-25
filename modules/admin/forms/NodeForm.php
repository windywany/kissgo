<?php
/**
 * 
 * @author Leo
 *
 */
class NodeForm extends DataForm {
    var $author = array ();
    var $cachetime = array ();
    var $commentable = array ();
    var $custome_tpl_chk = array ();
    var $description = array ();
    var $figure = array ();
    var $keywords = array ();
    var $mid = array ();
    var $nid = array ();
    var $node_id = array ();
    var $ontopto = array ();
    var $tags = array ();
    var $flags = array ();
    var $source = array ();
    var $subtitle = array ();
    var $template = array ();
    var $title = array (FWT_VALIDATOR => array ('required' => '请填写标题.' ) );
    var $node_type = array ();
    var $url = array (FWT_VALIDATOR => array ('callback(@check_node_url)' => '文件名或URL格式不正确.' ) );
}