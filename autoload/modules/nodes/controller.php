<?php

/**
 *
 * nodes controller.
 * 
 * @author guangfeng.ning
 *
 */
class NodesController extends Controller {
	private $user;
	private $unkown_type;
	public function preRun() {
		$user = whoami ();
		if (! $user->isLogin ()) {
			Response::redirect ( ADMINCP_URL );
		}
		$this->user = $user;
		$this->unkown_type = array (
				'未知类型' 
		);
	}
	/**
	 * 列表页入口.
	 *
	 * @param string $key        	
	 * @return SmartyView
	 */
	public function index($key = '') {
		$data ['key'] = $key;
		$ctm = ContentTypeManager::getInstance ();
		$data ['types'] = array_merge ( array (
				'' => '全部' 
		), $ctm->getTypeOptions () );
		return view ( 'index.tpl', $data );
	}
	/**
	 * 列表数据.
	 *
	 * @param number $page
	 *        	当着页面
	 * @param number $rp
	 *        	每页显示条数
	 * @param string $sortname
	 *        	排序字段
	 * @param string $sortorder
	 *        	排序
	 * @param string $status
	 *        	状态
	 * @param string $sd
	 *        	开始日期
	 * @param string $ed
	 *        	结束日期
	 * @return JsonView
	 */
	public function data($page = 1, $rp = 15, $sortname = 'id', $sortorder = 'desc', $status = '', $sd = '', $ed = '') {
		$ctm = ContentTypeManager::getInstance ();
		$types = $ctm->getTypes ();
		
		$page = intval ( $page );
		$rp = intval ( $rp );
		$rp = $rp ? $rp : 15;
		$start = ($page ? $page - 1 : $page) * $rp;
		$where ['ND.deleted'] = 0;
		
		if ($status == 'trush') {
			$where ['ND.deleted'] = 1;
		} else if ($status == 'n') {
			$where ['ND.status'] = 0;
		} else if ($status == 'p') {
			$where ['ND.status'] = 1;
		}
		$nodes = dbselect ( 'ND.*', 'U1.display_name AS owner', 'U2.display_name AS create_user', 'U3.display_name AS update_user' )->from ( '{nodes} AS ND' )->sort ( $sortname, $sortorder )->limit ( $start, $rp );
		$nodes->where ( $where );
		$nodes->join ( '{users} AS U1', 'ND.uid = U1.id' );
		$nodes->join ( '{users} AS U2', 'ND.create_uid = U2.id' );
		$nodes->join ( '{users} AS U3', 'ND.update_uid = U3.id' );
		$jsonData = array (
				'page' => $page,
				'total' => $nodes->count ( 'ND.id' ),
				'rows' => array (),
				'rp' => $rp 
		);
		
		if ($jsonData ['total'] > 0 && count ( $nodes )) {
			foreach ( $nodes as $row ) {
				$cell [0] = $row ['id'];
				$cell [1] = $row ['title'];
				$type = isset ( $types [$row ['content_type']] ) ? $types [$row ['content_type']] : $this->unkown_type;
				$cell [2] = $type [0];
				$cell [3] = $row ['owner'];
				$cell [4] = $row ['ontop'];
				$cell [5] = $row ['status'];
				$cell [6] = $row ['commentable'];
				$cell [7] = $row ['cache_time'];
				$cell [8] = $row ['update_time'];
				$cell [9] = $row ['update_user'];
				$cell [10] = $row ['create_user'];
				$cell [11] = $row ['create_time'];
				$cell [12] = $row ['author'];
				$cell [13] = $row ['source'];
				$cell [14] = $row ['figure'];
				$jsonData ['rows'] [] = array (
						'id' => $row ['id'],
						'cell' => $cell 
				);
			}
		}
		return new JsonView ( $jsonData );
	}
	/**
	 * 修改导航菜单时ajax的数据源.
	 *
	 * @param number $p
	 *        	当前页
	 * @param string $q
	 *        	查询关键词
	 * @return JsonView
	 */
	public function select2($p = 1, $q = '') {
		$more = true;
		$where = array (
				'deleted' => 0 
		);
		$p = intval ( $p );
		$start = ($p ? $p - 1 : $p) * 15;
		
		$nodes = dbselect ( 'id, title AS text' )->from ( '{nodes}' );
		if (empty ( $q )) {
			$more = false;
		} else {
			$where ['title LIKE'] = "%{$q}%";
		}
		$nodes->where ( $where )->limit ( $start, 15 )->desc ( 'id' );
		$size = count ( $nodes );
		if ($more) {
			$more = $size == 15;
		}
		$data = array (
				'more' => $more,
				'results' => $nodes->toArray () 
		);
		return new JsonView ( $data );
	}
	public function edit($id) {
		$data = array ();
		return view ( 'node_form.tpl', $data );
	}
	public function create($type) {
		$data = array ();
		return view ( 'node_form.tpl', $data );
	}
}