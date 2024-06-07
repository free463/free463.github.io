<?
error_reporting(7);
set_time_limit(0);
// 加载后台函数集合
require_once ("global.php");
require_once ("class/make_inc.php");
            $start = ($i-1)*$configuration[flashnum];
            //$multipage = multi($total, $configuration[articlenum], $i, "index","html");  
            $multipage = showpages($total, $configuration[articlenum], $i, "index");
            $list=$MK->getarticlelist($sortid,$start,$configuration[articlenum]);
            if ($total<1){
                 $list="<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"center\">本类暂无文章</td></tr></table>";
            }
            $MK->makesort($sortid,$sortdir,$i,$list,$total,$multipage);
?>