<?php
namespace app\common\model;
use app\common\model\Base;
use think\Db;
use think\Model;
/**	
 * 需求表模型
 */
class DemandModel extends Base{
	protected $table = 'yqy_demand';
	// 读取需求列表
	public function get_demand_page($is_show = 'all',$field='*',$limit=10){
		if($is_show == 'all'){
			$list = Db::name('demand')->order('xid desc')->field($field)->paginate($limit);
		}else{
			$list = Db::name('demand')->where('is_show',$is_show)->order('xid desc')->field($field)->paginate($limit);
		}
		$page = $list->render();
		$data = ['list'=>$list,'page'=>$page];
		return $data;
	}



}