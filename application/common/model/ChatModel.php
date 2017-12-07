<?php
namespace app\common\model;
use app\common\model\Base;
use think\db;
use think\Model;
/**	
 * 闲聊模型
 */
class ChatModel extends Base{
	//闲聊列表时
	protected $table = 'yqy_chat';
	//打开自动写入时间戳
	protected $autoWriteTimestamp = true;
	// 关闭自动写入update_time字段
    protected $updateTime = false;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
}