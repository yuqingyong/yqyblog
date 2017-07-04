<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\common\model\Demand;
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
  	$demand = new Demand();
  	$data   = $demand->get_demand_page('1','type,xid,create_time,see_num,comment_num,content,title',10);
  	return view('Release/index',['list'=>$data['list'],'page'=>$data['page']]);
  }	
  
  //发布需求
  public function fabu(Request $request)
  {
  	$this->view->engine->layout(false);
  	$demand = new Demand();
  	$data   = $demand->get_demand_page('1','xid,create_time,title',6);
  	if($request->ispost()){
  		if(Session::has('users')){
  		$data['title'] = input('post.title');
  		$data['type']  = input('post.type');
  		$data['content']   = input('post.content','','');
  		$data['__token__'] = input('post.__token__');
		$validate = Loader::validate('Demand');
		if(!$validate->check($data)){
		    $this->error($validate->getError());die;
		}else{
			//验证通过处理数据
			$data['create_time'] = date('y-m-d H:i:s',time());
			$data['user_id']     = Session::get('users.uid');
			$data['user_name']   = Session::get('users.username');
			$res = $demand->allowField(true)->save($data);
			//if($res){$this->success('提交成功');exit;}
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
  	//统计网站信息
	$web['all_article_num'] = db('article')->count();
	$web['web_day'] = timediff(1493568000,time());
	//查询详情
	$detail = db('demand')->where('xid',$xid)->find();
  	return view('Release/demand_detail',['web'=>$web,'art_detail'=>$detail]);
  }


  //点击量增加
  public function see_num($xid)
  {
  	db('demand')->where('xid',$xid)->field('see_num')->setInc('see_num');
  }


   
}
