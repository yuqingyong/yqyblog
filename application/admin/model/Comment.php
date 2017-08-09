<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 评论模型
 */
class Comment extends Model{
	//前台读取评论列表
	public function get_comment($aid)
	{
		//读取无限级分类评论
		$list = db('comment')
			  ->alias('a')
			  ->join('yqy_users b','a.uid = b.uid')
			  ->where(['a.status'=>1,'aid'=>$aid])
			  ->field('username,content,aid,pid,cmtid,date')->select();
		$data = third_category($list,0);
		return $data;
	}

	//后台读取评论列表方式
	public function get_comment_data()
	{
		$comment_list = db('comment')
    	              ->alias('a')
    	              ->join('article b','a.aid = b.aid')
    	              ->join('users c','a.uid = c.uid')
    	              ->field('a.content,cmtid,b.title,c.username,a.date,a.status')
    	              ->paginate(15);
    	$page = $comment_list->render();
    	$data = ['list'=>$comment_list,'page'=>$page];
    	return $data;
	}





}