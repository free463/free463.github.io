<?php
// ========================== 文件说明 ==========================// 
// 本文件说明：生成htm页
// =============================================================// 
error_reporting(7);
set_time_limit(0);
// 加载后台函数集合
require_once ("global.php");
require_once ("class/make_inc.php");
cpheader();
//生成文章页
if($_GET['action'] == "article"){
    if (!empty($_GET['articleid'])){
        $MK->makearticle($articleid);
        redirect("生成".$articleid.".html成功!", "javascript:history.go(-1);");
    }
    !$step && $step=1;
    !$percount && $percount=100;
    $sstart=($step-1)*$percount;
    $next=$sstart+$percount;
    $step++;
    $goon=0;
    if (!empty($_GET['sortid'])){
        $maketype = "WHERE sortid='".$sortid."' and visible='1'";
    } else {
        $maketype = "WHERE visible='1'";
    }
    if ($_GET['type']=="date"){
        $Ddate = $Dday? mktime (0, 0,0 ,$Dmonth, $Dday,$Dyear):mktime (0, 0,0 ,$Dmonth, 1,$Dyear);
        $Ddate1 = $Dday? mktime (0, 0,0 ,$Dmonth, $Dday+1,$Dyear):mktime (0, 0,0 ,$Dmonth+1, 1,$Dyear);
        $maketype = "WHERE addtime>'".$Ddate."' and addtime<'".$Ddate1."' and visible='1'";
    }
    $articles="SELECT articleid FROM ".$db_prefix."article $maketype";
    $maketotal=$DB->num_rows($DB->query($articles));
    if($new > 0) $maketotal=$new;
    $makefile = $DB->query("SELECT articleid FROM ".$db_prefix."article $maketype order by articleid desc LIMIT $sstart,$percount");
    if($maketotal < $next){
        $next=$maketotal;
    }
    $i=0;
    while ($row=$DB->fetch_array($makefile)) 
    {
        $i++;
        if(($i+$sstart) <= $maketotal && $row['articleid']) {
	   $goon=1;
           $MK->makearticle($row['articleid']);
        }
    }
    if($goon){
        $db=debuginfo();
        $ds=$db+$d;
        $jumpurl="make.php?action=article&type=$type&Dyear=$Dyear&Dmonth=$Dmonth&Dday=$Dday&sortid=$sortid&new=$new&step=$step&percount=$percount&d=$ds";
        echo "<font color=red><b>正在生成文章的Html页,请等待......</b></font><br><br>";
        echo "正在生成(".$sstart."-".$next.")个页面&nbsp;&nbsp;&nbsp;&nbsp;共<font color=red><b>$maketotal</b></font> 个";
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=$jumpurl'>";
    } else {
        echo "<b>操作成功！共生成<font color=red>".$maketotal."</font>个页面! 总费时<font color=red>".$d."</font>秒 <a href=./article.php?action=make>[返回]</a></b>";
    }

}
//生成文章页结束
//生成分类页
if($_GET['action'] == "sort"){
    !$step && $step=0;
    $goon=0;
    if (!empty($_GET['sortid'])){
        $maketype = "WHERE sortid='$sortid'";
    }
    else
    {
        $maketype = "order by sortid LIMIT $step,1";
        $makesorts="SELECT * FROM ".$db_prefix."sort";
        $maketotal=$DB->num_rows($DB->query($makesorts));
    }
    $makefile = $DB->query("SELECT * FROM ".$db_prefix."sort $maketype");
    while ($row=$DB->fetch_array($makefile)) 
    {
        $sid=$row['sortid'];
	$query = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid=$sid");
	$sort = $DB->fetch_array($query);
        if ($sort['parentid']=='0'){
          $type = "WHERE pid = '$sid' or sortid = '$sid' and visible='1'";
        } else {
          $type = "WHERE sortid = '$sid' and visible='1'";
        }
        $pagequery="SELECT * FROM ".$db_prefix."article $type";
        $total=$DB->num_rows($DB->query($pagequery));
        $show  =  $configuration[articlenum];  
        $totalPage  =  ceil($total/$show);
        if ($totalPage=="0") $totalPage="1";
	    if(!$row['sortid'])continue;
	    $goon=1;
        for ($i = 1;$i<=$totalPage; $i++){
            $start = ($i-1)*$configuration[articlenum];
            //$multipage = multi($total, $configuration[articlenum], $i, "index","html");  
            $multipage = showpages($total, $configuration[articlenum], $i, "index");
            $list=$MK->getarticlelist($sid,$start,$configuration[articlenum]);
            if ($total<1){
                 $list="<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"center\">本类暂无文章</td></tr></table>";
            }
            $MK->makesort($row['sortid'],$row['sortdir'],$i,$list,$total,$multipage);
        }
    }
    cpheader();
    if (!empty($_GET['sortid']))
    {
        echo "<b>生成该分类页成功!共<font color=red>".($i-1)."</font>个页面! 总费时<font color=red>".debuginfo()."</font>秒 <a href=./article.php?action=make>[返回]</a></b>";
        exit;
    }
    $step++;
    $page=$page+$i-1;
    if($goon){
        $db=debuginfo();
        $ds=$db+$d;
        $jumpurl="make.php?action=sort&sortid=$sortid&step=$step&page=$page&d=$ds";
        echo "<font color=red><b>正在生成文章分类的Html页,请等待......</b></font><br><br>";
        echo "(".($i-1)."页)<b><font color=red>".$step."</font>/<font color=red>".$maketotal."</font><b>";
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=$jumpurl'>";
    } else {
        echo "<b>操作成功！共生成分类<font color=red>".$maketotal."</font>个，页面<font color=red>".($page+1)."</font>个，总费时<font color=red>".$d."</font>秒 <a href=./article.php?action=make>[返回]</a></b>";
    }
}
//生成首页
if($_GET['action'] == "index"){
    $MK->makeindex();
    redirect("生成index.html成功", "./article.php?action=make");
}
?>
