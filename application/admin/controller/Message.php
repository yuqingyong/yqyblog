<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\Comment;
use think\controller;
use think\Db;
use think\request;
class Message extends Adminbase
{
    public function message()
    {
    	//获取评论列表信息
    	$message = db('message')->paginate(15);
    	$page = $message->render();
		return view('Message/message',['list'=>$message,'page'=>$page]);
    }


    //设置文章的显示状态
    public function is_show()
    {
    	$id = input('post.id');
    	$is_show = input('post.is_show');
    	$res = db('message')->where('id',$id)->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除评论
    public function del()
    {
    	$id = input('post.id');
    	$res = db('message')->where('id',$id)->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

}
