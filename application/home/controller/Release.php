<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\common\model\DemandModel;
use think\request;
use think\Session;
use think\Controller;
use think\Db;
use think\Loader;
class Release extends Homebase
{
  public function _empty()
  {
  	$this->view->engine->layout(false);
  	return view('Index/404');
  }


  //需求列表
  public function index()
  {
  	$demand = new DemandModel();
  	$data   = $demand->get_demand_page('1','type,xid,create_time,see_num,comment_num,content,title',10);
  	return view('Release/index',['list'=>$data['list'],'page'=>$data['page']]);
  }	
  
  //发布需求
  public function fabu(Request $request)
  {
  	$this->view->engine->layout(false);
  	$demand = new DemandModel();
  	$data   = $demand->get_demand_page('1','xid,create_time,title',6);
  	if($request->ispost()){
  		if(Session::has('users')){
  		$data['title'] = $requst->post('title');
  		$data['type']  = $requst->post('type');
  		$data['content']   = $requst->post('content','','');
  		$data['__token__'] = $requst->post('__token__');
		$validate = Loader::validate('Demand');
		if(!$validate->check($data)){
		    $this->error($validate->getError());die;
		}else{
			//验证通过处理数据
			$data['create_time'] = date('y-m-d H:i:s',time());
			$data['user_id']     = Session::get('users.uid');
			$data['user_name']   = Session::get('users.username');
			$res = $demand->allowField(true)->save($data);
		}
	    }else{
	    	$this->error('您还未登录，请先登录','home/Index/login');exit;
	    }

  	}
    return view('Release/fabu',['list'=>$data['list']]);
  } 

  
  //需求详情
  public function demand_detail()
  {
  	$xid = input('xid');
  	$this->see_num($xid);
	//查询详情
	$detail = Db::name('demand')->where('xid',$xid)->find();
  	return view('Release/demand_detail',['art_detail'=>$detail]);
  }


  //点击量增加
  public function see_num($xid)
  {
  	Db::name('demand')->where('xid',$xid)->field('see_num')->setInc('see_num');
  }


   
}
