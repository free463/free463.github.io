<?
error_reporting(7);
require "class/template.php";
$MK = new makes;
$MK->pf=$db_prefix;
$MK->cf=$configuration;
$MK->DB=$DB;
$MK->t=new template("../templates/".$configuration[template]); 
class makes {
     // ####################### 生成文章html页 #######################
     function makearticle($articleid) {
         $makefile = $this->DB->query("SELECT * FROM ".$this->pf."article where articleid='".$articleid."'");
         $row = $this->DB->fetch_array($makefile);
         $addtime=date("Y-m-d",$row['addtime']);
         $putsort1=$this->getputsort1(article);
         $putsort2=$this->getputsort2($row['sortid'],article);
         if ($row['ishtml']=='1') {
             $content=$row['content'];
         } else {
             $content=ubb2html(html_clean($row['content']));
         }
         $locality=$this->getlocality($row['sortid']);
         $sortname=$this->getsort($row['sortid']);
         $this->t->set_file("article","article.html");	
         $this->t->set_var(array("article_id"=>$row['articleid'],
		                "putsort1"=>$putsort1,
		                "putsort2"=>$putsort2,
                                "locality"=>$locality,
	    	                "article_title"=>$row['title'],
		                "article_sortname"=>$sortname,
		                "article_author"=>$row['author'],
                                "article_source"=>$row['source'],
		                "article_content"=>$content,
		                "article_addtime"=>$addtime,
		                "hits"=>$row['hits'],
	    	                "allhot"=>$this->gethot("",10,20,article),
	    	                "thishot"=>$this->gethot($row[sortid],10,20,article),
                                "allcommend"=>$this->getcommend("",10,20,article),
                                "thiscommend"=>$this->getcommend($row['sortid'],10,20,article),
	    	                "template"=>$this->cf['template'],
		                "puttitle"=>$this->cf[title],
		                "puturl"=>$this->cf[url]));
         $this->t->parse("OUT","article");
         $query = $this->DB->query("SELECT sortdir FROM ".$this->pf."sort where sortid='".$row['sortid']."'");
         $dir = $this->DB->fetch_array($query);
         $this->checkdir($dir['sortdir']);
         $filename="../".$this->cf[htmldir]."/".$dir['sortdir']."/".$row['articleid'].".html";
         $this->t->savetofile("$filename","OUT");
         $this->t->renew();
     }

     // ####################### 生成分类页 #######################
     function makesort($sortid,$sortdir,$i,$list,$total,$multipage)
     {
         $putsort1=$this->getputsort1(sort);
         $putsort2=$this->getputsort2($sortid,sort);
         $locality=$this->getlocality($sortid);
         $this->t->set_file("list","list.html");
         $this->t->set_var(array("puttitle"=>$this->cf[title],
	    	                "puturl"=>$this->cf[url],
	    	                "sortid"=>$sortid,
                                "putsort1"=>$putsort1,
                                "putsort2"=>$putsort2,
                                "locality"=>$locality,
		                "articlelist"=>$list,
	    	                "allhot"=>$this->gethot("",10,20,sort),
	    	                "thishot"=>$this->gethot($sortid,10,20,sort),
                                "allcommend"=>$this->getcommend("",10,20,sort),
                                "thiscommend"=>$this->getcommend($sortid,10,20,sort),
		                "total"=>$total,
		                "pagetotals"=>$this->cf[flashnum],
		                "multipage"=>$multipage,
	    	                "template"=>$this->cf['template'],
		                "puttitle"=>$this->cf[title],
		                "puturl"=>$this->cf[url]));
         $this->t->parse("OUT","list");
         $this->checkdir($sortdir);
	 $filename = ($i==1)? "../".$this->cf[htmldir]."/".$sortdir."/index.html":"../".$this->cf[htmldir]."/".$sortdir."/index-$i.html";
         $this->t->savetofile("$filename","OUT");
         $this->t->renew();
     }
     // ####################### 生成首页 #######################
     function makeindex()
     {
         $putsort1=$this->getputsort1();
         $this->t->set_file("index","index.html");
         $this->t->set_var(array("putsort1"=>$putsort1,
	    	                "article_hot"=>$this->gethot("",12,20),
                                "article_commend"=>$this->getcommend("",12,20),
	    	                "template"=>$this->cf['template'],
		                "puttitle"=>$this->cf[title],
		                "puturl"=>$this->cf[url]));
         $sorts = $this->DB->query("SELECT * FROM ".$this->pf."sort");
         while ($sort=$this->DB->fetch_array($sorts))
         {                                               
              $this->t->set_var("articlelist?sortid=".$sort['sortid'],$this->getsortarticle($sort['sortid']));
         }
         $this->t->parse("OUT","index");
	 $filename = "../index.html";
         $this->t->savetofile("$filename","OUT");
         $this->t->renew();
     }
     // ####################### 输出推荐文章列表 #######################
     function getcommend($sortid="0",$num="15",$len="20",$type="index")
     {
         $path=($type=='index')? "":"../../";
         if ($sortid>0) {
             $istype = "WHERE pid = '$sortid' or sortid = '$sortid' and iscommend='1' and visible='1'";
         } else {
             $istype = "where iscommend='1' and visible='1'";
         }
         $list = "";
         $query = $this->DB->query("SELECT * FROM ".$this->pf."article $istype order by articleid desc LIMIT 0,$num");
         while ($row=$this->DB->fetch_array($query))
         {
             $sortquery = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$row['sortid']."'");
             $sort = $this->DB->fetch_array($sortquery);  
             $title=strlen($row['title'])>$len ? cn_substr($row['title'],$len)."...":$row['title'];
             $url=$path.$this->cf[htmldir]."/".$sort['sortdir']."/".$row['articleid'].".html";
             $list.="<tr><td><img src=../images/main.gif><a href=\"".$url."\" target=\"_blank\" title=\"".$row['title']."\">".$title."</a></td></tr><tr><td height=1 background=/images/dot.gif></td></tr>";
         }
         return $list;
     }
     // ####################### 输出热门文章列表 #######################
     function gethot($sortid="0",$num="15",$len="20",$type="index")
     {
         $path=($type=='index')? "":"../../";
         if ($sortid>0) {
               $istype = "WHERE pid = '$sortid' or sortid = '$sortid' and visible='1'";
         } else {
               $istype = "WHERE visible='1'";
         }
         $list = "";
         $query = $this->DB->query("SELECT * FROM ".$this->pf."article $istype order by hits desc LIMIT 0,$num");
         while ($row=$this->DB->fetch_array($query))
         { 
             $sortquery = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$row['sortid']."'");
             $sort = $this->DB->fetch_array($sortquery);  
             $title=strlen($row['title'])>$len ? cn_substr($row['title'],$len)."...":$row['title'];
             $url=$path.$this->cf[htmldir]."/".$sort['sortdir']."/".$row['articleid'].".html";
             $list.="<tr><td><img src=../images/main.gif><a href=\"".$url."\" target=\"_blank\" title=\"".$row['title']."\">".$title."</a></td></tr><tr><td height=1 background=/images/dot.gif></td></tr>";
         }
         return $list;
     }
     // ####################### 输出分类文章调用 #######################
     function getsortarticle($sortid="0",$num="8",$len="26",$type="index")
     {
         $path=($type=='index')? "":"../../";
         if ($sortid>0) {
               $istype = "WHERE pid = '$sortid' or sortid = '$sortid' and visible='1'";
         } else {
               $istype = "WHERE visible='1'";
         }
         $list = "";
	 $query = $this->DB->query("SELECT * FROM ".$this->pf."article $istype order by articleid desc LIMIT 0,$num");
         while ($row=$this->DB->fetch_array($query))
         { 
             $sortquery = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$row['sortid']."'");
             $sort = $this->DB->fetch_array($sortquery);
             $addtime=date("m-d",$row['addtime']);
             $sortname = "<a href=\"".$this->cf[htmldir]."/".$sort['sortdir']."/\">[".$sort["sortname"]."]</a>";  
             $title=strlen($row['title'])>$len ? cn_substr($row['title'],$len)."...":$row['title'];
             $url=$path.$this->cf[htmldir]."/".$sort['sortdir']."/".$row['articleid'].".html";
	     $list.="<tr><td width=81%>·<a href=\"".$url."\" target=\"_blank\" title=\"".$row['title']."\">".$title."</a><td width=19%><font color=red>[".$addtime."]</font></td></tr><tr><td height=1 colspan=\"2\" background=images/dot.gif></td></tr>\r\n";
         }
         return $list;
     }
     // ####################### 取得当前所在位置 #######################
     function getlocality($sortid)
     {
         $query = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$sortid."'");
         $sort2 = $this->DB->fetch_array($query);
         $query = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$sort2['parentid']."'");
         $sort1 = $this->DB->fetch_array($query);
         $sort[1][dir]="../../".$this->cf[htmldir]."/".$sort1['sortdir']."/";
         $sort[1][name]=$sort1['sortname'];
         $sort[2][dir]="../../".$this->cf[htmldir]."/".$sort2['sortdir']."/";
         $sort[2][name]=$sort2['sortname'];
         if ($sort[1][name]) {
             $locality="<a href=\"".$sort[1][dir]."\">".$sort[1][name]."</a> <font color=4C4C4C>&gt;&gt; </font><a href=\"".$sort[2][dir]."\">".$sort[2][name]."</a>";
         } else {
             $locality="<a href=\"".$sort[2][dir]."\">".$sort[2][name]."</a>";
         }
         return $locality;
     }
     // ####################### 获取文章所在分类 #######################
     function getsort($sortid)
     {
         $query = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$sortid."'");
         $sort = $this->DB->fetch_array($query);
         return $sort['sortname'];
     }
     // ####################### 输出一级分类列表 #######################
     function getputsort1($type="index")
     {
         $path=($type=='index')? "":"../../";
         $this->t->set_file("putsort1","list_sort.html");
         $this->t->set_block("putsort1","RowSort1","RowsSort1");
         $sorts = $this->DB->query("SELECT * FROM ".$this->pf."sort where parentid='0' order by displayorder");
         while ($sort=$this->DB->fetch_array($sorts))
         { 
             $url=$path.$this->cf[htmldir]."/".$sort['sortdir']."/";
             $this->t->set_var(array("sort_url"=>$url,
	  	                      "sort_name"=>$sort['sortname']));
	     $this->t->parse("RowsSort1","RowSort1",true);
         }
         $putsort1=$this->t->get(RowsSort1);
         $this->t->renew();
         return $putsort1;
     }
     // ####################### 输出二级分类列表 #######################
     function getputsort2($sortid,$type="index")
     {
         $path=($type=='index')? "":"../../";
	 $query = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid=$sortid");
	 $sort = $this->DB->fetch_array($query);
         if ($sort['parentid']=='0'){
             $type = "WHERE parentid='".$sortid."'";
         } else {
             $type = "WHERE parentid='".$sort['parentid']."'";
         }
         $this->t->set_file("putsort2","list_sort.html");
         $this->t->set_block("putsort2","RowSort2","RowsSort2");
         $sorts = $this->DB->query("SELECT * FROM ".$this->pf."sort $type order by displayorder");
         while ($row=$this->DB->fetch_array($sorts))
         { 
             $url=$path.$this->cf[htmldir]."/".$row['sortdir']."/";
             $this->t->set_var(array("sort_url"=>$url,
	  	                      "sort_name"=>$row['sortname']));
	     $this->t->parse("RowsSort2","RowSort2",true);
         }
         $putsort2=$this->t->get(RowsSort2);
         $this->t->renew();
         return $putsort2;
     }
     // ####################### 输出文章列表 #######################
     function getarticlelist($sortid,$start,$num,$len="60")
     {
	$query = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid=$sortid");
	$sort = $this->DB->fetch_array($query);
        if ($sort['parentid']=='0'){
            $type = "WHERE pid = '$sortid' or sortid = '$sortid' and visible='1'";
        } else {
            $type = "WHERE sortid = '$sortid' and visible='1'";
        }
        $this->t->set_file("list_article","list_article.html");
        $this->t->set_block("list_article","RowArticle","RowsArticle");
        $this->t->set_block("list_article","RowBr","RowsArticle");
        $list="";
        $query = $this->DB->query("SELECT * FROM ".$this->pf."article $type order by articleid desc LIMIT $start,$num");
        $i=0;
	while ($row=$this->DB->fetch_array($query)) 
        {
            $i++;
            $sortquery = $this->DB->query("SELECT * FROM ".$this->pf."sort WHERE sortid='".$row['sortid']."'");
            $sort = $this->DB->fetch_array($sortquery);
            $url="../../".$this->cf[htmldir]."/".$sort['sortdir']."/".$row['articleid'].".html";  
            $id=$row['id'];
            $sortname=$this->getsort($row['sortid']);
            if (!$row['author']) $row['author']="未知"; 
            $content=ubb2html($row['content']);
            if (strlen($title)>$len) $title=cn_substr($title,$len)."...";
            $addtime=date("y-m-d",$row['addtime']);
            $this->t->set_var(array("article_url"=>$url,
                                    "article_title"=>$row['title'],
                                    "article_sortname"=>$sortname,
		                    "article_author"=>$row['author'],
		                    "article_addtime"=>$addtime,
		                    "article_hits"=>$row['hits']));
	    $this->t->parse("RowsArticle","RowArticle",true);
            if($i%$this->cf[colnum]==0) {
	         $this->t->parse("RowsArticle","RowBr",true);
            }
         } 
         $flashlist=$this->t->get(RowsArticle);
         $this->t->renew();
         return $flashlist;
     }
     // ####################### 检查目录是否存在，不存在则建立 #######################
     function checkdir($sortdir)
     {
         if(!is_dir("../".$this->cf[htmldir]."")) mkdir("../".$this->cf[htmldir]."",0777);
         if(!is_dir("../".$this->cf[htmldir]."/".$sortdir."")) mkdir("../".$this->cf[htmldir]."/".$sortdir."",0777);
     }
}
?>
