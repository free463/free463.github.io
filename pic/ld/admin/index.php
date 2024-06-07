<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台框架
// =============================================================//
error_reporting(7);
// 加载后台函数集合
require "global.php";

if (!isset($_GET['action'])) {
    $_GET['action']="frames";
}

if($_GET['action'] == "logout")
{
	userlogout();
} //logout

if ($_GET['action']=="frames") {
?>
    <html>
    <head>
    <title>控制面版</title>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
    </head>
	<frameset rows="*" cols="150,*" framespacing="0" frameborder="NO" border="0">
	<frame src="index.php?action=menu" name="menuFrame" scrolling="AUTO" noresize marginwidth="0" border="no" frameborder="0">
	<frame src="index.php?action=main" name="mainFrame" noresize marginwidth="0" border="no" frameborder="0">
	</frameset>
    <noframes>
	<body bgcolor="#FFFFFF" text="#000000">
    </body>
	</noframes>
    </html>
<?php
}

if ($_GET['action']=="menu") 
{
?>
<html>
<head>
<META content="text/html; charset=gb2312" http-equiv="Content-Type">
<link href="./cp.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" id="navbody">
<table width="100%" border="0" cellspacing="4" cellpadding="1" align="center">
 <tr>
  <td align="center">
   <b><a href="../index.html" target="_blank">站点首页</a></b>
  </td>
 </tr>
 <tr>
  <td align="center">
   <b><a href="index.php?action=main" target="mainFrame">Control Panel</a></b>
  </td>
 </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="2" align="center">
<?php
makenav("系统设置",array("基本设置"=>"configurate.php",
			 "查看PHP信息"=>"configurate.php?action=phpinfo",
                            ));

makenav("文章管理",array("添加文章"=>"article.php?action=add",
			 "编辑文章"=>"article.php?action=edit",
			 "生成html"=>"article.php?action=make",
                            ));
makenav("分类管理",array("添加分类"=>"sort.php?action=add",
			 "编辑分类"=>"sort.php?action=edit",
                            ));
makenav("模板管理",array("编辑模板"=>"template.php?action=edit",
                            ));
makenav("数据库管理",array("备份数据库"=>"database.php?action=backup",
                           "优化数据库"=>"database.php?action=optimize",
                           "修复数据库"=>"database.php?action=repair",
                            ));

makenav("管理日志",array("操作记录"=>"log.php?action=admin",
                         "登陆记录"=>"log.php?action=login",
							));

makenav("管理员选项",array("添加管理员"=>"user.php?action=add",
			    "管理员列表"=>"user.php?action=edit",
			    "修改自己的密码"=>"user.php?action=modpass",
			    "退出登陆"=>"index.php?action=logout",
                            ));
echo "</table>\n";

?>
</body>
</html>
<?php
}
if ($_GET['action']=="main"){

	$serverinfo = PHP_OS.' / PHP v'.PHP_VERSION;
	$serverinfo .= @ini_get('safe_mode') ? ' / 安全模式' : NULL;

	if(@ini_get("file_uploads")) {
	$fileupload = "允许 - 文件 ".ini_get("upload_max_filesize")." - 表单：".ini_get("post_max_size");
	} else {
	$fileupload = "<font color=\"red\">禁止</font>";
	}
	
	if (get_cfg_var('register_globals')){
        $onoff="打开";
    }else{
        $onoff="关闭";
    }

	//获取数据库大小
	$article_datasize = 0;
    $tables = $DB->query("SHOW TABLE STATUS");
    while ($table = $DB->fetch_array($tables)) {
           $datasize += $table['Data_length'];
           $indexsize += $table['Index_length'];
           if ($table['Name']==$db_prefix."article") {
               $article_datasize += $table['Data_length']+$table['Index_length'];
           }
    }

	//查询数据信息
	$sorts="SELECT * FROM ".$db_prefix."sort";
    $sorttotal=$DB->num_rows($DB->query($sorts));
	$articles="SELECT * FROM ".$db_prefix."article";
    $arttotal=$DB->num_rows($DB->query($articles));
	$hiddenarticles="SELECT * FROM ".$db_prefix."article WHERE visible='0'";
    $hiddenarttotal=$DB->num_rows($DB->query($hiddenarticles));
cpheader()
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr>
<td align="center"><b>欢迎您 <?=$_SESSION[username];?>,　现在时间是: <?=date("Y-m-d　H:i:s","".time()."");?></b></td>
</tr>
</table>
<br>
<table cellpadding="1" cellspacing="0" border="0" align="center" width="90%" class="tblborder"><tr><td>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr class="tblhead"><td colspan="2"><b><span class="tblhead">系统信息</span></a></td></tr><tr class='firstalt' valign='top'>
<td width="50%"><p><b>服务器环境:</b></p></td>
<td width="50%"><p><?php echo "".$serverinfo.""; ?></p></td>
</tr>
<tr class='secondalt' valign='top'>
<td width="50%"><p><b>MySQL 版本:</b></p></td>
<td width="50%"><p><?php echo "".mysql_get_server_info().""; ?></p></td>
</tr>
<tr class='firstalt' valign='top'>
<td width="50%"><p><b>文件上传:</b></p></td>
<td width="50%"><p><?php echo "".$fileupload.""; ?></p></td>
</tr>
<tr class='secondalt' valign='top'>
<td width="50%"><p><b>register_globals:</b></p></td>
<td width="50%"><p><?php echo $onoff;?></p></td>
</tr>
</table></td></tr></table>
<br>
<table cellpadding="1" cellspacing="0" border="0" align="center" width="90%" class="tblborder"><tr><td>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr class="tblhead"><td colspan="2"><b><span class="tblhead">数据统计</span></a></td>
</tr>
<tr class='firstalt' valign='top'>
<td width="50%"><p><b>分类数量:</b></p></td>
<td width="50%"><p><?=$sorttotal?></p></td>
</tr>
<tr class='secondalt' valign='top'>
<td width="50%"><p><b>文章总数:</b></p></td>
<td width="50%"><p><?=$arttotal?></p></td>
</tr>
<tr class='firstalt' valign='top'>
<td width="50%"><p><b>隐藏文章数量:</b></p></td>
<td width="50%"><p><?=$hiddenarttotal?></p></td>
</tr>
<tr class='secondalt' valign='top'>
<td width="50%"><p><b>数据库大小:</b></p></td>
<td width="50%"><p><?php echo get_real_size($datasize+$indexsize);?></p></td>
</tr>
<tr class='firstalt' valign='top'>
<td width="50%"><p><b>文章数据大小:</b></p></td>
<td width="50%"><p><?php echo get_real_size($article_datasize);?></p></td>
</tr>
</table></td></tr></table>
<br>
<table cellpadding="1" cellspacing="0" border="0" align="center" width="90%" class="tblborder"><tr><td>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
<tr class="tblhead"><td colspan="2"><b><span class="tblhead">程序其它相关信息</span></a></td></tr><tr class='firstalt' valign='top'>
<td width="50%"><p><b>程序制作:</b></p></td>
<td width="50%"><p><a href="mailto:wupei@china.com.cn" target="_blank">小猪会气功</a></p></td>
</tr>
<tr class='secondalt' valign='top'>
<td width="50%"><p><b>官方主页:</b></p></td>
<td width="50%"><p><a href="http://www.ssfun.com" target="_blank">http://www.ssfun.com</a></p></td>
</tr>
<tr class='firstalt' valign='top'>
<td width="50%"><p><b>官方论坛:</b></p></td>
<td width="50%"><p><a href="http://www.guan8.net" target="_blank">http://www.guan8.net</a></p></td>
</tr>
</table></td></tr></table>
<?php
cpfooter()
?>
</body>
</html>
<?php
}
?>
