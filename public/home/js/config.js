var m3u8_host = 'https://langren16.com';
var api_host = 'https://langren85.com';
var img_host = 'https://90rrxx.com';

var user = gStorage('user');

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
var road = getQueryString('road');
if(road == 2){
    m3u8_host = 'https://991video.com';
}
if(road == 3){
    m3u8_host = 'https://90rrxx.com';
}
var href = window.location.href;

//存
function sStorage(key,val) {
    var str = JSON.stringify(val);
    sessionStorage.setItem(key,str);
}
//读取
function gStorage(key) {
    str = sessionStorage.getItem(key);
    //重新转换为对象
    obj = JSON.parse(str);
    return obj;
}

/*
$.ping = function(option)
{
    var ping, requestTime, responseTime ;
    var getUrl = function(url){    //保证url带http://
        var strReg="^((https|http)?://){1}"
        var re=new RegExp(strReg);
        return re.test(url)?url:"http://"+url;
    }
    $.ajax({
        url: getUrl(option.url)+'?'+ (new Date()).getTime(),  //设置一个空的ajax请求
        type: 'GET',
        dataType: 'html',
        timeout: 10000,
        beforeSend : function()
        {
            if(option.beforePing) option.beforePing();
            requestTime = new Date().getTime();
        },
        complete : function()
        {
            responseTime = new Date().getTime();
            ping = Math.abs(requestTime - responseTime);
            if(option.afterPing) option.afterPing(ping);
        }
    });

    if(option.interval && option.interval > 0)
    {
        var interval = option.interval * 1000;
        setTimeout(function(){$.ping(option)}, interval);
        // option.interval = 0;        // 阻止多重循环
        // setInterval(function(){$.ping(option)}, interval);
    }
};*/
