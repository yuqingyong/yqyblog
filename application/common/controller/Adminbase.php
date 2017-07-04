<?php
namespace app\common\controller;
use think\Controller;
use think\Session;
class Adminbase extends controller{
	
	public function _initialize()
	{
		//echo "hello world";die;
		$this->view->engine->layout(false);
		if(empty(Session::get('admin_user.username'))){
			$this->error('您还未登录，请先登录','admin/Login/login');
		}


		// $request = Request::instance();
		// $module_name = $request->module();//当前操作的模型名
		// $controller_name = $request->controller();//当前操作的控制器名
		// $action_name = $request->action();//当前操作的方法名
		// //实例化auth权限认证类
		// $auth=new \think\Auth();
		// $rule_name=$module_name.'/'.$controller_name.'/'.$action_name;
		// $result=$auth->check($rule_name,session('admin_user.id'));
		
		// if(!$result){
		// 	$this->error('您没有权限访问', 'javascript:history.back(-1);');
		// }


	}

	
	
}