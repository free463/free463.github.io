// JavaScript Document
function DATA(songname,songurl)
{
	songname = (document.getElementById("list").options.length + 1) + "." + songname;
	var opt = new Option(songname,songurl);
	opt.setAttribute("downloadid",arguments[2]);
	document.getElementById("list").options.add(opt);
}

function GetRandomNum(Min,Max)
{
	var Range = Max - Min;
	var Rand = Math.random();
	return(Min + Math.round(Rand * Range));
}

function get_cookie(_name){
	var Res=eval('/'+_name+'=([^;]+)/').exec(document.cookie); return Res==null?'':unescape(Res[1]);
};

function chk_open() {
    if( 'Y' == get_cookie('open_player') )
    {
        location.href='close.htm';
    }else {
        setTimeout('chk_open()', 3000);
    }
};
chk_open();
