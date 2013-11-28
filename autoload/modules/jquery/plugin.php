<?php
bind ( 'get_admincp_style_files', 'jquery_add_style_files' );
function jquery_add_style_files($files) {
    //$files [] = MODULE_URL . 'jquery/css/gridster.css';
    return $files;
}