<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\admin\model\Comment;
use think\Db;
use think\request;
class Message extends AdminBase
{
    public function message()
    {
    	//获取评论列表信息
    	$message = Db::name('message')->paginate(15);
    	$page = $message->render();
		return view('Message/message',['list'=>$message,'page'=>$page]);
    }


    //设置文章的显示状态
    public function is_show()
    {
    	$id = $this->request->post('id');
    	$is_show = $this->request->post('is_show');
    	$res = Db::name('message')->where('id',$id)->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除评论
    public function del()
    {
    	$id = $this->request->post('id');
    	$res = Db::name('message')->where('id',$id)->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

}
