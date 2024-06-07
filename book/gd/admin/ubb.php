<script language="javascript">
<!--
function validate(theform){
if (document.all||document.getElementById){
for (i=0;i<theform.length;i++){
var tempobj=theform.elements[i]
if(tempobj.type.toLowerCase()=="submit"||tempobj.type.toLowerCase()=="reset")
tempobj.disabled=true
}
}
}
//-->
</script>

<script language="javascript">
<!--
function bbcode(thebbcode) {
if (thebbcode!=""){
document.form.content.value += thebbcode+" ";
document.form.content.focus();
}
}
//-->
</script>
<SCRIPT language=JavaScript1.2>





function html_trans(str) {

	str = str.replace(/\r/g,"");

	str = str.replace(/on(load|click|dbclick|mouseover|mousedown|mouseup)="[^"]+"/ig,"");

	str = str.replace(/<script[^>]*?>([\w\W]*?)<\/script>/ig,"");

	

	str = str.replace(/<a[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/ig,"\n[url=$1]$2[/url]\n");

	

	str = str.replace(/<font[^>]+color=([^ >]+)[^>]*>(.*?)<\/font>/ig,"\n[color=$1]$2[/color]\n");

	

	str = str.replace(/<img[^>]+src="([^"]+)"[^>]*>/ig,"\n[img]$1[/img]\n");

	

	str = str.replace(/<([\/]?)b>/ig,"[$1b]");

	str = str.replace(/<([\/]?)strong>/ig,"[$1b]");

	str = str.replace(/<([\/]?)u>/ig,"[$1u]");

	str = str.replace(/<([\/]?)i>/ig,"[$1i]");

	

	str = str.replace(/&nbsp;/g," ");

	str = str.replace(/&/g,"&");

	str = str.replace(/"/g,"\"");

	str = str.replace(/</g,"<");

	str = str.replace(/>/g,">");

	

	str = str.replace(/<br>/ig,"\n");

	str = str.replace(/<[^>]*?>/g,"");

	str = str.replace(/\[url=([^\]]+)\]\n(\[img\]\1\[\/img\])\n\[\/url\]/g,"$2");

	str = str.replace(/\n+/g,"\n");

	

	return str;

}



function trans(){

	var str = "";

	rtf.focus();

	rtf.document.body.innerHTML = "";

	rtf.document.execCommand("paste");

	str = rtf.document.body.innerHTML;

	if(str.length == 0) {

		alert("剪切版不存在超文本数据！");

		return "";

	}

	return html_trans(str);

}

</SCRIPT>

<input type="button" value="粗体" style="FONT-WEIGHT: bold" onclick="bbcode('[B][/B]')" title="粗体 (alt+b)" accesskey="b">
<input type="button" value="斜体" style="FONT-STYLE: italic" onclick="bbcode('[I][/I]')" title="斜体 (alt+i)" accesskey="i">
<input type="button" value="下划线" style="TEXT-DECORATION: underline" onclick="bbcode('[U][/U]')" title="下划线 (alt+u)" accesskey="u">
<input type="button" value="引用" onclick="bbcode('[QUOTE][/QUOTE]')" title="引用">
<input type="button" value="代码" onclick="bbcode('[CODE][/CODE]')" title="代码">
<input type="button" value="居中" onclick="bbcode('[CENTER][/CENTER]')" title="居中">
<input type="button" value="网址" style="TEXT-DECORATION: underline" onclick="bbcode('[URL][/URL]')" title="网址">
<input type="button" value="链接" style="TEXT-DECORATION: underline" onclick="bbcode('[URL=][/URL]')" title="链接">
<input type="button" value="E-mail" onclick="bbcode('[EMAIL][/EMAIL]')" title="Email">
<input type="button" value="图片" onclick="bbcode('[IMG][/IMG]')" title="图片">
<IFRAME id=rtf style="WIDTH: 0px; HEIGHT: 0px" marginWidth=0 marginHeight=0 
src="about:blank" scrolling=no></IFRAME>
<INPUT onclick="document.getElementById('content').value += trans()" type=button value=转贴>
<SCRIPT>

rtf.document.designMode="On";

</SCRIPT>