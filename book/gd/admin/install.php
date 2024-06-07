<?php
// ========================== 文件说明 ==========================// 
// 本文件说明：程序自动安装脚本
// -------------------------------------------------------------// 
// 小猪会气功
// =============================================================// 
error_reporting(7);

// 允许程序在 register_globals = off 的环境下工作
if ( function_exists('ini_get') ) {
	$onoff = ini_get('register_globals');
} else {
	$onoff = get_cfg_var('register_globals');
}
if ($onoff != 1) {
	@extract($_POST, EXTR_SKIP);
	@extract($_GET, EXTR_SKIP);
}

// 去除转义字符
function stripslashes_array($array) {
	while (list($k,$v) = each($array)) {
		if (is_string($v)) {
			$array[$k] = stripslashes($v);
		} else if (is_array($v))  {
			$array[$k] = stripslashes_array($v);
		}
	}
	return $array;
}

// 判断 magic_quotes_gpc 状态
if (get_magic_quotes_gpc()) {
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

set_magic_quotes_runtime(0);

// 返回上一页函数
function sa_exit($msg) {
	global $step;
	echo "<p>$msg</p>";
	echo "<p><a href=\"javascript:history.go(-1);\">返回上一页</a></p>";
	cpfooter();
	exit;
}

function cpheader(){
?>
<HTML>
<HEAD>
<TITLE>Sarticle安装脚本</TITLE>
<META http-equiv=Content-Type content="text/html; charset=gb2312">
<STYLE type=text/css>
BODY {BACKGROUND-COLOR: #EEEEEE; COLOR: #3F3849; line-height: 18px;	font-family: "Verdana", "Tahoma", "宋体";	FONT-SIZE: 12px;}
TD{FONT-FAMILY: "Verdana", "Tahoma", "宋体"; FONT-SIZE: 12px; line-height: 18px;}
A:active {COLOR: #000000 ;}
A:visited {COLOR: #000000 ;text-decoration: none}
A:hover {COLOR: #3D4F7A;text-decoration: underline}
A:link {COLOR: #000000;text-decoration: none}
</STYLE>
</HEAD>
<body leftmargin="0" topmargin="20" marginwidth="0" marginheight="0" style="table-layout:fixed; word-break:break-all">
<TABLE width=760 border=0 align=center cellPadding=5 cellSpacing=5>
  <TBODY>
    <TR> 
      <TD width="100%"><div align="center"><b>Sarticle安装脚本</b></div>
        <hr noshade></TD>
    </TR>
    <TR>
      <TD>
<?
}

function cpfooter(){
?>
</TD>
  </TR>
    <TR>
      <TD align=right>
      <hr align="center" noshade>
      <center>
        <b>Copyright &copy; 2004 Security Angel Team[S4T] All Rights Reserved.</b>
      </center></TD>
    </TR>
  </TBODY>
</TABLE>
</BODY></HTML>
<?php
}

cpheader();

if (empty($step)) {
	$step = "1";
    require "config.php";
	echo "<p><b>设置数据库</b></p>\n";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>\n";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>\n";
    echo "<p>服务器地址: <input type=\"text\" value=\"$servername\" name=\"servername\"></p>\n";
    echo "<p>数据库名: <input type=\"text\" value=\"$dbname\" name=\"dbname\"></p>\n";
    echo "<p>数据库用户名: <input type=\"text\" value=\"$dbusername\" name=\"dbusername\"></p>\n";
    echo "<p>数据库用户密码: <input type=\"password\" value=\"\" name=\"dbpassword\"></p>\n";
    echo "<p>数据表前缀: <input type=\"text\" value=\"$db_prefix\" name=\"db_prefix\"></p>\n";
	echo "<p>删除日志的密匙: <input type=\"text\" value=\"\" name=\"dellog_pass\"></p>\n";
	echo "<p>要删除后台的管理记录以及登陆记录，必须输入此密匙.</p>";
    echo "<p><input type=\"submit\" name=\"next\" value=\"下一步\"></p>\n";
}

// step three
if($step==2){

   if(trim($dbname)=="" or trim($servername)=="" or trim($dbusername)=="" or trim($dellog_pass=="")){
      sa_exit("请返回并确认所有选项均已正确填写");
   }
   $file = "./config.php";

   if (file_exists($file)){
      @chmod ($file, 0777);
   }

   $fp = fopen($file,w);
   $filecontent = "<?php

/********** 数据库主机名或IP **********/
\$servername = '$servername';

/********** 数据库用户名 **********/
\$dbusername = '$dbusername';

/********** 数据库密码 **********/
\$dbpassword = '$dbpassword';

/********** 数据库连接方式 **********/
\$usepconnect = '1';

/********** 数据库名 **********/
\$dbname = '$dbname';

/********** 数据表前缀 **********/
\$db_prefix = '$db_prefix';

/********** 删除日志的密匙 **********/
\$dellog_pass = '$dellog_pass';

?>";
   fwrite($fp,$filecontent,strlen($filecontent));
   fclose($fp);


   $link = mysql_connect($servername,$dbusername,$dbpassword);
   if ($link) {
       echo "<p>数据库服务器连接成功</p>";
       if (mysql_select_db($dbname)) {
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">下一步(删除已存在的表)</a></p>";
           echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">下一步(不删除已存在的表)</a></p>";
       } else {
           echo "<p>正尝试创建数据库 $dbname</p>";

           if (mysql_create_db($dbname)) {
               echo "<p>数据库创建成功</p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=1\">下一步(删除已存在的表)</a></p>";
               echo "<p><a href=\"./install.php?step=".($step+1)."&delete_existing=0\">下一步(不删除已存在的表)</a></p>";
           } else {
               echo "<p>数据库创建失败</p>";
               echo "<p><a href=\"./install.php?step=".($step-1)."\">返回上一步</a></p>";
           }
       }

   } else {
       echo "<p>数据库服务器连接失败</p>";
   }
   mysql_close($link);
}




if($step>=3){

   require "config.php";
   require "class/mysql.php";

   $DB = new DB_MySQL;

   $DB->servername=$servername;
   $DB->dbname=$dbname;
   $DB->dbusername=$dbusername;
   $DB->dbpassword=$dbpassword;

   $DB->connect();
   $DB->selectdb();

}

if($step==3){

   $mysql_data = "

DROP TABLE IF EXISTS `".$db_prefix."adminlog`;
CREATE TABLE `".$db_prefix."adminlog` (
  `adminlogid` int(15) NOT NULL auto_increment,
  `action` varchar(50) NOT NULL default '',
  `script` varchar(255) NOT NULL default '',
  `date` varchar(10) NOT NULL default '',
  `ipaddress` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`adminlogid`)
) ;


DROP TABLE IF EXISTS `".$db_prefix."article`;
CREATE TABLE `".$db_prefix."article` (
  `articleid` int(15) NOT NULL auto_increment,
  `pid` int(11) NOT NULL default '0',
  `sortid` int(11) NOT NULL default '0',
  `title` varchar(120) NOT NULL default '',
  `author` varchar(20) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `source` varchar(100) NOT NULL default '',
  `addtime` varchar(10) NOT NULL default '',
  `content` text NOT NULL,
  `comment` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `iscommend` int(11) NOT NULL default '0',
  `isparseurl` int(11) NOT NULL default '1',
  `ishtml` int(11) NOT NULL default '0',
  `visible` int(11) NOT NULL default '1',
  PRIMARY KEY  (`articleid`)
) ;

DROP TABLE IF EXISTS `".$db_prefix."loginlog`;
CREATE TABLE `".$db_prefix."loginlog` (
  `loginlogid` int(15) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `date` varchar(10) NOT NULL default '',
  `ipaddress` varchar(16) NOT NULL default '',
  `result` int(11) NOT NULL default '0',
  PRIMARY KEY  (`loginlogid`)
) ;

DROP TABLE IF EXISTS `".$db_prefix."setting`;
CREATE TABLE `".$db_prefix."setting` (
  `settingid` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  `type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`settingid`)
) ;

INSERT INTO `".$db_prefix."setting` VALUES (1, '文章系统名称', '将在所有页面标题上显示该名称', 'title', '文章中心', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (2, '文章系统地址', '文章系统在网络上的地址', 'url', 'http://www.ssfun.com', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (3, '前台模板', '', 'template', 'default', 'templates');
INSERT INTO `".$db_prefix."setting` VALUES (4, '每页显示多少条文章', '', 'articlenum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (5, '文章分多少列显示', '', 'colnum', '2', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (6, '每页显示多少条评论', '', 'commentnum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (7, '搜索引擎显示多少个结果', '', 'searchnum', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (8, '是否开放评论功能', '', 'iscomment', '1', 'yesno');
INSERT INTO `".$db_prefix."setting` VALUES (9, '提交评论时间间隔', '可以防止他人灌水,单位秒,0为不限制', 'post_time', '20', 'integer');
INSERT INTO `".$db_prefix."setting` VALUES (10, 'HTML页存放目录', '', 'htmldir', 'html', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (11, '附件存放目录', '', 'attachdir', 'attachments', 'string');
INSERT INTO `".$db_prefix."setting` VALUES (12, '允许的最大附件大小', '上传的附件的最大字节数，设为0将不做限制。<br>1 KB = 1024 字节 1 MB = 1048576 字节', 'maxattachsize', '1048576', 'integer');

    

DROP TABLE IF EXISTS `".$db_prefix."sort`;
CREATE TABLE `".$db_prefix."sort` (
  `sortid` int(15) NOT NULL auto_increment,
  `parentid` int(15) NOT NULL default '0',
  `sortname` varchar(20) NOT NULL default '',
  `sortdir` varchar(20) NOT NULL default '',
  `count` int(15) NOT NULL default '0',
  `displayorder` int(15) NOT NULL default '0',
  PRIMARY KEY  (`sortid`)
) ;



DROP TABLE IF EXISTS `".$db_prefix."user`;
CREATE TABLE `".$db_prefix."user` (
  `userid` int(15) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`userid`)
) ;

";
	
	echo "<p><b>正在建立数据表......</b></p>";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>";
    $a_query = explode(";",$mysql_data);
    while (list(,$query) = each($a_query)) {
           $query = trim($query);
           if ($query) {
               if (strstr($query,'CREATE TABLE')) {
                   ereg('CREATE TABLE ([^ ]*)',$query,$regs);
                   if ($delete_existing) {
                       $DB->query("DROP TABLE IF EXISTS $regs[1]");
                   }
				   echo "<p>正在建立表: ".$regs[1]." …… ";
				   $DB->query($query);
					if ($query)
					{
						echo "成功</p>\n";
					} else {
						echo "失败</p>\n";
					}
               } else {
                   $DB->query($query);
               }

           }
    }
	echo "<p><input type=\"submit\" name=\"next\" value=\"下一步\"></p>";
}

if ($step==4) {

	echo "<p><b>添加管理员帐号</b></p>";
	echo "<p><form action=\"install.php\"  method=\"post\"></p>";
	echo "<p><input type=\"hidden\" name=\"step\" value=\"".($step+1)."\"></p>";
    echo "<p>用户名: <input type=\"text\" value=\"\" name=\"username\"></p>";
    echo "<p>用户密码: <input type=\"password\" value=\"\" name=\"password\"></p>";
    echo "<p>确认用户密码: <input type=\"password\" value=\"\" name=\"password2\"></p>";
    echo "<p><input type=\"submit\" name=\"next\" value=\"下一步\"></p>";
}

if ($step==5) {

    if (trim($username)=="" OR trim($password)=="") {
        sa_exit("请返回并输入所有选项");
    }
	if(strlen($_POST['password']) < 6)
	{
		sa_exit("密码长度不能小于6位","javascript:history.go(-1);");
	}
    if ($password!=$password2) {
        sa_exit("两个输入的密码不相同,请返回重新输入");
    }
    $DB->query("INSERT INTO ".$db_prefix."user (username,password)
                       VALUES ('".htmlspecialchars(trim($_POST[username]))."','".md5(trim($_POST[password]))."')");


    echo "<p>安装完毕,请删除安装文件 install.php,以免被他人恶意利用.</p>";
    echo "<p>感谢您使用本程序.</p>";
    echo "<p><a href=\"index.php\">登陆控制面版</a></p>";

}
cpfooter();
?>
