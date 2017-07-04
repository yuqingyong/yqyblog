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
    	$comment_list = db('comment')
    	              ->alias('a')
    	              ->join('article b','a.aid = b.aid')
    	              ->join('users c','a.uid = c.uid')
    	              ->field('a.content,cmtid,b.title,c.username,a.date,a.status')
    	              ->paginate(15);
    	$page = $comment_list->render();
		return view('Comment/comment_list',['list'=>$comment_list,'page'=>$page]);
    }


    //设置文章的显示状态
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
