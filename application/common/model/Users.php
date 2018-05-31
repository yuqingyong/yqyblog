<?php
namespace app\common\model;
use think\Db;
use think\Model;
/**	
 * 用户表模型
 */
class Users extends Model{
	//读取会员列表
	public function get_user_page($type='all',$status='all',$limit=10,$kw='')
	{
		//判断查询类型
		if($type == 'all' && $kw == ''){
			if($status == 'all'){
				$where = true;
			}else{
				$where = ['status'=>$stauts];
			}
			$list = Db::name('users')->where($where)->paginate($limit);
		}elseif(!empty($kw)){
			//$where = ['nickname',['like',"%".$kw."%"]];
			$list = Db::name('users')->where(['nickname'  =>  ['like',"%".$kw."%"]])->paginate($limit);
		}else{
			$where = ['type'=>$type];
			$list = Db::name('users')->where($where)->paginate($limit);
		}

		//分配变量
		$page = $list->render();
		$data = ['list'=>$list,'page'=>$page];
		return $data;
	}


	//验证密码
	public function check_password($username = '' , $password = '')
	{
		//查询是否存在该用户 用户状态是否为启用状态 1
		$res = DB::name('users')->where(['username'=>$username,'password'=>$password])->field('type,status,uid,username')->find();
		if($res > 0){
			//如果验证通过则更新用户的登录IP和时间
			$data['last_login_time'] = time();
			$data['last_login_ip']   = get_real_ip();
			$result = Db::name('users')->where('uid',$res['uid'])->field('last_login_time,last_login_ip')->update($data);
			//登录次数自增1
			Db::name('users')->where('uid',$res['uid'])->setInc('login_times');
			return $res;
		}
		return false;
	}


	//用户注册
	public function user_register($data)
	{
		$da['password'] = md5($data['password']);
		$da['username'] = $data['username'];
		$da['email'] = $data['email'];
		$res = $this->insert($da);
		return $res;
	}










}