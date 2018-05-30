<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use think\request;
use think\Db;
class Chat extends HomeBase
{
	public function _empty()
	{
		$this->view->engine->layout(false);
		return $this->fetch('Index/404');
	}
   //闲言碎语
   public function chat()
   {
   	 $this->view->engine->layout(false);
   	 //闲言碎语列表
   	 $chat = Db::name('chat')->where('is_show',1)->order('chid desc')->select();
   	 //标签列表
     $tags = Db::name('tags')->order('tid desc')->select();
     return $this->fetch('Chat/chat',['chat'=>$chat,'tags'=>$tags]);
   } 

   //心愿墙页面
   public function message(Request $request)
   {
   	 $this->view->engine->layout(false);
   	 //查询留言数据
   	 $message = Db::name('message')->where('is_show',1)->order('id desc')->select();
   	 if($request->ispost())
   	 {
   	 	if(!empty($request->post('content')))
   	 	{
   	 		$data['content'] = $request->post('content','','htmlentities');
	   	 	$data['nickname']= $request->post('nickname') ? $request->post('nickname') : '游客';
	   	 	$data['create_time'] = time();
	   	 	$res = Db::name('message')->insert($data);
	   	 	if($res){$this->success('提交成功...');exit;}
   	 	}else{$this->error('亲，请输入想说的话！');exit;}
   	 	
   	 }
   	 return $this->fetch('Chat/message',['message'=>$message]);
   }

   
}
