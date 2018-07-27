<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\common\model\ChatModel;
use think\Db;
use think\request;
class Chat extends AdminBase
{
	private $db;
    // 构造函数 实例化ArticleModel表
    public function __construct(){
        parent::__construct();
        $this->db = model('ChatModel');
    }

	// 随言列表
    public function chat_list()
    {
    	// 读取闲聊列表
    	$assign = $this->db->getPage('chat','1=1','chid desc',20,'create_time,chid,is_show,content'); 
		return view('Chat/chat_list',$assign);
    }

    // 添加随笔
    public function add_chat(Request $request)
    {
    	if($request->ispost())
    	{
    		$data['content'] = $this->request->post('content');
    		$data['create_time'] = time();
			$res  = $this->db->addData($data);
			if($res){$this->success('添加成功','Chat/chat_list');exit;}
    	}
    	return view('Chat/add_chat');
    }

    // 修改随笔
    public function edit_chat(Request $request)
    {
    	$chid = $this->request->param('chid');
    	$chat = ChatModel::where('chid',$chid)->field('content,chid')->find();
    	if($request->ispost())
    	{
    		$data['content'] = $this->request->post('content');
    		$map = ['chid'=>input('post.chid')];
    		$res = $this->db->editData($map,$data);
    		if($res){$this->success('修改成功','Chat/chat_list');exit;}
    	}
    	return view('Chat/edit_chat',['chat'=>$chat]);
    }

    // 设置随笔的状态
    public function is_show()
    {
    	$is_show = $this->request->post('is_show');
    	$data    = ['is_show'=>$is_show];
    	$map = ['chid'=>input('post.chid')];
    	$res = $this->db->editData($map,$data);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    // 删除随笔
    public function del()
    {
    	$res = ChatModel::where('chid',input('post.chid'))->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }






}
