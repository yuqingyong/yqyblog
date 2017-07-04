<?php
namespace app\common\controller;
use think\Controller;
class Shopbase extends controller{
	
	public function _initialize()
	{
		//读取链接
		$friend_url = db('friend_url')->where('status',1)->field('url_name,url,friend_id')->select();
		$this->assign('friend_url',$friend_url);
	}

	
	
}