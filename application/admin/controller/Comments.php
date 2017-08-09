<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\Comment;
use think\controller;
use think\Db;
use think\request;
class Comments extends Adminbase
{
    public function comment_list()
    {
    	//获取评论列表信息
    	$result = model('Comment')->get_comment_data();
		return view('Comment/comment_list',['list'=>$result['list'],'page'=>$result['page']]);
    }


    //设置评论的显示状态
    public function is_show()
    {
    	$is_show = input('post.status');
    	$res = Comment::where('cmtid',input('post.cmtid'))->update(['status'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除评论
    public function del()
    {
    	$res = Comment::where('cmtid',input('post.cmtid'))->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

}
