<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\admin\model\Article;
use think\request;
use think\Controller;
use think\Db;
class Chat extends Homebase
{
	public function _empty()
	{
		$this->view->engine->layout(false);
		return view('Index/404');
	}
   //闲言碎语
   public function chat()
   {
   	 $this->view->engine->layout(false);
   	 //闲言碎语列表
   	 $chat = db('chat')->where('is_show',1)->order('chid desc')->select();
   	 //标签列表
     $tags = db('tags')->order('tid desc')->select();
     return view('Chat/chat',['chat'=>$chat,'tags'=>$tags]);
   } 
   
}
