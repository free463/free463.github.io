<?php
// ========================== 文件说明 ==========================// 
// 本文件说明：文章管理
// =============================================================// 
error_reporting(7);
// 加载后台函数集合
require_once ("global.php");
require_once ("class/make_inc.php");
//添加文章操作
if($_POST['action'] == "addarticle")
{
    //验证逻辑性
    $result=checksubject($title);
    $result.=checkauthor($author);
    $result.=choosesort($sortid);
    $result.=checksource($source);
    $result.=checkcontent($content);
    if($result)
    {
	sa_exit($result, "javascript:history.go(-1);");
    }
    $query="SELECT * FROM ".$db_prefix."article WHERE title='".addslashes($_POST['title'])."' and author='".addslashes($_POST['author'])."' and content='".addslashes($_POST['content'])."'";
    $result=$DB->num_rows($DB->query($query));
    if($result > 0)
    {
        sa_exit("该文章已存在!", "./article.php?action=add&sortid=$sortid");
    }
    //验证逻辑性结束
    $sort = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."sort WHERE sortid='".intval($_POST['sortid'])."'");
    $query="INSERT INTO ".$db_prefix."article (sortid, pid, title, author, source, addtime, ishtml,iscommend, hits, content, isparseurl, visible) VALUES ('".intval($_POST['sortid'])."', '".$sort['parentid']."', '".addslashes($_POST['title'])."', '".addslashes($_POST['author'])."', '".addslashes($_POST['source'])."', '".time()."', '".intval($_POST['ishtml'])."', '".intval($_POST['iscommend'])."', '".intval($_POST['hits'])."', '".addslashes($_POST['content'])."', '".intval($_POST['isparseurl'])."', '".intval($_POST['visible'])."')";
    $DB->query($query) or die("添加文章失败!");
    if($_POST['ismake'] == "1"){
         $makefile = $DB->query("SELECT * FROM ".$db_prefix."article order by articleid desc");
         $row = $DB->fetch_array($makefile);
         $MK->makearticle($row['articleid']);
    }
    redirect("添加文章成功!", "./article.php?action=add&sortid=$sortid");
}

//修改文章操作
elseif($_POST['action'] == "modarticle")
{
    //验证逻辑性
    $result=checksubject($title);
    $result.=checkauthor($author);
    $result.=choosesort($sortid);
    $result.=checksource($source);
    $result.=checkcontent($content);
    if($result)
    {
	sa_exit($result, "javascript:history.go(-1);");
    }
    //验证逻辑性结束
    $sort = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."sort WHERE sortid='".intval($_POST['sortid'])."'");
    $query="UPDATE ".$db_prefix."article SET sortid='".intval($_POST['sortid'])."', pid='".$sort[parentid]."', title='".addslashes($_POST['title'])."', author='".addslashes($_POST['author'])."', source='".addslashes($_POST['source'])."', ishtml='".intval($_POST['ishtml'])."', iscommend='".intval($_POST['iscommend'])."', content='".addslashes($_POST['content'])."', isparseurl='".intval($_POST['isparseurl'])."', visible='".intval($_POST['visible'])."' WHERE articleid='".intval($articleid)."'";
    $DB->query($query) or die("修改文章失败!");
    if($_POST['ismake'] == "1"){
        if($_POST['oldsortid'] != $_POST['sortid']){
            $delfile="../".$configuration[htmldir]."/".$sort[sortdir]."/".intval($articleid).".html";
            @delete_file($delfile);
        }
        $MK->makearticle(intval($articleid));
    }
    redirect("修改文章成功!", "./article.php?action=edit");
}
//显示文章操作
elseif($_GET['action'] == "show")
{
        $DB->query("UPDATE ".$db_prefix."article SET visible='1' WHERE articleid='".intval($articleid)."'");
        redirect("显示文章成功!", "./article.php?action=edit");
}
//隐藏文章操作
elseif($_GET['action'] == "hide")
{
        $DB->query("UPDATE ".$db_prefix."article SET visible='0' WHERE articleid='".intval($articleid)."'");
        redirect("隐藏文章成功!", "./article.php?action=edit");
}
//删除文章操作
elseif($_POST['action'] == "delarticle")
{
    $articlequery = $DB->query("SELECT * FROM ".$db_prefix."article WHERE articleid='".intval($articleid)."'");
    $article = $DB->fetch_array($articlequery);
    $sortid = $article['sortid'];
    $query="DELETE FROM ".$db_prefix."article WHERE articleid='".intval($articleid)."'";
    $DB->query($query) or die("删除文章失败!");
    $sortquery = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='".$sortid."'");
    $sort = $DB->fetch_array($sortquery);
    $delfile="../".$configuration[htmldir]."/".$sort['sortdir']."/".$articleid.".html";
    if(@delete_file($delfile)){
        redirect("删除文章成功!<br>删除".$delfile."成功", "./article.php?action=edit");
    }else{
        redirect("删除文章成功!<br>删除".$delfile."失败", "./article.php?action=edit");
    }
}
//批量显示Flash操作
elseif($_POST['action'] == "moreshow")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        sa_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
             $DB->query("UPDATE ".$db_prefix."article SET visible='1' WHERE articleid='".intval($articleid)."'");
    }
    redirect("批量显示文章成功!", "./article.php?action=edit");
}
//批量隐藏Flash操作
elseif($_POST['action'] == "morehide")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        sa_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
             $DB->query("UPDATE ".$db_prefix."article SET visible='0' WHERE articleid='".intval($articleid)."'");
    }
    redirect("批量隐藏文章成功!", "./article.php?action=edit");
}
//批量生成Flash操作
elseif($_POST['action'] == "moremake")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        sa_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
            $MK->makearticle(intval($articleid));
    }
    redirect("批量生成文章html页成功!", "./article.php?action=edit");
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//批量推荐软件操作
elseif($_POST['action'] == "commend")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        admin_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
             $DB->query("UPDATE ".$db_prefix."article SET iscommend='1' WHERE articleid='".intval($articleid)."'");
    }
    redirect("批量推荐软件成功!", "./article.php?action=edit");
}
//批量取消推荐
elseif($_POST['action'] == "cancelcommend")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        admin_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
             $DB->query("UPDATE ".$db_prefix."article SET iscommend='0' WHERE articleid='".intval($articleid)."'");
    }
    redirect("批量取消推荐成功!", "./article.php?action=edit&type=commend");
}
//批量移动软件操作
elseif($_POST['action'] == "move")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        admin_exit("未选择任何数据!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
        $sort = $DB->fetch_one_array("SELECT * FROM ".$db_prefix."sort WHERE sortid='".intval($_POST['tosortid'])."'");
        $DB->query("UPDATE ".$db_prefix."article SET sortid='".intval($_POST['tosortid'])."', pid='".$sort[parentid]."' WHERE articleid='".intval($articleid)."'");
    }
    redirect("批量移动软件成功!", "./article.php?action=edit");
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//批量删除文章操作
elseif($_POST['action'] == "moredel")
{
	//验证逻辑性
    if (empty($_POST['article']) OR !is_array($_POST['article'])) {
        sa_exit("未选择任何文章!", "javascript:history.go(-1);");
    }
	//验证逻辑性结束
    foreach ($article AS $articleid=>$value) {
	$articlequery = $DB->query("SELECT * FROM ".$db_prefix."article WHERE articleid='".intval($articleid)."'");
	$article = $DB->fetch_array($articlequery);
        $sortid = $article['sortid'];
	$sortquery = $DB->query("SELECT * FROM ".$db_prefix."sort WHERE sortid='".$sortid."'");
	$sort = $DB->fetch_array($sortquery);
        $DB->query("DELETE FROM ".$db_prefix."article WHERE articleid='".intval($articleid)."'");
        $delfile="../".$configuration[htmldir]."/".$sort['sortdir']."/".$articleid.".html";
        @delete_file($delfile);
    }
    redirect("批量删除文章成功!", "./article.php?action=edit");
} else {
    sa_exit("参数错误,请重试!", "javascript:history.go(-1);");
}
?>
