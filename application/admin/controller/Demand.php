<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\DemandModel;
use think\Db;
use think\request;
class Demand extends AdminBase
{
	//需求列表
	public function demand_list()
	{
		$demand = new DemandModel();
  		$data   = $demand->get_demand_page('all','type,xid,create_time,user_name,see_num,title,is_show',10);
		return view('Demand/demand_list',['list'=>$data['list'],'page'=>$data['page']]);
	}

	// 设置文章的显示状态
    public function is_show()
    {
    	$is_show = $this->request->post('is_show');
    	$res = DemandModel::where('xid',input('post.xid'))->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    // 删除需求
    public function delete()
    {
    	$xid = $this->request->post('xid');
    	$res = Db::name('demand')->where('xid',$xid)->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }




}