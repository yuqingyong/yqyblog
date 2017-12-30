<?php
namespace app\admin\model;
use think\Db;
use think\Model;
use think\Session;
/**	
 * 管理员模型
 */
class LoginModel extends Model{
	#设置数据表
	protected $table = 'yqy_admin_user';

	//管理员账号验证
	public function check_login($data){
		$data['password'] = md5(md5($data['password']).'yqy');
		$resu = $this->where(['username'=>$data['username'],'password'=>$data['password']])->field('status,id,username,password')->find();
		if($resu['status'] == 1){return $resu;}else{return false;}
	}

	//管理员信息保存和更新
	public function up_user_info($result){
		Session::set('admin_user',$result);
		#更新用户的最新登录时间和IP
		$gx['last_login_ip']   = get_real_ip();
		$gx['last_login_time'] = time();
		$this->where('id',$result['id'])->field('last_login_ip,last_login_time')->update($gx);
		return true;
	}

	//管理员信息更改
	public function edit_admin_user($mpass,$newpass)
	{
		if(md5(md5($mpass).'yqy') == session('admin_user.password')) {
			$data['password'] = md5(md5($newpass).'yqy');
			$res = $this->where('id',session('admin_user.id'))->field('password')->update($data);
			if($res){return true;}
		}else{
			return false;
		}

	}



}