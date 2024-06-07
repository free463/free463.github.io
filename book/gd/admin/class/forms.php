<?php
// ========================== 文件说明 ==========================//
// 本文件说明：后台表单类
// =============================================================//
error_reporting(7);

class FORMS {

      function formheader($arguments=array()) {

               global $HTTP_SERVER_VARS;
               if ($arguments[enctype]){
                   $enctype="enctype=\"$arguments[enctype]\"";
               } else {
                   $enctype="";
               }
               if (!isset($arguments[method])) {
                   $arguments[method] = "post";
               }
               if (!isset($arguments[action])) {
                   $arguments[action] = $HTTP_SERVER_VARS[PHP_SELF];
               }

               if (!$arguments[colspan]) {
                   $arguments[colspan] = 2;
               }


               echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tableoutline\">\n";
               echo "<form action=\"$arguments[action]\" $enctype method=\"$arguments[method]\" name=\"$arguments[name]\" $arguments[extra]>\n";
               if ($arguments[title]!="") {
                   echo "<tr id=\"cat\">
                          <td class=\"tbhead\" colspan=\"$arguments[colspan]\">
                          <b><font color=\"#F5D300\">$arguments[title]</font></b>
                          </td>
                         </tr>\n";
               }

      }

      function formfooter($arguments=array()){

               echo "<tr class=\"tbhead\">\n";

               if ($arguments[confirm]==1) {

                   $arguments[button][submit][type] = "submit";
                   $arguments[button][submit][name] = "submit";
                   $arguments[button][submit][value] = "确认";
                   $arguments[button][submit][accesskey] = "y";

                   $arguments[button][back][type] = "button";
                   $arguments[button][back][value] = "取消";
                   $arguments[button][back][accesskey] = "r";
                   $arguments[button][back][extra] = " onclick=\"history.back(1)\" ";

               } elseif (empty($arguments[button])) {

                   $arguments[button][submit][type] = "submit";
                   $arguments[button][submit][name] = "submit";
                   $arguments[button][submit][value] = "提交";
                   $arguments[button][submit][accesskey] = "y";

                   $arguments[button][reset][type] = "reset";
                   $arguments[button][reset][value] = "重置";
                   $arguments[button][reset][accesskey] = "r";

               }

               if (empty($arguments[colspan])) {
                   $arguments[colspan] = 2;
               }

               echo "<td colspan=\"$arguments[colspan]\" align=\"center\">\n";
               if (isset($arguments) AND is_array($arguments)) {
                   foreach ($arguments[button] AS $k=>$button) {
                            if (empty($button[type])) {
                                $button[type] = "submit";
                            }
                            echo " <input class=\"bginput\" accesskey=\"$button[accesskey]\" type=\"$button[type]\" name=\"$button[name]\" value=\" $button[value] \" $button[extra]> \n";
                   }
               }
               echo "</td>
                     </tr>\n";
               echo "</form>\n";
               echo "</table>\n";

      }

      function tableheader() {
               echo "<table width=\"100%\" align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"4\" class=\"tableoutline\">\n";
      }

      function tablefooter() {
               echo "</table>\n";
      }

      function maketd($arguments = array()) {

               echo "<tr ".$this->getrowbg()." nowrap>";
               foreach ($arguments AS $k=>$v) {
                        echo "<td>$v</td>";
               }
               echo "</tr>\n";
      }

      function makeinput($arguments = array()) {

               if (empty($arguments[size])) {
                   $arguments[size] = 35;
               }
               if (empty($arguments[maxlength])) {
                   $arguments[maxlength] = 50;
               }
               if ($arguments[html]) {
                   $arguments[value] = htmlspecialchars($arguments[value]);
               }
               if (!empty($arguments[css])) {
                   $class = "class=\"$arguments[css]\"";
               }

               if (empty($arguments[type])) {
                   $arguments[type] = "text";
               }
               echo "<tr ".$this->getrowbg()." nowrap>
                      <td>$arguments[text]</td>
                       <td>
                         <input $class type=\"$arguments[type]\" name=\"$arguments[name]\" size=\"$arguments[size]\" maxlength=\"$arguments[maxlength]\" value=\"$arguments[value]\" $arguments[extra]>\n
                       </td>
                     </tr>\n";

      }

      function maketextarea($arguments = array()){

               if (empty($arguments[cols])) {
                   $arguments[cols] = 50;
               }
               if (empty($arguments[rows])) {
                   $arguments[rows] = 7;
               }
               if (!empty($arguments[html])) {
                   $arguments[value] = htmlspecialchars($arguments[value]);
               }

               echo "<tr ".$this->getrowbg()." nowrap>
                     <td valign=\"top\">$arguments[text]</td>
                     <td>
                       <textarea type=\"text\" name=\"$arguments[name]\" cols=\"$arguments[cols]\" rows=\"$arguments[rows]\" $arguments[extra]>$arguments[value]</textarea>
                     </td>
                   </tr>\n";
      }


	  function makeselect($arguments = array()){

               if ($arguments[html]==1) {
                   $value = htmlspecialchars($value);
               }
               if ($arguments[multiple]==1) {
                   $multiple = " multiple";
                   if ($arguments[size]>0) {
                       $size = "size=$arguments[size]";
                   }
               }

               echo "<tr ".$this->getrowbg().">
                      <td valign=\"top\">$arguments[text]</td>
                      <td>
                      <select name=\"$arguments[name]\"$multiple $size>\n";
               if (is_array($arguments[option])) {

                   foreach ($arguments[option] AS $key=>$value) {
                            if (!is_array($arguments[selected])) {
                                if ($arguments[selected]==$key) {
                                    echo "<option value=\"$key\" selected class=\"{$arguments[css][$key]}\">$value</option>\n";
                                } else {
                                    echo "<option value=\"$key\" class=\"{$arguments[css][$key]}\">$value</option>\n";
                                }

                            } elseif (is_array($arguments[selected])) {

                                if ($arguments[selected]["$key"]==1) {
                                    echo "<option value=\"$key\" selected class=\"{$arguments[css][$key]}\">$value</option>\n";
                                } else {
                                    echo "<option value=\"$key\" class=\"{$arguments[css][$key]}\">$value</option>\n";
                                }
                            }
                   }
               }

               echo "</select>\n";
               echo "</td>
                     </tr>\n";

      }

      function makeyesno($arguments = array()) {

               $arguments[option] = array('1'=>'是','0'=>'否');
               $this->makeselect($arguments);

      }

      function makehidden($arguments = array()){

               echo "<input type=\"hidden\" name=\"$arguments[name]\" value=\"$arguments[value]\">\n";

      }

      function getrowbg() {

               global $bgcounter;
               if ($bgcounter++%2==0) {
                   return "class=\"firstalt\"";
               } else {
                   return "class=\"secondalt\"";
               }

      }

}
?>
