<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use app\admin\model\Link;
use app\admin\model\Images;
use think\controller;
use think\Db;
use think\request;
class Elses extends AdminBase
{
	//友情链接列表
    public function friend_url()
    {
    	$list = Link::where(true)->order('lid desc')->field('lanem,lid,is_show,sort,url')->paginate(10);
    	$page = $list->render();
		return view('Elses/friend_url',['list'=>$list,'page'=>$page]);
    }

    //添加友情链接
    public function add_friend_url(Request $request)
    {
    	if($request->ispost())
    	{
    		$data = $this->request->post();
    		$res = Db::name('link')->insert($data);
    		if($res){$this->success('添加成功','Elses/friend_url');exit;}
    	}
    	return view('Elses/add_friend_url');
    }

    //修改友情链接
    public function edit_friend_url(Request $request)
    {
    	$link= Db::name('link')->where('lid',input('lid'))->find();
    	if($request->ispost())
    	{
    		$data = $this->request->post();
    		$res = Db::name('link')->where('lid',input('post.lid'))->update($data);
    		if($res){$this->success('修改成功','Elses/friend_url');exit;}
    	}
    	return view('Elses/edit_friend_url',['link'=>$link]);
    }

    //删除链接
    public function del_friend_url()
    {
    	$res = Db::name('link')->where('lid',input('post.lid'))->delete();
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    }


    /*****************广告图片管理********************/
    public function img_list()
    {
        //查询图片广告列表
        $img_list = Images::where(true)->order('mid desc')->field('mname,mid,is_show,create_time,end_time,url,img,from')->paginate(5);
        $page = $img_list->render();
        return view('Elses/img_list',['list'=>$img_list,'page'=>$page]);
    }


    //添加图片
    public function add_img(Request $reqeust)
    {
        if($reqeust->ispost()){
            $data = $this->request->post();
            $data['create_time'] = strtotime(input('post.create_time'));
            $data['end_time']    = strtotime(input('post.end_time'));
            //图片上传
            $file = request()->file('img');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $data['img'] = '/uploads/'.$info->getSaveName();

            $Images = new Images();
            $result = $Images->validate('ImageValidate')->save($data);
            if(false === $result){
                // 验证失败 输出错误信息
                $this->error($Images->getError());exit;
            }else{
                $this->success('添加成功','Elses/img_list');exit;
            }
        }
        return view('Elses/add_img');
    }


    //修改图片
    public function edit_img(Request $reqeust)
    {
        $img = Db::name('advert')->where('mid',input('mid'))->find();
        if($reqeust->ispost()){
            $data = $this->request->post();
            $data['create_time'] = strtotime(input('post.create_time'));
            $data['end_time']    = strtotime(input('post.end_time'));
            //图片上传
            $file = request()->file('img');
            if($file != null){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data['img'] = '/uploads/'.$info->getSaveName();
                $res = Images::where('mid',input('post.mid'))->field('img,mname,mid,create_time,end_time,from,url')->update($data);
                if($res > 0){$this->success('更新成功');exit;}
            }else{
                $res = Images::where('mid',input('post.mid'))->field('mname,mid,create_time,end_time,from,url')->update($data);
                if($res > 0){$this->success('更新成功');exit;}
            }
        }
        return view('Elses/edit_img',['img'=>$img]);
    }


    //设置图片的状态
    public function is_show()
    {
        $is_show = $this->request->post('is_show');
        $res = Images::where('mid',input('post.mid'))->update(['is_show'=>$is_show]);
        if($res){echo json_encode(['ok'=>'y']);exit;}
    }

    //删除图片
    public function del_img()
    {
        $res = Images::where('mid',input('post.mid'))->delete();
        if($res){echo json_encode(['ok'=>'y']);exit;}
    }









}
