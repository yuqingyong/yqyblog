<?php
namespace app\common\model;
use app\common\model\Base;
use think\Db;
use think\Model;
/**	
 * 评论模型
 */
class CommentModel extends Base{
	// 定义表
	protected $table = 'yqy_comment';

	// 状态获取器
	public function getStatusAttr($value)
    {
        $status = [0=>'禁用',1=>'正常'];
        return $status[$value];
    }

	// 读取评论列表
	public function get_comment($aid)
	{
		$list = Db::name('comment')
			  ->alias('a')
			  ->join('yqy_users b','a.uid = b.uid')
			  ->where(['a.status'=>1,'aid'=>$aid])->field('content,date,a.email,a.status,b.username,b.head_img')->select();
		foreach ($list as $k => $v) {
			$list[$k]['date'] = date('Y-m-d H:i:s',$v['date']);
		}
		return $list;
	}
	
}