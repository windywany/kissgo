<?php
assert_login ();
/**
 * 
 * save
 * @param Request $req
 * @param Response $res
 * @param int $id
 */
function do_admin_article_save_post($req, $res, $id = 0) {
    $data ['success'] = true;
    $article = array ();
    $article ['title'] = $req ['title'];
    
    $article ['body'] = $req ['body'];
    $articleTable = new ArticleTable ();
    if (empty ( $id )) {
        $rst = $articleTable->insert ( $article );
    } else {
        $rst = $articleTable->update ( $article, array ('aid' => $id ) );
        if ($rst && isset ( $req ['sync'] )) {
            $node = new KsgNodeTable ();
            $node->update ( array ('title' => $article ['title'] ), array ('node_type' => 'plain', 'node_id' => $id ) );
        }
    }
    if (! $rst) {
        $data ['success'] = false;
        $data ['msg'] = 'Cannot save the article';
    } else {
        $data ['article'] = array ('aid' => $rst ['aid'], 'title' => $rst ['title'] );
    }
    return new JsonView ( $data );
}