<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\common\model\Demand;
use think\controller;
use think\Db;
use think\request;
class Demands extends Adminbase
{
	//需求列表
	public function demand_list()
	{
		$demand = new Demand();
  		$data   = $demand->get_demand_page('all','type,xid,create_time,user_name,see_num,title,is_show',10);
		return view('Demand/demand_list',['list'=>$data['list'],'page'=>$data['page']]);
	}

	//设置文章的显示状态
    public function is_show()
    {
    	$is_show = input('post.is_show');
    	$res = Demand::where('xid',input('post.xid'))->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除需求
    public function delete()
    {
    	$xid = input('post.xid');
    	$res = db('demand')->where('xid',$xid)->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }




}