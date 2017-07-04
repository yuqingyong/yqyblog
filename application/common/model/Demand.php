<?php
namespace app\common\model;
use think\db;
use think\Model;
/**	
 * 需求表模型
 */
class Demand extends Model{
	//读取需求列表
	public function get_demand_page($is_show = 'all',$field='*',$limit=10){
		if($is_show == 'all'){
			$list = db('demand')->order('xid desc')->field($field)->paginate($limit);
		}else{
			$list = db('demand')->where('is_show',$is_show)->order('xid desc')->field($field)->paginate($limit);
		}
		$page = $list->render();
		$data = ['list'=>$list,'page'=>$page];
		return $data;
	}



}