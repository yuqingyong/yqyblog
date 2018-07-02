<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Elses\img_list.html";i:1516915804;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="renderer" content="webkit">
<title></title>
<link rel="stylesheet" href="__PUBLIC__/admin/css/pintuer.css">
<link rel="stylesheet" href="__PUBLIC__/admin/css/admin.css">
<script src="__PUBLIC__/admin/js/jquery.js"></script>
<script src="__PUBLIC__/admin/js/pintuer.js"></script>
<script src="__PUBLIC__/admin/js/layer.js"></script>
</head>
<body>
  <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 广告图片列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li> <a class="button border-main icon-plus-square-o" href="<?php echo url('admin/Elses/add_img'); ?>"> 添加广告图片</a> </li>
      </ul>
    </div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>图片名</th>
        <th>来源</th>
        <th>url</th>
        <th>开始时间</th>
        <th>结束时间</th>
        <th>是否展示</th>
        <th width="310">操作</th>
      </tr>
				<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['mid']; ?></td>
          <td width="10%"><?php echo $va['mname']; ?></td>
          <td width="10%"><?php echo $va['from']; ?></td>
          <td width="10%"><?php echo $va['url']; ?></td>
          <td width="10%"><?php echo $va['create_time']; ?></td>
          <td width="10%"><?php echo date('Y-m-d',$va['end_time']); ?></td>
          <?php if($va['is_show'] == 1): ?>
            <td width="10%">是</td>
          <?php else: ?>
            <td width="10%">否</td>
          <?php endif; ?>
          <td>
          	<div class="button-group"> 
              <?php if($va['is_show'] == 0): ?>
              <a class="button border-main" href="javascript:void(0)" onclick="return is_show(<?php echo $va['mid']; ?>,1)"><span class="icon-edit"></span> 启用</a>
              <?php else: ?>
              <a class="button border-main" href="javascript:void(0)" onclick="return is_show(<?php echo $va['mid']; ?>,0)"><span class="icon-edit"></span> 禁用</a>
              <?php endif; ?>
          		<a class="button border-main" title="编辑" href="<?php echo url('admin/Elses/edit_img',array('mid'=>$va['mid'])); ?>"><span class="icon-edit"></span>修改</a>
          		<a class="button border-main" onclick="return del(<?php echo $va['mid']; ?>)"><span class="icon-edit"></span>删除</a>
          	</div>
          </td>
        </tr>
				<?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
    <div class="page">
      <?php echo $page; ?>
    </div>
  </div>
</body>
<script>
  //删除
function del(mid){
  //询问框
  layer.confirm('您确定要删除该笔记吗?', {
    btn: ['确定','取消'] //按钮
  }, function(){
      $.ajax({
      type:"post",
      url:"<?php echo url('admin/Elses/del_img'); ?>",
      data:{'mid':mid},
      success:function(msg){
        var _json = JSON.parse(msg);
        if(_json.ok == 'y'){
          layer.msg('已删除');
          location.reload();
        }else{
          alert('删除失败');location.reload();
        }
      }
    });
  }, function(){
     layer.msg('已取消');
  });

}



//是否显示
function is_show(mid,is_show)
{

  layer.confirm('您确定要修改状态?', {
    btn: ['确定','取消'] //按钮
  }, function(){
      $.ajax({
      type:"post",
      url:"<?php echo url('admin/Elses/is_show'); ?>",
      data:{'mid':mid,'is_show':is_show},
      success:function(msg){
        var _json = JSON.parse(msg);
        if(_json.ok == 'y'){
          location.reload();
        }else{
          alert('设置失败');location.reload();
        }
      }
    });
  }, function(){
     layer.msg('已取消');
  });
  
  
}
</script>
</html>