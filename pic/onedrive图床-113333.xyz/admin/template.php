<?php
// ========================== 文件说明 ==========================// 
// 本文件说明:软件管理
// 作者:小猪会气功
// =============================================================// 
error_reporting(7);
// 加载后台函数集合
require_once ("global.php");
cpheader();
if (!isset($_GET['action'])) {
	$_GET['action']="edit";
}
// 保存修改
if($_POST['action'] == "modfile")
{
	$filepath = "../templates/".$fname;
	$tname = str_replace(strrchr($fname, "/"), "", $fname);
	$rs=write_file($filepath,$_POST['content']);
	if ($rs) {
		redirect("修改成功!", "./template.php?action=edit&tname=".$tname); 
	} else {
		admin_exit("保存失败", "./template.php?action=edit&tname=".$tname); 
	}
}
// 修改页面
if($_GET['action'] == "mod")
{
	$fname = $_GET['fname'];
	if (!isset($fname) || empty($fname)) exit("缺少参数");
        if(strstr($fname,'../'))exit('Denied');
	$filepath = "../templates/".$fname;
	$fp = @fopen($filepath,"r") or exit('该文件不存在'); 
        $filesize = filesize($filepath);
        if ($filesize) {
                $content = fread($fp,$filesize);  
                $content = htmlspecialchars($content); 
        }
	fclose($fp); 
        $nav = "<a href=\"template.php?action=edit\"><strong>模板管理</strong></a> / ";
        $t=explode("/",trim($fname,"/"));
        for($i=0,$r=array(),$z='';($r[]=@$t[$i]),$z=@$t[$i];$i++) 
        {
                if(implode("/",$r)!==trim($fname,"/"))
                {
                        $nav .= "<a href=\"template.php?action=edit&tname=".implode("/",$r)."\"><strong>".$z."</strong></a> / ";                 } else {
                        $nav .= "<strong>".$z."</strong>";
                }
        }
	echo "<table width=\"95%\" border=\"0\" align=\"center\" cellspacing=\"0\" cellpadding=\"1\">\n";
	echo "  <tr><td height=30>";
        echo $nav;
        echo "</td></tr></table>\n";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
	$cpforms->formheader(array('title'=>"模板编辑",'name'=>"form"));
	$cpforms->maketextarea(array('text'=>"模板内容:<br><br><a href=javascript:HtmlEdit('content')><b>[HtmlEdit编辑器]</b></a>",
				     'cols'=>'100',
			             'rows'=>'30',
   	                             'name'=>'content',
                                     'extra'=>"name=\"content\"",
				     'value'=>$content));
	$cpforms->makehidden(array('name'=>'fname','value'=>$fname));
	$cpforms->makehidden(array('name'=>'action','value'=>'modfile'));
	$cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";
}//end mod

//模板列表页面
if($_GET['action'] == "edit")
{
        function e($s){$p=strrpos($s,'.');return substr($s,$p+1,strlen($s));}
        $tname=isset($_GET['tname'])?urldecode($_GET['tname']):'';
        if(strstr($tname,'../'))exit('你想干嘛？');
        $tname=$tname!==''&&$tname!=='/'?trim($tname,'/').'/':'';
        $nav = "<a href=\"template.php?action=edit\"><strong>模板管理</strong></a>";
	if($tname!=='')
	{
                $name = "文件列表";
                $t=explode("/",trim($tname,"/"));
        $nav. ($d!=='')?"<a href=\"$burl\"><strong>$name</strong></a> / ":"<strong>$name</strong> ";
        $nav .= " / ";
        for($i=0,$r=array(),$z='';($r[]=@$t[$i]),$z=@$t[$i];$i++) 
        {
                if(implode("/",$r)!==trim($tname,"/"))
                {
                        $nav .= "<a href=\"template.php?action=edit&tname=".implode("/",$r)."\"><strong>".$z."</strong></a> / ";                 } else {
                        $nav .= "<strong>".$z."</strong>";
                }
        }
	} else {
                $name = "模板列表";
        }
	echo "<table width=\"95%\" border=\"0\" align=\"center\" cellspacing=\"0\" cellpadding=\"1\">\n";
	echo "  <tr><td height=30>";
        echo $nav;
        echo "</td></tr></table>\n";
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>\n";
        $cpforms->tableheader(); 
	echo "<tr>\n";
        echo " <td class=\"tbhead\" colspan=\"4\">\n";
        echo " <b><font color=\"#F5D300\">".$name."</font></b>\n";
        echo " </td>\n";
        echo "</tr>\n";

	echo "<tr bgcolor=\"#999999\">\n";
	echo " <td align=\"center\" width=\"30%\"><b>名称</b></td>\n";
	echo " <td align=\"center\" width=\"16%\"><b>大小</b></td>\n";
	echo " <td align=\"center\" width=\"16%\"><b>类型</b></td>\n";
	echo " <td align=\"center\" width=\"22%\"><b>更新时间</b></td>\n";
	echo " <td align=\"center\" width=\"16%\"><b>操作</b></td>\n";
	echo "</tr>\n";
        $dirpath = "../templates/".$tname;
        $dirhandle = @opendir($dirpath);
        $F=array();$D=array();
        while(false !== ($file=@readdir($dirhandle))){
                if ($file[0] == '.' or $file[0] == '..') continue;
                if (is_dir($dirpath.$file)) {
                       $D[]=array('n'=>$file,
                                  'm'=>filemtime($dirpath.$file),
                                  's'=>get_real_size(dirsize($dirpath.$file)),
                                  't'=>'Directory');
                } else {
                        $F[]=array('n'=>$file,
                                   'm'=>filemtime($dirpath.$file),
                                   't'=>e($file),
                                   's'=>get_real_size(filesize($dirpath.$file)));
                }
        }
        @closedir($dirhandle);
        for($i=0,$c="";($c=@$D[$i++]);)
        {
                echo "<tr class=\"".getrowbg()."\">\n";
                echo "  <td><a href=\"template.php?action=edit&tname=".urlencode($tname.$c['n'])."\"><b>[".$c['n']."]</b></td>\n";
                echo "  <td align=\"center\">".$c['s']."</td>\n";
                echo "  <td align=\"center\">目录</td>\n";
                echo "  <td align=\"center\">".date("Y-m-j g:i:s",$c['m'])."</td>\n";
                echo "  <td align=\"center\"><a href=\"template.php?action=edit&tname=".urlencode($tname.$c['n'])."\">[打开]</a></td>\n";
                echo "</tr>\n";
        }
        for($i=0,$c='';($c=@$F[$i++]);)
        {
                echo "<tr class=\"".getrowbg()."\">\n";
                echo "  <td><a href=\"template.php?action=mod&fname=".$tname.$c['n']."\">".$c['n']."</td>\n";
                echo "  <td align=\"center\">".$c['s']."</td>\n";
                echo "  <td align=\"center\">".$c['t']."</td>\n";
                echo "  <td align=\"center\">".date("Y-m-j g:i:s",$c["m"])."</td>\n";
                echo "  <td align=center><a href=\"template.php?action=mod&fname=".$tname.$c['n']."\">[编辑]</a></td>\n";
                echo "</tr>\n";
        }
	$cpforms->tablefooter();
	echo "</td>\n</tr>\n</table>";
}//endedit
cpfooter();
?>  