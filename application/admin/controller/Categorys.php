<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\Category;
use think\controller;
use think\Db;
use think\request;
class Categorys extends Adminbase
{
	/***********************************标签管理*****************************************************/
    //标签列表
    public function tag_list()
    {
    	$Category = new Category();
    	$list = $Category->paginate(10);
    	$page = $list->render();
		return view('Category/tag_list',['list'=>$list,'page'=>$page]);
    }

    //修改标签
    public function edit_tag(Request $request){
    	$tag = Category::where('tid',input('tid'))->field('tname,tid')->find();
    	if($request->ispost()){
    		$data = input('post.');
    		$res  = Category::where('tid',$data['tid'])->field('tid,tname')->update($data);
    		if($res){$this->success('更新成功','Categorys/tag_list');exit;}
    	}
    	return view('Category/edit_tag',['tag'=>$tag]);
    }

    //增加标签
    public function add_tag(Request $request){
    	if($request->ispost()){
    		$data = input('post.');
    		$res  = Category::create($data);
    		if($res){$this->success('新增成功','Categorys/tag_list');exit;}
    	}
    	return view('Category/add_tag');
    }

    //删除标签
    public function del_tag(Request $request){
		$tid = input('tid');
		$res  = Category::destroy($tid);
		if($res){$this->success('删除成功');exit;}
    }


    /********************************分类管理***********************************/
    //分类列表
    public function category_list(){
    	$list = db('category')->order('cid desc')->field('cname,cid,descripiton')->select();
    	return view('Category/category_list',['list'=>$list]);
    }

    //添加分类
    public function add_category(Request $request){
    	if($request->ispost()){
    		$data = input('post.');
    		$res  = db('category')->insert($data);
    		if($res){$this->success('添加成功','Categorys/category_list');exit;}
    	}
    	return view('Category/add_category');
    }	

    //修改分类
    public function edit_category(Request $request){
    	$cid = input('cid');
    	$category = db('category')->where('cid',$cid)->field('cid,cname,descripiton,keywords')->find();
    	if($request->ispost()){
    		$data = input('post.');
    		$res  = db('category')->where('cid',input('post.cid'))->update($data);
    		if($res){$this->success('修改成功','Categorys/category_list');exit;}
    	}
    	return view('Category/edit_category',['category'=>$category]);
    }

    //删除分类
    public function del_category(){
    	$cid = input('cid');
    	$res = db('category')->where('cid',$cid)->delete();
    	if($res){$this->success('删除成功');exit;}
    }

}
