<?php
class Template {
  var $classname = "Template";
  var $debug     = false;
  var $file  = array();
  var $root   = "";
  var $varkeys = array();
  var $varvals = array();
  var $unknowns = "remove";
  var $halt_on_error  = "yes";
  var $last_error     = "";
  function Template($root = ".", $unknowns = "remove") {
    $this->set_root($root);
    $this->set_unknowns($unknowns);
  }
  function set_root($root) {
    if (!is_dir($root)) {
      $this->halt("set_root: $root is not a directory.");
      return false;
    }
    
    $this->root = $root;
    return true;
  }
  function set_unknowns($unknowns = "keep") {
    $this->unknowns = $unknowns;
  }
  function set_file($handle, $filename = "") {
    if (!is_array($handle)) {
      if ($filename == "") {
        $this->halt("set_file: For handle $handle filename is empty.");
        return false;
      }
      $this->file[$handle] = $this->filename($filename);
    } else {
      reset($handle);
      while(list($h, $f) = each($handle)) {
        $this->file[$h] = $this->filename($f);
      }
    }
  }
  function set_block($parent, $handle, $name = "") {
    if (!$this->loadfile($parent)) {
      $this->halt("subst: unable to load $parent.");
      return false;
    }
    if ($name == "")
      $name = $handle;

    $str = $this->get_var($parent);
    $reg = "/<!--\s+BEGIN $handle\s+-->(.*)\n\s*<!--\s+END $handle\s+-->/sm";
    preg_match_all($reg, $str, $m);
    $str = preg_replace($reg, "{" . "$name}", $str);
    $this->set_var($handle, $m[1][0]);
    $this->set_var($parent, $str);
  }
  function set_var($varname, $value = "") {
    if (!is_array($varname)) {
      if (!empty($varname))
        if ($this->debug) print "scalar: set *$varname* to *$value*<br>\n";
        $this->varkeys[$varname] = "/".$this->varname($varname)."/";
        $this->varvals[$varname] = $value;
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($k))
          if ($this->debug) print "array: set *$k* to *$v*<br>\n";
          $this->varkeys[$k] = "/".$this->varname($k)."/";
          $this->varvals[$k] = $v;
      }
    }
  }
  function subst($handle) {
    if (!$this->loadfile($handle)) {
      $this->halt("subst: unable to load $handle.");
      return false;
    }

    $str = $this->get_var($handle);
    $str = @preg_replace($this->varkeys, $this->varvals, $str);
    return $str;
  }
  function psubst($handle) {
    print $this->subst($handle);
    
    return false;
  }
  function parse($target, $handle, $append = false) {
    if (!is_array($handle)) {
      $str = $this->subst($handle);
      if ($append) {
        $this->set_var($target, $this->get_var($target) . $str);
      } else {
        $this->set_var($target, $str);
      }
    } else {
      reset($handle);
      while(list($i, $h) = each($handle)) {
        $str = $this->subst($h);
        $this->set_var($target, $str);
      }
    }
    
    return $str;
  }
  
  function pparse($target, $handle, $append = false) {
    print $this->parse($target, $handle, $append);
    return false;
  }
  function get_vars() {
    reset($this->varkeys);
    while(list($k, $v) = each($this->varkeys)) {
      $result[$k] = $this->varvals[$k];
    }
    
    return $result;
  }
  function get_var($varname) {
    if (!is_array($varname)) {
      return $this->varvals[$varname];
    } else {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        $result[$k] = $this->varvals[$k];
      }
      
      return $result;
    }
  }
  function get_undefined($handle) {
    if (!$this->loadfile($handle)) {
      $this->halt("get_undefined: unable to load $handle.");
      return false;
    }
    
    preg_match_all("/\{([^}]+)\}/", $this->get_var($handle), $m);
    $m = $m[1];
    if (!is_array($m))
      return false;

    reset($m);
    while(list($k, $v) = each($m)) {
      if (!isset($this->varkeys[$v]))
        $result[$v] = $v;
    }
    
    if (count($result))
      return $result;
    else
      return false;
  }
  function finish($str) {
    switch ($this->unknowns) {
      case "keep":
      break;
      
      case "remove":
        $str = preg_replace('/{[^ \t\r\n}]+}/', "", $str);
      break;

      case "comment":
        $str = preg_replace('/{([^ \t\r\n}]+)}/', "<!-- Template $handle: Variable \\1 undefined -->", $str);
      break;
    }
    
    return $str;
  }
  function p($varname) {
    print $this->finish($this->get_var($varname));
  }

  function get($varname) {
    return $this->finish($this->get_var($varname));
  }
  function filename($filename) {
    if (substr($filename, 0, 1) != "/") {
      $filename = $this->root."/".$filename;
    }
    
    if (!file_exists($filename))
      $this->halt("filename: file $filename does not exist.");

    return $filename;
  }
  function varname($varname) {
    return preg_quote("{".$varname."}");
  }
  function loadfile($handle) {
    if (isset($this->varkeys[$handle]) and !empty($this->varvals[$handle]))
      return true;

    if (!isset($this->file[$handle])) {
      $this->halt("loadfile: $handle is not a valid handle.");
      return false;
    }
    $filename = $this->file[$handle];

    $str = implode("", @file($filename));
    if (empty($str)) {
      $this->halt("loadfile: While loading $handle, $filename does not exist or is empty.");
      return false;
    }

    $this->set_var($handle, $str);
    
    return true;
  }
  function halt($msg) {
    $this->last_error = $msg;
    
    if ($this->halt_on_error != "no")
      $this->haltmsg($msg);
    
    if ($this->halt_on_error == "yes")
      die("<b>Halted.</b>");
    
    return false;
  }
  function haltmsg($msg) {
    printf("<b>Template Error:</b> %s<br>\n", $msg);
  }
//保存到文件中
  function savetofile ($filename, $varname){
    $data = $this->finish($this->get_var($varname));
    $fp = fopen($filename,"w+");
    if (!is_writable ($filename)){ 
      die ("文件：".$filename."不可写，生成html页失败！");
    }
    if (!fwrite ($fp,$data)){ //将信息写入文件
      die ("生成文件".$filename."失败！");
    }
    fclose ($fp); 
  }
//清除已赋值数组
function renew(){
$this->varkeys=array();
$this->varvals=array();
$this->file=array();
}
}
?>
