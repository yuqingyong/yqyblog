<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Category\tag_list.html";i:1516915804;}*/ ?>
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
    <div class="panel-head"><strong class="icon-reorder"> 标签列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li> <a class="button border-main icon-plus-square-o" href="<?php echo url('admin/Categorys/add_tag'); ?>"> 添加标签</a> </li>
      </ul>
    </div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>标签名</th>
        <th width="310">操作</th>
      </tr>
				<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['tid']; ?></td>
          <td width="10%"><?php echo $va['tname']; ?></td>
          <td>
          	<div class="button-group"> 
          		<a class="button border-main" title="编辑" href="<?php echo url('admin/Categorys/edit_tag',array('tid'=>$va['tid'])); ?>"><span class="icon-edit"></span>修改</a>
          		<a class="button border-main" href="<?php echo url('admin/Categorys/del_tag',array('tid'=>$va['tid'])); ?>"><span class="icon-edit"></span>删除</a>
          	</div>
          </td>
        </tr>
				<?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
  </div>
</body>
</html>