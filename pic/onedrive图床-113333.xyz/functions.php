<?php
// ========================== 文件说明 ==========================//
// 本文件说明：前台函数
// =============================================================//
error_reporting(7);
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];
// 加载数据库配置信息
require_once ("./admin/config.php");
// 加载数据库类
require_once ("./admin/class/mysql.php");
// 加载常规选项信息
require_once ("./admin/settings.php");
// 加载模板类
require_once ("./admin/class/template.php");   
// 初始化数据库类
$DB = new DB_MySQL;
$DB->servername=$servername;
$DB->dbname=$dbname;
$DB->dbusername=$dbusername;
$DB->dbpassword=$dbpassword;
$DB->connect();
$DB->selectdb();

// 初始化模板类
$t=new template("./templates/".$configuration[template]); 

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
function stripslashes_array(&$array) {
	while(list($key,$var) = each($array)) {
		if ($key != 'argc' && $key != 'argv' && (strtoupper($key) != $key || ''.intval($key) == "$key")) {
			if (is_string($var)) {
				$array[$key] = stripslashes($var);
			}
			if (is_array($var))  {
				$array[$key] = stripslashes_array($var);
			}
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

// 消息显示页面
function message($Msg,$ReturnUrl)
{
	global $t;
	$t->set_file("Message", "message.html");
	$t->set_var(array("Msg"=>$Msg,
			          "ReturnUrl"=>htmlspecialchars($ReturnUrl)));
	$t->parse("OUT","Message");
	$t->p("OUT");
	exit;
}

// 分页函数
function multi($num, $perpage, $curr_page, $mpurl) {
	$multipage = '';
	if($num > $perpage) {
		$page = 10;
		$offset = 2;

		$pages = ceil($num / $perpage);
		$from = $curr_page - $offset;
		$to = $curr_page + $page - $offset - 1;
			if($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				if($from < 1) {
					$to = $curr_page + 1 - $from;
					$from = 1;
					if(($to - $from) < $page && ($to - $from) < $pages) {
						$to = $page;
					}
				} elseif($to > $pages) {
					$from = $curr_page - $pages + $to;
					$to = $pages;
						if(($to - $from) < $page && ($to - $from) < $pages) {
							$from = $pages - $page + 1;
						}
				}
			}
			$multipage .= "<a href=\"$mpurl&page=1\"><font face=webdings>9</font></a>  ";
			for($i = $from; $i <= $to; $i++) {
				if($i != $curr_page) {
					$multipage .= "<a href=\"$mpurl&page=$i\">$i</a> ";
				} else {
					$multipage .= '<u><b>'.$i.'</b></u> ';
				}
			}
			$multipage .= $pages > $page ? " ... <a href=\"$mpurl&page=$pages\"> $pages <Font face=webdings>:</font></a>" : " <a href=\"$mpurl&page=$pages\"><Font face=webdings>:</font></a>";
	}
	return $multipage;
}

// ####################### 清除HTML代码 #######################
function html_clean($content){
	$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br>", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $content);
	return $content;
}
// ####################### 检查评论信息 #######################
function checkcomment($msg)
{
	if(trim($msg) == "")
	{
		$result="内容不能为空<br>";
		return $result;
	}
	if(strlen($msg) > 400)
	{
		$result="内容不能超过400个字符<br>";
		return $result;
	}
}
function checkguest($guest)
{
	if(!empty($guest))
	{
		if(strlen($guest)>16)
		{
			$result.="名字不能超过16个字节！";
			return $result;
		}
	}
}
// ####################### 获取客户端IP #######################
function getip() {
	if (isset($_SERVER)) {
		if (isset($_SERVER[HTTP_X_FORWARDED_FOR])) {
			$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
		} elseif (isset($_SERVER[HTTP_CLIENT_IP])) {
			$realip = $_SERVER[HTTP_CLIENT_IP];
		} else {
			$realip = $_SERVER[REMOTE_ADDR];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv( "HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}
?> 
