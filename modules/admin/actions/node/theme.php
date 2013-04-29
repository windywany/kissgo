<?php
assert_login ();
function do_admin_node_theme_get($req, $res) {
    return view ( 'admin/views/node/theme.tpl' );
}