<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use think\request;
use think\Controller;
use think\Db;
class User extends Homebase
{
	//客服聊天界面
	public function user_html()
	{
		$this->view->engine->layout(false);
		return view('Chat/user_html');
	}

	//实时查询客服向用户发送的消息
	public function user_server()
	{
		set_time_limit(0);
		//实时查询是否有用户向客服发送新消息
		//ob_start();
		//echo str_repeat('', 4096);
		//ob_end_flush();
		//ob_flush();
		while (true) {
			$res = db('user_chat')->where(['user_type'=>2,'status'=>0])->order('id','desc')->find();
			if($res){
				//如果存在新消息，则更新该条信息的状态
				db('user_chat')->where('id',$res['id'])->update(['status'=>1]);
				//反向ajax
				die(json_encode($res));
			}
			
		}
	}


	//接收用户发送的消息存库
	public function user_send_msg()
	{
		$data['content'] = input('post.content');
		$data['create_time'] = date('Y-m-d H:i:s',time());
		$data['user_name'] = '小明';
		$data['user_type'] = 1;
		if(!empty($data['content'])){
			$res = db('user_chat')->insert($data);
			echo json_encode($data);
		}
		
	}



}