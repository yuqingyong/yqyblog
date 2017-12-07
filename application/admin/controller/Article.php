<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\common\model\ArticleModel;
use think\controller;
use think\Db;
use think\request;
class Article extends Adminbase
{
    private $db;
    // 构造函数 实例化ArticleModel表
    public function __construct(){
        parent::__construct();
        $this->db = model('ArticleModel');
    }

    //文章列表
    public function article_list()
    {
        //读取已有的标签
        $tags = Db::name('tags')->field('tid,tname')->select();
        $res = $this->db->getPageData('all','all','all');
        return view('Article/article_list',['list'=>$res['list'],'page'=>$res['page'],'tags'=>$tags]);
    }

    //添加文章
    public function add_article(Request $request)
    {
        //读取文章分类
        $category = $this->db->getArticleCat();
        if($request->ispost())
        {
            $data = Request::instance()->post();
            $data['create_time'] = time();
            $result = $this->db->validate('ArticleValidate')->save($data);
            if(false === $result){
                // 验证失败 输出错误信息
                $this->error($this->db->getError());exit;
            }else{
                //如果添加成功则获取文章的封面图
                $ress = $this->db->add_article_pic();
                if($ress == true){$this->success('添加成功','Article/article_list');exit;}
            }
        }
        return view('Article/add_article',['category'=>$category]);
    }

    //修改文章
    public function edit_article(Request $request)
    {
        //查询详细信息
        $article = $this->db->getDataByAid();
        //读取文章分类
        $category = $this->db->getArticleCat();
        if($request->ispost())
        {
            $result = $this->db->edit_article();;
            if($result == true){$this->success('修改成功','Article/article_list');exit;}
        }
        return view('Article/edit_article',['category'=>$category,'info'=>$article]);
    }

    //设置文章的显示状态
    public function is_show()
    {
        $is_show = input('post.is_show');
        $ziduan  = ['is_show'=>$is_show];
        $res = $this->db->edit_ziduan(input('post.aid'),$ziduan);
        if($res){return json_encode(['ok'=>'y']);exit;}
    }

    //删除文章,设为已删除状态
    public function is_delete()
    {
        //$res = Article::where('aid',input('post.aid'))->update(['is_delete'=>1]);
        $ziduan  = ['is_delete'=>1];
        $res = $this->db->edit_ziduan(input('post.aid'),$ziduan);
        if($res){return json_encode(['ok'=>'y']);exit;}
    }

    //添加标签
    public function add_tag()
    {
        //获取到需要添加的标签
        $data = input('post.');
        //首先判断标签库中是否存在该文章的标签
        $a_tag = Db::name('article_tag')->where('aid',$data['aid'])->field('aid,tid')->select();
        if(!empty($a_tag)){
            //如果存在标签，则执行替换标签操作（将原有标签删除，增加新的标签）
            Db::name('article_tag')->where('aid',$data['aid'])->delete();
            foreach ($data['tid'] as $k => $v) {
                Db::name('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
            }
            $this->success('已替换标签成功');exit;
        }else{
            //如果不存在，则新增文章标签
            foreach ($data['tid'] as $k => $v) {
                Db::name('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
            }
            $this->success('已新增标签成功');exit;
        }
    }

    //文章回收站
    public function huishou()
    {
        //读取已经被标记为is_delete=1的文章
        $res = $this->db->getPageData('all','all','all','title,a.aid,b.path,create_time,author,sort,click,is_show,cid',1);
        return view('Article/huishou',['list'=>$res['list'],'page'=>$res['page']]);
    }

    //文章恢复
    public function a_huifu()
    {
    	$ziduan  = ['is_delete'=>0];
        $res = $this->db->edit_ziduan(input('post.aid'),$ziduan);
        if($res){return json_encode(['ok'=>'y']);exit;}
    }

    //彻底删除文章
    public function c_del()
    {
        if(model('article')->del_all() == true)
        {
            return json_encode(['ok'=>'y']);exit;
        }
    }



}
