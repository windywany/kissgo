<?php
assert_login ();

function do_admin_article_new_get($req, $res) {
    $data = array ();
    
    
    $data ['article'] = array ('');
    $data ['crumb_title'] = __('New Article');
    $data ['formIcon'] = 'icon-plus-sign';
    $data ['articleURL'] = murl ( 'admin', 'article' );
    return view ( 'admin/views/article/form.tpl', $data );
}
// end of article/add.php