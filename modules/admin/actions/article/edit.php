<?php
assert_login ();

function do_admin_article_edit_get($req, $res, $id = '') {
    $data = array ();
    if (empty ( $id )) {
        show_page_tip ( '文章不存在.', 'error' );
        Response::back ();
    }
    $articleTable = new ArticleTable ();
    $article = $articleTable->read ( array ('aid' => $id ) );
    if (empty ( $article )) {
        show_page_tip ( '文章不存在.', 'error' );
        Response::back ();
    }
    $data ['article'] = $article;
    $data ['crumb_title'] = __('Edit Article');
    $data ['formIcon'] = 'icon-edit';
    $data ['articleURL'] = murl ( 'admin', 'article' );
    return view ( 'admin/views/article/form.tpl', $data );
}