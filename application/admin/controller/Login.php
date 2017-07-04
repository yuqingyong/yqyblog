<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\AdminUser;
use think\Request;
use think\Session;
use think\Cookie;
class Login extends Adminbase{
	public function _initialize()
	{
		$this->view->engine->layout(false);
	}

	public function login(){
		if(request()->ispost()){
			$data = input('post.');
			$remember = input('post.remember');
			if(!captcha_check($data['code'])){
				$this->error('验证码错误');
			}else{
				$AdminUser = new AdminUser;
				$res = $AdminUser->check_login($data);
				if($res != false){
					Session::set('admin_user',$res);
					//更新登录时间和IP
					$gx['last_login_ip']   = get_real_ip();
					$gx['last_login_time'] = time();
					$AdminUser->save([
						'last_login_time' => $gx['last_login_time'],
						'last_login_ip'   => $gx['last_login_ip'],
					],['id'=>$res['id']]);
					$this->success('登录成功', 'Index/index');
				}
			}
			
		}
		return view('Login/login');
	}



	//登出
	public function login_out()
	{
		Session::set('admin_user',null);
        $this->success('退出成功、前往登录页面', url('admin/Login/login'));
	}



	//修改密码   md5(md5(input('post.password')).$salt)
	public function edit_pass(Request $request)
	{
		if($request->ispost()){
			if(md5(md5(input('post.mpass')).'yqy') == session('admin_user.password')) {
				$data['password'] = md5(md5(input('post.newpass')).'yqy');
				//$data['salt'] = input('post.salt');
				$res = db('admin_user')->where('id',session('admin_user.id'))->field('password')->update($data);
				if($res){$this->success('修改成功');exit;}
			}else{
				$this->error('原密码输入错误');exit;
			}
		}
		return view('Login/pass');
	}














}
