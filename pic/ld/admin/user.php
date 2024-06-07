<?php
// ========================== 文件说明 ==========================// 
// 本文件说明：用户管理
// =============================================================// 
error_reporting(7);
// 加载后台函数集合
require_once ("global.php");

// 添加管理员操作
if ($_POST['action']=="adduser"){
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);
	// 验证逻辑性
    if (trim($username)=="")
	{
        sa_exit("用户名不能为空","javascript:history.go(-1);");
    }
	if(strlen($username)>16)
	{
		sa_exit("用户名不能超过16个字符","javascript:history.go(-1);");
	}
	if(eregi("[<>{}(),%#|^&!`$]",$username))
	{
		sa_exit("用户名只能用a-z,0-9和'_'线组成","javascript:history.go(-1);");
	}
    if (trim($password)=="")
	{
        sa_exit("密码不能为空","javascript:history.go(-1);");
    }
	if (strlen($password) < 6)
	{
		sa_exit("密码长度不能少于6字节","javascript:history.go(-1);");
	}
	if(trim($password) != trim($password2))
	{
		sa_exit("两次密码输入不一致","javascript:history.go(-1);");
	}

    $checkuser = $DB->fetch_one_array("SELECT username FROM ".$db_prefix."user WHERE  username='".addslashes($user_name)."'");

    if ($checkuser) {
       if ($username==$checkuser['username']) {
           sa_exit("用户已存在,请使用其它用户名","javascript:history.go(-1);");
       }
    }
	// 验证逻辑性结束
    $DB->query("INSERT INTO ".$db_prefix."user (username,password) VALUES ('".addslashes($username)."','".md5($password)."')");
    redirect("管理员已添加", "./user.php?action=add");
}// 添加结束

// 修改密码操作
if($_POST['action'] == "modpassword")
{
	// 验证逻辑性
	if (trim($newpassword)=="")
	{
        sa_exit("密码不能为空","javascript:history.go(-1);");
    }
	if (strlen($newpassword) < 6)
	{
		sa_exit("密码长度不能少于6字节","javascript:history.go(-1);");
	}
	if(trim($newpassword) != trim($comfirpassword))
	{
		sa_exit("两次密码输入不一致","javascript:history.go(-1);");
	}
	
	$user = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid='".intval($userid)."'");
    if (empty($user)) {
        sa_exit("该用户不存在","javascript:history.go(-1);");
    }
    if (md5($_POST['oldpassword'])!=$user['password']) {
        sa_exit("密码无效","javascript:history.go(-1);");
    }
	// 验证逻辑性结束
	$query="UPDATE ".$db_prefix."user SET password='".addslashes(md5($_POST['newpassword']))."' WHERE  userid='".intval($user['userid'])."'";
    $DB->query($query) or die("修改密码失败");
    redirect("修改密码成功!", "./index.php?action=main");
}// 修改密码结束

// 删除用户操作
if($_POST['action'] == "deluser")
{
	$query="DELETE FROM ".$db_prefix."user WHERE userid='".intval($userid)."'";
    $DB->query($query) or die("删除用户失败!");
    redirect("删除用户成功!", "./user.php?action=edit");
}// 删除用户结束

cpheader();

// 添加管理员页面
if ($_GET['action']==add){

	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
    $cpforms->formheader(array('title'=>'添加管理员'));

    $cpforms->makehidden(array('name'=>'action',
                          'value'=>'adduser'));

    $cpforms->makeinput(array('text'=>'用户名:',
                               'name'=>'username',
                               ));

    $cpforms->makeinput(array('text'=>'密码:',
                               'type'=>'password',
                               'name'=>'password'));

    $cpforms->makeinput(array('text'=>'确认密码:',
                               'type'=>'password',
                               'name'=>'password2'));

    $cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";

}//end add


// 管理员列表页面
if ($_GET['action']=="edit") {

	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>\n";

    $cpforms->tableheader();
	echo "<tr>\n";
    echo " <td class=\"tbhead\" colspan=\"2\">\n";
    echo " <b><font color=\"#F5D300\">管理员列表</font></b>\n";
    echo " </td>\n";
    echo "</tr>\n";
	echo "<tr bgcolor=\"#999999\">\n";
	echo " <td><b>用户名</b></td>\n";
	echo " <td align=\"center\" width=\"14%\"><b>管理操作</b></td>\n";
	echo "</tr>\n";


	$query="SELECT * FROM ".$db_prefix."user";
    $total=$DB->num_rows($DB->query($query));

    $users = $DB->query("SELECT * FROM ".$db_prefix."user ORDER BY userid DESC");
    while ($user = $DB->fetch_array($users)) {
		echo "<tr class=\"".getrowbg()."\">";
		echo "<td>".htmlspecialchars($user['username'])."</td>\n";
		echo "<td align=\"center\">";
		echo "<a href=\"user.php?action=del&userid=$user[userid]\">删除</a></td>\n";
        echo "</tr>\n";
    }
	echo "<tr class=".getrowbg().">";
	echo "<td colspan=\"2\"><table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">";
	echo "<tr>";
	echo "<td>共有 <font color=\"#000000\"><b>".$total."</b></font> 个管理员</td>\n";
	echo "</tr></table>\n";
	echo "</td></tr>\n";
    $cpforms->tablefooter();
	echo "</td>\n</tr>\n</table>";
} //end edit

// 修改密码页面
if ($_GET['action']=="modpass"){
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
	$user = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE username='".$_SESSION['username']."'");
    if (empty($user))
	{
		sa_exit("该管理员不存在","javascript:history.go(-1);");
	}
    $cpforms->formheader(array('title'=>'修改密码'));
    $cpforms->makehidden(array('name'=>'action',
                                'value'=>'modpassword'));
	$cpforms->makehidden(array('name'=>'userid',
							   'value'=>intval($user['userid'])));
    $cpforms->makeinput(array('text'=>'旧密码:',
                               'name'=>'oldpassword',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'新密码:',
                               'name'=>'newpassword',
                               'type'=>'password'));
    $cpforms->makeinput(array('text'=>'确认新密码:',
                               'name'=>'comfirpassword',
                               'type'=>'password'));
    $cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";
}//end modpass

// 删除用户页面
if($_GET['action'] == "del")
{
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
	$user = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE userid='$userid'");
    if (empty($user))
	{
		sa_exit("该管理员不存在","javascript:history.go(-1);");
	}
	$cpforms->formheader(array('title'=>'删除用户',
							   'colspan'=>'1'));
	
    echo "<tr align=\"center\" class=\"firstalt\">\n";
    echo "  <td width=\"50%\">用户名：".htmlspecialchars($user['username'])."</td>\n";
	echo "</tr>\n";
	echo "<tr align=\"center\" class=\"secondalt\">\n";
	echo "  <td colspan=\"2\"><font color=\"#FF0000\">注意：删除此用户，确定吗？</font></td>\n";
    echo "</tr>\n";
	
	$cpforms->makehidden(array('name'=>'userid','value'=>intval($user['userid'])));
    $cpforms->makehidden(array('name'=>'action',
                               'value'=>'deluser'));
    $cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";
}//del

cpfooter();
?>
