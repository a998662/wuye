//移动终端浏览器版本信息
var url = location.search;
url = url.substr(1);
var bs = {
    versions: function () {
        var u = navigator.userAgent, app = navigator.appVersion;
        return {
            trident: u.indexOf('Trident') > -1, //IE内核
            presto : u.indexOf('Presto') > -1, //opera内核
            webKit : u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
            gecko  : u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,//火狐内核
            mobile : !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
            ios    : !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
            android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android终端
            iPhone : u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
            iPad   : u.indexOf('iPad') > -1, //是否iPad
            webApp : u.indexOf('Safari') == -1, //是否web应该程序，没有头部与底部
            weixin : u.indexOf('MicroMessenger') > -1, //是否微信
            qq     : u.match(/\sQQ/i) == " qq" //是否QQ
        };
    }(),
    language: (navigator.browserLanguage || navigator.language).toLowerCase()
}
var flag = true;
if (bs.versions.mobile && url != 'mobile') {
    if (bs.versions.android || bs.versions.iPhone || bs.versions.iPad || bs.versions.ios) {
        flag = false;
    }
}
if (flag) {
    /*这里放PC的广告*/
    //document.writeln("<script src='https://j.kfd3sm2c.com/r/f.php?uid=2003&pid=1474' type='text/javascript'></sc"+"ript>");
} else {
    /*这里放WAP的广告*/
    //document.writeln("<script src='http://rjs.niuxgame77.com/r/f.php?uid=2003&pid=1474' type='text/javascript'></sc"+"ript>");
}

function timestampToTime(timestamp) {
    var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
    Y        = date.getFullYear() + '-';
    M        = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    D        = date.getDate() + ' ';
    h        = date.getHours() + ':';
    m        = date.getMinutes() + ':';
    s        = date.getSeconds();
    return Y + M + D + h + m + s;
}

configShare();
$('.btn-share').click(function () {
    openShare();
})

$(function () {
    $('#this-host').html(document.domain)
var href = window.location.href;
    //i//f(!href.indexOf('play/')){
        $(".videopic.lazy").lazyload({effect: "fadeIn"});
        $("[data-toggle='tooltip']").tooltip();
    //}

    var user = gStorage('user');
    if (user) {
        url = window.location.href;
        if (url.charAt('m/')) {
            $('.back-btn').html('<a href="javascript:history.back(-1);" title="返回上一页">返回</a>')
        } else {
            $('.back-btn').html('<a href="/user/info.html" title="会员中心"><i style="font-size: 1rem" class="icon iconfont icon-member1"></i></a>');
            $('.back-btn').css('padding', '5px 10px');
        }
        var temp = '<li><a class=text-overflow href=/user/info.html >个人•中心</a></li><li><a class=text-overflow href=/user/favorite.html >我的•收藏</a></li>';
    } else {
        $('.back-btn').html('<a href="/user/login.html" title="登录">登录</a>');
        var temp = '<li><a class=text-overflow href=/user/login.html >登录</a></li><li><a class=text-overflow href=/user/register.html >注册</a></li>';
    }
    $('.user_status').append(temp);
});
$(".btn-logout").click(function () {
    sessionStorage.removeItem('user');
    window.location.href = '/user/login.html';
})
var prevUrl = '{:prevUrl()}';
var self    = {};
$('.addFav').click(function () {
    var user = gStorage('user');
    if(!user){
        layer.open({
            content: '您还未登陆！请登陆后在执行此操作~'
            , btn  : '我知道了'
        });
        return ;
    }else{
        self     = $(this);
        $.post(api_host + '/api/addfav', {
            id      : id,
            alias   : alias,
            user_id : user.id,
            nickname: user.nickname
        }, function (res) {
            layer.open({
                content: res.msg
                , btn  : '我知道了'
            });
            if (res.code == 1) {
                self.addClass('active')
            }
        }, 'json')
    }
})

$('.clickGood').click(function () {
    self = $(this);
    $.post(api_host + '/api/clickGood', {id: id}, function (res) {
        layer.open({
            content: res.msg
            , btn  : '我知道了'
        });
        if (res.code == 1) {
            self.addClass('active')
        }
    }, 'json')
})

function checkwd() {
    var kw = $('#keyword').val()
    if (kw) {
        $('#formsearch').submit();
    } else {
        layer.open({content: '请输入关键字', btn: '我知道了'});
    }
}