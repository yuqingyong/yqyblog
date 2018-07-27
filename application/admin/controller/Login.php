<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\admin\model\LoginModel;
use think\Loader;
use think\Session;
use think\Controller;
class Login extends controller
{
	public function _initialize(){
		$this->view->engine->layout(false);
	}

	/**
	 * 登录页面
	 * @param $data 接收提交的数据
	 */
    public function login()
    {
    	if($this->request->isPost()){
    		# 如果提交数据则处理登录验证
    		$data = $this->request->post();
    		# 验证数据
    		$validate = Loader::validate('Login');
    		if(!$validate->check($data)){
			    $this->error($validate->getError());
			}

			if(!captcha_check($data['code'])){
			  	# 验证失败
				$this->error('验证码错误，请重新输入');
			};

			# 通过数据和验证码之后，验证账号密码是否正确
			$login  = new LoginModel();
			$result = $login->check_login($data);
			if($result != false){
				$login->up_user_info($result);
				$this->redirect('Index/index');
			}

			$this->error('账号或者密码错误');

    	}
        return $this->fetch('Login/login');
    }

    // 修改密码   md5(md5(input('post.password')).$salt)
	public function edit_pass()
	{
		if($this->request->isPost()){
			$mpass   = $this->request->post('mpass');
			$newpass = $this->request->post('newpass');

			$login = new LoginModel();
			$res   = $login->edit_admin_user($mpass,$newpass);

			if($res){$this->success('修改成功');}else{$this->error('输入的原密码错误');}

		}
		return $this->fetch('Login/pass');
	}

	// 登出
	public function login_out()
	{
		Session::set('admin_user',null);
        $this->success('退出成功、前往登录页面', url('admin/Login/login'));
	}

}
