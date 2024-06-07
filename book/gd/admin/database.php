<?php
// ========================== 文件说明 ==========================//
// 本文件说明：数据库管理操作
// ==============================================================//
error_reporting(7);
// 加载后台函数集合
require_once ("global.php");
cpheader();

//优化/修复数据库页面
if ($_GET['action']=="optimize" OR $_GET['action']=="repair") {
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
    if ($_GET['action']=="optimize") {
        $cpforms->formheader(array('title'=>'优化数据库,请选择要优化的表'));
    } else {
        $cpforms->formheader(array('title'=>'修复数据库,请选择要修复的表'));
    }

    $tables = mysql_list_tables($dbname);
    if (!$tables) {
        print "DB Error, could not list tables\n";
        print 'MySQL Error: ' . mysql_error();
        sa_exit("数据库错误", "./index.php?action=main");
    }
    while ($table = $DB->fetch_row($tables)) {
           $cachetables[$table[0]] = $table[0];
           $tableselected[$table[0]] = 1;
    }

    $DB->free_result($tables);

    $cpforms->makeselect(array('text'=>'请选择表:',
                                'name'=>'table[]',
                                'option'=>$cachetables,
                                'selected'=>$tableselected,
                                'multiple'=>1,
                                'size'=>15
                                ));


    $cpforms->makehidden(array('name'=>'action',
                                'value'=>"do$_GET[action]"));
    $cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";
}//end optimize and repair

// 优化/修复操作
if ($_POST['action']=="dooptimize" OR $_POST['action']=="dorepair") {

    if ($_POST['action']=="dooptimize") {
        $a = "OPTIMIZE";
        $text = "优化";
    } else {
        $a = "REPAIR";
        $text = "修复";
    }
    if (!is_array($table) OR empty($table)) {
        sa_exit("还未选中任何要${text}的表", "javascript:history.go(-1);");
    }

    $table = array_flip($_POST['table']);

    foreach ($table AS $name=>$value) {
             if (isset($value)) {
                 echo "正在{$text}表: $name";
                 $result = $DB->query("$a TABLE $name");
                 if ($result) {
                     echo " <b>OK</b>";
                 } else {
                     echo " <font color=\"red\"><b>Failed</b></font>";
                 }
                 echo "<br>\n";
             }
    }

    echo "<p>所有表均已$text.</p>";
}// 优化/修复操作结束


//备份数据库页面
if ($_GET['action']=="backup") {
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\" width=\"95%\" class=\"tblborder\">\n<tr>\n<td>";
    $cpforms->formheader(array('title'=>'备份数据库,请选择要备份的表'));

    $tables = mysql_list_tables($dbname);
    while ($table = $DB->fetch_row($tables)) {
           $cachetables[$table[0]] = $table[0];
           $tableselected[$table[0]] = 1;
    }

    $DB->free_result($tables);
    $cpforms->makeselect(array('text'=>'请选择表:',
                                'name'=>'table[]',
                                'option'=>$cachetables,
                                'selected'=>$tableselected,
                                'multiple'=>1,
                                'size'=>15
                                ));

    $cpforms->makeinput(array('text'=>'备份数据所保存的路径:<br>请确认该文件夹的属性为 0777 ',
                               'name'=>'path',
                               'value'=>"./backup/Article".date("YmdHis",time()).".sql"));
    $cpforms->makehidden(array('name'=>'action','value'=>'dobackup'));
    $cpforms->formfooter();
	echo "</td>\n</tr>\n</table>";
}//backup

// 备份操作
if ($_POST['action']=="dobackup") {

    $table = array_flip($_POST[table]);

    $filehandle = fopen($path,"w");
    $result = $DB->query("SHOW tables");
    while ($currow = $DB->fetch_array($result)) {
           if (isset($table[$currow[0]])) {
               sqldumptable($currow[0], $filehandle);
               fwrite($filehandle,"\n\n\n");
           }
    }
    fclose($filehandle);
    sa_exit("数据库已备份", "./index.php?action=main");
}// 备份操作结束


// 重要函数
function sqldumptable($table, $fp=0) {
         global $DB;

         $tabledump = "DROP TABLE IF EXISTS $table;\n";
         $tabledump .= "CREATE TABLE $table (\n";

         $firstfield=1;

         $fields = $DB->query("SHOW FIELDS FROM $table");
         while ($field = $DB->fetch_array($fields)) {
                if (!$firstfield) {
                    $tabledump .= ",\n";
                } else {
                    $firstfield=0;
                }
                $tabledump .= "   $field[Field] $field[Type]";
                if (!empty($field["Default"])) {
                    $tabledump .= " DEFAULT '$field[Default]'";
                }
                if ($field['Null'] != "YES") {
                    $tabledump .= " NOT NULL";
                }
                if ($field['Extra'] != "") {
                    $tabledump .= " $field[Extra]";
                }
         }
         $DB->free_result($fields);

         $keys = $DB->query("SHOW KEYS FROM $table");
         while ($key = $DB->fetch_array($keys)) {
                $kname=$key['Key_name'];
                if ($kname != "PRIMARY" and $key['Non_unique'] == 0) {
                  $kname="UNIQUE|$kname";
                }
                if(!is_array($index[$kname])) {
                  $index[$kname] = array();
                }
                $index[$kname][] = $key['Column_name'];
         }
         $DB->free_result($keys);

         while(list($kname, $columns) = @each($index)){
               $tabledump .= ",\n";
               $colnames=implode($columns,",");

               if ($kname == "PRIMARY") {
                  $tabledump .= "   PRIMARY KEY ($colnames)";
               } else {
                  if (substr($kname,0,6) == "UNIQUE") {
                      $kname=substr($kname,7);
                  }
                  $tabledump .= "   KEY $kname ($colnames)";
               }
         }

         $tabledump .= "\n);\n\n";
         if ($fp) {
             fwrite($fp,$tabledump);
         } else {
             echo $tabledump;
         }

         $rows = $DB->query("SELECT * FROM $table");
         $numfields = mysql_num_fields($rows);
         while ($row = $DB->fetch_array($rows)) {
                $tabledump = "INSERT INTO $table VALUES(";

                $fieldcounter=-1;
                $firstfield=1;
                while (++$fieldcounter<$numfields) {
                       if (!$firstfield) {
                           $tabledump.=", ";
                       } else {
                           $firstfield=0;
                       }

                       if (!isset($row[$fieldcounter])) {
                           $tabledump .= "NULL";
                       } else {
                           $tabledump .= "'".mysql_escape_string($row[$fieldcounter])."'";
                       }
                }

                $tabledump .= ");\n";

                if ($fp) {
                    fwrite($fp,$tabledump);
                } else {
                    echo $tabledump;
                }
         }
         $DB->free_result($rows);
}

cpfooter();

?>
