<?php
// ========================== 文件说明 ==========================// 
// 本文件说明：后台的常用函数 
// =============================================================// 

// ####################### 控制面版各页面页眉 #######################
function cpheader($extraheader=""){
	global $configuration;
	echo "<html>\n";
	echo "<head>\n";
	echo "<title> $configuration[title]</title>\n";
	echo "<meta content=\"text/html; charset=gb2312\" http-equiv=\"Content-Type\">\n";
	echo "<link rel=\"stylesheet\" href=\"./cp.css\" type=\"text/css\">\n";
	echo "".$extraheader."\n";
        echo "<script language=\"JavaScript\" src=\"common.js\"></script>\n";
	echo "</head>\n";
	echo "<body leftmargin=\"20\" topmargin=\"20\" marginwidth=\"20\" marginheight=\"20\"  style=\"table-layout:fixed; word-break:break-all\">\n";
}
function ShowMsg($msg,$gotoPage)
{
	$msg = str_replace("'","`",trim($msg));
	$gotoPage = str_replace("'","`",trim($gotoPage));
	echo "<script language='javascript'>\n";
	echo "alert('$msg');";
	if($gotoPage=="back")
	{
		echo "history.go(-1);\r\n";
	}
	else if(ereg("^-",$gotoPage))
	{
		echo "history.go($gotoPage);\r\n";
	}
	else if($gotoPage!="")
	{
		echo "location.href='$gotoPage';\r\n";
	}
	echo "</script>";
}
// ####################### 操作成功提示页面 #######################
function redirect($msg,$url){
	cpheader();
	echo "$msg <a href=$url>[返回]</a>\n";
	echo "<meta http-equiv=\"refresh\" content=\"1;URL=$url\">\n";
	echo "</body>\n</html>";
	exit;
}

// ####################### 控制面版各页面页脚 #######################
function cpfooter(){
	global $configuration;
	echo "\n<br>\n<center>Powered by: <a href=\"mailto:wupei@china.com.cn\" target=\"_blank\">小猪会气功</a>  ".$configuration[version]."</center><br>\n";
	echo "</body>\n</html>";

}

// ####################### 表格行间的背景色替换 #######################
function getrowbg() {
	global $bgcounter;
	if ($bgcounter++%2==0) {
		return "firstalt";
	} else {
		return "secondalt";
	}
}

// ####################### 错误提示信息 #######################
function sa_exit($msg, $url) {
	cpheader();
    echo "<p>$msg</p>";
	echo "<p><a href=\"".$url."\">点击这里返回...</a></p>";
    echo "</body>\n</html>";
    exit;
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

// 产生表格
function makenav($ctitle="",$nav=array()) {
	echo "<tr class=\"tblhead\">\n";
	echo "  <td class=\"space\"><span class='tblhead'><b>$ctitle</b></span></td>\n";
	echo "</tr>\n";
    foreach ($nav AS $title=>$link)	{
		echo "<tr>\n";
		echo "  <td style=\"PADDING-LEFT: 10px;\"><a href=\"$link\" target=\"mainFrame\">$title</a></td>\n";
		echo "</tr>\n";
	}
}

// ####################### 用户登录 #######################
function checkuser($username,$password){
	global $DB,$db_prefix,$userinfo;
	$username = htmlspecialchars(trim($username));
	$username = trim($username);
	$userinfo = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."user WHERE username='".addslashes($username)."' AND password='".addslashes($password)."'");
	if (empty($userinfo)) {
		return false;
	} else {
		return true;
	}
}

// ####################### 验证用户是否处于登陆状态 #######################
function islogin($username,$password){
	global $DB,$db_prefix;
	if ($username=="" or $password=="")
	{
		loginpage();
	}
	$result = $DB->query("SELECT password FROM ".$db_prefix."user WHERE username='$username'");
	$getpass = $DB->fetch_array($result);
	if ($getpass[password] != $password)
	{
		loginpage();
	}
}

// ####################### 获取数据库大小单位 #######################
function get_real_size($size) {
	$kb = 1024;         // Kilobyte
    $mb = 1024 * $kb;   // Megabyte
    $gb = 1024 * $mb;   // Gigabyte
    $tb = 1024 * $gb;   // Terabyte

    if($size < $kb) {
		return $size." B";
	}else if($size < $mb) {
		return round($size/$kb,2)." KB";
	}else if($size < $gb) {
		return round($size/$mb,2)." MB";
	}else if($size < $tb) {
		return round($size/$gb,2)." GB";
	}else {
		return round($size/$tb,2)." TB";
	}
}

// ####################### 后台成功登录记录 #######################
function loginsucceed($username="",$password="") {
	global $DB,$db_prefix;
	$extra .= "\nScript: ".getenv("REQUEST_URI");
	$DB->query("INSERT INTO ".$db_prefix."loginlog (username,password,date,ipaddress,result) VALUES
	('".$username."','密码正确','".time()."','".getip()."','1')");
}

// ####################### 后台失败登录记录 #######################
function loginfaile($username="",$password="") {
	global $DB,$db_prefix;
	$extra .= "\nScript: ".getenv("REQUEST_URI");
	$DB->query("INSERT INTO ".$db_prefix."loginlog (username,password,date,ipaddress,result) VALUES
	('".$username."','密码错误','".time()."','".getip()."','2')");
}

// ####################### 后台管理记录 #######################
function getlog() {
	global $DB,$db_prefix;
	if (isset($_POST[action])) {
		$action = $_POST[action];
	} elseif (isset($_GET[action])) {
		$action = $_GET[action];
	}
	if (isset($action)) {
		$script = "".getenv("REQUEST_URI");
		$DB->query("INSERT INTO ".$db_prefix."adminlog (action,script,date,ipaddress) VALUES ('".htmlspecialchars(trim($action))."','".htmlspecialchars(trim($script))."','".time()."','".getip()."')");
	}
}
// ####################### 获取分类 #######################
function sortselect($sortid='0',$name='sortid') {
	global $DB,$db_prefix;
        $temp="<select name=\"".$name."\" class=\"saveHistory\">\n";
        $temp.="<option value=\"\" selected>选择分类</option>\n";
	$query = "SELECT * FROM ".$db_prefix."sort where parentid='0' ORDER BY sortid";
	$result = $DB->query($query);
	while ($sort=$DB->fetch_array($result))
	{ 
		if ($sort[sortid] == $sortid){
			$temp.="<option value=".$sort['sortid']." selected>".htmlspecialchars($sort['sortname'])."</option>\n";
		} else {
			$temp.="<option value=".$sort['sortid'].">".htmlspecialchars($sort['sortname'])."</option>\n";
		}
	        $tsorts = $DB->query("SELECT * FROM ".$db_prefix."sort where parentid='".$sort['sortid']."' ORDER BY sortid");
	        while ($tsort=$DB->fetch_array($tsorts))
                {
		     if ($tsort[sortid] == $sortid){
			     $temp.="<option value=".$tsort['sortid']." selected> ├ ".htmlspecialchars($tsort['sortname'])."</option>\n";
		     } else {
			     $temp.="<option value=".$tsort['sortid']."> ├ ".htmlspecialchars($tsort['sortname'])."</option>\n";
		     }
                }
	}
        $temp.="</select>";
		return $temp;
}
// ####################### 检查分类名是否符合逻辑 #######################
function checksortLen($sortname)
{
	if(empty($sortname))
	{
		$result="分类名不能为空<br>";
		return $result;
	}
	if(strlen($sortname) > 16)
	{
		$result="分类名不能超过16个字符<br>";
		return $result;
	}
}
// ####################### 检查分类文件是否为空 #######################
function checksortdir($sortdir)
{
	if(empty($sortdir))
	{
		$result="分类文件夹不能为空<br>";
		return $result;
	}
}

// ####################### 检查分类是否已选择 #######################
function choosesort($sortid)
{
	if(trim($sortid) == "")
	{
		$result="你还没有选择分类<br>";
		return $result;
	}
}

// ####################### 检查标题是否合法 #######################
function checksubject($title)
{
	if(trim($title) == "")
	{
		$result="标题不能为空<br>";
		return $result;
	}
	if(strlen($title) > 120)
	{
		$result="标题不能超过120个字符<br>";
		return $result;
	}
}

// ####################### 检查作者合法性 #######################
function checkauthor($author)
{
	if(!empty($author))
	{
		if(strlen($author)>20)
		{
			$result.="作者名字不能超过20个字节！";
			return $result;
		}
	}
}

// ####################### 检查文章出处合法性 #######################
function checksource($source)
{
	if(!empty($source))
	{
		if(strlen($source)>100)
		{
			$result.="文章出处不能超过100个字节！";
			return $result;
		}
	}
}

// ####################### 检查EMAIL地址合法性 #######################
function checkemail($email)
{
    if(!trim($email)=="")
	{
		if(strlen($email)>100)
		{
			$result.="Email 地址过长<br>";
			return $result;
		}
		if(!eregi("^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$",$email))
		{ 
			$result.="Email 格式不正确<br>";
			return $result;
		}
	}
}

// ####################### 检查提交内容合法性 #######################
function checkcontent($content)
{
	if(trim($content)=="")
	{
		$result.="内容不能为空<br>";
		return $result;
	}
	if(strlen($content)<4)
	{
		$result.="内容不能少于4个字符<br>";
		return $result;
	}
}

// ####################### 分页函数 #######################
function multi($num, $perpage, $curr_page, $mpurl, $type) {
	$multipage = '';
	if($num > $perpage) {
		$page = 5;
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
                if ($type == html) {
		   $multipage .= "<a href=\"".$mpurl.".html\"><Font face=webdings>9</font></a>  ";
                } else {
		   $multipage .= "<a href=\"$mpurl&page=1\"><Font face=webdings>9</font></a>  ";
                }
		for($i = $from; $i <= $to; $i++) {
			if($i != $curr_page) {
                                if ($type == html) {
				$multipage .=($i==1)? "<a href=\"".$mpurl.".html\">$i</a> ":"<a href=\"".$mpurl."-$i.html\">$i</a> ";
			        } else {
				$multipage .= "<a href=\"$mpurl&page=$i\">$i</a> ";
			        }
			} else {
				$multipage .= '<u><b>'.$i.'</b></u> ';
			}
		}
                if ($type == html) {
		$multipage .= $pages > $page ? " ... <a href=\"".$mpurl."-$pages.html\"> $pages <Font face=webdings>:</font></a>" : " <a href=\"".$mpurl."-$pages.html\"><Font face=webdings>:</font></a>";
                } else {
		$multipage .= $pages > $page ? " ... <a href=\"$mpurl&page=$pages\"> $pages <Font face=webdings>:</font></a>" : " <a href=\"$mpurl&page=$pages\"><Font face=webdings>:</font></a>";
                }

	}
	return $multipage;
}
// #######################分页函数2 #######################
function showpages($num, $perpage, $page, $mpurl)
{
    $pages = ceil($num / $perpage);
    $first="首页";
    $prev="上一页";
    if($page > 1){
        $first="<a href='".$mpurl.".html'>首页</a>";
        $prev= $page == '2'? "<a href='".$mpurl.".html'>上一页</a>":"<a href=\"".$mpurl."-".($page-1).".html\">上一页</a>";
    }
    $next="下一页";
    $last="尾页";
    if($page < $pages){
        $next="<a href=\"".$mpurl."-".($page+1).".html\">下一页</a>";
        $last="<a href=\"".$mpurl."-".$pages.".html\">尾页</a>";
    }
    $showPages="<select size=1 
 onchange=\"javascript:window.location.href=''+this.options[this.selectedIndex].value+'.html'\">";		        for($i=1;$i<=$pages;$i++){
       $value = $i==1 ? $mpurl:$mpurl."-".$i; 
       $i == $page ? $showPages.="<option value=".$value." selected>第".$i."页</option>" : $showPages .= "<option value=".$value.">第".$i."页</option>";
   }
   $showPages.="</select>";
   $showPages=$first."&nbsp;".$prev."&nbsp;".$next."&nbsp;".$last."&nbsp;&nbsp;转到:".$showPages."";
   return $showPages;
}
// ####################### 自动识别URL #######################
function parseurl($content) {
	return preg_replace(	array(
					"/(?<=[^\]A-Za-z0-9-=\"'\\/])(https?|ftp|gopher|news|telnet|mms){1}:\/\/([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/is",
					"/([\n\s])www\.([a-z0-9\-]+)\.([A-Za-z0-9\/\-_+=.~!%@?#%&;:$\[\]\\()|]+)((?:[^\x7f-\xff,\s]*)?)/is",
					"/(?<=[^\]A-Za-z0-9\/\-_.~?=:.])([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4}))/si"
				), array(
					"[url]\\1://\\2[/url]",
					"\\1[url]www.\\2.\\3\\4[/url]",
					"[email]\\0[/email]"
				), ' '.$content);
}
// ####################### UBB标签转换 #######################
function ubb2html($content)
{

		$content = parseurl($content);

	//自动识别结束

	$content = eregi_replace(quotemeta("[b]"),quotemeta("<b>"),$content);
	$content = eregi_replace(quotemeta("[/b]"),quotemeta("</b>"),$content);
	$content = eregi_replace(quotemeta("[i]"),quotemeta("<i>"),$content);
	$content = eregi_replace(quotemeta("[/i]"),quotemeta("</i>"),$content);
	$content = eregi_replace(quotemeta("[u]"),quotemeta("<u>"),$content);
	$content = eregi_replace(quotemeta("[/u]"),quotemeta("</u>"),$content);
	$content = eregi_replace(quotemeta("[center]"),quotemeta("<center>"),$content);
	$content = eregi_replace(quotemeta("[/center]"),quotemeta("</center>"),$content);

	$content = eregi_replace(quotemeta("[quote]"),quotemeta("<table width=\"96%\" border=\"0\" cellspacing=\"3\" cellpadding=\"0\" style=word-break:break-all align=\"center\"><tr><td><b>引用:</b></td></tr><tr><td><hr width=\"100%\" noshade></td></tr><tr><td class=\"content\"><font color=\"#0000FF\">"),$content);
	$content = eregi_replace(quotemeta("[/quote]"),quotemeta("</font></td></tr><tr><td><hr width=\"100%\" noshade></td></tr></table>"),$content);

	$content = eregi_replace(quotemeta("[code]"),quotemeta("<table width=\"96%\" border=\"0\" cellspacing=\"3\" cellpadding=\"0\" style=word-break:break-all align=\"center\"><tr><td><b>代码:</b></td></tr><tr><td><hr width=\"100%\" noshade></td></tr><tr><td class=\"code\"><font color=\"#0000FF\">"),$content);
	$content = eregi_replace(quotemeta("[/code]"),quotemeta("</font></td></tr><tr><td><hr width=\"100%\" noshade></td></tr></table>"),$content);

	$content = eregi_replace("\\[img\\]([^\\[]*)\\[/img\\]","<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=0 onload=\"javascript:if(this.width>screen.width-270)this.width=screen.width-270\" title=\"用新窗口浏览原始图片\" align=\"center\"></a>",$content);
		 
	$content = eregi_replace("\\[url\\]www.([^\\[]*)\\[/url\\]", "<a href=\"http://www.\\1\" target=_blank>www.\\1</a>",$content);
	$content = eregi_replace("\\[url\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=_blank>\\1</a>",$content);
	$content = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]","<a href=\"\\1\" target=_blank>\\2</a>",$content);
	$content = eregi_replace("\\[email\\]([^\\[]*)\\[/email\\]", "<a href=\"mailto:\\1\">\\1</a>",$content);
	
	//$content = preg_replace( '/javascript/i', 'java script', $content);
	return $content;
} 

// ####################### 清除HTML代码 #######################
function html_clean($content){
	//$content = htmlspecialchars($content);
	$content = str_replace("\n", "<br>", $content);
	$content = str_replace("  ", "&nbsp;&nbsp;", $content);
	$content = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $content);
	return $content;
}
// ####################### 定义删除文件函数 #######################
function delete_file($file){
         $delete = @unlink($file);
         clearstatcache();
         if(@file_exists($file)){
              $filesys = eregi_replace("/","\\",$file);
              $delete = @system("del $filesys");
              clearstatcache();
              if(@file_exists($file)){
                   $delete = @chmod ($file, 0777);
                   $delete = @unlink($file);
                   $delete = @system("del $filesys");
              }
         }
         clearstatcache();
         if(@file_exists($file)){
              return false;
         }else{
              return true;
         }
}
// #######################定义删除目录函数 #######################
function removeDir($dirName)
{
    $result = false;
    if(! is_dir($dirName))
    {
        trigger_error("该目录不存在", E_USER_ERROR);
    }
    $handle = opendir($dirName);
    while(($file = readdir($handle)) !== false)
    {
        if($file != '.' && $file != '..')
        {
            $dir = $dirName . DIRECTORY_SEPARATOR . $file;
            is_dir($dir) ? removeDir($dir) : unlink($dir);
        }
    }
    closedir($handle);
    $result = rmdir($dirName) ? true : false;
    return $result;
}
// #######################计算目录大小 #######################
function dirsize($dirpath){
	$dirsize = 0;
	if (false ==($dirhandle = @opendir($dirpath))) return $dirsize ;
	while (false !== ($name = readdir($dirhandle))){
		if ($name == "." or $name == "..") continue;
		if (!is_dir($dirpath.DIRECTORY_SEPARATOR.$name)){
			$dirsize += filesize($dirpath.DIRECTORY_SEPARATOR.$name);
		} else {
			$dirsize += dirsize($dirpath.DIRECTORY_SEPARATOR.$name);
		}
	}
	closedir($dirhandle);
	return $dirsize;
}
// #######################写入文件 #######################
function write_file($filename,$contents) 
{
	if ($fp=fopen($filename,"w")) 
	{ 
		fwrite($fp,stripslashes($contents)); 
		fclose($fp); 
		return true; 
	} else {
		return false; 
	} 
} 
// #######################中文字符 #######################
function cn_substr($str,$len)
{
    $r_str="";
    $i=0;
    while($i<$len)
    {
        $ch=substr($str,$i,1);
 	if(ord($ch)>0x80) $i++;
 	$i++;
    }
    $r_str=substr($str,0,$i);
    return $r_str;
}
?>
