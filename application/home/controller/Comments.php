<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\admin\model\Comment;
use think\request;
use think\Session;
use think\Controller;
use think\Db;
class Comments extends Homebase
{
	public function _empty()
	{
		$this->view->engine->layout(false);
		return view('Index/404');
	}

     //添加评论
  	public function add_comment()
  	{
    	//判断用户是否登录
    	if(Session::has('users')){
    	  $data = $request->post();
    	  $data['uid'] = Session::get('users.uid');
    	  $data['date']= time();
    	  $res = Db::name('Comment')->insert($data);
    	  if($res){
	      	  //如果已经评论成功，则增加该文章的评论次数
	      	  Db::name('article')->where('aid',$data['aid'])->field('comment_num')->setInc('comment_num');
	      	  return json_encode(['ok'=>'y']);exit;
    	  }
	    }else{
	      	  return json_encode(['ok'=>'n']);exit;
	    }
    } 

  






   
}
