<?php
/**
 * 
 * nodes controller
 * @author guangfeng.ning
 *
 */
class NodesController extends Controller {
	public function index($key = '') {
		$data ['key'] = $key;
		return view ( 'index.tpl', $data );
	}
	public function data($page = 1, $rp = 10) {
		$jsonData = array('page'=>$page,'total'=>0,'rows'=>array(),'rp'=>$rp);
		$rows[1] = array('name'=>'Tony',   'favorite_color'=>'green',  'favorite_pet'=>'hamster',   'primary_language'=>'english');
		$rows[2] = array('name'=>'Mary',   'favorite_color'=>'red',    'favorite_pet'=>'groundhog', 'primary_language'=>'spanish');
		$rows[3] = array('name'=>'Seth',   'favorite_color'=>'silver', 'favorite_pet'=>'snake',     'primary_language'=>'french');
		$rows[4] = array('name'=>'Sandra', 'favorite_color'=>'blue',   'favorite_pet'=>'cat',       'primary_language'=>'mandarin');
		foreach($rows AS $rowNum => $row){
			//If cell's elements have named keys, they must match column names
			//Only cell's with named keys and matching columns are order independent.
			$entry = array('id' => $rowNum,
					'cell'=>array(
							'employeeID'       => $rowNum,
							'name'             => $row['name'],
							'primary_language' => $row['primary_language'],
							'favorite_color'   => $row['favorite_color'],
							'favorite_pet'     => $row['favorite_pet']
					)
			);
			$jsonData['rows'][] = $entry;
		}
		$jsonData['total'] = count($rows);
		return new JsonView($jsonData);
	}
}