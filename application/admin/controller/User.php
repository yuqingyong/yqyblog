<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\common\model\Users;
use think\controller;
use think\Db;
use think\request;
class User extends Adminbase
{
	//会员列表
    public function user_list(Request $request)
    {
    	if($request->ispost())
    	{
    		$kw = input('post.word');
    		$users = new Users();
    		$res = $users->get_user_page('all','all',10,$kw);
    	}else{
    		$users = new Users();
    		$res = $users->get_user_page('all','all');
    	}
		return view('User/user_list',['list'=>$res['list'],'page'=>$res['page']]);
    }

    //修改会员状态
    public function edit_status()
    {
    	$status = input('post.status');
    	$res = Users::where('uid',input('post.uid'))->update(['status'=>$status]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //邮件回复
    public function replay_email()
    {
    	echo "这是邮箱回复功能！";die;
    }



}
