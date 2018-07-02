<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Request;
use think\Db;
class Config extends AdminBase
{
	//所有配置
    public function config_list()
    {
        # 读取配置
        $config = Db::name('config')->field('id,config_name,config,status,type')->order('id desc')->select();

        $assign = [
            'config' => $config
        ];
        return $this->fetch('Config/config',$assign);
    }

    //添加配置
    public function add_config(Request $request)
    {
        if($request->isAjax()){
            # 提交类型
            $type = $this->request->post('type');
            $config_name = $this->request->post('config_name');
            $data['appid'] = $this->request->post('appid');
            $data['appsecret'] = $this->request->post('appsecret');
            if($type == 1){
                # 支付宝参数添加
                $data['mchid'] = $this->request->post('mchid');
                $data['mchkey'] = $this->request->post('mchkey');

                if($data['appid'] == '' || $data['appsecret'] == '' || $data['mchid'] == '' || $data['mchkey'] == '') return json_encode(['status'=>0,'msg'=>'参数不完整']);
            }elseif ($type == 2) {
                # 微信参数添加
                $data['mchid'] = $this->request->post('mchid');
                $data['mchkey'] = $this->request->post('mchkey');

                if($data['appid'] == '' || $data['appsecret'] == '' || $data['mchid'] == '' || $data['mchkey'] == '') return json_encode(['status'=>0,'msg'=>'参数不完整']);
            }elseif ($type == 3) {
                # 短信参数添加
                $data['smsid'] = $this->request->post('smsid');
                $data['sign'] = $this->request->post('sign');

                if($data['appid'] == '' || $data['appsecret'] == '' || $data['smsid'] == '' || $data['sign'] == '') return json_encode(['status'=>0,'msg'=>'参数不完整']);
            }
            # 添加json数据
            $config = json_encode($data);
            $res = Db::name('config')->insert(['type'=>$type,'config_name'=>$config_name,'config'=>$config]);
            if($res){return json_encode(['status'=>1,'msg'=>'添加成功']);}else{return json_encode(['status'=>0,'msg'=>'添加失败']);}
        }

        return $this->fetch('Config/add_config');
    }

    // 修改配置
    public function edit_config(Request $request)
    {
        $id = $this->request->param('id');
        $config = Db::name('config')->where('id',$id)->field('config_name,id,config,type')->find();
        $config['config'] = json_decode($config['config'],true);

        if($request->isPost()){
            //$type = $this->request->post('type');
            $data['appid'] = $this->request->post('appid');
            $data['appsecret'] = $this->request->post('appsecret');
            if($config['type'] == 1){
                # 支付宝参数修改
                $data['mchid'] = $this->request->post('mchid');
                $data['mchkey'] = $this->request->post('mchkey');
                $config_name = $this->request->post('config_name');
                $id = $this->request->post('id');
                $config = json_encode($data);
                $res = Db::name('config')->where('id',$id)->update(['config_name'=>$config_name,'config'=>$config]);
            }elseif ($config['type'] == 2) {
                # 微信配置修改
            }
        }

        return $this->fetch('Config/edit_config',['config'=>$config]);
    }

    //是否启用
    public function is_show(Request $request)
    {
        if($request->isPost()){
            $status = $this->request->post('is_show');
            $id = $this->request->post('id');
            $res = Db::name('config')->where('id',$id)->update(['status'=>$status]);
            if($res){
                return json_encode(['status'=>1,'msg'=>'修改成功']);
            }
        }
    }

    // 删除配置
    public function del_config(Request $request)
    {
        if($request->isPost()){
            $id = $this->request->post('id');
            $res = Db::name('config')->where('id',$id)->delete();
            if($res){
                return json_encode(['status'=>1,'msg'=>'删除成功']);
            }
        }
    }

}
