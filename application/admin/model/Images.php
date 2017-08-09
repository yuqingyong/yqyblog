<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 标签模型
 */
class Images extends Model{
	protected $table = 'yqy_advert';

	//获取图片列表
	public function get_images_list($map,$field,$limit = 5)
	{
		$img_list = db('advert')->where($map)->order('mid desc')->field($field)->paginate($limit);
        $page = $img_list->render();
        $data = ['list'=>$img_list,'page'=>$page];
        return $data;
	}

	//添加图片
	public function edit_images()
	{
		$data = input('post.');
        $data['create_time'] = strtotime(input('post.create_time'));
        $data['end_time']    = strtotime(input('post.end_time'));
        //图片上传
        $file = request()->file('img');
        if($file != null){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $data['img'] = '/uploads/'.$info->getSaveName();
            $res = db('advert')->where('mid',input('post.mid'))->field('img,mname,mid,create_time,end_time,from,url')->update($data);
            if($res > 0){return true;}
        }else{
            $res = db('advert')->where('mid',input('post.mid'))->field('mname,mid,create_time,end_time,from,url')->update($data);
            if($res > 0){return true;}
        }
	}

}