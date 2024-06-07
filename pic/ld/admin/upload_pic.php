<html>
<head>
<title>上传图片</title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<STYLE><!--
BODY {
	COLOR: #3F3849; 
	line-height: 140%;
	font-family: "Verdana", "Tahoma", "宋体";
	FONT-SIZE: 12px;
	SCROLLBAR-ARROW-COLOR: #F5D300;
	SCROLLBAR-BASE-COLOR:  #40364d;
	SCROLLBAR-HIGHLIGHT-COLOR: #90869E;
}
A:LINK		{COLOR: #3F3849; TEXT-DECORATION: none}
A:VISITED	{COLOR: #3F3849; TEXT-DECORATION: none}
A:HOVER		{COLOR: #F5D300; BACKGROUND-COLOR: #40364d}
A:ACTIVE	{COLOR: #F5D300; BACKGROUND-COLOR: #40364d}
FORM		{FONT-FAMILY: "Verdana", "Tahoma", "宋体"; FONT-SIZE: 12px}
INPUT		{FONT-FAMILY: "Verdana", "Tahoma", "宋体"; COLOR: #51485F; FONT-SIZE: 12px}
//-->
</STYLE>
</head>
<script language="javascript">
<!--
function bbcode(thebbcode) {
    if (thebbcode!=""){
        parent.form.content.value += "\n"+thebbcode+"\n";
        parent.form.content.focus();
    }
}
function preview(url, width, height)
{
    var feature = "dialogWidth:" + width + "px;dialogHeight:" + height + "px;help:no;status:no;"
    window.showModalDialog(url, null, feature);
}
//-->
</script>
<body bgcolor=#C5C5C5 topmargin=0 leftmargin=0>
<?
if($_POST['action'] == "upload_pic")
{
    require_once ("global.php");
    function getExtention($type)
    {
        switch ($type)
        {  
            case "image/gif":
                $extention=".gif";
                break;
            case "image/pjpeg":
                $extention=".jpg";
                break;
            case "mage/x-png":
               $extention=".png";
               break;
        }
        return $extention;
    }
    function MakeName($length) 
    { 
        $possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"."ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
        $str = ""; 
        while(strlen($str) < $length) 
        { 
            $str .= substr($possible, (rand() % strlen($possible)), 1); 
        } 
        return($str); 
    }
    function GetImageInfo($file) 
    {
	$data	= getimagesize($file);
	$imageInfo["width"]	= $data[0];
	$imageInfo["height"]= $data[1];
	$imageInfo["type"]	= $data[2];
	$imageInfo["name"]	= basename($file);
	$imageInfo["size"]  = filesize($file);
	return $imageInfo;		
    } 
    if (($upfile_type=="image/gif") || ($upfile_type=="image/pjpeg") || ($upfile_type=="image/x-png"))
    {
    // 构造文件名
        $datetime = date("YmdHis");
        $pic_name = $datetime.MakeName(6).getExtention($upfile_type);
        $filename = "../upload/".$pic_name;
        if(!is_dir("../upload")) mkdir("../upload",0777);
        // 将文件存放到服务器
        if (copy($upfile,$filename))
        {
	    $imageInfo	= GetImageInfo($filename);
            $size = get_real_size($imageInfo["size"]);
            echo "<a href=\"javascript:\" title=预览图片 onclick=\"preview('".$filename."',".($imageInfo['width']+6).",".($imageInfo['height']+32).")\">$pic_name</a> (".$size.") 上传成功 <a href=\"upload_pic.php\">[继续上传]</a>";
            $bbcode="../../upload/".$pic_name;
            echo "<script>bbcode('[IMG]".$bbcode."[/IMG]')</script>";
        } else {
            echo "上传失败! \n";
            echo "<a href=javascript:history.back(1)>[返回]</a>\n";
        }
    } else {
    echo "不是有效的图片文件, \n";
    echo "<a href=javascript:history.back(1)>[返回]</a>\n";
    }
} else {
?>
<form name="form" method="post" action="upload_pic.php" enctype="multipart/form-data" >
<input type="hidden" name="action" value="upload_pic">
<input type="file" name="upfile" size=35 value=""> <input type="submit" name="Submit" value="上传">
</form>
</body>
</html>
<?
}
?>
