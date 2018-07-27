<?php 
namespace app\common\model;
use think\Db;
use think\Model;

class Base extends Model{

	/**
     * 数据库字段 网页字段转换
     * #Date:
     * @param $array 转化数组
     * @return 返回数据数组
     */
    protected function buildParam($array=[])
    {
        $data=[];
        if (is_array($array)&&!empty($array)){
            foreach( $array as $item=>$value ){
                $data[$item] = $this->request->param($value);
            }
        }
        return $data;
    }



	// 查询分页数据
	public function getPage($table,$map='1=1',$order='',$limit=10,$field='*'){
		# 判断是否需要排序
		if(empty($order)){
			$list = Db::name($table)->where($map)->field($field)->paginate($limit);
		}else{
			$list = Db::name($table)->where($map)->field($field)->order($order)->paginate($limit);
		}

		# 分配变量
		$page = $list->render();

		$data = [
			'list'=>$list,
			'page'=>$page
		];

		# 返回数据
		return $data;

	}


	// 增加数据
	public function addData($data){
		# 去除键值首尾的空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        $id=$this->insertGetId($data);
        return $id;
	}


	// 修改数据
	public function editData($map,$data){
        # 去除键值首位空格
        foreach ($data as $k => $v) {
            $data[$k]=trim($v);
        }
        $result=$this->where($map)->update($data);
        return $result;
    }


    /**
     * 删除数据
     * @param   array   $map    where语句数组形式
     * @return  boolean         操作是否成功
     */
    public function deleteData($map){
        if (empty($map)) {
            die('条件为空的危险操作');
        }
        $result=$this->where($map)->delete();
        return $result;
    }













}










