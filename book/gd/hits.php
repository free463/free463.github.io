<?php
// ========================== 文件说明 ==========================//
// 本文件说明：点击次数
// ==============================================================//
error_reporting(7);
require "functions.php";
$articleid = intval($articleid);
$article = $DB->fetch_array($DB->query("SELECT * FROM ".$db_prefix."article WHERE articleid='$articleid'"));
$article['hits']++;
$DB->query("UPDATE ".$db_prefix."article SET hits='".$article['hits']."' WHERE articleid=$articleid");
echo "document.write(\"".$article['hits']."\");\n"; 
?>
