<?php
namespace app\common\model;
use app\common\model\Base;
use think\Db;
use think\Model;
/**	
 * 评论模型
 */
class CommentModel extends Base{
	//定义表
	protected $table = 'yqy_comment';

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