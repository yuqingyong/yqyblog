<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\CommentModel;
use think\Db;
use think\request;
class Comment extends AdminBase
{
    public function comment_list()
    {
    	// 获取评论列表信息
    	$comment_list = Db::name('comment')
    	              ->alias('a')
    	              ->join('article b','a.aid = b.aid')
    	              ->join('users c','a.uid = c.uid')
    	              ->field('a.content,cmtid,b.title,c.username,a.date,a.status')
    	              ->paginate(15);
    	$page = $comment_list->render();
		return view('Comment/comment_list',['list'=>$comment_list,'page'=>$page]);
    }


    // 设置评论的显示状态
    public function is_show()
    {
    	$status = $this->request->post('status');
    	$data   = ['status'=>$status];
    	$map 	= ['cmtid'=>input('post.cmtid')];
        $comment = new CommentModel();
    	$res 	= $comment->editData($map,$data);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    // 删除评论
    public function del()
    {
    	$res = CommentModel::where('cmtid',input('post.cmtid'))->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

}
