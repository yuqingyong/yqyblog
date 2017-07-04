<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 评论模型
 */
class Comment extends Model{
	//读取评论列表
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
}