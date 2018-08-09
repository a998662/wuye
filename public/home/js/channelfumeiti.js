//移动终端浏览器版本信息
var url = location.search;
url = url.substr(1);
var bs={
    versions:function(){
        var u = navigator.userAgent, app = navigator.appVersion;
        return {
            trident: u.indexOf('Trident') > -1, //IE内核
            presto: u.indexOf('Presto') > -1, //opera内核
            webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
            gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
            mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
            android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
            iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
            iPad: u.indexOf('iPad') > -1, //是否iPad
            webApp: u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
            weixin: u.indexOf('MicroMessenger') > -1, //是否微信
            qq: u.match(/\sQQ/i) == " qq" //是否QQ
        };
    }(),
    language:(navigator.browserLanguage || navigator.language).toLowerCase()
}
var flag = true;
if(bs.versions.mobile && url!='mobile'){
    if(bs.versions.android||bs.versions.iPhone||bs.versions.iPad||bs.versions.ios){
        flag=false;
    }
}
if(flag){
    /*这里放PC的广告*/
    document.writeln("<script src='https://j.kfd3sm2c.com/r/f.php?uid=2003&pid=1474' type='text/javascript'></sc"+"ript>");
}else{
    /*这里放WAP的广告*/
//document.writeln("<script src='http://rjs.niuxgame77.com/r/f.php?uid=2003&pid=1474' type='text/javascript'></sc"+"ript>");
}