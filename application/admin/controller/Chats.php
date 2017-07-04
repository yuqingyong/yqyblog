<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\Chat;
use think\controller;
use think\Db;
use think\request;
class Chats extends Adminbase
{
	//随言列表
    public function chat_list()
    {
    	//读取闲聊列表
    	$list = Chat::where(true)->order('chid desc')->field('create_time,chid,is_show,content')->paginate(10);
    	$page = $list->render();
		return view('Chat/chat_list',['list'=>$list,'page'=>$page]);
    }

    //添加随笔
    public function add_chat(Request $request)
    {
    	if($request->ispost())
    	{
    		$data['content'] = input('post.content');
    		$data['create_time'] = time();
    		$chat = new Chat($data);
			$res  = $chat->save();
			if($res){$this->success('添加成功','Chats/chat_list');exit;}
    	}
    	return view('Chat/add_chat');
    }

    //修改随笔
    public function edit_chat(Request $request)
    {
    	$chid = input('chid');
    	$chat = Chat::where('chid',$chid)->field('content,chid')->find();
    	if($request->ispost())
    	{
    		$data['content'] = input('post.content');
    		$res = Chat::where('chid',input('post.chid'))->update($data);
    		if($res){$this->success('修改成功','Chats/chat_list');exit;}
    	}
    	return view('Chat/edit_chat',['chat'=>$chat]);
    }

    //设置随笔的状态
    public function is_show()
    {
    	$is_show = input('post.is_show');
    	$res = Chat::where('chid',input('post.chid'))->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除随笔
    public function del()
    {
    	$res = Chat::where('chid',input('post.chid'))->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }






}
