<?php
/**
 * 
 * 前台页面类
 * 此类负责维护所有可用于前台展示的页面，它会为模板提供基本数据：
 * 1. 页面的标题，副标题
 * 2. 页面的来源，作者，创建时间，发布时间
 * 3. 页面的SEO信息
 * 4. 页面的插图，置顶，页面类型，页面URL
 * 5. 页面所处导航菜单的层次信息
 * 6. 面包屑导航信息
 * 7. 等其它信息，详细信息见toArray方法
 * 
 * 模块可以通过此类来维护一个与自定义内容类型相关的页面
 * 
 * @author Guangfeng Ning
 *
 */
class FrontPage {
    protected $nid = 0;
    protected $vpid = 1;
    protected $deleted = 0;
    protected $create_uid = 0;
    protected $create_time;
    protected $update_uid = 0;
    protected $update_time;
    protected $cachetime = 0;
    protected $publish_uid = 0;
    protected $publish_time;
    protected $status = 'draft';
    protected $commentable = 0;
    protected $title;
    protected $subtitle;
    protected $ontopto;
    protected $node_id = 0;
    protected $node_type;
    protected $template;
    protected $author;
    protected $keywords;
    protected $description;
    protected $url_slug;
    protected $url;
    protected $source;
    protected $figure;
    protected $create_user_info = null;
    protected $update_user_info = null;
    protected $publish_user_info = null;
    protected $path;
    protected $paths;
    protected $tags = array ();
    protected $flags = array ();
    protected $mids = array ();
    /**     
     * @var KsgNodeTagsTable
     */
    private $nodeTagTable = null;
    /**     
     * @var KsgTagTable
     */
    private $tagTable = null;
    private $nodeTable = null;
    /**
     * 
     * 使用$page创建一个新的页面
     * @param array $page
     */
    public function __construct($page = null) {
        if ($page && is_array ( $page )) {
            foreach ( $page as $f => $v ) {
                $this->{$f} = $v;
            }
        }
        $this->nodeTagTable = new KsgNodeTagsTable ();
        $this->tagTable = new KsgTagTable ();
        $this->nodeTable = new KsgNodeTable ();
    }
    /**
     * 
     * 根据url初始化一个页面
     * @param string $url
     * @return FrontPage | null 如果页面表中有这个页面返回一个FrontPage实例，反之返回 null.
     */
    public static function initWithPageURL($url) {
        static $nodeTable = false, $vpathTable = false;
        if (! $nodeTable) {
            $nodeTable = new KsgNodeTable ();
            $vpathTable = new KsgVpathTable ();
        }
        $cache = Cache::getCache ();
        if ($url) {
            $key = md5 ( $url );
            $page = $cache->get ( $key, 'page' );
            if (empty ( $page )) {
                $page = $nodeTable->query ( 'ND.*,VP.path,VP.paths', 'ND' )->ljoin ( $vpathTable, 'ND.nid = VP.nid', 'VP' )->where ( array ('url_slug' => $key ) );
                $page = $page [0];
                $cache->add ( $key, $page, 0, 'page' );
            }
            if ($page) {
                $frontPage = new FrontPage ( $page );
                return $frontPage;
            }
        }
        return null;
    }
    /**
     * 
     * 根据$id初始化一个页面
     * @param int $id
     * @return FrontPage | null 如果页面表中有这个页面返回一个FrontPage实例，反之返回 null.
     */
    public static function initWithPageId($id) {
        static $nodeTable = false, $vpathTable = false;
        if (! $nodeTable) {
            $nodeTable = new KsgNodeTable ();
            $vpathTable = new KsgVpathTable ();
        }
        $page = $nodeTable->query ( 'ND.*,VP.path,VP.paths', 'ND' )->ljoin ( $vpathTable, 'ND.nid = VP.nid', 'VP' )->where ( array ('ND.nid' => $id ) );
        $page = $page [0];
        if ($page) {
            $frontPage = new FrontPage ( $page );
            return $frontPage;
        }
        return null;
    }
    /**
     * 
     * 根据$type,$id初始化一个页面
     * @param string $type
     * @param int $id
     * @return FrontPage | null 如果页面表中有这个页面返回一个FrontPage实例，反之返回 null.
     */
    public static function initWithNodeType($type, $id, $empty = false) {
        static $nodeTable = false, $vpathTable = false;
        if (! $nodeTable) {
            $nodeTable = new KsgNodeTable ();
            $vpathTable = new KsgVpathTable ();
        }
        $page = $nodeTable->query ( 'ND.*,VP.path,VP.paths', 'ND' )->ljoin ( $vpathTable, 'ND.nid = VP.nid', 'VP' )->where ( array ('node_type' => $type, 'node_id' => $id ) );
        $page = $page [0];
        if ($page) {
            $frontPage = new FrontPage ( $page );
            return $frontPage;
        } else if ($empty) {
            return new FrontPage ();
        }
        return null;
    }
    /**
     * 
     * 保存这个页面到数据库，本方法运行在事务之外，
     * 如果要使用事务，请在执行此方法之前开启事务，并在执行成功后提交事务
     * @param boolean $publish 是否同时发布这个页面
     * @return boolean
     */
    public function save($publish = false) {
        $nodeTable = $this->nodeTable;
        //TODO need transction    
        $dialect = $nodeTable->getDialect ();
        $dialect->beginTransaction ();
        
        $data = $this->toArray ( true );
        $data = apply_filter ( 'before_save_node', $data );
        
        if (! is_array ( $data ) || empty ( $data )) {
            $dialect->rollBack ();
            return false;
        }
        if ($publish) {
            $data ['status'] = 'published';
            $data ['publish_time'] = time ();
            $I = whoami ();
            $data ['publish_uid'] = $I->getUid ();
        } else {
            unset ( $data ['status'] );
        }
        $nid = $data ['nid'];
        unset ( $data ['nid'] );
        $tags = $data ['tags'];
        $flags = $data ['flags'];
        unset ( $data ['tags'], $data ['flags'] );
        $mid = $data ['mid'];
        unset ( $data ['mid'] );
        if ($nid == 0) {
            $data = $nodeTable->insert ( $data );
        } else {
            if ($nodeTable->update ( $data, array ('nid' => $nid ) )) {
                $data ['nid'] = $nid;
            } else {
                $data = false;
            }
        }
        if ($data) {
            $this->nid = $data ['nid'];
            
            $data ['tags'] = $tags;
            $data ['flags'] = $flags;
            $rst = true;
            do {
                $rst = apply_filter ( 'after_save_node', $data );
                if (! $rst) {
                    break;
                }
                $rst = apply_filter ( 'after_save_node_for_' . $data ['node_type'], $data );
                if (! $rst) {
                    break;
                }
                
                if ($data ['node_type'] == 'catalog') {
                    $rst = $this->savePathInfo ( $data );
                    if (! $rst) {
                        break;
                    }
                }
                
                $this->generateUrl ( $data, $nodeTable );
                
                if (! $this->tagTable->addTagToNode ( $data ['nid'], $tags, 'tag' )) {
                    break;
                }
                if (! $this->tagTable->addTagToNode ( $data ['nid'], $flags, 'flag', true )) {
                    break;
                }
                if (! empty ( $data ['author'] ) && ! $this->tagTable->addTags ( $data ['author'], 'author' )) {
                    break;
                }
                if (! empty ( $data ['source'] ) && ! $this->tagTable->addTags ( $data ['source'], 'source' )) {
                    break;
                }
                if (! empty ( $data ['keywords'] ) && ! $this->tagTable->addTags ( $data ['keywords'], 'keyword' )) {
                    break;
                }
            } while ( false );
            
            if ($rst) {
                $dialect->commit ();
                return $data;
            }
        }
        $dialect->rollBack ();
        return false;
    }
    public function getId() {
        return $this->nid;
    }
    public function getMid() {
        return $this->mid;
    }
    public function isDeleted() {
        return $this->deleted;
    }
    public function getCreateUser() {
        if (! $this->create_uid) {
            return array ();
        }
        if (! $this->create_user_info || $this->create_user_info ['uid'] != $this->create_uid) {
            $this->create_user_info = $this->getUserInfo ( $this->create_uid );
        }
        return $this->create_user_info;
    }
    public function getCreateTime() {
        return $this->create_time;
    }
    public function getUpdateUser() {
        if (! $this->update_uid) {
            return array ();
        }
        if (! $this->update_user_info || $this->update_user_info ['uid'] != $this->update_uid) {
            $this->update_user_info = $this->getUserInfo ( $this->update_uid );
        }
        return $this->update_user_info;
    }
    public function getUpdateTime() {
        return $this->update_time;
    }
    public function getCachetime() {
        return $this->cachetime;
    }
    public function getStatus() {
        return $this->status;
    }
    public function getPublishTime() {
        return $this->publish_time;
    }
    public function getPublishUser() {
        if (! $this->publish_uid) {
            return array ();
        }
        if (! $this->publish_user_info || $this->publish_user_info ['uid'] != $this->publish_uid) {
            $this->publish_user_info = $this->getUserInfo ( $this->publish_uid );
        }
        return $this->publish_user_info;
    }
    public function getCommentable() {
        return $this->commentable;
    }
    public function getTags($flat = false) {
        $tags = $this->nodeTagTable->query ( 'TAG.tag_id,tag,type,slug', 'NT' )->where ( array ('NT.node_id' => $this->nid, 'TAG.type' => 'tag' ) );
        $tags->ljoin ( $this->tagTable, 'NT.tag_id = TAG.tag_id', 'TAG' );
        if ($flat) {
            return $tags->toArray ( 'tag_id', 'tag' );
        } else {
            return $tags->toArray ();
        }
    }
    public function getFlags($flat = false) {
        $tags = $this->nodeTagTable->query ( 'TAG.tag_id,tag,type,slug', 'NT' )->where ( array ('NT.node_id' => $this->nid, 'TAG.type' => 'flag' ) );
        $tags->ljoin ( $this->tagTable, 'NT.tag_id = TAG.tag_id', 'TAG' );
        if ($flat) {
            return $tags->toArray ( 'tag_id', 'tag' );
        } else {
            return $tags->toArray ();
        }
    }
    public function getTypeInfo() {
        $nodeTypeTable = new KsgNodeTypeTable ();
        $type = $nodeTypeTable->query ()->where ( array ('type' => $this->node_type ) );
        return $type [0];
    }
    public function getAuthorInfo() {
        if ($this->author) {
            $author = $this->tagTable->query ( 'tag_id,tag,type,slug' )->where ( array ('type' => 'author', 'tag' => $this->author ) );
            return $author [0];
        }
        return array ();
    }
    public function getSourceInfo() {
        if ($this->source) {
            $source = $this->tagTable->query ( 'tag_id,tag,type,slug' )->where ( array ('type' => 'source', 'tag' => $this->source ) );
            return $source [0];
        }
        return array ();
    }
    public function getTitle() {
        return $this->title;
    }
    public function getSubtitle() {
        return $this->subtitle;
    }
    public function getOntopto() {
        return $this->ontopto;
    }
    public function getNodeID() {
        return $this->node_id;
    }
    public function getNodeType() {
        return $this->node_type;
    }
    public function getNodeContent() {
        if ($this->node_id && $this->node_type) {
            return apply_filter ( 'get_node_content_for_' . $this->node_type, array (), $this->node_id );
        }
        return false;
    }
    public function getTemplate() {
        if (! $this->template) {
            $theme = get_theme ();
            $this->template = KsgNodeTemplateTable::getTemplate ( $theme, $this->node_type );
            if ($this->template == null) {
                $this->template = 'page.tpl';
            }
        }
        return $this->template;
    }
    public function getAuthor() {
        return $this->author;
    }
    public function getKeywords() {
        return $this->keywords;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getUrl() {
        return $this->url;
    }
    public function getSource() {
        return $this->source;
    }
    public function getFigure() {
        return $this->figure;
    }
    public function getCrumb() {
        $menuTable = new KsgMenuItemTable ();
        $vpathTable = new KsgVpathTable ();
        if (empty ( $this->vpid )) {
            $this->vpid = 1;
        }
        $crumb = $vpathTable->getFullPathName ( $this->vpid );
        $pids = array ($this->nid );
        foreach ( $crumb as $c ) {
            $pids [] = $c ['nid'];
        }
        $this->mids = array ();
        $mids = $menuTable->query ( 'menuitem_id as id,up_id' )->where ( array ('type' => 'page', 'page_id IN' => $pids ) );
        foreach ( $mids as $c ) {
            if (! isset ( $this->mids [$c ['id']] )) {
                $this->mids [$c ['id']] = true;
                if ($c ['up_id'] > 0 && ! isset ( $this->mids [$c ['up_id']] )) {
                    $this->mids [$c ['up_id']] = true;
                    $menuTable->getIds ( $c ['up_id'], $this->mids );
                }
            }
        }
        return $crumb;
    }
    public function getMetadata() {
        return apply_filter ( 'get_metadata_for_' . $this->node_type, array (), $this->node_id );
    }
    public function setDeleted($deleted) {
        $this->deleted = $deleted;
    }
    public function setCreateUid($create_uid) {
        $this->create_uid = $create_uid;
    }
    public function setCreateTime($create_time) {
        $this->create_time = $create_time;
    }
    public function setUpdateUid($update_uid) {
        $this->update_uid = $update_uid;
    }
    public function setUpdateTime($update_time) {
        $this->update_time = $update_time;
    }
    public function setCachetime($cachetime) {
        $this->cachetime = $cachetime;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
    public function setCommentable($commentable) {
        $this->commentable = $commentable;
    }
    public function setTitle($title) {
        $this->title = $title;
    }
    public function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
    }
    public function setOntopto($ontopto) {
        $this->ontopto = $ontopto;
    }
    public function setNodeID($node_id) {
        $this->node_id = $node_id;
    }
    public function setNodeType($node_type) {
        $this->node_type = $node_type;
    }
    public function setTemplate($template) {
        $this->template = $template;
    }
    public function setAuthor($author) {
        $this->author = $author;
    }
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setUrl($url) {
        $this->url = $url;
        $this->url_slug = '';
    }
    public function setSource($source) {
        $this->source = $source;
    }
    public function setFigure($figure) {
        $this->figure = $figure;
    }
    public function setVpid($vpid) {
        $this->vpid = $vpid;
    }
    public function getVpath($vpid = null, $name = false) {
        $vpid = $vpid == null ? $this->vpid : $vpid;
        $vpTable = new KsgVpathTable ();
        if ($name) {
            $fpns = $vpTable->getFullPathName ( $vpid );
            $fns = array ();
            foreach ( $fpns as $f ) {
                $fns [] = $f ['name'];
            }
            return implode ( ' > ', $fns );
        } else {
            $path = $vpTable->read ( array ('id' => $vpid ) );
            if ($path) {
                return ltrim ( $path ['paths'], '/' );
            } else {
                return '';
            }
        }
    }
    /**
     * 
     * 将该实例转化为array，便于在模板中使用或便于存储到数据库中
     * @param boolean $persist 是存储还是用于模板显示
     * @param boolean $flat 是否获取扁平信息
     * @return array 
     */
    public function toArray($persist = false, $flat = false) {
        $cache = $key = null;
        if (! $persist) { //如果是用于展示，尝试从缓存加载
            $cache = Cache::getCache ();
            $key = md5 ( $this->nid );
            $page = $cache->get ( $key, 'page_content' );
            if ($page) {
                return $page;
            }
        }
        $page ['nid'] = $this->nid;
        $page ['deleted'] = $this->deleted;
        if (! $persist) {
            $page ['theme'] = get_theme ();
            $page ['create_uid'] = $this->create_uid;
            $page ['create_time'] = $this->create_time;
            $page ['update_uid'] = $this->update_uid;
            $page ['update_time'] = $this->update_time;
            $page ['publish_uid'] = $this->publish_uid;
            $page ['publish_time'] = $this->publish_time;
            $page ['tags'] = $this->getTags ( $flat );
            $page ['flags'] = $this->getFlags ( $flat );
            $page ['crumb'] = $this->getCrumb ();
            $page ['active_menus'] = $this->mids;
            $page ['path'] = $this->path;
            $page ['paths'] = $this->paths;
            $page ['pathsname'] = $this->getVpath ( $this->vpid, true );
            if ($flat) {
                $page ['node_type'] = $this->node_type;
                $page ['author'] = $this->author;
                $page ['source'] = $this->source;
            } else {
                $page ['create_user'] = $this->getCreateUser ();
                $page ['update_user'] = $this->getUpdateUser ();
                $page ['publish_user'] = $this->getPublishUser ();
                $page ['content'] = $this->getNodeContent ();
                $page ['metadata'] = $this->getMetadata ();
                $page ['node_type'] = $this->getTypeInfo ();
                $page ['author'] = $this->getAuthorInfo ();
                $page ['source'] = $this->getSourceInfo ();
            }
        } else {
            $page ['node_type'] = $this->node_type;
            $page ['author'] = $this->author;
            $page ['source'] = $this->source;
            $page ['tags'] = $this->tags;
            $page ['flags'] = $this->flags;
        }
        $page ['cachetime'] = $this->cachetime;
        if ($this->status) {
            $page ['status'] = $this->status;
        }
        $page ['commentable'] = $this->commentable;
        $page ['title'] = $this->title;
        $page ['subtitle'] = $this->subtitle;
        $page ['ontopto'] = $this->ontopto;
        $page ['node_id'] = $this->node_id;
        $page ['template'] = $this->template;
        $page ['keywords'] = $this->keywords;
        $page ['description'] = $this->description;
        $page ['url'] = apply_filter ( 'get_url_for_' . $page ['node_type'], $this->url, $page );
        $page ['figure'] = $this->figure;
        $page ['vpid'] = $this->vpid;
        if ($cache) {
            $cache->add ( $key, $page, 0, 'page_content' );
        }
        return $page;
    }
    /**
     * 
     * 清空页面缓存
     * @param int $id
     */
    public static function clearCache($id) {
        $cache = Cache::getCache ();
        $key = md5 ( $id );
        $cache->delete ( $key, 'page_content' );
    }
    /**
     * 
     * 清除页面组缓存
     */
    public static function clearGroupCache() {
        $cache = Cache::getCache ();
        $cache->clear ( false, 'page_content' );
    }
    protected function getUserInfo($uid) {
        static $userMode = false;
        if (! $userMode) {
            $userMode = new KsgUserTable ();
        }
        $rst = $userMode->query ( 'uid,email,login,username,deleted,status' )->where ( array ('uid' => $uid ) );
        return $rst [0];
    }
    private function savePathInfo($data) {
        $ksgVfs = new KsgVpathTable ();
        $builder = $ksgVfs->getBuilder ();
        $path = trim ( $data ['url'], '/' );
        $up = $ksgVfs->read ( array ('id' => $data ['vpid'] ) );
        if ($up) {
            $rst = true;
            $where = array ('nid' => $data ['nid'] );
            $npaths = $up ['paths'] . $path . '/';
            $node_id = 0;
            if ($ksgVfs->exist ( $where )) {
                $ovpid = rqst ( 'ovpid' );
                $cp = $ksgVfs->read ( $where );
                $node_id = $cp ['id'];
                $rst = $ksgVfs->update ( array ('upid' => $data ['vpid'], 'name' => $data ['subtitle'], 'path' => $path, 'paths' => $npaths ), $where );
                if ($rst && ($ovpid != $data ['vpid'] || $path != $cp ['path'])) {
                    $opaths = $cp ['paths'];
                    if ($rst) {
                        $sub = imtv ( $builder->f_substr ( imtf ( 'paths' ), strlen ( $opaths ) + 1 ) );
                        $concat = imtv ( $builder->f_concat ( $npaths, $sub ) );
                        $rst = $ksgVfs->update ( array ('paths' => $concat ), array ('paths LIKE' => $opaths . '%' ) );
                    }
                }
            } else {
                $rst = $ksgVfs->insert ( array ('upid' => $data ['vpid'], 'nid' => $data ['nid'], 'name' => $data ['subtitle'], 'path' => $path, 'paths' => $npaths ) );
                $node_id = $rst ? $rst ['id'] : 0;
            }
            $rst = $rst ? true : false;
            if ($rst) {
                return $this->nodeTable->update ( array ('node_id' => $node_id ), array ('nid' => $data ['nid'] ) );
            }
        }
        return false;
    }
    /**
     * 生成url,默认使用模式{vpath}/{title}.html
     * url中可使用的变量：
     * {vpath} - 虚拟目录
     * {title} - 标题拼音
     * {type} - 内容
     * {id} - 页面ID
     * {nid} - 内容ID
     * {author} - 作者拼音
     * {source} - 来源拼音
     * {Y} - 4位年
     * {y} - 2位年
     * {m} - 月
     * {d} - 日
     * @param array $data
     */
    protected function generateUrl($data, $nodeTable) {
        if (! empty ( $data ['nid'] )) {
            if ($data ['node_type'] == 'catalog') {
                $data ['url'] = $this->getVpath ( $data ['vpid'] ) . $data ['url'];
            } else {
                $data = apply_filter ( 'get_the_url_for_' . $data ['node_type'], $data );
                if (empty ( $data ['url'] )) {
                    $data ['url'] = cfg ( 'url_pattern', '{vpath}/{title}.html' );
                }
                if (preg_match ( '#^(http|ftp)s?://.+#', $data ['url'] )) {
                    return;
                }
            }
            if (strpos ( $data ['url'], '{' ) === false) {
                if (! preg_match ( '#\.html?#', $data ['url'] )) {
                    $url = trailingslashit ( ltrim ( $data ['url'], '/' ) );
                    $nodeTable->update ( array ('url' => $url, 'url_slug' => md5 ( $url ) ), array ('nid' => $data ['nid'] ) );
                }
                return;
            }
            $varsp ['vpath'] = $this->getVpath ( $data ['vpid'] );
            $varsp ['title'] = preg_replace ( '[^a-b0-9_-]', '', Pinyin::c ( $data ['title'] ) );
            $varsp ['type'] = $data ['node_type'];
            $varsp ['id'] = $data ['nid'];
            $varsp ['nid'] = $data ['node_id'];
            $varsp ['author'] = preg_replace ( '[^a-b0-9_-]', '', Pinyin::c ( $data ['author'] ) );
            $varsp ['source'] = preg_replace ( '[^a-b0-9_-]', '', Pinyin::c ( $data ['source'] ) );
            $varsp ['Y'] = date ( 'Y' );
            $varsp ['y'] = date ( 'y' );
            $varsp ['m'] = date ( 'm' );
            $varsp ['d'] = date ( 'd' );
            $varsp ['.*?'] = '';
            $url = $data ['url'];
            foreach ( $varsp as $key => $val ) {
                $url = preg_replace ( '#\{\s*' . $key . '\s*\}#', $val, $url );
            }
            $url = trim ( preg_replace ( '#/{2,}#', '/', $url ), '/' );
            $nodeTable->update ( array ('url' => $url, 'url_slug' => md5 ( $url ) ), array ('nid' => $data ['nid'] ) );
        }
    }
}
//end of FrontPage