<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use app\admin\model\Comment;
use think\request;
use think\Session;
use think\Db;
class Comments extends HomeBase
{
  	public function _empty()
  	{
  		$this->view->engine->layout(false);
  		return view('Index/404');
  	}

    // 添加评论
  	public function add_comment(Request $request)
  	{
      $data['email'] = $this->request->post('email');
      $data['content'] = $this->request->post('content','','htmlspecialchars');
      $data['uid'] = Session::get('users.uid');
      $data['date'] = time();
      $data['aid'] = $this->request->post('aid');
      $rule = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
      if(!Session::has('users')) return json_encode(['status'=>0,'msg'=>'您还未登录，请先登录']);
      if(!preg_match($rule, $data['email'])) return json_encode(['status'=>0,'msg'=>'邮箱格式不正确']);
      # 插入数据
      $res = Db::name('comment')->insert($data);
      if($res) return json_encode(['status'=>1,'msg'=>'评论成功']);
    }

}
