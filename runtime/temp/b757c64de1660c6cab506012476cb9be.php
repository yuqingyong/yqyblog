<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Elses\friend_url.html";i:1523149060;}*/ ?>
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
    <div class="panel-head"><strong class="icon-reorder"> 友情链接列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li> <a class="button border-main icon-plus-square-o" href="<?php echo url('admin/Elses/add_friend_url'); ?>"> 添加友情链接</a> </li>
      </ul>
    </div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>友情链接名</th>
        <th>url</th>
        <th>排序</th>
        <th width="310">操作</th>
      </tr>
				<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['lid']; ?></td>
          <td width="10%"><?php echo $va['lname']; ?></td>
          <td width="10%"><?php echo $va['url']; ?></td>
          <td width="10%"><?php echo $va['sort']; ?></td>
          <td>
          	<div class="button-group"> 
          		<a class="button border-main" title="编辑" href="<?php echo url('admin/Elses/edit_friend_url',array('lid'=>$va['lid'])); ?>"><span class="icon-edit"></span>修改</a>
          		<a class="button border-main" onclick="return del(<?php echo $va['lid']; ?>)"><span class="icon-edit"></span>删除</a>
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
function del(lid){
  //询问框
  layer.confirm('您确定要删除该笔记吗?', {
    btn: ['确定','取消'] //按钮
  }, function(){
      $.ajax({
      type:"post",
      url:"<?php echo url('admin/Elses/del_friend_url'); ?>",
      data:{'lid':lid},
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

</script>
</html>